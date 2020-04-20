
<header class="slidePanel-header bg-blue-500">
    <div class="slidePanel-actions" aria-label="actions" role="group">
        <button type="button" class="btn btn-icon btn-pure btn-inverse slidePanel-close actions-top icon wb-close"
                aria-hidden="true" id="back-btn"></button>
    </div>
    <h1>Shops</h1>
</header>

    <div class="page-header my-10">
        @if(auth()->user()->stripe_express_id != null)
        <div class="page-header-actions add-pulse">
            <a class="btn btn-primary btn-round"
               href="https://discordapp.com/oauth2/authorize?client_id=590725202489638913&amp;scope=bot&amp;permissions=281020422" target="_blank" id="Addbtn">
                <i class="icon wb-plus" aria-hidden="true"></i>
                Add Bot
            </a>
            <a href="#click-first=true" class="btn btn-primary btn-outline btn-round d-none"
            id="Refreshbtn" data-toggle="slidePanel" data-url="/slide-servers">
                <i class="icon wb-refresh" aria-hidden="true"></i>
                Refresh
            </a>
        </div>
        @else
        <div class="page-header-actions add-pulse">
            <a class="btn btn-primary btn-round"
               href="{{ env('STRIPE_CONNECT_LINK') }}">
                Connect Stripe
                <i class="icon-stripe ml-2" aria-hidden="true"></i>
            </a>
        </div>
        @endif
    </div>

    <div class="page-content-table app-beast">
        <div class="page-main">
            <table class="table" data-plugin="animateList" data-animate="fade" data-child="tr">
                <tbody id="servers-table"></tbody>
            </table>
            <!--<table class="table">
                <tbody>
                    <tr>
                        <td class="text-center" onClick="document.location.href='/server/${key}';">
                            Add Server
                        </td>
                    </tr>
                </tbody>
            </table>-->
        </div>
    </div>
    <script>
    @include('partials/servers/servers_script')
    </script>
    <script type="text/javascript">
    setTimeout(function(){
    if(window.location.href.includes('click-first=true')) {
        $('#servers-table tr').each(function(i, el) {
            if (i === 0) {
            $(this).addClass('bg-grey-1');
            setTimeout(function(){
                $("tr").click();
            },100)
            }
        })
        }
    },500);
    </script>

    <script type="text/javascript">
    setTimeout(function(){
    if( $('#servers-table tr').length === 0 ) {
        $(".add-pulse").addClass('pulse');
           /* var hrf = 'document.location.href="/server/';
            var guid = '#guide-ultimate=true';
            var slash = '/servers';
            var clickfirst = '?click-first=true';
            $('#RBbtn').attr('href',slash + clickfirst + guid);*/
    } else {
        setTimeout(function(){
            if(window.location.href.includes('guide-ultimate=true')) {
            var hrf = 'document.location.href="/server/';
            var guid = '#guide-ultimate=true';
            var slash = '/servers';
            var clickfirst = '?click-first=true';
            $('#RBbtn').attr('href',slash + clickfirst + guid);
             $('#servers-table tr').each(function(){
                 var atr = $('tr').attr('data-key');
                 var str = atr.split('1');
                 var natr = hrf + str + guid + '"';
                 $('tr').attr('onclick',natr);

             })
             if(!window.location.href.includes('click-first=true')) {
                $(".slide-button-ultimate").click();
                }
            }}, 200)
        }

    },1500);

    </script>

    <script type="text/javascript">
    $('#Addbtn').click(function(){ // add bot button
        window.setInterval(function(){
            if( $('#servers-table tr').length == 0 ) { // run if 0

            var guild_id = null, role_id = null;
            socket.emit('get_guilds', [socket_id, '{{ auth()->user()->discord_id }}']);

            socket.on('res_guilds_' + socket_id, function (message) {
                $('#servers-table').empty();

                Object.keys(message).forEach(function (key) {

                    @include('partials/servers/servers_html')

                    $('#servers-table').append(html);
                    socket.emit('get_guild_subs', [socket_id, key]);
                });
            });

            socket.on('res_guild_subs_' + socket_id, function (message) {
                var guild_id = message['id'];
                var sub_count = message['count'];
                $('#subCount' + guild_id).text(sub_count + ' Subscribers');
            });


            }else{
                clearInterval()  // clear if not 0
            }
        }, 2000)
    });
    </script>

@include('partials/clear_script')
