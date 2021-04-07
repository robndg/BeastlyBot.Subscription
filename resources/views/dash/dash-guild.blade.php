@extends('layouts.dash')

@section('title', 'Dashboard Guild')

@section('content')
<div class="container-fluid beast-details">
           
    <div class="desktop-header">
        <div class="card card-block topnav-left">
            <div class="card-body write-card">
                 <div class="d-flex flex-wrap align-items-center justify-content-between">
                       <h4>{{ $guild->name }}</h4>
                       <div class="media flex-wrap align-items-center">
                          <div class="iq-search-bar card-search mr-3 position-relative">
                             <a href="#" class="search-toggle rounded" id="dropdownSearch" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                   <i class="ri-search-line"></i>
                             </a>
                             <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownSearch" style="">
                                   <form action="#" class="searchbox">
                                      <div class="form-group mb-0 position-relative">
                                      <input type="text" class="text search-input font-size-12" placeholder="Find a product...">
                                      <a href="#" class="search-link"><i class="ri-search-line"></i></a> 
                                      </div>
                                   </form>
                             </div>
                          </div>
                          <a class="btn btn-info add-btn" href="/shop/{{ $store_settings->url_slug }}" target="_blank"><i class="las la-store pr-2"></i>Store Front</a>
                       </div>
                 </div>
              </div>
        </div>
        @include('partials.dash.topnav-right')
    </div>



        <div class="col-lg-12">
                        <div class="card-stretch">
                            <div class="card-body custom-notes-space">
                                <h3 class="">Guild Products</h3>
                                <div class="iq-tab-content">
                                    <div class="d-flex flex-wrap align-items-top justify-content-between">
                                        <ul class="d-flex nav nav-pills text-center beast-tab mb-3" id="beast-pills-tab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link home active show" data-toggle="pill" data-init="note" href="#active" role="tab" aria-selected="false">Products Active</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link home" data-toggle="pill" data-init="shared-note" href="#archived" role="tab" aria-selected="true">Inactive</a>
                                            </li>
                                        </ul>
                                        <div class="media align-items-top iq-grid">
                                            <div class="view-btn rounded body-bg btn-dropdown filter-btn mr-3">
                                                <div class="dropdown">
                                                    <a class="cursor-pointer" id="dropdownMenuButton003" href="/dashboard/{{ $guild_id . '/product' ?? '' }}">
                                                        <i class="ri ri-add-fill font-size-20"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-right border-none" aria-labelledby="dropdownMenuButton003">
                                                        <div class="dropdown-item mb-3">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <h6 class="mr-4"><i class="las la-book mr-3"></i>Located In</h6>
                                                                <div class="form-group mb-0">
                                                                    <select name="type" class="basic-select form-control dropdown-toggle" data-style="py-0">
                                                                <option value="1">Project Plans</option>
                                                                <option value="2">Routine Notes</option>
                                                                <option value="3">Planning</option>
                                                            </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="dropdown-item mb-3">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <h6 class="mr-4"><i class="las la-paste mr-3"></i>Contains</h6>
                                                                <div class="form-group mb-0">
                                                                    <select name="type" class="basic-select form-control dropdown-toggle" data-style="py-0">
                                                                <option value="1">Address</option>
                                                                <option value="2">Archive Files</option>
                                                                <option value="3">Code Blocks</option>
                                                            </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="dropdown-item mb-2">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <h6 class="mr-4"><i class="las la-calendar mr-3"></i>Created</h6>
                                                                <div class="form-group mb-0">
                                                                    <select id="date-select" name="type" class="basic-select form-control dropdown-toggle" data-style="py-0">
                                                                <option value="today">Today</option>
                                                                <option value="yest">Yesterday</option>
                                                                <option value="last-week">Last Week</option>
                                                                <option value="last-month">Last Month</option>
                                                            </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="list-grid-toggle cursor-pointer">
                                                <span class="icon active i-grid rounded"><i class="ri-layout-grid-line font-size-20"></i></span>
                                                <span class="icon active i-list rounded"><i class="ri-list-check font-size-20"></i></span>
                                                <span class="label label-list">List</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="beast-content tab-content">
                                        <div id="note1" class="tab-pane fade active show">
                                            <div class="icon active animate__animated animate__fadeIn i-grid">
                                                <div class="row">
                                                <!-- TODO; if plans have no prices or archived put in tab -->
                                                    @foreach($product_roles as $product)

                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="card card-block card-stretch card-height card-bottom-border-purple beast-detail blur-shadow">
                                                            <div class="card-header d-flex justify-content-between pb-1">
                                                                <div class="icon iq-icon-box-2 icon-border-purple rounded">
                                                                    
                                                                    <svg class="svg-icon" width="23" height="23" id="iq-ui-1-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" style="stroke-dasharray: 72, 92; stroke-dashoffset: 0;"></path>
                                                                    </svg>
                                                                </div>
                                                                <div class="card-header-toolbar d-flex align-items-center">
                                                                    <a href="#" data-toggle="tooltip" data-placement="top" class="show-tab" data-show-tab="[href='#roleid']" title="" data-original-title="pin product"><i class="las la-thumbtack mr-2"></i></a>
                                                                    <div class="card-header-toolbar d-flex align-items-center">
                                                                        <div class="dropdown">
                                                                            <span class="dropdown-toggle dropdown-bg" id="beast-dropdownMenuButton5" data-toggle="dropdown" aria-expanded="false" role="button">
                                                                        <i class="ri-more-fill"></i>
                                                                    </span>
                                                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="beast-dropdownMenuButton5" style="">
                                                                                <a href="" class="dropdown-item new-note2" data-toggle="modal" data-target="#view-productshare"><i class="las la-link mr-3"></i>Share</a>
                                                                                <a href="#" class="dropdown-item edit-note1" data-toggle="modal" data-target="#edit-roleid"><i class="las la-pen mr-3"></i>Edit Role</a>
                                                                                <a class="dropdown-item" data-extra-toggle="delete" data-closest-elem=".card" href="#archive-roleid"><i class="las la-trash-alt mr-3"></i>Archive</a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-body rounded">
                                                                <h4 class="card-title">{{ $product->title }}
                                                                   
                                                                    {{-- auth()->user()->discord_helper->getRole($guild_id, $product->role_id)->name --}}</h4>
                                                                <span class="card-text card-text-info">@if($product->access > 0){{'Available'}}@else{{'Archived'}}@endif</span>
                                                                <p class="mb-3 card-description short">{{ $product->description }}</p>
                                                                

                                                                <ul class="list-inline p-0 m-0 text-center">
                                                                    <li class="mb-2">
                                                                        <h5>Plans</h5>
                                                                    </li>
                                                                    <li class="mb-2">
                                                                    <div class="btn-group btn-group-toggle btn-group-flat">
                                                                    @foreach($product->prices()->get()->where('status', 1) as $price) <!-- TODO Rob: going to move prices to ajax popup with edit fields, checking relationship -->
                                                                        <span class="button btn button-icon bg-primary" target="_blank" href="#">${{ number_format(($price->price/100),2) }}</a>
                                                                    @endforeach
                                                                    </div>
                                                                    </li>
                                                                    <li>
                                                                        <div class="btn-group btn-group-toggle btn-group-flat">
                                                                            <a href="/dashboard/{{ $guild_id }}/product?uuid={{ $product->id }}" class="btn btn-success mt-2"><i class="ri-settings-4-fill"></i>Manage</a>
                                                                            <button type="button" class="btn btn-success mt-2"><i class="ri-bill-fill"></i></button>
                                                                        </div>
                                                                    </li>
                                                                </ul>
                                                                
                                                            </div>
                                                            <div class="card-footer">
                                                                <div class="d-flex align-items-center justify-content-between beast-text beast-text-purple">
                                                                    
                                                                    <a href="index.html#" class=""><i class="las la-user mr-2 font-size-20"></i>{{\App\Subscription::where('product_id', $product->id)->where('status', 1)->count()}} @if($product->max_sales != null){{'/'}}{{$product->max_sales}}@endif</a>
                                                                    <a href="index.html#" class=""><i class="las la-calendar mr-2 font-size-20"></i>{{ Carbon\Carbon::createFromTimestamp(strtotime($product->start_date))->diffForHumans() }}</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                               
                                                </div>
                                                
                                                </div>
                                            </div>
                                            <div class="icon active animate__animated animate__fadeIn i-list">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="table-responsive">
                                                            <table class="table  tbl-server-info">
                                                                <thead>
                                                                    <tr class="ligth">
                                                                        <th class="w-50" scope="col">Title</th>
                                                                        <th scope="col">Permission</th>
                                                                        <th scope="col">Start Date</th>
                                                                        <th scope="col">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            <h4 class="mb-2">Weekly Planner</h4>
                                                                            <span>Virtual Digital Marketing Course every week on Monday, Wednesday and Saturday</span>
                                                                        </td>
                                                                        <td>
                                                                            <i class="las la-user-friends mr-2 font-size-20"></i> 03 members
                                                                        </td>
                                                                        <td>Dec 20</td>
                                                                        <td>
                                                                            <div>
                                                                                <a href="index.html#" class="badge badge-success mr-3" data-toggle="modal" data-target="#edit-note1"><i class="las la-pen mr-0"></i></a>
                                                                                <a href="index.html#" class="badge badge-danger" data-extra-toggle="delete" data-closest-elem="tr"><i class="las la-trash-alt mr-0"></i></a>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <h4 class="mb-2">Birthday Celebration <i class="las la-thumbtack ml-2 show-tab" data-show-tab="[href='#note3']"></i></h4>
                                                                            <span>You can easily share via message, WhatsApp, emails etc for half price.</span>
                                                                        </td>
                                                                        <td>
                                                                            <i class="las la-lock mr-2 font-size-20"></i> 5 share
                                                                        </td>
                                                                        <td>Dec 20</td>
                                                                        <td>
                                                                            <div>
                                                                                <a href="index.html#" class="badge badge-success mr-3" data-toggle="modal" data-target="#edit-note1"><i class="las la-pen mr-0"></i></a>
                                                                                <a href="index.html#" class="badge badge-danger" data-extra-toggle="delete" data-closest-elem="tr"><i class="las la-trash-alt mr-0"></i></a>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <h4 class="mb-2">Essay Outline <i class="lar la-heart ml-2 show-tab" data-show-tab="[href='#note4']"></i></h4>
                                                                            <span>Doing the essay team group for a limited time offer.</span>
                                                                        </td>
                                                                        <td>
                                                                            <i class="las la-lock mr-2 font-size-20"></i> 20 share
                                                                        </td>
                                                                        <td>Dec 20</td>
                                                                        <td>
                                                                            <div>
                                                                                <a href="index.html#" class="badge badge-success mr-3" data-toggle="modal" data-target="#edit-note1"><i class="las la-pen mr-0"></i></a>
                                                                                <a href="index.html#" class="badge badge-danger" data-extra-toggle="delete" data-closest-elem="tr"><i class="las la-trash-alt mr-0"></i></a>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <h4 class="mb-2">Lecture Notes <i class="lar la-heart ml-2 show-tab" data-show-tab="[href='#note4']"></i></h4>
                                                                            <span>Chapter 1 notes, Chapter 2 Assignment, Chapter 3 practical File.</span>
                                                                        </td>
                                                                        <td>
                                                                            <i class="las la-lock mr-2 font-size-20"></i> Only You
                                                                        </td>
                                                                        <td>Dec 20</td>
                                                                        <td>
                                                                            <div>
                                                                                <a href="index.html#" class="badge badge-success mr-3" data-toggle="modal" data-target="#edit-note1"><i class="las la-pen mr-0"></i></a>
                                                                                <a href="index.html#" class="badge badge-danger" data-extra-toggle="delete" data-closest-elem="tr"><i class="las la-trash-alt mr-0"></i></a>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <h4 class="mb-2">Image Notes<i class="las la-thumbtack ml-2"></i></h4>
                                                                            <span>View the weeks daily subscriptions for half price</span>
                                                                        </td>
                                                                        <td>
                                                                            <i class="las la-lock mr-2 font-size-20"></i> Everyone
                                                                        </td>
                                                                        <td>Dec 20</td>
                                                                        <td>
                                                                            <div>
                                                                                <a href="index.html#" class="badge badge-success mr-3" data-toggle="modal" data-target="#edit-note1"><i class="las la-pen mr-0"></i></a>
                                                                                <a href="index.html#" class="badge badge-danger" data-extra-toggle="delete" data-closest-elem="tr"><i class="las la-trash-alt mr-0"></i></a>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <h4 class="mb-2">Benefits of NotePlus</h4>
                                                                            <span>Take organized notes and share later as meeting minutes or check-list</span>
                                                                        </td>
                                                                        <td>
                                                                            <i class="las la-user-friends mr-2 font-size-20"></i> 2 share
                                                                        </td>
                                                                        <td>Dec 20</td>
                                                                        <td>
                                                                            <div>
                                                                                <a href="index.html#" class="badge badge-success mr-3" data-toggle="modal" data-target="#edit-note1"><i class="las la-pen mr-0"></i></a>
                                                                                <a href="index.html#" class="badge badge-danger" data-extra-toggle="delete" data-closest-elem="tr"><i class="las la-trash-alt mr-0"></i></a>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <h4 class="mb-2">Quick Summary <i class="las la-thumbtack ml-2 show-tab" data-show-tab="[href='#note3']"></i></h4>
                                                                            <span>Need to write a summary note of the subject you just finished</span>
                                                                        </td>
                                                                        <td>
                                                                            <i class="las la-lock mr-2 font-size-20"></i> Only You
                                                                        </td>
                                                                        <td>Dec 19</td>
                                                                        <td>
                                                                            <div>
                                                                                <a href="index.html#" class="badge badge-success mr-3" data-toggle="modal" data-target="#edit-note1"><i class="las la-pen mr-0"></i></a>
                                                                                <a href="index.html#" class="badge badge-danger" data-extra-toggle="delete" data-closest-elem="tr"><i class="las la-trash-alt mr-0"></i></a>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <h4 class="mb-2">Address & Email</h4>
                                                                            <span>Quickly note down the address and email address on NotePlus</span>
                                                                        </td>
                                                                        <td>
                                                                            <i class="las la-user-friends mr-2 font-size-20"></i> 04 share
                                                                        </td>
                                                                        <td>Dec 19</td>
                                                                        <td>
                                                                            <div>
                                                                                <a href="index.html#" class="badge badge-success mr-3" data-toggle="modal" data-target="#edit-note1"><i class="las la-pen mr-0"></i></a>
                                                                                <a href="index.html#" class="badge badge-danger" data-extra-toggle="delete" data-closest-elem="tr"><i class="las la-trash-alt mr-0"></i></a>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <h4 class="mb-2">NotePlus for Entrepreneurs <i class="lar la-heart ml-2 show-tab" data-show-tab="[href='#note4']"></i></h4>
                                                                            <span>With NotePlus, you can easily share via message, WhatsApp, emails etc.</span>
                                                                        </td>
                                                                        <td>
                                                                            <i class="las la-lock mr-2 font-size-20"></i> Only You
                                                                        </td>
                                                                        <td>Dec 19</td>
                                                                        <td>
                                                                            <div>
                                                                                <a href="index.html#" class="badge badge-success mr-3" data-toggle="modal" data-target="#edit-note1"><i class="las la-pen mr-0"></i></a>
                                                                                <a href="index.html#" class="badge badge-danger" data-extra-toggle="delete" data-closest-elem="tr"><i class="las la-trash-alt mr-0"></i></a>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="note2" class="tab-pane fade">
                                            <div class="icon active animate__animated animate__fadeIn i-grid">
                                                <div class="row">
                                                    
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="card card-block card-stretch card-height card-bottom-border-info beast-detail">
                                                            <div class="card-header d-flex justify-content-between pb-1">
                                                                <div class="icon iq-icon-box-2 icon-border-info rounded">
                                                                    <svg width="23" height="23" class="svg-icon" id="iq-main-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                                </div>
                                                                <div class="card-header-toolbar d-flex align-items-center">
                                                                    <div class="dropdown">
                                                                        <span class="dropdown-toggle dropdown-bg" id="beast-dropdownMenuButton13" data-toggle="dropdown" aria-expanded="false" role="button">
                                                                    <i class="ri-more-fill"></i>
                                                                </span>
                                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="beast-dropdownMenuButton13" style="">
                                                                            <a href="index.html#" class="dropdown-item new-note1" data-toggle="modal" data-target="#new-note1"><i class="las la-eye mr-3"></i>View</a>
                                                                            <a href="index.html#" class="dropdown-item edit-note1" data-toggle="modal" data-target="#edit-note1"><i class="las la-pen mr-3"></i>Edit</a>
                                                                            <a class="dropdown-item" data-extra-toggle="delete" data-closest-elem=".card" href="index.html#"><i class="las la-trash-alt mr-3"></i>Delete</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-body rounded">
                                                                <h4 class="card-title"></h4>
                                                                <p class="mb-3 card-description short">Virtual Digital Marketing Course every week on Monday, Wednesday and Saturday.Virtual Digital Marketing Course every week on Monday</p>
                                                            </div>
                                                            <div class="card-footer">
                                                                <div class="d-flex align-items-center justify-content-between beast-text beast-text-info">
                                                                    <a href="index.html#" class=""><i class="las la-user-friends mr-2 font-size-20"></i>03 share</a>
                                                                    <a href="index.html#" class=""><i class="las la-calendar mr-2 font-size-20"></i>12 Jan 2021</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                   
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="card card-block card-stretch card-height card-bottom-border-success beast-detail">
                                                            <div class="card-header d-flex justify-content-between pb-1">
                                                                <div class="icon iq-icon-box-2 icon-border-success rounded">
                                                                    <svg width="23" height="23" class="svg-icon" id="iq-main-11" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                                            </svg>
                                                                </div>
                                                                <div class="card-header-toolbar d-flex align-items-center">
                                                                    <div class="dropdown">
                                                                        <span class="dropdown-toggle dropdown-bg" id="beast-dropdownMenuButton14" data-toggle="dropdown" aria-expanded="false" role="button">
                                                                    <i class="ri-more-fill"></i>
                                                                </span>
                                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="beast-dropdownMenuButton14" style="">
                                                                            <a href="index.html#" class="dropdown-item new-note6" data-toggle="modal" data-target="#new-note6"><i class="las la-eye mr-3"></i>View</a>
                                                                            <a href="index.html#" class="dropdown-item edit-note1" data-toggle="modal" data-target="#edit-note1"><i class="las la-pen mr-3"></i>Edit</a>
                                                                            <a class="dropdown-item" data-extra-toggle="delete" data-closest-elem=".card" href="index.html#"><i class="las la-trash-alt mr-3"></i>Delete</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-body rounded">
                                                                <h4 class="card-title">Benefits of NotePlus</h4>
                                                                <p class="mb-3 card-description short">Take organized notes and share later as meeting minutes or check-list with this simple accessible Noteplus. Each note you create will be stored on a virtual page of the NotePlus. You can
                                                                    store groups of seperate notes. You can store an unlimited number of separate notes within the NotePlus.</p>
                                                            </div>
                                                            <div class="card-footer">
                                                                <div class="d-flex align-items-center justify-content-between beast-text beast-text-success">
                                                                    <a href="index.html#" class=""><i class="las la-user-friends mr-2 font-size-20"></i>02 share</a>
                                                                    <a href="index.html#" class=""><i class="las la-calendar mr-2 font-size-20"></i>10 Jan 2021</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="card card-block card-stretch card-height card-bottom-border-warning beast-detail">
                                                            <div class="card-header d-flex justify-content-between pb-1">
                                                                <div class="icon iq-icon-box-2 icon-border-warning rounded">
                                                                    <svg width="23" height="23" class="svg-icon" id="iq-main-12" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76" />
                                                            </svg>
                                                                </div>
                                                                <div class="card-header-toolbar d-flex align-items-center">
                                                                    <div class="dropdown">
                                                                        <span class="dropdown-toggle dropdown-bg" id="beast-dropdownMenuButton15" data-toggle="dropdown" aria-expanded="false" role="button">
                                                                    <i class="ri-more-fill"></i>
                                                                </span>
                                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="beast-dropdownMenuButton15" style="">
                                                                            <a href="index.html#" class="dropdown-item new-note8" data-toggle="modal" data-target="#new-note8"><i class="las la-eye mr-3"></i>View</a>
                                                                            <a href="index.html#" class="dropdown-item edit-note1" data-toggle="modal" data-target="#edit-note1"><i class="las la-pen mr-3"></i>Edit</a>
                                                                            <a class="dropdown-item" data-extra-toggle="delete" data-closest-elem=".card" href="index.html#"><i class="las la-trash-alt mr-3"></i>Delete</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-body rounded">
                                                                <h4 class="card-title">Address & Email</h4>
                                                                <p class="mb-3 card-description short">Quickly note down the address and email address on NotePlus so that you can access it from anywhere.</p>
                                                            </div>
                                                            <div class="card-footer">
                                                                <div class="d-flex align-items-center justify-content-between beast-text beast-text-warning">
                                                                    <a href="index.html#" class=""><i class="las la-user-friends mr-2 font-size-20"></i>04 share</a>
                                                                    <a href="index.html#" class=""><i class="las la-calendar mr-2 font-size-20"></i>8 Jan 2021</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="card card-block card-stretch card-height card-bottom-border-danger beast-detail">
                                                            <div class="card-header d-flex justify-content-between pb-1">
                                                                <div class="icon iq-icon-box-2 icon-border-danger rounded">
                                                                    <svg width="23" height="23" class="svg-icon" id="iq-main-13" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                                </div>
                                                                <div class="card-header-toolbar d-flex align-items-center">
                                                                    <div class="card-header-toolbar d-flex align-items-center">
                                                                        <div class="dropdown">
                                                                            <span class="dropdown-toggle dropdown-bg" id="beast-dropdownMenuButton16" data-toggle="dropdown" aria-expanded="false" role="button">
                                                                        <i class="ri-more-fill"></i>
                                                                    </span>
                                                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="beast-dropdownMenuButton16" style="">
                                                                                <a href="index.html#" class="dropdown-item new-note4" data-toggle="modal" data-target="#new-note4"><i class="las la-eye mr-3"></i>View</a>
                                                                                <a href="index.html#" class="dropdown-item edit-note1" data-toggle="modal" data-target="#edit-note1"><i class="las la-pen mr-3"></i>Edit</a>
                                                                                <a class="dropdown-item" data-extra-toggle="delete" data-closest-elem=".card" href="index.html#"><i class="las la-trash-alt mr-3"></i>Delete</a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-body rounded">
                                                                <h4 class="card-title">Lecture Notes</h4>
                                                                <div class="checkbox mb-2">
                                                                    <input type="checkbox" class="checkbox-input mr-3" id="checkbox4">
                                                                    <label for="checkbox4" class="beast-checkbox mb-0">Chapter 1 notes.</label>
                                                                </div>
                                                                <div class="checkbox mb-2">
                                                                    <input type="checkbox" class="checkbox-input mr-3" id="checkbox5">
                                                                    <label for="checkbox5" class="beast-checkbox mb-0">Chapter 2 Assignment.</label>
                                                                </div>
                                                                <div class="checkbox mb-2">
                                                                    <input type="checkbox" class="checkbox-input mr-3" id="checkbox6">
                                                                    <label for="checkbox6" class="beast-checkbox mb-0">Chapter 3 practical File.</label>
                                                                </div>
                                                            </div>
                                                            <div class="card-footer">
                                                                <div class="d-flex align-items-center justify-content-between beast-text beast-text-danger">
                                                                    <a href="index.html#" class=""><i class="las la-user-friends mr-2 font-size-20"></i>05 share</a>
                                                                    <a href="index.html#" class=""><i class="las la-calendar mr-2 font-size-20"></i>09 Jan 2021</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="card card-block card-stretch card-height card-bottom-border-purple beast-detail">
                                                            <div class="card-header d-flex justify-content-between pb-1">
                                                                <div class="icon iq-icon-box-2 icon-border-purple rounded">
                                                                    <svg width="23" height="23" class="svg-icon" id="iq-main-14" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z" />
                                                            </svg>
                                                                </div>
                                                                <div class="card-header-toolbar d-flex align-items-center">
                                                                    <div class="card-header-toolbar d-flex align-items-center">
                                                                        <div class="dropdown">
                                                                            <span class="dropdown-toggle dropdown-bg" id="beast-dropdownMenuButton17" data-toggle="dropdown" aria-expanded="false" role="button">
                                                                        <i class="ri-more-fill"></i>
                                                                    </span>
                                                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="beast-dropdownMenuButton17" style="">
                                                                                <a href="index.html#" class="dropdown-item new-note2" data-toggle="modal" data-target="#new-note2"><i class="las la-eye mr-3"></i>View</a>
                                                                                <a href="index.html#" class="dropdown-item edit-note1" data-toggle="modal" data-target="#edit-note1"><i class="las la-pen mr-3"></i>Edit</a>
                                                                                <a class="dropdown-item" data-extra-toggle="delete" data-closest-elem=".card" href="index.html#"><i class="las la-trash-alt mr-3"></i>Delete</a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-body rounded">
                                                                <h4 class="card-title">Birthday Celebration</h4>
                                                                <p class="mb-3 card-description short">You can easily share via message, WhatsApp, emails etc. You can also save your notes and edit it later or can easily delete the note.</p>
                                                                <ul class="pl-3 mb-0">
                                                                    <li class="beast-list">Cakes and Balloons.</li>
                                                                </ul>
                                                            </div>
                                                            <div class="card-footer">
                                                                <div class="d-flex align-items-center justify-content-between beast-text beast-text-purple">
                                                                    <a href="index.html#" class=""><i class="las la-lock mr-2 font-size-20"></i>Only You</a>
                                                                    <a href="index.html#" class=""><i class="las la-calendar mr-2 font-size-20"></i>10 Jan 2021</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="card card-block card-stretch card-height card-bottom-border-info beast-detail">
                                                            <div class="card-header d-flex justify-content-between pb-1">
                                                                <div class="icon iq-icon-box-2 icon-border-info rounded">
                                                                    <svg width="23" height="23" class="svg-icon" id="iq-main-15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M5 3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H5zm0 2h10v7h-2l-1 2H8l-1-2H5V5z" clip-rule="evenodd" />
                                                            </svg>
                                                                </div>
                                                                <div class="card-header-toolbar d-flex align-items-center">
                                                                    <div class="card-header-toolbar d-flex align-items-center">
                                                                        <div class="dropdown">
                                                                            <span class="dropdown-toggle dropdown-bg" id="beast-dropdownMenuButton18" data-toggle="dropdown" aria-expanded="false" role="button">
                                                                        <i class="ri-more-fill"></i>
                                                                    </span>
                                                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="beast-dropdownMenuButton18" style="">
                                                                                <a href="index.html#" class="dropdown-item new-note9" data-toggle="modal" data-target="#new-note9"><i class="las la-eye mr-3"></i>View</a>
                                                                                <a href="index.html#" class="dropdown-item edit-note1" data-toggle="modal" data-target="#edit-note1"><i class="las la-pen mr-3"></i>Edit</a>
                                                                                <a class="dropdown-item" data-extra-toggle="delete" data-closest-elem=".card" href="index.html#"><i class="las la-trash-alt mr-3"></i>Delete</a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-body rounded">
                                                                <h4 class="card-title">NotePlus for Entrepreneurs</h4>
                                                                <p class="mb-3 card-description short">With NotePlus, you can easily share via message, WhatsApp, emails etc. You can also save your notes and edit it later or can easily delete the note.</p>
                                                            </div>
                                                            <div class="card-footer">
                                                                <div class="d-flex align-items-center justify-content-between beast-text beast-text-info">
                                                                    <a href="index.html#" class=""><i class="las la-user-friends mr-2 font-size-20"></i>07 share</a>
                                                                    <a href="index.html#" class=""><i class="las la-calendar mr-2 font-size-20"></i>16 Jan 2021</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="icon active animate__animated animate__fadeIn i-list">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="table-responsive">
                                                            <table class="table  tbl-server-info">
                                                                <thead>
                                                                    <tr class="ligth">
                                                                        <th class="w-50" scope="col">Title</th>
                                                                        <th scope="col">Permission</th>
                                                                        <th scope="col">Created At</th>
                                                                        <th scope="col">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            <h4 class="mb-2">Weekly Planner</h4>
                                                                            <span>Virtual Digital Marketing Course every week on Monday, Wednesday and Saturday</span>
                                                                        </td>
                                                                        <td>
                                                                            <i class="las la-user-friends mr-2 font-size-20"></i> 03 share
                                                                        </td>
                                                                        <td>Dec 20</td>
                                                                        <td>
                                                                            <div>
                                                                                <a href="index.html#" class="badge badge-success mr-3" data-toggle="modal" data-target="#edit-note1"><i class="las la-pen mr-0"></i></a>
                                                                                <a href="index.html#" class="badge badge-danger" data-extra-toggle="delete" data-closest-elem="tr"><i class="las la-trash-alt mr-0"></i></a>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <h4 class="mb-2">Benefits of NotePlus</h4>
                                                                            <span>Take organized notes and share later as meeting minutes or check-list</span>
                                                                        </td>
                                                                        <td>
                                                                            <i class="las la-user-friends mr-2 font-size-20"></i> 2 share
                                                                        </td>
                                                                        <td>Dec 20</td>
                                                                        <td>
                                                                            <div>
                                                                                <a href="index.html#" class="badge badge-success mr-3" data-toggle="modal" data-target="#edit-note1"><i class="las la-pen mr-0"></i></a>
                                                                                <a href="index.html#" class="badge badge-danger" data-extra-toggle="delete" data-closest-elem="tr"><i class="las la-trash-alt mr-0"></i></a>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <h4 class="mb-2">Address & Email</h4>
                                                                            <span>Quickly note down the address and email address on NotePlus</span>
                                                                        </td>
                                                                        <td>
                                                                            <i class="las la-user-friends mr-2 font-size-20"></i> 04 share
                                                                        </td>
                                                                        <td>Dec 20</td>
                                                                        <td>
                                                                            <div>
                                                                                <a href="index.html#" class="badge badge-success mr-3" data-toggle="modal" data-target="#edit-note1"><i class="las la-pen mr-0"></i></a>
                                                                                <a href="index.html#" class="badge badge-danger" data-extra-toggle="delete" data-closest-elem="tr"><i class="las la-trash-alt mr-0"></i></a>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <h4 class="mb-2">Lecture Notes</h4>
                                                                            <span>Chapter 1 notes, Chapter 2 Assignment, Chapter 3 practical File.</span>
                                                                        </td>
                                                                        <td>
                                                                            <i class="las la-user-friends mr-2 font-size-20"></i> 05 share
                                                                        </td>
                                                                        <td>Dec 20</td>
                                                                        <td>
                                                                            <div>
                                                                                <a href="index.html#" class="badge badge-success mr-3" data-toggle="modal" data-target="#edit-note1"><i class="las la-pen mr-0"></i></a>
                                                                                <a href="index.html#" class="badge badge-danger" data-extra-toggle="delete" data-closest-elem="tr"><i class="las la-trash-alt mr-0"></i></a>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <h4 class="mb-2">Birthday Celebration</h4>
                                                                            <span>You can easily share via message, WhatsApp, emails etc.</span>
                                                                        </td>
                                                                        <td>
                                                                            <i class="las la-lock mr-2 font-size-20"></i> Only You
                                                                        </td>
                                                                        <td>Dec 20</td>
                                                                        <td>
                                                                            <div>
                                                                                <a href="index.html#" class="badge badge-success mr-3" data-toggle="modal" data-target="#edit-note1"><i class="las la-pen mr-0"></i></a>
                                                                                <a href="index.html#" class="badge badge-danger" data-extra-toggle="delete" data-closest-elem="tr"><i class="las la-trash-alt mr-0"></i></a>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <h4 class="mb-2">NotePlus for Entrepreneurs</h4>
                                                                            <span>Quickly note down the address and email address on NotePlus</span>
                                                                        </td>
                                                                        <td>
                                                                            <i class="las la-user-friends mr-2 font-size-20"></i> 07 share
                                                                        </td>
                                                                        <td>Dec 20</td>
                                                                        <td>
                                                                            <div>
                                                                                <a href="index.html#" class="badge badge-success mr-3" data-toggle="modal" data-target="#edit-note1"><i class="las la-pen mr-0"></i></a>
                                                                                <a href="index.html#" class="badge badge-danger" data-extra-toggle="delete" data-closest-elem="tr"><i class="las la-trash-alt mr-0"></i></a>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
 

       
    </div>





Hello
Select Guild

</div>
@endsection('content')