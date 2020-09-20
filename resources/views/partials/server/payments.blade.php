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
         
                <div class="card card-shadow card-responsive" id="paymentsChart">
                    <div class="card-block p-0">
                    <div class="p-30" style="height:120px;">
                        <div class="row">
                            <div class="col-4">
                                <div class="counter text-left">
                                <div class="counter-label blue-grey-200">Total Payout</div>
                                <div class="counter-number-group">
                                    <span class="counter-number green-600">{{ number_format(($total_payout / 100), 2, '.', ',') }}</span>
                                    <span class="counter-number-related green-600">USD</span>
                                </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="counter text-left">
                                <div class="counter-label">Weekly Average</div>
                                <div class="counter-number-group">
                                    <span class="counter-number">{{ number_format(($average_weekly / 100), 2, '.', ',') }}</span>
                                    <span class="counter-number-related">USD</span>
                                </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="counter text-left">
                                <div class="counter-label">Pending Payout</div>
                                <div class="counter-number-group">
                                    <span class="counter-number">{{ number_format(($pending_payout / 100), 2, '.', ',') }}</span>
                                    <span class="counter-number-related">USD</span>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($subscriptions->count() > 0)
                    <ul class="list-unstyled list-hover pb-50 mb-0" style="height:calc(100% - 270px);">
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
                                    @if($subscription->latest_paid_out_invoice_id == $subscription->latest_invoice_id) 
                                    <div class="col-3 green-600">
                                        Paid Out
                                    </div>
                                    @else 
                                    <div class="col-3">
                                        @if($subscription->latest_invoice_paid_at > Carbon\Carbon::now()->subDays(15)) 
                                            Pending Payout
                                        @else 
                                        Invoice Sent
                                        @endif 
                                    </div>
                                    @endif
                                </div>
                            </li>
                            @endforeach
                    </ul>
                    {{ $subscriptions->render() }}
                    @endif
                    <div class="ct-chart h-150"></div>
                    </div>
                </div>

                    

             
            </div>
@section('scripts')

<script>

(function () {
        var timeline_labels = [];
        var timeline_data1 = [];
        var timeline_data2 = [];
        var totalPoints = 20;
        var updateInterval = 1000;
        var now = new Date().getTime();
  
        function GetData() {
          timeline_labels.shift();
          timeline_data1.shift();
          timeline_data2.shift();
  
          while (timeline_data1.length < totalPoints) {
            var x = Math.random() * 100 + 800;
            var y = Math.random() * 100 + 400;
            timeline_labels.push(now += updateInterval);
            timeline_data1.push(x);
            timeline_data2.push(y);
          }
        }
  
        var timlelineData = {
          labels: timeline_labels,
          series: [timeline_data1, timeline_data2]
        };
        var timelineOptions = {
          low: 0,
          showArea: true,
          showPoint: false,
          showLine: false,
          fullWidth: true,
          chartPadding: {
            top: 0,
            right: 0,
            bottom: 0,
            left: 0
          },
          axisX: {
            showLabel: false,
            showGrid: false,
            offset: 0
          },
          axisY: {
            showLabel: false,
            showGrid: false,
            offset: 0
          },
          plugins: [Chartist.plugins.tooltip()]
        };
        new Chartist.Line("#paymentsChart .ct-chart", timlelineData, timelineOptions);
  
      })();
</script>
@endsection
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

<script>
function tabPayments(){
    setTimeout(function(){
        $('#payments-loading_table').hide();
    },2000)
};
</script>