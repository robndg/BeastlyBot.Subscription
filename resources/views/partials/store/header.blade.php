<div data-collapse="medium" data-animation="over-right" data-duration="1000" data-easing="ease-out-expo" data-easing2="ease-out-expo" data-w-id="58db7844-5919-d71b-dd74-2323ed8dffe9" role="banner" class="header w-nav">
            <div class="container-header">
                <div data-w-id="b08d9188-5f88-7cc3-b97d-62a680e404f7" class="split-content header-left card-blog-post-author-top-content" style="margin-bottom:0px; opacity: 1;">
                    <a href="/" class="brand w-nav-brand w-inline-block" aria-label="home">
                        <div class="styleguide-icon-link color-container">
                           
                           <img src="{{ asset('android-chrome-192x192.png') }}" alt="BeastlyBot" class="styelguide-sidebar-icon">
                           
                       </div>
                    </a>
                    @if(isset($guild))
                    <a href="{{ $store_settings ? '/shop/' . $store_settings->url_slug : '/' }}" class="brand w-nav-brand styleguide-link w-inline-block" aria-label="home">
                        
                        @if($guild)
                        <div class="">
                            @if($guild->icon == NULL)
                                <img src="https://i.imgur.com/qbVxZbJ.png" alt="" class="image-wrapper blog-post-author">
                            @else
                            <img id="server_icon" src="https://cdn.discordapp.com/icons/{{ $guild->id }}/{{ $guild->icon }}.png?size=256" alt="..." class="image-wrapper blog-post-author">
                            @endif
                        </div>
                        <div><h3 style="margin-bottom:0px">
                            {{ $guild->name }}
                            </h3>
                        </div>
                        
                        @else
                            <img src="{{asset('store/assets/img/beastlybot-logo4-w.png') }}" alt="" class="header-logo" style="max-width: 40%;">
                        @endif

                    </a>
                    @endif


                </div>
                @if(isset($guild))
                <div data-w-id="7215a8a0-bb24-315b-4a87-24f3dad59dfc" class="split-content header-right" style="opacity: 1;">
                    <nav role="navigation" class="nav-menu w-nav-menu">
                        <ul role="list" class="header-navigation">
                            <li class="nav-item-wrapper"><a href="{{ $store_settings ? '/guild/' . $store_settings->url_slug : '/' }}" class="nav-link">Home</a></li>
                            <li class="nav-item-wrapper">
                                <div data-hover="" data-delay="0" data-w-id="2b2d6c1c-2e02-85e3-50e3-053cd961e58a" class="nav-link-dropdown w-dropdown">
                                    <div class="nav-link dropdown w-dropdown-toggle" id="w-dropdown-toggle-0" aria-controls="w-dropdown-list-0" aria-haspopup="menu" aria-expanded="false" role="button" tabindex="0">
                                        <div class="dropdown-text">Roles{{--&nbsp;<span class="dropdown-arrow" style="transform: translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg, 0deg); transform-style: preserve-3d;"></span>--}}</div>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item-wrapper"><a href="#" class="nav-link">Contact</a></li>
                            <li class="nav-item-wrapper login-mobile"><a href="#" class="button-secondary login-header-mobile w-button">Login</a></li>
                            <li class="nav-item-wrapper sign-up-mobile"><a href="#" class="button-primary sign-up-header-mobile w-button">Sign Up</a></li>
                        </ul>
                    </nav>
                    @if(Auth::check())
                    <div class="_2-buttons header-buttons-wrapper">
                        <a href="utility-pages/sign-up" class="button-primary sign-up-header w-button">{{ auth()->user()->getDiscordHelper()->getUsername() }}</a>
                       {{-- @if(isset($owner)) --}}
                        @if($owner)
                        <div class="space _2-buttons header-buttons"></div>
                        <a href="/dashboard/{{$guild->id}}/settings" class="button-secondary login-header w-button">Store Settings</a>
                        @endif 
                       {{-- @endif --}}
                    </div>
                    @endif
                    <div class="menu-button w-nav-button" style="-webkit-user-select: text;" aria-label="menu"
                        role="button" tabindex="0" aria-controls="w-nav-overlay-0" aria-haspopup="menu" aria-expanded="false">
                        <div class="menu-button-wrapper">
                            <div class="menu-button-icon">
                                <div class="menu-line-top"></div>
                                <div class="menu-line-middle"></div>
                                <div class="menu-line-bottom"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="w-nav-overlay" data-wf-ignore="" id="w-nav-overlay-0"></div>
        </div>
</div>
</div>