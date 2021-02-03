<div class="iq-sidebar  sidebar-default ">
            <div class="iq-sidebar-logo d-flex align-items-center justify-content-between">
                <a href="../backend/index.html" class="header-logo">
                    <img src="https://beastlybot.com/android-chrome-512x512.png" class="img-fluid rounded-normal light-logo" alt="logo">
                    <h4 class="logo-title ml-3">BeastlyBot</h4>
                </a>
                <div class="iq-menu-bt-sidebar">
                    <i class="las la-times wrapper-menu"></i>
                </div>
            </div>
            <div class="sidebar-caption dropdown">
                <a href="#" class="iq-user-toggle d-flex align-items-center justify-content-between" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src="{{ auth()->user()->getDiscordHelper()->getAvatar() }}" class="img-fluid rounded avatar-50 mr-3" alt="user">
                    <div class="caption">
                        <h6 class="mb-0 line-height">{{ auth()->user()->getDiscordHelper()->getUsername() }}</h6>
                    </div>
                    <i class="las la-angle-down"></i>
                </a>
                <div class="dropdown-menu w-100 border-0 my-2" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item mb-2" href="../app/user-profile.html">
                        <i class="lar la-user-circle font-size-20 mr-1"></i>
                        <span class="mt-2">My Profile</span>
                    </a>
                    <a class="dropdown-item mb-2" href="/app/user-profile-edit">
                        <i class="las la-user-edit font-size-20 mr-1"></i>
                        <span>Edit Profile</span>
                    </a>
                    <a class="dropdown-item mb-2" href="/app/user-account-setting">
                        <i class="las la-user-cog font-size-20 mr-1"></i>
                        <span>Account Settings</span>
                    </a>
                    <a class="dropdown-item mb-3" href="/app/user-privacy-setting">
                        <i class="las la-user-shield font-size-20 mr-1"></i>
                        <span>Privacy Settings</span>
                    </a>
                    <hr class="my-2">
                    <a class="dropdown-item" href="/app/auth-sign-in">
                        <i class="las la-sign-out-alt font-size-20 mr-1"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
            <div class="data-scrollbar" data-scroll="1">
                <div class="sidebar-btn dropdown mb-3">
                    <a href="#" id="dropdownMenuButton01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-primary pr-5 position-relative iq-user-toggle d-flex align-items-center justify-content-between" style="height: 40px;">
                        <span class="btn-title"><i class="ri-add-line mr-3"></i>Add Bot</span><span class="beast-add-btn" style="height: 40px;"><i class="las la-angle-down"></i></span>
                    </a>
                    <div class="dropdown-menu w-100 border-0 py-3" aria-labelledby="dropdownMenuButton01">
                    @php
                        $guilds = $discord_helper->getOwnedGuilds();
                    @endphp
                    @if($guilds)
                        @foreach($guilds as $guild) 
                        <a class="dropdown-item mb-2" href="?guild={{ $guild['id'] }}">
                            <span>@if($guild['icon'] == NULL)
                                <img src="https://i.imgur.com/qbVxZbJ.png" class="mr-1" style="width:25px;" alt="...">
                                @else
                                <img src="https://cdn.discordapp.com/icons/{{ $guild['id'] }}/{{ $guild['icon'] }}.png?size=256" class="mr-1" style="width:25px;" alt="...">
                                @endif
                                {{ $guild['name'] }}</span>
                        </a>
                        @endforeach
                        <a class="dropdown-item" href="{{ 'https://discordapp.com/oauth2/authorize?client_id=' . env('DISCORD_CLIENT_ID') . '&scope=bot&permissions=' . env('DISCORD_BOT_PERMISSIONS') }}" target="_blank">
                            <span><i class="ri-add-line mr-3"></i>Add Bot</span>
                        </a>
                    @endif
                        
                    </div>
                </div>
                <nav class="iq-sidebar-menu">
                    <ul id="iq-sidebar-toggle" class="iq-menu">
                        <li class="active">
                            <a href="index.html" class="svg-icon">
                                <i>
                                    <svg class="svg-icon" id="iq-ui-1-5" width="20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" style="stroke-dasharray: 90, 110; stroke-dashoffset: 0;"></path>
                                    </svg>
                                  </i>
                                <span>Your Plans</span>
                            </a>
                            <ul id="index" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                            </ul>
                        </li>
                        <li class="">
                            <a href="#notebooks" class="collapsed svg-icon" data-toggle="collapse" aria-expanded="false">
                                <i>
                                    <svg class="svg-icon" id="iq-ui-1" width="20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" style="stroke-dasharray: 97, 117; stroke-dashoffset: 0;"></path>
                                    </svg>
                              </i>
                                <span>Store Front</span>
                                <i class="las la-angle-right iq-arrow-right arrow-active"></i>
                                <i class="las la-angle-down iq-arrow-right arrow-hover"></i>
                            </a>
                            <ul id="notebooks" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                <li class="">
                                    <a href="/dashboard/store/settings" class="svg-icon">
                                    <i class="">
                                        <svg class="svg-icon" id="iq-auth-1-2" width="20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" style="stroke-dasharray: 74, 94; stroke-dashoffset: 0;"></path>
                                        </svg>
                                    </i>
                                        <span>Settings</span>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="/dashboard/store/branding" class="svg-icon">
                                        <i class="">
                                            <svg class="svg-icon" id="iq-ui-1-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" style="stroke-dasharray: 56, 76; stroke-dashoffset: 0;"></path>
                                            </svg>
                                        </i>
                                        <span>Branding</span>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="/dashboard/store" class="svg-icon">
                                        <i class="">
                                            <svg class="svg-icon feather feather-columns" id="iq-ui-1-15" xmlns="http://www.w3.org/2000/svg" width="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7m0-18H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7m0-18v18" style="stroke-dasharray: 87, 107; stroke-dashoffset: 0;"></path>
                                            </svg>
                                        </i>
                                        <span>View Store</span>
                                        <i class="las la-play iq-arrow-right arrow-active"></i>
                                        <i class="las la-play iq-arrow-right arrow-hover"></i>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="">
                            <a href="/dashboard/members" class="svg-icon">
                                <i>
                                    <svg class="svg-icon" id="iq-user-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" style="stroke-dasharray: 62, 82; stroke-dashoffset: 0;"></path>
                                    </svg>
                              </i>
                                <span>Members</span>
                            </a>
                        </li>
                        <li class="" data-extra-toggle="right-sidebar">
                            <a href="javascript:void(0);" class="svg-icon">
                                <i>
                                    <svg width="23" height="23" class="svg-icon" id="iq-main-07" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" style="stroke-dasharray: 54, 74; stroke-dashoffset: 0;"></path>
                                    </svg>                          
                              </i>
                                <span>Affiliates</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="page-bin.html" class="svg-icon">
                                <i>
                                    <svg width="23" height="23" class="svg-icon" id="iq-main-03" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" style="stroke-dasharray: 74, 94; stroke-dashoffset: 0;"></path>
                                    </svg>
                              </i>
                                <span>Settings</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="index.html#otherpage" class="collapsed svg-icon" data-toggle="collapse" aria-expanded="false">
                                <i>
                                  <svg width="20" class="svg-icon" id="iq-main-9" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                  </svg>
                              </i>
                                <span>Support</span>
                                <i class="las la-angle-right iq-arrow-right arrow-active"></i>
                                <i class="las la-angle-down iq-arrow-right arrow-hover"></i>
                            </a>
                        </li>
                        
                    </ul>
                </nav>
                <div id="sidebar-bottom" class="position-relative sidebar-bottom">
                    <div class="card rounded shadow-none">
                        <div class="card-body">
                            <div class="sidebarbottom-content">
                               <!-- <div class="image"><img src="{{ asset('dash/assets/images/layouts/side-bkg.png') }}" class="img-fluid" alt="side-bkg"></div>-->
                               <div class="d-flex justify-content-center">
                                    <div class="xx banner-image">
                                        <div class="xx-head"></div>
                                        <div class="xx-body"></div>
                                        <div class="xx-hand"></div>
                                    </div>
                                </div>
                                <p class="mb-0">Set Business Account To Explore Premiun Features</p>
                                <button type="button" class="btn bg-primary mt-3">Upgrade</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-3"></div>
            </div>
        </div>