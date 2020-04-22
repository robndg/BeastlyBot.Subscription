var guild_id = null, role_id = null;

socket.emit('get_guilds', [socket_id, '{{ auth()->user()->DiscordOAuth->discord_id }}']);

socket.on('res_guilds_' + socket_id, function (message) {
    $('#servers-table').empty();

    Object.keys(message).forEach(function (key) {
        
        $('#servers-table').append(`
            <tr onClick="document.location.href='/server/${key}';" data-key="${key}">
                <td class="cell-100 pl-15 pl-lg-30">
                    <a class="avatar avatar-lg" href="javascript:void(0)">
                    <img src="${message[key]['iconURL']}" alt="...">
                    </a>
                </td>
                <td>
                    <div class="title">${message[key]['name']}</div>
                </td>
                <td class="cell-150 hidden-md-down text-center">
                    <div class="time" id="subCount${key}">0 Subscribers</div>
                </td>
                <td class="cell-100 hidden-md-up">
                    <button class="btn btn-link">Settings</button>
                </td>
            </tr>
        `);

        socket.emit('get_guild_subs', [socket_id, key]);
    });
});

socket.on('res_guild_subs_' + socket_id, function (message) {
    var guild_id = message['id'];
    var sub_count = message['count'];
    $('#subCount' + guild_id).text(sub_count + ' Subscribers');
});
