<script type="text/javascript">
    var roles = null; 

    $(document).ready(function () {
        var socket_id = '{{ uniqid() }}';
        var roles = [];
        socket.emit('get_roles', [socket_id, guild_id]);

        socket.on('res_roles_' + socket_id, function (msg) {
            roles = msg;
            // Populate roles array because we will need the roles array later
            Object.keys(roles).forEach(function (key) {
                roles[key] = {
                    name: roles[key]['name'],
                    color: roles[key]['color']
                };
            });
            @if($has_order)
            fillRecentPayments();
            @endif
        });
    });
    @if($has_order)
    function fillRecentPayments() {
        if((jQuery("#recent-transactions-table:contains('$')").length)) {
            $('#btn_recent-refresh').addClass('btn-primary').removeClass('btn-dark');
        }
        $('#recent-transactions-table').empty();
        var count = 0;
        var html_array = [];
        $('.loading-bg').show();
        $('#btn_recent-refresh').addClass('spinning').attr("disabled", true);

        $.ajax({
            url: `/get-latest-transactions`,
            type: 'GET',
            data: {
                guild: '{{ $id }}',
                roles: roles,
                _token: '{{ csrf_token() }}'
            },
        }).done(function (response) {
            for (var i = 0; i < response.length; i++) {
                var timeDiff = timeDiffStr(new Date(response[i]['created']* 1000).getTime(), (new Date()).getTime());
                var html = `
                        <tr data-url="/slide-invoice/${response[i]['id']}" data-toggle="slidePanel">
                        <td class="w-120 font-size-12 pl-20">${timeDiff}</td>
                        <td class="content"><div>${response[i]['discord_username']}</div></td>
                        <td class="green-600 w-80">+ $${response[i]['amount']}</td>
                    </tr>
                `;

                html_array.push(html);
            }

            html_array.forEach(function(html) {
                $('#recent-transactions-table').append(html);
            });
            $('.loading-bg').hide();
            $('#btn_recent-refresh').removeClass('spinning').attr("disabled", false);
            if ($('#btn_recent-refresh').hasClass('btn-primary')){
                $('#btn_recent-refresh').addClass('btn-dark').removeClass('btn-primary');
            }

        });
    }
    @endif
</script>
