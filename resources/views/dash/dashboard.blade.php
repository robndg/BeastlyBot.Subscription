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
                              <a class="btn btn-primary add-bot-button" onclick="addBotScript()" href="{{ 'https://discordapp.com/oauth2/authorize?client_id=' . env('DISCORD_CLIENT_ID') . '&scope=bot&permissions=' . env('DISCORD_BOT_PERMISSIONS') }}" target="_blank"><i class="las la-plus pr-2"></i>Add Bot</a>
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
{{--
    @php
        $guilds = $discord_helper->getOwnedGuilds();
    @endphp
    <a href="#" id="dropdownMenuButton01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-primary pr-5 position-relative iq-user-toggle d-flex align-items-center justify-content-between" style="height: 40px;">
        <span class="btn-title text-truncate">@if(isset($guild_id)) {!! '<img src="https://cdn.discordapp.com/icons/' . $guild_id . '/' . $guild->icon . '.png?size=256" class="rounded-circle mr-1" style="width:25px" alt="...">' !!} @else {!! '<i class="ri-add-line mr-3"></i>' !!} @endif {{ $guild->name ?? 'Select Guild' }}</span><span class="beast-add-btn" style="height: 40px;"><i class="las la-angle-down mt-1"></i></span>
    </a>
    <div class="dropdown-menu w-100 border-0 py-3" aria-labelledby="dropdownMenuButton01">
    
    @if($guilds)
        @foreach($guilds as $guild) 
        <a class="dropdown-item mb-2" href="/dashboard/{{ $guild['id'] }}">
            <span>@if($guild['icon'] == NULL)
                <img src="https://i.imgur.com/qbVxZbJ.png" class="rounded-circle mr-1" style="width:25px;" alt="...">
                @else
                <img src="https://cdn.discordapp.com/icons/{{ $guild['id'] }}/{{ $guild['icon'] }}.png?size=256" class="rounded-circle mr-1" style="width:25px;" alt="...">
                @endif
                {{ substr($guild['name'], 0, 18) . '...' }}</span>
        </a>
        @endforeach
    @endif   
--}}

    <div class="row mb-3">
            <div class="col-lg-12">
               <div class="card car-transparent">
                  <div class="card-body p-0">
                     <div class="profile-image position-relative">
                        <div class="img-fluid rounded w-100" style="height:80px"></div>
                     </div>
                     <div class="profile-overly">
                        <h3>Money</h3>
                        <span>Graph here</span>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-lg-12">
                <h3 class="mb-2 mt-1">Guild Shops</h3>
            </div>
            @php
                $guilds = $discord_helper->getOwnedGuilds();
            @endphp

            @if($guilds)
            @foreach($guilds as $guild) 
            <div class="col-lg-3 col-md-6 col-sm-6">

                    <div class="card card-block card-stretch card-height card-bottom-border-info beast-detail blur-shadow">
                        <div class="card-header d-flex justify-content-between pb-1">
                            <div class="icon iq-icon-box-2 icon-border-purple rounded">
                                <div class="icon icon-discord pt-2"></div>
                            </div>
                            <div class="card-header-toolbar d-flex align-items-center">
                                <a href="#" data-toggle="tooltip" data-placement="top" class="show-tab text-white" title="" data-original-title="View Store"><i class="las la-store mr-2"></i></a>
                                <div class="card-header-toolbar d-flex align-items-center">
                                    <div class="dropdown">
                                        <span class="dropdown-toggle dropdown-bg" id="beast-dropdownMenuButton5" data-toggle="dropdown" aria-expanded="false" role="button">
                                    <i class="ri-more-fill"></i>
                                </span>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="beast-dropdownMenuButton5" style="">
                                            <a href="" class="dropdown-item new-note2" data-toggle="modal" data-target="#view-guildshare"><i class="las la-link mr-3"></i>Store Settings</a>
                                            <a href="#" class="dropdown-item edit-note1" data-toggle="modal" data-target="#edit-guildid"><i class="las la-pen mr-3"></i>View Products</a>
                                            <a class="dropdown-item" data-extra-toggle="delete" data-closest-elem=".card" href="#archive-guildid"><i class="las la-trash-alt mr-3"></i>Reconnect</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body rounded pt-1">
                            <div class="text-center">
                                @if($guild['icon'] == NULL)
                                <img src="https://i.imgur.com/qbVxZbJ.png" class="rounded-circle mb-2" style="width:75px;" alt="...">
                                @else
                                <img src="https://cdn.discordapp.com/icons/{{ $guild['id'] }}/{{ $guild['icon'] }}.png?size=256" class="rounded-circle mb-2" style="width:75px;" alt="...">
                                @endif
                                <h5>{{ substr($guild['name'], 0, 30) . '...' }}</h5>
                                
                                <h2 class="mb-2 mt-4 mb-3 las la-store"></h2>
                            </div>
                            
                            <ul class="list-inline mb-0 p-0"> <!-- bg-primary rounded p-0x -->
                                <li>
                                    <div class="d-flex align-items-center justify-content-between mb-3 row"> <!-- list-inline px-3 pt-3 pb-1 mb-2 bg-primary rounded-->
                                        <div class="col-10">
                                        <div class="d-flex align-items-center">
                                            <p class="mb-0 font-size-16 mr-3">Active Products</p>
                                            <div class="iq-progress-bar-linear d-inline-block iq-progress-height mt-1 w-80">
                                                <div class="iq-progress-bar iq-bg-danger">
                                                    <span class="bg-danger" data-percent="75" style="transition: width 2s ease 0s; width: 75%;"></span>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                            <div class="col-2">
                                                <div class="percentage float-right text-purple font-weight-bold">5
                                            </div>
                                        </div>                                    
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex align-items-center justify-content-between mb-3 row">
                                        <div class="col-10">
                                        <div class="d-flex align-items-center">
                                            <p class="mb-0 font-size-16 mr-3">Subscriptions</p>
                                            <div class="iq-progress-bar-linear d-inline-block iq-progress-height mt-1 w-80">
                                                <div class="iq-progress-bar iq-bg-danger">
                                                    <span class="bg-danger" data-percent="75" style="transition: width 2s ease 0s; width: 75%;"></span>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                            <div class="col-2">
                                                <div class="percentage float-right text-danger font-weight-bold">75
                                            </div>
                                        </div>                                    
                                    </div>
                                </li>

                                <li>
                                    <div class="d-flex align-items-center justify-content-between mb-3 row">
                                        <div class="col-10">
                                        <div class="d-flex align-items-center">
                                            <p class="mb-0 font-size-16 mr-3">Store Hits</p>
                                            <div class="iq-progress-bar-linear d-inline-block iq-progress-height mt-1 w-80">
                                                <div class="iq-progress-bar iq-bg-danger">
                                                    <span class="bg-danger" data-percent="75" style="transition: width 2s ease 0s; width: 75%;"></span>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                            <div class="col-2">
                                                <div class="percentage float-right text-success font-weight-bold">500
                                            </div>
                                        </div>                                    
                                    </div>
                                </li>
                            </ul>
                            <a class="button btn button-icon bg-primary btn-outline-info btn-block mb-2" href="/dashboard/{{ $guild['id'] }}">Store Products</a>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex align-items-center justify-content-between beast-text beast-text-purple">
                               <!-- <a class="button btn button-icon bg-primary" target="_blank" href="#">View Store</a>-->
                                <!--<a class="button btn button-icon bg-primary btn-block" target="_blank" href="#">Store Products</a>-->
                                <a href="#" class="text-light"><i class="las la-user mr-2 font-size-20"></i>Members</a>
                                <a href="#" class="text-success"><i class="las la-calendar mr-2 font-size-20"></i>Live</a>
                            </div>
                        </div>
                    </div>
                                                   
            </div>
            @endforeach
            @endif  

         
           
         </div>                          



</div>
@endsection('content')