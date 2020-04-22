module.exports = {init_subscriptions, verify_and_end_subscriptions};
var index = require('../index.js');
var task_handler = require('../TaskHandler.js');
var refund_handler = require('./RefundHandler.js');

async function init_subscriptions() {
    /**
     * Any new orders are stored in the "orders" table under the Stripe subscription ID.
     * Once an order is fulfilled (giving the user the desired role) the row is removed from the database.
     */
    index.mysqlConnection.query("SELECT * FROM orders", function(err, result) {
        // simple error handling
        if(err) {
            task_handler.sendMessage(err.message, 3);
            return;
        }

        // if there are no new orders return
        if(result.length < 1) return;

        // iterate over all new orders in the table (these are deleted at the bottom once successfully processed)
        for(var i = 0; i < result.length; i++) {
            // the Stripe subscription ID
            var sub_id = result[i].id;
            // grab the subscription object from Stripe
            index.stripe.subscriptions.retrieve(sub_id, async function(err, subscription) {
                // simple error handling
                if(err) {
                    task_handler.sendMessage(err.message, 3);
                    return;
                }

                var user_id = subscription.metadata['id'];
                var discord_id = subscription.metadata['discord_id'];
                var guild_id = subscription.items.data[0].plan.id.split('_')[0];
                var role_id = subscription.items.data[0].plan.id.split('_')[1];
                var guild = index.bot.guilds.cache.get(guild_id);
                var valid_guild = guild !== null && guild !== undefined;
                var role = valid_guild ? guild.roles.cache.get(role_id) : null;
                var valid_role = role !== null && role !== undefined;

                // if invalid subscription status (anything != completed) we break out of this subscription object
                var valid_subscription_status = await valid_sub_status(subscription, discord_id);
                if(!valid_subscription_status) {
                    end_subscription(subscription, true, true, true);
                    return;
                }

                // if invalid guild or role we break out of this subscription object
                var valid_guild_role = await valid_guild_role(subscription, discord_id, valid_guild, valid_role);
                if(!valid_guild_role) {
                    end_subscription(subscription, true, true, true);
                    return;
                }

                /**
                 * At this point we've validated everything to ensure this is a good and valid order/subscription
                 */
                // try to get the user from the guild
                guild.members.fetch(discord_id).then(guildMember => {
                    // try to add the desired role to the GuildMember
                    guildMember.roles.add(role_id).then(user => {
                        // let the user know the role was purchased successfully
                        user.send(`Purchase completed for the role ${role.name} in the ${guild.name} server! Role added.`);
                        task_handler.sendWebNotificationFromSub(subscription, 'success', `${guildMember.displayName} purchased ${role.name}.`, `${user_id}`, null);
                    }).catch(error => {
                        console.log(error.message);
                        // if there is an error we need to refund and cancel the order and let the product owner know
                        end_subscription(subscription, true, true, true);
                        // TODO: this doesnt seem to remove their subscription if it failed, still shows in subscriptions
                        if(error.message == 'Missing Permissions'){
                            guildMember.send(`Move Bot Role Higher (missing permissions). Failed to add the ${role.name} role to your account in the ${guild.name} server. Transaction cancelled. Order refunded.`);
                            task_handler.sendWebNotificationFromSub(subscription, 'error', `Move Bot Role Higher (missing permissions). Could not add ${role.name} role to ${guildMember.displayName}. Order refunded.`);
                        } else {
                            guildMember.send(`Failed to add the ${role.name} role to your account in the ${guild.name} server. Transaction cancelled. Order refunded.`);
                            task_handler.sendWebNotificationFromSub(subscription, 'error', `Could not add ${role.name} role to ${guildMember.displayName}. Order refunded.`);
                        }
                    });
                }).catch(error => {
                    // TODO: Auto accept invite to guild from bot to user
                    var invite = guild.channels.cache.array()[0].createInvite({
                        maxAge: 10 * 60 * 1000, // maximum time for the invite, in milliseconds
                        maxUses: 1 // maximum times it can be used
                      });

                    // if we are here then the user who purchased the role is not in the server
                    index.bot.users.fetch(discord_id).then(user => {
                        user.send(`You are not in the ${guild.name} server. You have until 24 hours to accept this invite or the order will be cancelled and refunded.`);
                        user.send(`${invite}`);
                    }).catch(error => {
                        console.log(error.message)
                    });
                });

                // order successful so we are just deleting the order from table
                // end_subscription(subscription, true, false, false);
            });
        }
    });
};

function verify_and_end_subscriptions() {
    // check for orders not completed in 24 hours as well and remove those and end/cancel/refund. It's from above where people are not in the server and need to accept the invite.
    var status_to_check = ['incomplete', 'incomplete_expired', 'past_due', 'canceled', 'unpaid'];

    var unix_end = new Date();
    // end date is today
    unix_end.setDate(unix_end.getDate());

    var unix_start = new Date();
    // start day is 50 days ago
    unix_start.setDate(unix_end.getDate()-50);

    // get all subscriptions that are either 'incomplete', 'incomplete_expired', 'past_due', 'canceled', or 'unpaid'
    // that also are gt (greater than) than the start date and lt (less than) the end date (today)
    // Go over the subscriptions and end them (removing role from user)
    // TODO: IT seems the current period end and current period start not working
    status_to_check.forEach(function(val, i) {
        index.stripe.subscriptions.list({
            status: val,
            // current_period_end: {
            //     lt: unix_end
            // },
            // current_period_start: {
            //     gt: unix_start
            // }
         }, function(err, subscriptions) {
            if(err) {
                task_handler.sendMessage(err.message, 3);
                console.log(err);
                return;
            }
            for (var subscriptionsKey in subscriptions.data) {
                var subscription = subscriptions.data[subscriptionsKey];
                // console.log(subscription);
                if (subscription.metadata['ended'] != 'true'){
                    if(subscription.plan.id == ('plan_GbiiDSRkOovFPF' || 'plan_GbiisRXZmt3IFC')){
                        endPartnerSubscription(subscription);
                    }else{
                        end_subscription(subscription);
                    }
                }
            }
        });
    });
}

async function valid_sub_status(subscription, discord_id) {
    // if the subscription status is incomplete then the payment of the first invoice failed
    if(subscription.status !== ('active' || 'trialing')) {
        // let the user know that their order failed
        index.bot.users.fetch(discord_id).then(user => {
            index.stripe.invoices.retrieve(subscription.latest_invoice, function(err, invoice) {
                // simple error handling
                if (err) {
                    task_handler.sendMessage(err.message, 3);
                    return false;
                }
                // let the user know over discord that their order failed
                user.send(`Payment processing failed for invoice #${invoice.number}. Transaction cancelled.`);
            });
        }).catch(error => {
            console.log(error.message)
        });
        return false;
    }

    return true;
}

async function valid_guild_role(subscription, discord_id, valid_guild, valid_role) {
    // ensure both the guild and role are valid before processing the order any further
    if(!valid_guild || !valid_role) {
        // let the user know the payment failed and the product owner
        index.bot.users.fetch(discord_id).then(user => {
            index.stripe.invoices.retrieve(subscription.latest_invoice, function(err, invoice) {
                // simple error handling
                if (err) {
                    task_handler.sendMessage(err.message, 3);
                    return false;
                }

                var msg = !valid_guild && !valid_role ? 'server and role.' : !valid_guild ? 'server.' : 'role.';
                // send message over discord to the user who ordered the role
                user.send(`Payment processing failed for invoice #${invoice.number}. Invalid ${msg}.  Transaction cancelled. Order refunded.`);
                // send notification to web interface for the owner
                task_handler.sendWebNotificationFromSub(subscription, 'error', `A ${msg} could not be found! Invoice #${invoice.number} was refunded.`, `${discord_id}`, `${invoice.number}`);
            });
        }).catch(error => {
            console.log(error.message)
        });
        return false;
    }
    return true;
}

function end_subscription(subscription, delete_order, cancel, refund) {
    if(delete_order) {
        index.mysqlConnection.query(`DELETE FROM orders WHERE id='${subscription.id}'`, function (err, result) {
            // simple error handling
            if (err) {
                task_handler.sendMessage(err.message, 3);
                console.log(err);
                return;
            }
        });
    }

    if(cancel) {
        index.stripe.subscriptions.del(
            subscription.id,
            function(err, confirmation) {
                if (err) {
                    task_handler.sendMessage(err.message, 3);
                    console.log(err);
                    return;
                }
            }
          );
    }

    if(refund) {
        refund_handler.refundOrder(subscription, null);
    }
}
