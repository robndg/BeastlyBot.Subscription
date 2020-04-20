<script type="text/javascript">

    var loaded_subs = false;

    function loadSubs() {
        if(loaded_subs) return;
        loaded_subs = true;
        socket.emit('get_guild_subs', [socket_id, guild_id]);
        $('#btn_subscribers-refresh').addClass('spinning');
    }

    $(document).ready(function() {
        socket.on('res_guild_subs_' + socket_id, function (msg) {
            var sub_count = msg['count'];
            if (sub_count == 1){
                $('#subscribers-suffix').hide();
            }else if (sub_count == 0){
                setTimeout(function (){
                $('#subscribers-loading_table').hide();
                $('#btn_subscribers-refresh').removeClass('spinning');
                },2000);
            }

            $('#subscribers_count').text(sub_count);

            Object.keys(msg).forEach(user_id => {
                if (user_id !== 'count' && user_id !== 'id') {
                    var html = `
                    <tr id="sub_${user_id}" data-url="/slide-server-member/${guild_id}/${user_id}" data-toggle="slidePanel">
                        <td class="cell-30 responsive-hide">
                            <a class="avatar avatar-lg" href="javascript:void(0)">
                                <img src="" alt="..." id="sub_avatar_${user_id}">
                            </a>
                        </td>
                        <td class="cell-60 responsive-hide">
                        </td>
                        <td class="cell-160">
                            <div class="content">
                                <div class="title" id="sub_name_${user_id}"></div>
                            </div>
                        </td>
                        <td class="text-right" id="roles_${user_id}">
                        </td>
                        <td class="cell-60 responsive-hide">
                        </td>
                    </tr>
                `;
                $('#subscribers-loading_table').hide();
                $('#btn_subscribers-refresh').removeClass('spinning');

                    if($('#sub_' + user_id).length === 0) {
                        $('#subscribers_table').append(html);
                    }

                    Object.values(msg[user_id]).forEach(role => {
                        var html = `<span class="badge m-5" style="color: white;background-color: ${role['color']};">${role['name']}</span>`;
                        $('#roles_' + user_id).append(html);
                    });

                    socket.emit('get_user_data', [socket_id, user_id]);
                }
            });
        });

        socket.on('res_user_data_' + socket_id, function(msg) {
            $('#sub_avatar_' + msg['id']).prop('src', msg['avatar']);
            $('#sub_name_' + msg['id']).text(msg['name'] + ' #' + msg['discriminator']);
        });
    });


</script>
