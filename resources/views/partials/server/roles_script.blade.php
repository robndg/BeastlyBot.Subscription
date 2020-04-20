<script type="text/javascript">
    var roles = {};
    var guild_id = '{{ $id }}';
    var shop_name = '{{ $shop->url }}';
    var Global = {};
    var enabled_get = false;
    var is_enabled = 'inactive-role';

    $(document).ready(function () {
        var socket_id = '{{ uniqid() }}';

        socket.emit('get_guild_data', [socket_id, '{{ $id }}']);

        socket.on('res_guild_data_' + socket_id, function (message) {
            var name = message['name'];
            Global.name = name;
            var iconURL = message['iconURL'];
            var memberCount = message['memberCount'];
            $('#server_name').text(name);
            $('#server_icon').attr('src', iconURL);
            $('#member_count').text(memberCount + ' Members');
        });

        socket.emit('get_roles', [socket_id, '{{ $id }}']);

            socket.on('res_roles_' + socket_id, function (message) {
           
                Object.keys(message).forEach(function (key) {
                    var role_id = key;
                    var color = message[key]['color'];
                    var name = message[key]['name'];

                    var is_enabled = 'inactive-role d-none';
                    var txt_active = 'Inactive';
                    var icon_active = 'wb-plus';
                    var btn_active = 'disabled btn-dark';
                    var role_active = {};

                    $('#roles_table').append(getHTML2(is_enabled, guild_id, role_id, color, name, txt_active, btn_active, role_active, icon_active));

                    socket.emit('get_role_for_sale', [socket_id, guild_id, key]);
                });

                $.ajax({
                        url: '/get-status-roles',
                        type: 'POST',
                        data: {
                            'roles': message,
                            //'guild_id': guild_id,
                            _token: '{{ csrf_token() }}'
                        },
                }).done(function (msg) {
                    //console.log(msg)
                    var prod_enabled = false;
                    Object.keys(msg).forEach(function (role) {
                        //console.log(msg[role]['product']);

                        if(msg[role]['active']){

                            role_id = msg[role]['product'];

                            $('#' + guild_id + '_' + role_id).removeClass('inactive-role d-none').addClass('active-role');
                            $('#state_' + role_id).text('Active');
                            $('#toggle-product-icon_' + guild_id + '_' + role_id).removeClass('wb-plus').addClass('wb-check');
                            $('#toggle-check_' + guild_id + '_' + role_id).addClass('green-600').removeClass('grey-2');
                            $('#product-settings_' + guild_id + '_' + role_id).removeClass('disabled btn-dark').addClass('btn-primary').attr("disabled", false);
                            $('#toggle-product_' + guild_id + '_' + role_id).addClass('active');
                            $('#product-settings_' + guild_id + '_' + role_id).removeClass('disabled').attr("disabled", false);
                            prod_enabled = true;
                        }
                    })
                    if(prod_enabled == false){
                        $("#btn_edit-roles").click();
                        $("#btn_save-roles").addClass('btn-dark').removeClass('btn-primary');
                    }
                    

                })

                /*socket.on('res_role_for_sale_' + socket_id, function (message) {
                    var role_id = message['role_id'];
                  
                    if (message['for_sale']) {
                      

                        $('#' + guild_id + '_' + role_id).removeClass('inactive-role d-none').addClass('active-role');
                        $('#state_' + role_id).text('Active');
                        $('#toggle-product-icon_' + guild_id + '_' + role_id).removeClass('wb-plus').addClass('wb-check');
                        $('#toggle-check_' + guild_id + '_' + role_id).addClass('green-600').removeClass('grey-2');
                        $('#product-settings_' + guild_id + '_' + role_id).removeClass('disabled btn-dark').addClass('btn-primary').attr("disabled", false);
                        $('#toggle-product_' + guild_id + '_' + role_id).addClass('active');
                        $('#product-settings_' + guild_id + '_' + role_id).removeClass('disabled').attr("disabled", false);
                        var prod_enabled = true;
                    } 
                    
                    if(prod_enabled){
                        if ($("#btn_edit-roles").hasClass('n-none')){
                            $("#btn_edit-roles").click();
                            $("#btn_save-roles").addClass('btn-dark').removeClass('btn-primary');
                        }
                    }
                });*/
            });

            function getHTML2(is_enabled, guild_id, role_id, color, name, txt_active, btn_active, role_active, icon_active) {
                return `
                    <tr class="role ${is_enabled}" id="${guild_id}_${role_id}">
                        <td class="pl-15">
                            <div class="content text-left">
                                <span class="badge badge-primary badge-lg" style="background-color: ${color}"><i class="icon-discord mr-2" aria-hidden="true"></i>
                                <span>${name}</span></span>
                            </div>
                        </td>
                        <td class="info-role w-20 grey-4" style="display:none; visiblity:hidden">
                            <i class="icon wb-payment" id="active-cell_${role_id}" aria-hidden="true" data-toggle="tooltip" data-original-title="Subscriptions Enabled"></i>
                        </td>
                        <td class="info-role w-200 pr-lg-10 text-right">
                            <div class="time"><span id="sub_count_${role_id}">0</span> Sub<span class="hidden-md-down">scription</span><span id="sub-suffix_${role_id}">s</span></div>
                            <div class="identity d-none" id="status_${guild_id}_${role_id}"><i class="icon wb-medium-point yellow-500" id="state_color_${role_id}" aria-hidden="true"></i><span id="state_${role_id}">${txt_active}</span></div>
                        </td>
                        <td class="cell-120 cell-sm-120 toggle-role d-none">
                            <button type="button" class="btn btn-icon ${btn_active} py-md-20 w-p100" disabled="true" id="product-settings_${guild_id}_${role_id}" data-url="/slide-roles-settings/${guild_id}/${role_id}" data-toggle="slidePanel"><i class="icon wb-more-horizontal" aria-hidden="true"></i></button>
                        </td>
                        <td class="cell-60 hidden-md-up settings-role" data-toggle="slidePanel" data-url="/slide-roles-settings/${guild_id}/${role_id}">
                            <button class="btn btn-primary btn-icon" data-toggle="tooltip" data-original-title="Settings"><i class="icon wb-more-horizontal" aria-hidden="true"></i></button>
                        </td>
                        <td class="cell-120 pr-15 hidden-sm-down settings-role" data-toggle="slidePanel" data-url="/slide-roles-settings/${guild_id}/${role_id}">
                            <button class="btn btn-block btn-primary btn-icon py-20" data-toggle="tooltip" data-original-title="Settings"><i class="icon wb-more-horizontal" aria-hidden="true"></i></button>
                        </td>
                        <td class="cell-100 cell-sm-100 toggle-role d-none text-right">
                            <button type="button" class="btn btn-primary btn-icon btn-round py-md-20 w-p80 animation-scale-up ${role_active} toggle-btn-trigger" id="toggle-product_${guild_id}_${role_id}" data-role_id="${role_id}"><i class="icon ${icon_active} text-white" aria-hidden="true" id="toggle-product-icon_${guild_id}_${role_id}"></i></button>
                        </td>
                    </tr>
                    `;
            }


 
            $(document).on('click', '.toggle-btn-trigger', function (e) {

                e.preventDefault();
                var role_id = $(this).data('role_id');

                var clicked = false;
                $('#toggle-product_' + guild_id + '_' + role_id).addClass('disabled').attr("disabled", true);
                $('#icon_save-roles').removeClass('wb-minus wb-check').addClass('wb-refresh spinning');
                $('#btn_save-roles').attr("disabled", true);
                $('#product-settings_' + guild_id + '_' + role_id).addClass('disabled').attr("disabled", true);
                // $('#icon_save-roles').addClass('spinning');

                Swal.fire({
                    title: 'Toggling Product...',
                    text: 'This can take a minute.'
                    // type: 'info',
                });

                Swal.showLoading();

                    //switchery.disable();
                    $.ajax({
                        url: '/toggle-role',
                        type: 'POST',
                        data: {
                            'guild_id': guild_id,
                            'role_id': role_id,
                            'guild_name': Global.name,
                            'role_name': name,
                            _token: '{{ csrf_token() }}'
                        },
                    }).done(function (msg) {
                        //switchery.enable();
                        if (!msg['success']) {
                            //$('.js-switch').click();
                            Swal.fire({
                                title: 'Failure',
                                text: msg['msg'],
                                type: 'warning',
                                showCancelButton: false,
                                showConfirmButton: true,
                                target: document.getElementById('tab-content')
                            });
                            $('#icon_save-roles').removeClass('wb-minus wb-refresh spinning').addClass('wb-alert')
                        } else {
                            //switchery.enable();
                            if (msg['active']) {
                                Toast.fire({
                                    title: 'Great. Add some prices to enable!',
                                    type: 'success',
                                    target: document.getElementById('tab-content')
                                });
                                $('#' + guild_id + '_' + role_id).removeClass('inactive-role').addClass('active-role');
                                $('#toggle-product-icon_' + guild_id + '_' + role_id).removeClass('wb-plus').addClass('wb-check');
                                $('#toggle-check_' + guild_id + '_' + role_id).addClass('green-600').removeClass('grey-2');
                                $('#product-settings_' + guild_id + '_' + role_id).removeClass('disabled btn-dark').addClass('btn-primary').attr("disabled", false).click();
                                $('#toggle-product_' + guild_id + '_' + role_id).addClass('active');
                                $('.text_save-roles').removeClass('d-none').text('Enabled');
                                $('#product-settings_' + guild_id + '_' + role_id).removeClass('disabled').attr("disabled", false);
                                socket.emit('toggle_role_activity', [socket_id, guild_id, role_id, 'true']);
                            } else {
                                Toast.fire({
                                    title: 'Product Disabled',
                                    type: 'success',
                                    target: document.getElementById('tab-content')
                                });
                                $('#' + guild_id + '_' + role_id).addClass('inactive-role').removeClass('active-role');
                                $('#toggle-product-icon_' + guild_id + '_' + role_id).addClass('wb-plus').removeClass('wb-check');
                                $('#toggle-check_' + guild_id + '_' + role_id).addClass('grey-2').removeClass('green-600');
                                $('#product-settings_' + guild_id + '_' + role_id).addClass('disabled btn-dark').removeClass('btn-primary').attr("disabled", true);
                                $('#toggle-product_' + guild_id + '_' + role_id).removeClass('active');
                                $('.text_save-roles').removeClass('d-none').text('Disabled');
                                socket.emit('toggle_role_activity', [socket_id, guild_id, role_id, 'false']);
                            }
                            if($('#btn_save-roles').hasClass('btn-primary')){
                                $('#btn_save-roles').addClass('btn-dark').removeClass('btn-primary').attr("disabled", false);;
                            }else{
                                $('#btn_save-roles').addClass('btn-primary').removeClass('btn-dark').attr("disabled", false);;
                            }
                            $('#icon_save-roles').removeClass('wb-minus wb-refresh spinning wb-alert').addClass('wb-check');
                            setTimeout(function(){
                                //$('#icon_save-roles').removeClass("wb-check").addClass("wb-minus");
                                $('.text_save-roles').addClass('d-none').text();
                            }, 2000);
                        

                        }
                        //clicked = false;
                        $('#toggle-product_' + guild_id + '_' + role_id).removeClass('disabled').attr("disabled", false);
                    });

                // clicked = true;
                });



       /* socket.on('get_roles_' + socket_id, function (message) {
            //$('#roles_table').empty();
            //$('#role_count').text(Object.keys(message).length + ' Roles');
            roles = message;
            Object.keys(message).forEach(function (key) {
                var guild_id = message[key]['guild_id'];
                var role_id = key;
                $.ajax({
                    url: '/get-active-roles',
                    type: 'POST',
                    data: {
                        'guild_id': guild_id,
                        'role_id': role_id,
                        _token: '{{ csrf_token() }}'
                    },
                })
            })
        });
        console.log()*/
/*** 
        socket.on('res_roles_' + socket_id, function (message) {
            $('#roles_table').empty();
            $('#role_count').text(Object.keys(message).length + ' Roles');
            roles = message;
            Object.keys(message).forEach(function (key) {
                var guild_id = message[key]['guild_id'];
                var role_id = key;
                var memberCount = message[key]['memberCount'];
                var name = message[key]['name'];
                var color = message[key]['color'];
                $.ajax({
                    //url: '/check-prices',
                    url: '/get-active-roles',
                    type: 'POST',
                    data: {
                        'guild_id': guild_id,
                        'role_id': role_id,
                        'guild_name': Global.name,
                        'role_name': name,
                        _token: '{{ csrf_token() }}'
                    },
                }).done(function (msg) {
                            
                            //switchery.enable();
                            if (msg['success']) {
                                var enabled_get = true;
                                var is_enabled = 'active-role';
                                var txt_active = 'Active';
                                var icon_active = 'wb-check';
                                //var color_active = 'green-600';
                                var btn_active = 'btn-primary';
                                var role_active = 'active';

                                console.log(msg);
                            }else{
                                var enabled_get = false;
                                var is_enabled = 'inactive-role d-none';
                                var txt_active = 'Inactive';
                                var icon_active = 'wb-plus';
                                //var color_active = 'grey-2';
                                var btn_active = 'disabled btn-dark';
                                var role_active = {};

                               console.log(msg);
                           }

                           var html = `
                                <tr class="role ${is_enabled}" id="${guild_id}_${role_id}">
                                    <td class="pl-15">
                                        <div class="content text-left">
                                            <span class="badge badge-primary badge-lg" style="background-color: ${color}"><i class="icon-discord mr-2" aria-hidden="true"></i>
                                            <span>${name}</span></span>
                                        </div>
                                    </td>
                                    <td class="info-role w-20 grey-4" style="display:none; visiblity:hidden">
                                        <i class="icon wb-payment" id="active-cell_${role_id}" aria-hidden="true" data-toggle="tooltip" data-original-title="Subscriptions Enabled"></i>
                                    </td>
                                    <td class="info-role w-200 pr-lg-10 text-right">
                                        <div class="time"><span id="sub_count_${role_id}">0</span> Sub<span class="hidden-md-down">scription</span><span id="sub-suffix_${role_id}">s</span></div>
                                        <div class="identity d-none" id="status_${guild_id}_${role_id}"><i class="icon wb-medium-point yellow-500" id="state_color_${role_id}" aria-hidden="true"></i><span id="state_${role_id}">${txt_active}</span></div>
                                    </td>
                                    <td class="cell-120 cell-sm-120 toggle-role d-none">
                                        <button type="button" class="btn btn-icon ${btn_active} py-md-20 w-p100" disabled="true" id="product-settings_${guild_id}_${role_id}" data-url="/slide-roles-settings/${guild_id}/${role_id}" data-toggle="slidePanel"><i class="icon wb-more-horizontal" aria-hidden="true"></i></button>
                                    </td>
                                    <td class="cell-60 hidden-md-up settings-role" data-toggle="slidePanel" data-url="/slide-roles-settings/${guild_id}/${role_id}">
                                        <button class="btn btn-primary btn-icon" data-toggle="tooltip" data-original-title="Settings"><i class="icon wb-more-horizontal" aria-hidden="true"></i></button>
                                    </td>
                                    <td class="cell-120 pr-15 hidden-sm-down settings-role" data-toggle="slidePanel" data-url="/slide-roles-settings/${guild_id}/${role_id}">
                                        <button class="btn btn-block btn-primary btn-icon py-20" data-toggle="tooltip" data-original-title="Settings"><i class="icon wb-more-horizontal" aria-hidden="true"></i></button>
                                    </td>
                                    <td class="cell-100 cell-sm-100 toggle-role d-none text-right">
                                        <button type="button" class="btn btn-primary btn-icon btn-round py-md-20 w-p80 animation-scale-up ${role_active}" id="toggle-product_${guild_id}_${role_id}"><i class="icon ${icon_active} text-white" aria-hidden="true" id="toggle-product-icon_${guild_id}_${role_id}"></i></button>
                                    </td>
                                </tr>
                                `;
                                $('#roles_table').append(html);
                });


                $(document).on('click', '#toggle-product_' + guild_id + '_' + role_id, function (e) {

                    e.preventDefault();

                    var clicked = false;
                    $('#toggle-product_' + guild_id + '_' + role_id).addClass('disabled').attr("disabled", true);
                    $('#icon_save-roles').removeClass('wb-minus wb-check').addClass('wb-refresh spinning');
                    $('#btn_save-roles').attr("disabled", true);
                    $('#product-settings_' + guild_id + '_' + role_id).addClass('disabled').attr("disabled", true);
                   // $('#icon_save-roles').addClass('spinning');

                    Swal.fire({
                        title: 'Toggling Product...',
                        text: 'This can take a minute.'
                        // type: 'info',
                    });

                    Swal.showLoading();

                        //switchery.disable();
                        $.ajax({
                            url: '/toggle-role',
                            type: 'POST',
                            data: {
                                'guild_id': guild_id,
                                'role_id': role_id,
                                'guild_name': Global.name,
                                'role_name': name,
                                _token: '{{ csrf_token() }}'
                            },
                        }).done(function (msg) {
                            //switchery.enable();
                            if (!msg['success']) {
                                //$('.js-switch').click();
                                Swal.fire({
                                    title: 'Failure',
                                    text: msg['msg'],
                                    type: 'warning',
                                    showCancelButton: false,
                                    showConfirmButton: true,
                                    target: document.getElementById('tab-content')
                                });
                                $('#icon_save-roles').removeClass('wb-minus wb-refresh spinning').addClass('wb-alert')
                            } else {
                                //switchery.enable();
                                if (msg['active']) {
                                    Toast.fire({
                                        title: 'Great. Add some prices to enable!',
                                        type: 'success',
                                        target: document.getElementById('tab-content')
                                    });
                                    $('#' + guild_id + '_' + role_id).removeClass('inactive-role d-none').addClass('active-role');
                                    $('#toggle-product-icon_' + guild_id + '_' + role_id).removeClass('wb-plus').addClass('wb-check');
                                    $('#toggle-check_' + guild_id + '_' + role_id).addClass('green-600').removeClass('grey-2');
                                    $('#product-settings_' + guild_id + '_' + role_id).removeClass('disabled btn-dark').addClass('btn-primary').attr("disabled", false).click();
                                    $('#toggle-product_' + guild_id + '_' + role_id).addClass('active');
                                    $('.text_save-roles').removeClass('d-none').text('Enabled');
                                    $('#product-settings_' + guild_id + '_' + role_id).removeClass('disabled').attr("disabled", false);
                                    socket.emit('toggle_role_activity', [socket_id, guild_id, role_id, 'true']);
                                } else {
                                    Toast.fire({
                                        title: 'Product Disabled',
                                        type: 'success',
                                        target: document.getElementById('tab-content')
                                    });
                                    $('#' + guild_id + '_' + role_id).addClass('inactive-role d-none').removeClass('active-role');
                                    $('#toggle-product-icon_' + guild_id + '_' + role_id).addClass('wb-plus').removeClass('wb-check');
                                    $('#toggle-check_' + guild_id + '_' + role_id).addClass('grey-2').removeClass('green-600');
                                    $('#product-settings_' + guild_id + '_' + role_id).addClass('disabled btn-dark').removeClass('btn-primary').attr("disabled", true);
                                    $('#toggle-product_' + guild_id + '_' + role_id).removeClass('active');
                                    $('.text_save-roles').removeClass('d-none').text('Disabled');
                                    socket.emit('toggle_role_activity', [socket_id, guild_id, role_id, 'false']);
                                }
                                if($('#btn_save-roles').hasClass('btn-primary')){
                                    $('#btn_save-roles').addClass('btn-dark').removeClass('btn-primary').attr("disabled", false);;
                                }else{
                                    $('#btn_save-roles').addClass('btn-primary').removeClass('btn-dark').attr("disabled", false);;
                                }
                                $('#icon_save-roles').removeClass('wb-minus wb-refresh spinning wb-alert').addClass('wb-check');
                                setTimeout(function(){
                                    //$('#icon_save-roles').removeClass("wb-check").addClass("wb-minus");
                                    $('.text_save-roles').addClass('d-none').text();
                                }, 2000);
                            

                            }
                            //clicked = false;
                            $('#toggle-product_' + guild_id + '_' + role_id).removeClass('disabled').attr("disabled", false);
                        });

                   // clicked = true;
                });

            });

        });
/*
        /*var sent = false;

        socket.on('res_role_for_sale_' + socket_id, function (message) {
            if (message['for_sale']) {

                $(`#state_${message['role_id']}`).text('Active');
                $(`#${message['guild_id']}_${message['role_id']}`).removeClass('d-none inactive-role').addClass('active-role');
                $(`#toggle-product-icon_${message['guild_id']}_${message['role_id']}`).removeClass('wb-plus').addClass('wb-check');
                $(`#toggle-check_${message['guild_id']}_${message['role_id']}`).addClass('green-600').removeClass('grey-2');
                $(`#product-settings_${message['guild_id']}_${message['role_id']}`).removeClass('disabled btn-dark').addClass('btn-primary').attr("disabled", false);
                $(`#toggle-product_${message['guild_id']}_${message['role_id']}`).addClass('active');

            }

            if(!sent) {
                sent = true;
                socket.emit('get_guild_subs', [socket_id, guild_id]);
            }
        });*/



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
<!--
<script>
        socket.on('res_get_roles' + socket_id, function (message) {
            if (message['for_sale']) {

                $(`#state_${message['role_id']}`).text('Active');
                $(`#${message['guild_id']}_${message['role_id']}`).removeClass('d-none inactive-role').addClass('active-role');
                $(`#toggle-product-icon_${message['guild_id']}_${message['role_id']}`).removeClass('wb-plus').addClass('wb-check');
                $(`#toggle-check_${message['guild_id']}_${message['role_id']}`).addClass('green-600').removeClass('grey-2');
                $(`#product-settings_${message['guild_id']}_${message['role_id']}`).removeClass('disabled btn-dark').addClass('btn-primary').attr("disabled", false);
                $(`#toggle-product_${message['guild_id']}_${message['role_id']}`).addClass('active');

            }

            if(!sent) {
                sent = true;
                socket.emit('get_guild_subs', [socket_id, guild_id]);
            }
        });
</script>-->




<script>

$(document).on('click', '#btn_edit-roles', function (e) {

    e.preventDefault();
    $('.role.inactive-role').removeClass('d-none');
    $('.toggle-role').removeClass('d-none');
    $('#btn_save-roles').removeClass('d-none');
    $('#btn_save-products').removeClass('d-none');

    $('#btn_edit-roles').addClass('d-none');
    $('.settings-role').addClass('d-none');
    $('.info-role').addClass('d-none');

    if($('#icon_save-roles').hasClass('wb-check')){
        $('#icon_save-roles').addClass('wb-minus').removeClass('wb-check');
    }
    if($('#btn_save-roles').hasClass('btn-primary')){
        $('#btn_save-roles').addClass('btn-dark').removeClass('btn-primary');
    }

})

$(document).on('click', '.btn_save-roles', function (e) {

    e.preventDefault();

    $('.role.inactive-role').addClass('d-none');
    $('.toggle-role').addClass('d-none');
    $('#btn_save-roles').addClass('d-none');
    $('#btn_save-products').addClass('d-none');

    $('#btn_edit-roles').removeClass('d-none');
    $('.settings-role').removeClass('d-none');
    $('.info-role').removeClass('d-none');

})

</script>
