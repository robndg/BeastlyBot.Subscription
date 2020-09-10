@extends('layouts.app')

@section('title', 'Subscriptions')

@section('content')
<div class="page-header text-md-center">
            <h4 class="font-weight-100">Subscriptions</h4>
        </div>
<div class="row">
    <div class="col-lg-10 offset-lg-1">


        <div class="page-content-table">
            <div class="page-main">

                <!-- nav-tabs -->
                <ul class="site-sidebar-nav nav nav-tabs nav-tabs-line bg-grey-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active show" data-toggle="tab" href="#tab-active" role="tab">
                            <i class="icon wb-star" aria-hidden="true"></i>
                            <h5>Active</h5>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-inactive" role="tab">
                            <i class="icon wb-star-outline" aria-hidden="true"></i>
                            <h5>Inactive</h5>
                        </a>
                    </li>
                </ul>

                <div class=" tab-content">

                    <div class="tab-pane active show" id="tab-active">
                    <!-- active subs tab -->
                        <table class="table table-hover" id="SubscriptionTable">
                            <thead>
                                <tr>
                                    <th class="cell-200 text-left">Server</th>
                                    <th class="text-left">Role</th>
                                    <th class="cell-30"></th>
                                    <th class="cell-150 text-right">More</th>
                                    <th class="cell-30 hidden-md-down"></th>
                                </tr>
                            </thead>
                            <tbody id="activeSubsTable" data-plugin="animateList" data-animate="fade"
                            data-child="tr">

                            @foreach(auth()->user()->getStripeHelper()->getSubscriptions() as $sub_array)
                                
                            @endforeach
                            </tbody>
                        </table>
                        <!-- pagination -->
                    </div>
                    <div class="tab-pane" id="tab-inactive">
                    <!-- inactive subs tab -->
                        <table class="table table-hover" id="SubscriptionTable">
                            <thead>
                                <tr>
                                    <th class="cell-200 text-left">Server</th>
                                    <th class="text-left">Role</th>
                                    <th class="cell-30"></th>
                                    <th class="cell-150 text-right">More</th>
                                    <th class="cell-30 hidden-md-down"></th>
                                </tr>
                            </thead>
                            <tbody id="inactiveSubsTable" data-plugin="animateList" data-animate="fade"
                            data-child="tr">



                            </tbody>
                        </table>
                        <!-- pagination -->
                    </div>

                </div>


            </div>

        </div>

    </div>
</div>
@endsection

@section('scripts')

    @include('partials/subscriptions/subscriptions_script')

    <script>
        $('#rolesTable tr').each(function() {
            $(this).first().attr('data-step', '2');
            $(this).first().attr('data-intro', 'Now lets open this');
        });
    </script>
    <script>
        setTimeout(function(){
        if (RegExp('multipage', 'gi').test(window.location.search)) {
            introJs().setOption('disableInteraction','false').start();
            $('#rolesTable tr').each(function(){
                $(this).attr('data-url', $(this).data('url') + '?multipage=true');
            })
        }}, 1000);
    </script>
@endsection

