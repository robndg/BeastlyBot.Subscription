<script type="text/javascript">
        var subscriptions = JSON.parse('{!! json_encode($subscriptions) !!}');
        var finished = [];

        $(document).ready(function () {
            var discord_username, discord_discriminator;
            socket.on('connect', function () {
                socket.emit('get_user_data', [socket_id, '{{ auth()->user()->DiscordOAuth->discord_id }}']);
            });
            socket.on('res_user_data_' + socket_id, function (message) {
                $('#avatarIconHeader').attr('src', message['avatar']);
                discord_username = message['name'];
                discord_discriminator = message['discriminator'];
                $('#discord_username').text(discord_username + " #" + discord_discriminator);
            });

            @foreach($subscriptions as $subscription)
                var data = '{{ $subscription['items']['data'][0]['plan']['id'] }}';
                var guild_id = data.split('_')[0];
                var role_id = data.split('_')[1];
                socket.emit('get_role_data', [socket_id, guild_id, role_id]);
            @endforeach

            socket.on('res_role_data_' + socket_id, function(message) {
                jQuery.each(subscriptions, function(i, val) {
                    var id = val.items.data[0].plan.id;
                    var guild_id = id.split('_')[0];
                    var role_id = id.split('_')[1];
                    if(guild_id === message['guild_id'] && role_id === message['id'] && !finished.includes(val.id)) {
                        finished.push(val.id);
                        var dateObj = new Date(val.current_period_end * 1000);
                        var month = dateObj.getUTCMonth() + 1; //months from 1-12
                        var day = dateObj.getUTCDate();
                        var year = dateObj.getUTCFullYear();
                        var newdate = month + "/" + day + "/" + year;
                        if(val.status == 'active' || val.status == 'trialing'){
                        $('#activeSubsTable').append(getHTML(i, message['guild_name'], message['name'], message['color'], toTitleCase(val.status), newdate));
                        }else{
                        $('#inactiveSubsTable').append(getHTML(i, message['guild_name'], message['name'], message['color'], toTitleCase(val.status), newdate));
                        }
                    }
                });
            });
        });

        function toTitleCase(str) {
            return str.replace(/\w\S*/g, function(txt){
                return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
            });
        }

        function getHTML(sub_id, guild_name, role_name, role_color, status, date_end) {
            if(status == 'Active' || status == 'Trialing'){
                var status_color = 'green-500';
            }else{
                var status_color = 'yellow-500';
            }
            var status_payment = status == 'Active' ? '' : 'd-none';
            return html = `
            <tr id="subscription_${sub_id}" data-url="/slide-account-subscription-settings/${sub_id}?role_name=${role_name}&guild_name=${guild_name}&role_color=${role_color}" data-toggle="slidePanel">
                    <td class="cell-200 text-left">
                        <h4>` + guild_name + `</h4>
                    </td>
                    <td class="text-left">

                        <span class="badge badge-primary text-left" style="background-color: ` + role_color + `">` + role_name + `</span>

                    </td>
                    <td class="cell-30">
                        <i class="icon wb-payment grey-4 ${status_payment}" aria-hidden="true"></i>
                    </td>
                    <td class="cell-150 text-right">
                        <div class="time"> ` + date_end + `</div>
                        <div class="identity">` + status + `<i class="icon ml-2 mt-1 wb-medium-point ${status_color}" aria-hidden="true"></i>
                        </div>
                    </td>
                    <td class="cell-30 hidden-md-down">

                    </td>
                </tr>
            `;
        }
    </script>
