@extends('layouts.app')

@section('title', 'Servers')

@section('content')

    <div class="page-header">
        <ol class="breadcrumb">
            <h4 class="font-weight-100">Servers</h4>
        </ol>
        @if(auth()->user()->StripeConnect->express_id != null)
        <div class="page-header-actions add-pulse">
            <a class="btn btn-primary btn-round"
               href="https://discordapp.com/oauth2/authorize?client_id=590725202489638913&amp;scope=bot&amp;permissions=281020422" target="_blank" id="Addbtn">
                <i class="icon wb-plus" aria-hidden="true"></i>
                Add Bot
            </a>
            <a class="btn btn-primary btn-outline btn-round d-none" id="Refreshbtn" href="/servers#refresh">
                <i class="icon wb-refresh" aria-hidden="true"></i>
                Refresh
            </a>
        </div>
        @else
        <div class="page-header-actions add-pulse">
            <a class="btn btn-primary btn-round"
               href="{{ BeastlyConfig::get('STRIPE_CONNECT_LINK') }}">
                Connect Stripe
                <i class="icon-stripe ml-2" aria-hidden="true"></i>
            </a>
        </div>
        @endif
    </div>
<div class="row">
    <div class="col-lg-12">

        <div class="page-content-table">
            <div class="page-main">
                <table class="table" data-plugin="animateList" data-animate="fade" data-child="tr">
                    <thead>
                        <tr>
                            <th class="cell-100"></th>
                            <th></th>
                            <th class="cell-150 hidden-md-down"></th>
                            <th class="cell-100 hidden-md-up"></th>
                        </tr>
                    </thead>
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

    </div>
</div>
@endsection


@section('scripts')

<script type="text/javascript">
    setInterval(function(){
        if(window.location.href.includes('refresh')) {
            @include('partials/servers/servers_script')
        }
    }, 2000);
</script>

<script type="text/javascript">
    $(document).ready(function() {
        @include('partials/servers/servers_script')
    });
</script>

    <script type="text/javascript">
    $('#Addbtn').click(function(){
        location.hash = "refresh";
    });
    </script>


    <script type="text/javascript">
    setInterval(function(){
    if(window.location.href.includes('click-first=true')) {
        $('#servers-table tr').each(function(i, el) {
            if (i === 0) {
            $("tr td").addClass('bg-grey-2');
            setTimeout(function(){
                $("tr").click();
            },100)
            }
        })
        }
    },1900);
    setTimeout(function(){
    if( $('#servers-table tr').length == 0 ) {
        if(!window.location.href.includes('refresh')) {
            location.hash = "auto-open";
            $(".slide-button-ultimate").click();
        }
        $(".add-pulse").addClass('pulse');
            var hrf = 'document.location.href="/server/';
            var guid = '#guide-ultimate=true';
            var slash = '/servers';
            var clickfirst = '?click-first=true';
            $('#Refreshbtn').attr('href',slash + clickfirst + guid);
    } else {
        if(window.location.href.includes('refresh')) {
            location.hash = "click-first=true";
        }
        setTimeout(function(){
            if(window.location.href.includes('guide-ultimate=true')) {
            var hrf = 'document.location.href="/server/';
            var guid = '#guide-ultimate=true';
            var slash = '/servers';
            var clickfirst = '?click-first=true';
            $('#Refreshbtn').attr('href',slash + clickfirst + guid);
             $('#servers-table tr').each(function(){
                 var atr = $('tr').attr('data-key');
                 var str = atr.split('1');
                 var natr = hrf + str + guid + '"';
                 $('tr').attr('onclick',natr);
                 //$(this).setAttribute('onClick', $(this).getAttribute('onClick') + '#guide-ultimate=true');
             })
             if((!window.location.href.includes('click-first=true')) && (!window.location.href.includes('refresh'))) {
                $(".slide-button-ultimate").click();
                }
            }}, 200)
        }

    },1500);
/*          setTimeout(function(){
         if (RegExp('ultimate-guide', 'gi').test(window.location.search)) {
             $(".slide-button-ultimate").click();
             $('#servers-table tr').each(function(){
                 var hrf = 'document.location.href="/server/';
                 var guid = '#guide-ultimate=true"';
                 var atr = $('tr').attr('data-key');
                 var str = atr.split('1');
                 var natr = hrf + str + guid;
                 $('tr').attr('onclick',natr);
                 //$(this).setAttribute('onClick', $(this).getAttribute('onClick') + '#guide-ultimate=true');
             })
        }}, 2000); */

       // setTimeout(function(){
        //    if(window.location.href.includes('ultimate-guide=true')) {
        //        $(".slide-button-ultimate").click();
        //        $('#servers-table').children("tr").forEach((elem, index) => {
        //            var dkey = $('tr').attr("data-key")
       //             $(this).attr('onclick', dkey)
       //         });
        //    }
       // },2000);
    </script>

@endsection
