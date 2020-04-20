<div class="tab-pane fade active show" id="tab-server">
    <div class="row">

        <div class="col-xxl-6 offset-xxl-1 col-lg-6">

            <!-- Panel Watchlist -->
            <div class="panel panel-shadow mt-5" id="panel-products">
                <div class="panel-heading">
                    <div class="ribbon-container">
                        <div class="ribbon ribbon-primary">
                            <span class="ribbon-inner">Products</span>
                        </div>
                        <p>Enable roles for purchase on your shop</p>
                    </div>
                    <div class="panel-actions panel-actions-keep">
                       <button type="button" class="btn btn-sm btn-dark btn-icon btn-round" id="btn_edit-roles"
                                data-toggle="tooltip" data-original-title="Show All">
                            <i class="icon wb-plus" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-dark btn-icon btn-round d-none btn_save-roles" id="btn_save-roles"
                                data-toggle="tooltip" data-original-title="Done">
                            <span class="d-none text_save-roles"></span> <i class="icon wb-minus" id="icon_save-roles" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <div class="panel-body px-0 pb-10">
                    <table class="table" data-plugin="animateList"
                        data-animate="fade"
                        data-child="tr">
                        <tbody id="roles_table"></tbody>
                    </table>
                <!-- <div class="card card-hover" data-toggle="slidePanel" data-url="/slide-list-products/{{ $id }}"><i class="icon wb-plus"></i> Shop Product
                    </div>-->
                {{-- <div class="card card-block card-hover mb-25" data-toggle="slidePanel" data-url="/slide-list-products/{{ $id }}"> --}}           
                </div>
                <!--<div class="panel-footer">
                    <div class="card card-block card-hover my-5 save-products d-none btn_save-roles" id="btn_save-products">
                        <div class="counter counter-lg counter-inverse blue-grey-300 vertical-align">
                        <div class="vertical-align-middle">
                            <span class="counter-number">Save Roles</span>
                        </div>
                        </div>
                    </div>
                    <div class="card card-block card-hover my-5 save-products btn_edit-roles" id="btn_edit-roles">
                        <div class="counter counter-lg counter-inverse blue-grey-300 vertical-align">
                        <div class="vertical-align-middle">
                            <span class="counter-number">Show Roles</span>
                        </div>
                        </div>
                    </div>
                </div>-->
            </div>
        </div>

        <div class="col-xxl-4 col-lg-6">
            <!-- Panel Watchlist -->
            <div class="panel panel-shadow mt-5">
                <div class="panel-heading">
                    <div class="ribbon-container">
                        <div class="ribbon ribbon-success">
                            <span class="ribbon-inner">New Payments</span>
                        </div>
                        <p>&#8205;</p>
                    </div>
                    <div class="panel-actions panel-actions-keep" onclick="fillRecentPayments();">
                        <button type="button" class="btn btn-sm btn-dark btn-icon btn-round" id="btn_recent-refresh"
                                data-toggle="tooltip" data-original-title="Refresh">
                            <i class="icon wb-refresh" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <div class="panel-body h-350 pb-10" data-plugin="scrollable">
                    <div data-role="container">
                        <div data-role="content" class="p-0">
                            <table class="table mb-0" data-plugin="animateList" data-animate="fade" data-child="tr">
                                <tbody class="table bg-grey-3 loading-bg">
                                    @if($has_order)
                                    @include('partials/server/loaders/new-payments')
                                    @endif                            
                                </tbody>
                                <tbody id="recent-transactions-table">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Panel Watchlist -->
        </div>

        


    </div>
</div>

<div class="tab-pane fade" id="sidebar-roles">
    <div class="page-header">
        <h1 class="page-title responsive-hide" id="role_count">Loading...</h1>
        <div class="page-header-actions">
            <button type="button" class="btn btn-sm btn-icon btn-inverse btn-round"
                    data-toggle="tooltip" data-original-title="Refresh">
                <i class="icon wb-refresh" aria-hidden="true"></i>
            </button>
        </div>
    </div>
    <div>

        <table class="table" data-plugin="animateList" data-animate="fade"
               data-child="tr">
            <tbody id="roles_table"></tbody>
        </table>
        <!-- pagination -->

    </div>
</div><!-- end tab -->

