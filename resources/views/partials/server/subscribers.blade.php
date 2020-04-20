<div class="tab-pane tab-large fade" id="tab-subscribers">

    <!--<div class="page-header">
        <h1 class="page-title responsive-hide"><span id="subscribers_count">0</span> Subscriber<span id="subscribers-suffix">s</span></h1>
        <div class="page-header-actions">
          <button type="button" class="btn btn-sm btn-icon btn-inverse btn-round waves-effect waves-classic spinning" 
          id="btn_subscribers-refresh" data-toggle="tooltip" data-original-title="Refresh">
            <i class="wb-refresh" aria-hidden="true"></i>
          </button>
        </div>
    </div>-->
    <div>
    <button type="button" class="btn btn-block btn-primary" data-url="/slide-server-member-role-add/{{ $id }}/301838193018273793" data-toggle="slidePanel">Add Role</button>
        <table class="table" data-plugin="animateList" data-animate="fade" data-child="tr">
            <tbody id="subscribers-loading_table">
                @include('partials/server/loaders/subscribers-loader')                                                                                             
            </tbody>
            <tbody id="subscribers_table">
            </tbody>
        </table>
        <!-- pagination -->

    </div>
</div><!-- end tab -->
