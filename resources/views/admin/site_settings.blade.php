@extends('layouts.app')

@section('title', 'Admin - Site Settings')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Site Settings</h1>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
          <li class="breadcrumb-item" href="/admin/site">Site</li>
          <li class="breadcrumb-item active">Settings</li>
        </ol>
        <div class="page-header-actions">
            <div class="input-group">
                <input type="text" class="form-control" name="SecurityCheck" id="securityCheck" placeholder="Enter Unlock Code">
            </div>
        </div>
      </div>
<div class="page-content">
    <div class="panel">
        <div class="panel-body container-fluid">
            <div class="row">

                    @foreach($site_settings as $site_setting)
                
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <!-- Example Search -->
                            <div class="example-wrap">
                                <h4 class="example-title">{{str_replace('_', ' ', $site_setting)}}</h4>
                                <div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="{{ $site_setting }}" value="asasdasdasdasd">
                                    <span class="input-group-btn">
                                        <button type="submit" class="btn btn-primary" data-name="{{ $site_setting }}"><i class="icon wb-check" aria-hidden="true"></i></button>
                                    </span>
                                </div>
                                </div>

                            </div>
                            <!-- End Example Search -->
                        </div>
            
                    @endforeach


                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection


<!-----

Script to send this to public function updateSiteSettings
1) Unlock key
2) Setting Name (from pressed button with data-name)
3) New Setting String (from value)

----->
