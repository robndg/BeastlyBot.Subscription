@extends('layouts.dash')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid note-details">
    
    <div class="desktop-header">
            <div class="card card-block topnav-left">
                <div class="card-body write-card">
                     <div class="d-flex flex-wrap align-items-center justify-content-between">
                           <h4>Dashboard</h4>
                           <div class="media flex-wrap align-items-center">
                              <div class="iq-search-bar card-search mr-3 position-relative">
                                 <a href="#" class="search-toggle rounded" id="dropdownSearch" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                       <i class="ri-search-line"></i>
                                 </a>
                                 <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownSearch" style="">
                                       <form action="#" class="searchbox">
                                          <div class="form-group mb-0 position-relative">
                                          <input type="text" class="text search-input font-size-12" placeholder="Find Your Server..">
                                          <a href="#" class="search-link"><i class="ri-search-line"></i></a> 
                                          </div>
                                       </form>
                                 </div>
                              </div>
                              <a href="#" class="btn btn-primary add-btn" data-toggle="modal" data-target="#new-note"><i class="las la-plus pr-2"></i>Add Bot</a>
                           </div>
                     </div>
                  </div>
            </div>
        <!--<div class="card card-block topnav-left">
            <div class="card-body d-flex align-items-center">
                <div class="d-flex justify-content-between">
                    <h4 class="text-capitalize">Dashboard</h4>
                </div>
            </div>
        </div>-->
              <!-- <div class="card card-block topnav-left">
            <div class="card-body write-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="iq-note-callapse-menu">
                        <a class="iq-note-callapse-btn show-note-button" data-toggle="collapse" href="#collapseMenu" role="button" data-extra-toggle="toggle" data-extra-class-show=".hide-note-button" data-extra-class-hide=".show-note-button" aria-expanded="false">
                            <i class="las la-pencil-alt pr-2"></i>{{-- $guild->name --}}
                        </a>
                        <span class="hide-note-button d-none"><i class="las la-folder pr-2"></i>Folder</span>
                    </div>
                    <div class="note-right media align-items-top hide-note-button d-none">
                        <div class="mr-2"><a href="#" class="btn view-btn body-bg" data-toggle="modal" data-target="#share-note">Share</a></div>
                        <div class="view-btn btn-dropdown body-bg rounded">
                            <div class="dropdown">
                                <span class="dropdown-toggle" id="dropdownMenuButton1" data-toggle="dropdown">
                            <i class="ri-more-2-fill"></i>
                        </span>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton1">
                                    <a class="dropdown-item" href="#"><i class="lar la-heart mr-3"></i>Add To Favourite</a>
                                    <a class="dropdown-item" href="#"><i class="las la-thumbtack mr-3"></i>Mark As Pin</a>
                                    <a class="dropdown-item" href="#"><i class="las la-trash-alt mr-3"></i>Move to Trash</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>-->

        @include('partials.dash.topnav-right')
    </div>

       
                                            



Hello
Select Guild

</div>
@endsection('content')