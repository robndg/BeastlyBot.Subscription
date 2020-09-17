<style>
.list-hover li:hover{
    background-color: #25252b;
}
</style>
<div class="tab-pane tab-large fade" id="tab-payments">
    <!--<div class="page-header">
        <h1 class="page-title responsive-hide">Payments</h1>
        <div class="page-header-actions">
          <button type="button" class="btn btn-sm btn-icon btn-inverse btn-round waves-effect waves-classic" id="btn_payments-refresh" data-toggle="tooltip" data-original-title="Refresh">
            <i class="wb-refresh" aria-hidden="true"></i>
          </button>
        </div>
    </div>-->

    <div class="page-content-table">
        <div class="page-main">
            <div class="row">

            <div class="col-md-10 offset-md-1">
         
                <div class="card card-shadow card-responsive" id="widgetTimeline">
                    <div class="card-block p-0">
                    <div class="p-30" style="height:120px;">
                        <div class="row">
                        <div class="col-4">
                            <div class="counter text-left">
                            <div class="counter-label blue-grey-200">Total Earnings</div>
                            <div class="counter-number-group">
                                <span class="counter-number green-600">{{ number_format(($total_payout / 100), 2, '.', ',') }}</span>
                                <span class="counter-number-related green-600">USD</span>
                            </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="counter text-left">
                            <div class="counter-label">Paid Out</div>
                            <div class="counter-number-group">
                                <span class="counter-number">{{ number_format(($pending_payout / 100), 2, '.', ',') }}</span>
                                <span class="counter-number-related">USD</span>
                            </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="counter text-left">
                            <div class="counter-label">Pending Payout</div>
                            <div class="counter-number-group">
                                <span class="counter-number">{{ number_format(($pending_payment / 100), 2, '.', ',') }}</span>
                                <span class="counter-number-related">USD</span>
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>
                    @if($subscriptions->count() > 0)
                    <ul class="list-unstyled list-hover pb-50 mb-0" style="height:calc(100% - 270px);">
                      {{--  @foreach($invoices as $sub_id => $invoices) --}}
                            {{-- @foreach($invoices as $invoice) --}}

                       
                            @foreach($subscriptions as $subscription)
                            @php
                           
                            $discord_helper = new \App\DiscordHelper(\App\User::where('id', $subscription->user_id)->first());
                            $role = $discord_helper->getRole($id, $subscription->metadata['role_id']);
                            @endphp
                            <li class="px-30 py-15 container-fluid" data-url="/slide-invoice?id={{ $subscription->latest_invoice_id }}&user_id={{ $subscription->user_id }}&role_id={{ $role->id }}&guild_id={{ $id }}" data-toggle="slidePanel">
                                <div class="row">
                                    <div class="col-3">{{ $discord_helper->getUsername() }}</div>
                                    <div class="col-3"><span class="badge m-5" style="color: white;background-color: #{{ dechex($role->color) }};">{{ $role->name }}</span></div>
                                    <div class="col-3 text-right">${{ number_format(($subscription->latest_invoice_amount / 100), 2, '.', ',') }} USD</div>
                                    @if($subscription->latest_invoice_paid_out_id == $subscription->latest_invoice_id) 
                                    <div class="col-3 green-600">
                                        Paid {{ $subscription->latest_invoice_paid_at->addDays(15)->diffForHumans() }} 
                                    </div>
                                    @else 
                                    <div class="col-3">
                                        @if($subscription->latest_invoice_paid_at > Carbon\Carbon::now()->subDays(15)) 
                                            {{ $subscription->latest_invoice_paid_at->addDays(15)->diffForHumans() }} 
                                        @else 
                                        Invoice Sent
                                        @endif 
                                    </div>
                                    @endif
                                </div>
                            </li>
                            @endforeach
                       {{-- @endforeach --}}
                    </ul>
                    {{ $subscriptions->render() }}
                    @endif
                    <div class="ct-chart h-150"><div class="chartist-tooltip" style="top: -14.0312px; left: 507px;"></div><svg xmlns:ct="http://gionkunz.github.com/chartist-js/ct" width="100%" height="100%" class="ct-chart-line" style="width: 100%; height: 100%;"><g class="ct-grids"></g><g><g class="ct-series ct-series-a"><path d="M0,150L0,7.753C22.807,8.36,45.614,9.575,68.421,9.575C91.228,9.575,114.035,7.886,136.842,7.886C159.649,7.886,182.456,16.215,205.263,16.215C228.07,16.215,250.877,12.058,273.684,9.939C296.491,7.82,319.298,3.497,342.105,3.497C364.912,3.497,387.719,11.694,410.526,11.694C433.333,11.694,456.14,4.396,478.947,4.396C501.754,4.396,524.561,10.612,547.368,10.612C570.175,10.612,592.982,7.663,615.789,5.999C638.596,4.335,661.404,0.559,684.211,0.559C707.018,0.559,729.825,1.063,752.632,1.51C775.439,1.956,798.246,3.087,821.053,3.776C843.86,4.464,866.667,4.665,889.474,5.672C912.281,6.68,935.088,13.112,957.895,13.112C980.702,13.112,1003.509,6.623,1026.316,4.689C1049.123,2.754,1071.93,0.263,1094.737,0.263C1117.544,0.263,1140.351,16.217,1163.158,16.217C1185.965,16.217,1208.772,7.751,1231.579,7.751C1254.386,7.751,1277.193,9.443,1300,10.289L1300,150Z" class="ct-area"></path></g><g class="ct-series ct-series-b"><path d="M0,150L0,70.008C22.807,69.958,45.614,69.858,68.421,69.858C91.228,69.858,114.035,70.521,136.842,71.421C159.649,72.322,182.456,81.356,205.263,81.356C228.07,81.356,250.877,73.53,273.684,71.68C296.491,69.829,319.298,67.788,342.105,67.788C364.912,67.788,387.719,79.636,410.526,80.204C433.333,80.772,456.14,81.119,478.947,81.119C501.754,81.119,524.561,71.483,547.368,70.43C570.175,69.377,592.982,68.591,615.789,68.576C638.596,68.561,661.404,68.568,684.211,68.554C707.018,68.539,729.825,67.472,752.632,67.472C775.439,67.472,798.246,81.256,821.053,81.271C843.86,81.286,866.667,81.294,889.474,81.294C912.281,81.294,935.088,78.982,957.895,78.982C980.702,78.982,1003.509,80.122,1026.316,80.122C1049.123,80.122,1071.93,68.902,1094.737,68.902C1117.544,68.902,1140.351,82.171,1163.158,82.171C1185.965,82.171,1208.772,71.958,1231.579,71.958C1254.386,71.958,1277.193,77.141,1300,79.733L1300,150Z" class="ct-area"></path></g></g><g class="ct-labels"></g></svg></div>
                    </div>
                </div>

                    

             
            </div>

            <div class="col-md-10 offset-md-1">
                 <div class="card">

                    <div class="card-header card-header-transparent py-20">
                        <ul class="nav nav-pills nav-pills-rounded chart-action">
                            <li class="nav-item"><a class="active nav-link" id="payments-btn" href="#">Sales</a></li>
                            <li class="nav-item"><a class="nav-link disabled" id="disputes-btn" href="#" disabled>Disputes</a></li>
                        </ul>
                    </div>

                    <div class="card-body pt-0 mt-md-10 payments">
                        <div class="list-group list-group-dividered" id="payments-loading_table">
                            @include('partials/server/loaders/payments-loader')
                        </div>
                        <div class="list-group list-group-dividered" id="payments_table">
                        </div>
                        <div class="list-group list-group-dividered hidden" id="disputes_table">
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
