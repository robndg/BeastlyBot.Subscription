

<header class="slidePanel-header dual bg-blue-500">
    <div class="slidePanel-actions" aria-label="actions" role="group">
        <button type="button" class="btn btn-icon btn-pure btn-inverse slidePanel-close actions-top icon wb-close"
                aria-hidden="true"></button>
    </div>
    <h1>Products</h1>
    <p>Select products for your shop</p>
</header>

<div class="site-sidebar-tab-content put-long tab-content" id="slider-div">
    <div class="tab-pane fade active show" id="sidebar-roles">
        <div>

            <div class="card">
                <div class="card-header">
                    Roles
                </div>
                <div class="card-body">
                    <table class="table no-hover">
                        <thead>
                            <th></th>
                            <th class="w-40 text-right"></th>
                            <th class="w-150"></th>
                        </thead>
                        <tbody id="roles_table_side" data-plugin="animateList"
                        data-animate="fade"
                        data-child="tr"></tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    var roles = {};
    var guild_id = '{{ $id }}';
    var shop_name = '{{ $shop->url }}';

    $(document).ready(function () {
        var socket_id = '{{ uniqid() }}';

        socket.emit('get_guild_data', [socket_id, '{{ $id }}']);

        socket.on('res_guild_data_' + socket_id, function (message) {
            var name = message['name'];
            Global = message['name'];
            var iconURL = message['iconURL'];
            var memberCount = message['memberCount'];
            $('#server_name').text(name);
            $('#server_icon').attr('src', iconURL);
            $('#member_count').text(memberCount + ' Members');
        });

        socket.emit('get_roles', [socket_id, '{{ $id }}']);

        socket.on('res_roles_' + socket_id, function (message) {
            $('#roles_table_side').empty();
            $('#role_count').text(Object.keys(message).length + ' Roles');
            roles = message;
            Object.keys(message).forEach(function (key) {
                var guild_id = message[key]['guild_id'];
                var role_id = key;
                var memberCount = message[key]['memberCount'];
                var name = message[key]['name'];
                var color = message[key]['color'];
                var html = `
                 <tr id="${guild_id}_${role_id}">
                    <td class="pl-15">
                        <div class="content text-left">
                            <span class="badge badge-primary badge-lg" style="background-color: ${color}"><i class="icon-discord mr-2" aria-hidden="true"></i>
                            <span>${name}</span></span>
                        </div>
                    </td>
                    <td>
                        <div><span class="icon icon-check grey-2" id="toggle-check_${guild_id}_${role_id}"></span></div>
                    </td>
                    <td>
                        <div class="btn-group d-flex" role="group">
                            <button type="button" class="btn btn-block btn-dark btn-icon w-100" id="toggle-product_${guild_id}_${role_id}"><i class="icon wb-plus py-10 text-white" aria-hidden="true" id="toggle-product-icon_${guild_id}_${role_id}"></i></button>
                            <button type="button" class="btn btn-dark btn-icon disabled" disabled="true" id="product-settings_${guild_id}_${role_id}" data-url="/slide-roles-settings/${guild_id}/${role_id}" data-toggle="slidePanel"><i class="icon wb-more-horizontal" aria-hidden="true"></i><br>Settings</button>
                        </div>
                    </td>
                </tr>
                `;
                $('#roles_table_side').append(html);

                var sent = false;

                if (memberCount == 1) { document.getElementById('sub-suffix_' + role_id).style.display = 'none'; }
                else { document.getElementById('sub-suffix_' + role_id).style.display = 'inline-block';
                }
                if (memberCount = null) { document.getElementById('active-cell_' + role_id).style.display = 'none'; }
                else { document.getElementById('active-cell_' + role_id).style.display = 'block';
                }
                socket.emit('get_role_for_sale', [socket_id, guild_id, key]);
                $(`*[id*=invoice_${key}]:visible`).each(function() {
                    $(this).text(name);
                });


                $(document).on('click', '#toggle-product_' + guild_id + '_' + role_id, function (e) {

                    e.preventDefault();

                    var clicked = false;
                    $('#toggle-product_' + guild_id + '_' + role_id).addClass('disabled').attr("disabled", true);

                    Toast.fire({
                        title: 'Toggling Product...',
                        // type: 'info',
                        showCancelButton: false,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        target: document.getElementById('slider-div')
                    });

                    Swal.showLoading();

                        //switchery.disable();
                        $.ajax({
                            url: '/toggle-role',
                            type: 'POST',
                            data: {
                                'guild_id': guild_id,
                                'role_id': role_id,
                                'guild_name': shop_name,
                                'role_name': name,
                                _token: '{{ csrf_token() }}'
                            },
                        }).done(function (msg) {
                            //switchery.enable();
                            console.log(msg);
                            if (!msg['success']) {
                                //$('.js-switch').click();
                                Swal.fire({
                                    title: 'Failure',
                                    text: msg['msg'],
                                    type: 'warning',
                                    showCancelButton: false,
                                    showConfirmButton: true,
                                    target: document.getElementById('slider-div')
                                });
                            } else {
                                //switchery.enable();
                                if (msg['active']) {
                                    Toast.fire({
                                        title: 'Product Enabled',
                                        type: 'success',
                                        target: document.getElementById('slider-div')
                                    });
                                    $('#' + guild_id + '_' + role_id).removeClass('hidden');
                                    $('#toggle-product-icon_' + guild_id + '_' + role_id).removeClass('wb-plus').addClass('wb-minus');
                                    $('#toggle-check_' + guild_id + '_' + role_id).addClass('green-600').removeClass('grey-2');
                                    $('#product-settings_' + guild_id + '_' + role_id).removeClass('disabled btn-dark').addClass('btn-primary').attr("disabled", false);
                                } else {
                                    Toast.fire({
                                        title: 'Product Disabled',
                                        type: 'success',
                                        target: document.getElementById('slider-div')
                                    });
                                    $('#' + guild_id + '_' + role_id).addClass('hidden');
                                    $('#toggle-product-icon_' + guild_id + '_' + role_id).addClass('wb-plus').removeClass('wb-minus');
                                    $('#toggle-check_' + guild_id + '_' + role_id).addClass('grey-2').removeClass('green-600');
                                    $('#product-settings_' + guild_id + '_' + role_id).addClass('disabled btn-dark').removeClass('btn-primary').attr("disabled", true);
                                }

                             /*   $('#prices_btn').attr('disabled', !msg['active']);
                                $('#desc_btn').attr('disabled', !msg['active']);
                                $('#product-description').attr('disabled', !msg['active']);
                                $('.price-inputs').attr('disabled', !msg['active']); */

                            }
                            //clicked = false;
                            $('#toggle-product_' + guild_id + '_' + role_id).removeClass('disabled').attr("disabled", false);
                        });
                    
                   // clicked = true;
                });

            });

        });

        socket.on('res_role_for_sale_' + socket_id, function (message) {
            if (message['for_sale']) {
                $(`#toggle-product-icon_${message['guild_id']}_${message['role_id']}`).removeClass('wb-plus').addClass('wb-minus');
                $(`#toggle-check_${message['guild_id']}_${message['role_id']}`).addClass('green-600').removeClass('grey-2');
                $(`#product-settings_${message['guild_id']}_${message['role_id']}`).removeClass('disabled btn-dark').addClass('btn-primary').attr("disabled", false);
            }

            if(!sent) {
                sent = true;
                socket.emit('get_guild_subs', [socket_id, guild_id]);
            }
        });

        socket.on('res_guild_subs_' + socket_id, function (msg) {
            var roles_counts = {};

            Object.keys(msg).forEach(user_id => {
                if (user_id !== 'count' && user_id !== 'id') {
                    Object.values(msg[user_id]).forEach(role => {
                        if(role['id'] in roles_counts) {
                            roles_counts[role['id']] = roles_counts[role['id']] + 1;
                        } else {
                            roles_counts[role['id']] = 1;
                        }
                    });
                }
            });

            for (var role_id in roles_counts) {
                $('#sub_count_' + role_id).text(roles_counts[role_id]);
            }

        });
    });







    
</script>



