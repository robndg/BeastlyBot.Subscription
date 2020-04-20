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
                 <div class="card">

                    <div class="card-header card-header-transparent py-20">
                        <ul class="nav nav-pills nav-pills-rounded chart-action">
                            <li class="nav-item"><a class="active nav-link" id="payments-btn" href="#" onclick="loadPayments()">Sales</a></li>
                            <li class="nav-item"><a class="nav-link disabled" id="disputes-btn" href="#" onclick="loadDisputes()" disabled>Disputes</a></li>
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
