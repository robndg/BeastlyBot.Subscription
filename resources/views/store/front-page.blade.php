@extends('layouts.store')

{{-- @section('title', 'Guild Product') --}}
@section('metadata')
   {{-- <title>{{ $guild->name }}: {{ $product_role->title }}</title>
    <meta content="Subscribe to {{ $role->name }}{{' Role. '}}{{ $product_role->description ?? '' }}" name="description">
    <meta content="{{ $guild->name }}: {{ $product_role->title }}" property="og:title">
    <meta content="Subscribe to {{ $role->name }}{{' Role. '}}{{ $product_role->description ?? '' }}" property="og:description">
    <meta content="{{ $guild->name }}: {{ $product_role->title }}" property="twitter:title">
    <meta content="Subscribe to {{ $role->name }}{{' Role. '}}{{ $product_role->description ?? '' }}" property="twitter:description">
    <meta name="keywords" content="{{ $guild->name }}, {{ $role->name }}, Discord, Shop, BeastlyBot"> <!-- server name -->
    <meta name="author" content="BeastlyBot.com">--}}
@endsection

@section('content')

<div class="section integration-page">
            <div class="container-large-1040px">

               <div data-w-id="3e7a78ef-77f6-6e11-e17e-da3aa1a04313" style="-webkit-transform:translate3d(0, 20px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 20px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 20px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 20px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);" class="integration-tabs-menu w-tab-menu">
                        <button type="button" class="integration-tab-link w-inline-block w-tab-link tab-block store-description-block-tab" onclick="showBlock('store-description-block')" data-target-block="store-description-block">
                           <div>About Store</div>
                        </button>
                        <button type="button" class="integration-tab-link w-inline-block w-tab-link tab-block store-products-block-tab w--current" onclick="showBlock('store-products-block')" data-target-block="store-products-block">
                           <div>Products</div>
                        </button>
                        @if($store_settings->referrals_enabled != 0)
                        <button type="button" class="integration-tab-link w-inline-block w-tab-link tab-block store-referalls-block-tab" onclick="showBlock('store-referalls-block')" data-target-block="store-referalls-block">
                           <div>Referrals</div>
                        </button>
                        @else
                           {{--@if($owner)
                           <button type="button" class="integration-tab-link w-inline-block w-tab-link tab-block store-referalls-block-tab" onclick="showBlock('store-referalls-block')" data-target-block="store-referalls-block">
                              <div>Referrals</div>
                           </button>
                           @endif--}}
                           <!-- TODO: suggest referrals program to owner -->
                        @endif
                     </div> 
               <div class="integration-page-tabs-wrapper store-block" id="store-description-block" style="display:none;">
                  <div data-duration-in="300" data-duration-out="100" class="integration-tabs w-tabs">
                    
                     <div  data-w-id="3e7a78ef-77f6-6e11-e17e-da3aa1a0431d" style="-webkit-transform:translate3d(0, 0, 0) scale3d(0.85, 0.85, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 0, 0) scale3d(0.85, 0.85, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 0, 0) scale3d(0.85, 0.85, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 0, 0) scale3d(0.85, 0.85, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);" class="card integration-tabs-content w-tab-content">
                        <div data-w-tab="Tab 1" class="integration-tab-pane w-tab-pane w--tab-active">
                           <div class="rich-text w-richtext">
                              <h2>About {{ $guild->name }}</h2>
                              <p><strong>{{ $store_settings->store_name }}</strong> has {{$product_roles->count() }} Products to subscribe to roles instantly.</p><!-- TODO: either DB from store or use stats from guild helper / subscriber count-->
                              <p>{{ $store_settings->description }} @if($owner && $store_settings->description == NULL) <a href="/dashboard"><span class="badge badge-primary">Add an intro</span></a> @endif</p>
                              <p>{!! $store_settings->about !!} @if($owner && $store_settings->about == NULL) <a href="/dashboard"><span class="badge badge-primary">Add an about section</span></a> @endif</p>
                              <h3>How does BeastlyBot work?</h3>
                              <p><a href="#">The beastly subscription bot</a>, read our <a href="#">subscriber FAQ's</a>. Guild owners can easily and instantly <strong>give you access</strong> to perks and roles securely on this store.</p>
                           </div>
                        </div>
                     </div>
                  </div>
                  
                 
               </div>

         <div class="related-integrations-section store-block" id="store-products-block">
            <div class="container-default w-container">

               <div data-w-id="0e53f2b7-9467-6285-9db7-c8917faec362" style="margin-bottom: 1rem; margin-top: 1rem; transform: translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg, 0deg); opacity: 1; transform-style: preserve-3d;" class="teams-v1-tabs-menu w-tab-menu" role="tablist">
                    <button type="button" onclick="showProductList('1')" class="teams-v1-tab-link w-inline-block w-tab-link show_product_list" id="show_product_list_1" aria-controls="w-tabs-0-data-w-pane-0" aria-selected="false">
                        <div>Access</div>
                    </button>
                    <button type="button" onclick="showProductList('2')" class="teams-v1-tab-link w-inline-block w-tab-link show_product_list w--current" id="show_product_list_2" role="tab" aria-controls="w-tabs-0-data-w-pane-1" aria-selected="true">
                        <div>Members</div>
                    </button>
                    <button type="button" onclick="showProductList('3')" class="teams-v1-tab-link w-inline-block w-tab-link show_product_list" role="tab" id="show_product_list_3"  aria-controls="w-tabs-0-data-w-pane-2" aria-selected="false">
                        <div>Special</div>
                    </button>
                </div>

               <div class="w-dyn-list product_roles_list" data-product-role-id="1" id="product_roles_list_1" style="display:none">
               @if($product_roles->where('access', 1)->count() > 0)
                
                  <div role="list" class="more-openings-grid w-dyn-items">
                  @foreach($product_roles->where('access', 1) as $product)
                     <div data-w-id="8116e32d-45f9-b5ec-fa48-53777bb1af4d" style="transform: translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg, 0deg); opacity: 1; transform-style: preserve-3d;" role="listitem" class="w-dyn-item">
                        <a href="/shop/{{ $store_settings->url_slug }}/{{ $product->url_slug }}" aria-current="page" class="card job-opening-v1 w-inline-block w--current" style="transform: translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg, 0deg); transform-style: preserve-3d;">
                           <div class="top-content job-opening-v1">
                              <div class="split-content job-opening-v1-left">
                                 <div class="image-wrapper icon-style-guide">
                                    @if($subscriptions && $subscriptions->where('product_id', $product->id)->count() > 0)
                                    <img src="{{ ($subscriptions->where('product_id', $product->id)->first()->status < 3) ? asset('store/assets/svg/icon-check-blue.svg') : asset('store/assets/svg/icon-lock-white.svg') }}" loading="lazy" alt="" class="image icon-style-guide"> 
                                     @else
                                     @if($product->max_sales >= 0 && $product->max_sales != NULL) <img src="{{ asset('store/assets/svg/icon-timer-blue.svg') }}" loading="lazy" alt="" class="image icon-style-guide"> @else <img src="{{ asset('store/assets/svg/icon-check-white.svg') }}" loading="lazy" alt="" class="image icon-style-guide">@endif  
                                    @endif
                                 </div>
                                 <div class="job-opening-v1-about-divider"></div>
                                 <div>@if($subscriptions && $subscriptions->where('product_id', $product->id)->count() > 0) {{ ($subscriptions->where('product_id', $product->id)->first()->status < 3) ? 'Subscribed' : 'Unsubscribed' }} @else
                                 @if($product->max_sales >= 0){{ $product->max_sales }} @endif Available @endif </div>
                              </div>
                              <div class="badge badge-success">Guild Access</div>
                           </div>
                           <h3 class="title job-opening-v1">{{ $product->title }}</h3>
                           <p class="paragraph job-opening-v1">{{ $product->description }}</p>
                           <div class="button-primary job-opening-v1">Get Role</div>
                        </a>
                     </div>
                     @endforeach
                  </div>
               
               @elseif($product_roles->count() > 0)
               <div role="list" class="more-openings-grid w-dyn-items">
                  <div data-w-id="8116e32d-45f9-b5ec-fa48-53777bb1af4d" style="transform: translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg, 0deg); opacity: 1; transform-style: preserve-3d;" role="listitem" class="w-dyn-item">
                        <div aria-current="page" class="card job-opening-v1 w-inline-block w--current" style="transform: translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg, 0deg); transform-style: preserve-3d;">
                           <div class="top-content job-opening-v1">
                           <div class="image-wrapper icon-style-guide"><img src="{{ ($subscriptions) ? asset('store/assets/svg/icon-check-white.svg') : asset('store/assets/svg/icon-check-blue.svg') }}" loading="lazy" alt="" class="image icon-style-guide"></div>
                              <div class="badge badge-success">Guild Access</div>
                           </div>
                        
                           <h3 class="title job-opening-v1">Included</h3>
                           <p class="paragraph job-opening-v1">All Products include Guild Access</p>
                           <div class="button-primary job-opening-v1" onclick="showProductList('2')">View Products</div>
                           </div>
                     </div>
                  </div>
               </div>
               @endif
               </div>

               @if($product_roles->where('access', 2)->count() > 0)
               <div class="w-dyn-list product_roles_list" data-product-role-id="2" id="product_roles_list_2">
                  <div role="list" class="more-openings-grid w-dyn-items">
                  @foreach($product_roles->where('access', 2) as $product)
                     <div data-w-id="8116e32d-45f9-b5ec-fa48-53777bb1af4d" style="transform: translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg, 0deg); opacity: 1; transform-style: preserve-3d;" role="listitem" class="w-dyn-item">
                        <a href="/shop/{{ $store_settings->url_slug }}/{{ $product->url_slug }}" aria-current="page" class="card job-opening-v1 w-inline-block w--current" style="transform: translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg, 0deg); transform-style: preserve-3d;">
                           <div class="top-content job-opening-v1">
                              <div class="split-content job-opening-v1-left">
                                 <div class="image-wrapper icon-style-guide">
                                    @if($subscriptions && $subscriptions->where('product_id', $product->id)->count() > 0)
                                    <img src="{{ ($subscriptions->where('product_id', $product->id)->first()->status < 3) ? asset('store/assets/svg/icon-check-white.svg') : asset('store/assets/svg/icon-lock-white.svg') }}" loading="lazy" alt="" class="image icon-style-guide"> 
                                     @else
                                    @if($product->max_sales >= 0 && $product->max_sales != NULL) <img src="{{ asset('store/assets/svg/icon-timer-blue.svg') }}" loading="lazy" alt="" class="image icon-style-guide"> @else <img src="{{ asset('store/assets/svg/icon-check-white.svg') }}" loading="lazy" alt="" class="image icon-style-guide">@endif  
                                    @endif
                                 </div>
                                 <div class="job-opening-v1-about-divider"></div>
                                 <div>@if($subscriptions && $subscriptions->where('product_id', $product->id)->count() > 0) {{ ($subscriptions->where('product_id', $product->id)->first()->status < 3) ? 'Subscribed' : 'Unsubscribed' }} @else
                                 @if($product->max_sales >= 0){{ $product->max_sales }} @endif Available @endif </div>
                              </div>
                              <div class="badge badge-primary">Member Access {{-- $discord_helper->getRole($discord_store->guild_id, $product->role_id, 1, true) --}}</div>
                           </div>
                           <h3 class="title job-opening-v1">{{ $product->title }}</h3>
                           <p class="paragraph job-opening-v1">{{ $product->description }}</p>
                           <div class="button-primary job-opening-v1">Get Role</div>
                        </a>
                     </div>
                     @endforeach
                  </div>
               </div>
               @endif

               @if($product_roles->where('access', 3)->count() > 0)
               <div class="w-dyn-list product_roles_list" data-product-role-id="3" id="product_roles_list_3" style="display:none">
                  <div role="list" class="more-openings-grid w-dyn-items">
                  @foreach($product_roles->where('access', 3) as $product)
                     <div data-w-id="8116e32d-45f9-b5ec-fa48-53777bb1af4d" style="transform: translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg, 0deg); opacity: 1; transform-style: preserve-3d;" role="listitem" class="w-dyn-item">
                        <a href="/shop/{{ $store_settings->url_slug }}/{{ $product->url_slug }}" aria-current="page" class="card job-opening-v1 w-inline-block w--current" style="transform: translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg, 0deg); transform-style: preserve-3d;">
                           <div class="top-content job-opening-v1">
                              <div class="split-content job-opening-v1-left">
                                 <div class="image-wrapper icon-style-guide">
                                    @if($subscriptions && $subscriptions->where('product_id', $product->id)->count() > 0)
                                    <img src="{{ ($subscriptions->where('product_id', $product->id)->first()->status < 3) ? asset('store/assets/svg/icon-check-white.svg') : asset('store/assets/svg/icon-lock-white.svg') }}" loading="lazy" alt="" class="image icon-style-guide"> 
                                     @else
                                     @if($product->max_sales >= 0 && $product->max_sales != NULL) <img src="{{ asset('store/assets/svg/icon-timer-blue.svg') }}" loading="lazy" alt="" class="image icon-style-guide"> @else <img src="{{ asset('store/assets/svg/icon-plus-blue.svg') }}" loading="lazy" alt="" class="image icon-style-guide">@endif  
                                    @endif
                                 </div>
                                 <div class="job-opening-v1-about-divider"></div>
                                 <div>@if($subscriptions && $subscriptions->where('product_id', $product->id)->count() > 0) {{ ($subscriptions->where('product_id', $product->id)->first()->status < 3) ? 'Subscribed' : 'Unsubscribed' }} @else
                                 @if($product->max_sales >= 0){{ $product->max_sales }} @endif Available @endif </div>
                              </div>
                              <div class="badge badge-secondary" style="background-color: #9992fc;">Special Access</div>
                           </div>
                           <h3 class="title job-opening-v1">{{ $product->title }}</h3>
                           <p class="paragraph job-opening-v1">{{ $product->description }}</p>
                           <div class="button-primary job-opening-v1">Get Role</div>
                        </a>
                     </div>
                     @endforeach
                  </div>
               </div>
               @endif

               {{--<div class="w-dyn-list">
                  <div role="list" class="related-integrations-grid w-dyn-items">
                     <div data-w-id="0797240b-8ac6-95cf-5154-660ebdef07fc" style="-webkit-transform:translate3d(0, 0, 0) scale3d(0.85, 0.85, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 0, 0) scale3d(0.85, 0.85, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 0, 0) scale3d(0.85, 0.85, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 0, 0) scale3d(0.85, 0.85, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);" role="listitem" class="card-integrations-v2-item w-dyn-item">
                        <a href="https://softwaretemplate.webflow.io/integration/twitter" class="card integrations-v2 w-inline-block">
                           <div class="card-integrations-v2-top-content">
                              <div class="image-wrapper card-integration-v2"><img src="https://assets.website-files.com/5fbd60e9c0e04cb42bf0c2e7/601dd88917f404bac2cd421a_twitter-logo-software.svg" alt="" class="image card-integration-v2"/></div>
                              <div class="card-integrations-v2-name-wrapper">
                                 <h3 class="title card-integrations-v2">Twitter</h3>
                                 <div class="badge integration-v2">
                                    <div>Social Network</div>
                                 </div>
                              </div>
                           </div>
                           <div class="card-integrations-v2-content">
                              <p class="paragraph card-integrations-v2">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Id in sed porta orci lacus gravida donec vel.</p>
                              <div class="card-integrations-v2-link">Learn More</div>
                           </div>
                        </a>
                     </div>
                     <div data-w-id="0797240b-8ac6-95cf-5154-660ebdef07fc" style="-webkit-transform:translate3d(0, 0, 0) scale3d(0.85, 0.85, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 0, 0) scale3d(0.85, 0.85, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 0, 0) scale3d(0.85, 0.85, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 0, 0) scale3d(0.85, 0.85, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);" role="listitem" class="card-integrations-v2-item w-dyn-item">
                        <a href="https://softwaretemplate.webflow.io/integration/messenger" class="card integrations-v2 w-inline-block">
                           <div class="card-integrations-v2-top-content">
                              <div class="image-wrapper card-integration-v2">
                                @if($guild)
                                    @if($guild->icon == NULL)
                                        <img src="https://i.imgur.com/qbVxZbJ.png" alt="" class="image card-integration-v2">
                                    @else
                                    <img id="server_icon" src="https://cdn.discordapp.com/icons/{{ $guild->id }}/{{ $guild->icon }}.png?size=256" alt="..." class="image card-integration-v2">
                                    @endif
                                @else
                                    <img src="{{asset('store/assets/img/beastlybot-logo4-w.png') }}" alt="" class="image card-integration-v2">
                                @endif
                                
                              </div>
                              <div class="card-integrations-v2-name-wrapper">
                                 <h3 class="title card-integrations-v2">Messenger</h3>
                                 <div class="badge integration-v2">
                                    <div>Communication</div>
                                 </div>
                              </div>
                           </div>
                           <div class="card-integrations-v2-content">
                              <p class="paragraph card-integrations-v2">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Id in sed porta orci lacus gravida donec vel.</p>
                              <div class="card-integrations-v2-link">Learn More</div>
                           </div>
                        </a>
                     </div>
                     <div data-w-id="0797240b-8ac6-95cf-5154-660ebdef07fc" style="-webkit-transform:translate3d(0, 0, 0) scale3d(0.85, 0.85, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 0, 0) scale3d(0.85, 0.85, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 0, 0) scale3d(0.85, 0.85, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 0, 0) scale3d(0.85, 0.85, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);" role="listitem" class="card-integrations-v2-item w-dyn-item">
                        <a href="https://softwaretemplate.webflow.io/integration/google" class="card integrations-v2 w-inline-block">
                           <div class="card-integrations-v2-top-content">
                              <div class="image-wrapper card-integration-v2"><img src="https://assets.website-files.com/5fbd60e9c0e04cb42bf0c2e7/601dd936787e9d6883deb5f5_google-logo-software.svg" alt="" class="image card-integration-v2"/></div>
                              <div class="card-integrations-v2-name-wrapper">
                                 <h3 class="title card-integrations-v2">Google</h3>
                                 <div class="badge integration-v2">
                                    <div>Communication</div>
                                 </div>
                              </div>
                           </div>
                           <div class="card-integrations-v2-content">
                              <p class="paragraph card-integrations-v2">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Id in sed porta orci lacus gravida donec vel.</p>
                              <div class="card-integrations-v2-link">Learn More</div>
                           </div>
                        </a>
                     </div>
                     <div data-w-id="0797240b-8ac6-95cf-5154-660ebdef07fc" style="-webkit-transform:translate3d(0, 0, 0) scale3d(0.85, 0.85, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 0, 0) scale3d(0.85, 0.85, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 0, 0) scale3d(0.85, 0.85, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 0, 0) scale3d(0.85, 0.85, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);" role="listitem" class="card-integrations-v2-item w-dyn-item">
                        <a href="https://softwaretemplate.webflow.io/integration/google" class="card integrations-v2 w-inline-block">
                           <div class="card-integrations-v2-top-content">
                              <div class="image-wrapper card-integration-v2"><img src="https://assets.website-files.com/5fbd60e9c0e04cb42bf0c2e7/601dd936787e9d6883deb5f5_google-logo-software.svg" alt="" class="image card-integration-v2"/></div>
                              <div class="card-integrations-v2-name-wrapper">
                                 <h3 class="title card-integrations-v2">Google</h3>
                                 <div class="badge integration-v2">
                                    <div>Communication</div>
                                 </div>
                              </div>
                           </div>
                           <div class="card-integrations-v2-content">
                              <p class="paragraph card-integrations-v2">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Id in sed porta orci lacus gravida donec vel.</p>
                              <div class="card-integrations-v2-link">Learn More</div>
                           </div>
                        </a>
                     </div>
                  </div>
               </div>--}}
         
              




         </div>
         @if($store_settings->referrals_enabled != 0)
         <div class="related-integrations-section store-block" id="store-referalls-block" style="display:none;">

               <div class="styleguide-seccion">
                  <div class="container-default">
                     <div class="flex">
                        <div data-w-id="b2fa23d4-8136-d8f4-e5c4-4d7ef74dcc62" style="transform: translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg, 0deg); opacity: 1; transform-style: preserve-3d;" class="styleguide-sidebar">
                           <div class="styleguide-sidebar-title">
                              <div>Referalls Program</div>
                           </div>
                           <nav class="styleguide-navigation">
                              <ul role="list" class="sidebar-navigation">
                                 <li class="styleguide-link-wrapper">
                                    <a href="#About" class="styleguide-link w-inline-block">
                                       <div class="styleguide-icon-link"><img src="{{ asset('store/assets/svg/icon-growth-white.svg') }}" alt="" class="styelguide-sidebar-icon"></div>
                                       <div>About</div>
                                    </a>
                                 </li>
                              
                              </ul>
                           </nav>
                           
                           <div class="styleguide-sidebar-title middle">
                              <div>My Program</div>
                           </div>
                           <nav class="styleguide-navigation">
                              <ul role="list" class="sidebar-navigation">
                              <li class="styleguide-link-wrapper">
                                    <a href="#Links" class="styleguide-link w-inline-block">
                                       <div class="styleguide-icon-link"><img src="{{ asset('store/assets/svg/icon-click-white.svg') }}" alt="" class="styelguide-sidebar-icon"></div>
                                       <div>Links</div>
                                    </a>
                                 </li>
                                 <li class="styleguide-link-wrapper">
                                    <a href="#Returns" class="styleguide-link w-inline-block">
                                       <div class="styleguide-icon-link"><img src="{{ asset('store/assets/svg/icon-plus-blue.svg') }}" alt="" class="styelguide-sidebar-icon"></div>
                                       <div>Returns</div>
                                    </a>
                                 </li>
                              </ul>
                           </nav>
                           <div class="styleguide-button-container"><a href="#" class="button-primary full-width w-button">View Terms</a></div>
                        </div>

                        
                       
                     <div>
                  </div>
               </div>
            </div>

         </div>
         @endif
      </div>
</div>


{{-- 
<div class="section integration-page">
            <div class="container-large-1040px">
               <div class="top-content integration-page">
                  <div class="container-default w-container">
                     <div data-w-id="2c782146-49ba-b762-ba1c-c5a1c8797c1d" style="-webkit-transform:translate3d(0, 20px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 20px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 20px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 20px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);" class="integration-name-main-wrapper">
                        <div class="integration-name-wrapper">
                           <img src="https://assets.website-files.com/5fbd60e9c0e04cb42bf0c2e7/601e09161023dba7af277900_facebook-logo-software%201.svg" alt="" class="image integration-logo"/>
                           <h1 class="title integration-name">Discord Store Title</h1>
                        </div>
                        <a href="https://softwaretemplate.webflow.io/integration-category/communication" class="category-button integration-page w-button">Trading Group</a>
                     </div>
                     <p data-w-id="4143eed4-baa7-abec-98fc-4be4d7e0e691" style="-webkit-transform:translate3d(0, 20px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 20px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 20px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 20px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);" class="paragraph integration-excerpt">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Id in sed porta orci lacus gravida donec vel.</p>
                  </div>

               </div>

              <div class="divider integration-page"></div>



<div class="styleguide-seccion">
   <div class="container-default">
      <div class="flex">
         <div data-w-id="b2fa23d4-8136-d8f4-e5c4-4d7ef74dcc62" style="transform: translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg, 0deg); opacity: 1; transform-style: preserve-3d;" class="styleguide-sidebar">
            <div class="styleguide-sidebar-title">
               <div>Store Title</div>
            </div>
            <nav class="styleguide-navigation">
               <ul role="list" class="sidebar-navigation">
                  <li class="styleguide-link-wrapper">
                     <a href="#Colors" class="styleguide-link w-inline-block">
                        <div class="styleguide-icon-link"><img src="https://assets.website-files.com/5fbd60e9c0e04c6e2ff0c2e0/5fbd60e9c0e04c7af4f0c305_colors-icon.svg" alt="" class="styelguide-sidebar-icon"></div>
                        <div>Program</div>
                     </a>
                  </li>
                 
               </ul>
            </nav>
            <div class="styleguide-sidebar-title middle">
               <div>Payouts</div>
            </div>
            <nav class="styleguide-navigation">
               <ul role="list" class="sidebar-navigation">
                <li class="styleguide-link-wrapper">
                     <a href="#Buttons" class="styleguide-link w-inline-block">
                        <div class="styleguide-icon-link"><img src="https://assets.website-files.com/5fbd60e9c0e04c6e2ff0c2e0/5fce8cadf0087fcd603f94b4_icon-value-4-software-ui-kit.svg" alt="" class="styelguide-sidebar-icon"></div>
                        <div>asd</div>
                     </a>
                  </li>
                  <li class="styleguide-link-wrapper">
                     <a href="#Buttons" class="styleguide-link w-inline-block">
                        <div class="styleguide-icon-link"><img src="https://assets.website-files.com/5fbd60e9c0e04c6e2ff0c2e0/5fc7b408d30eb93baedc18d9_button-icon.svg" alt="" class="styelguide-sidebar-icon"></div>
                        <div>asd</div>
                     </a>
                  </li>
               </ul>
            </nav>
            <div class="styleguide-button-container"><a href="#" class="button-primary full-width w-button">Results</a></div>
         </div>
         <div data-w-id="b2fa23d4-8136-d8f4-e5c4-4d7ef74dcc65" style="transform: translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg, 0deg); opacity: 1; transform-style: preserve-3d;" class="styleguide-content">

            <div id="Colors" class="styleguide-section">
               <div class="styleguide-subheader">
                  <h2>Returns</h2>
               </div>
               <div class="styleguide-content-wrapper">
                  <div class="color-content-wrapper">
                     <div class="mg-bottom-32px">
                        <h3 class="styleguide-subtitle">Guild Access</h3>
                     </div>
                     
                     <div class="w-layout-grid color-primary-grid">
                     @foreach($product_roles->where('access', 1) as $product)
                        <div class="color-container">
                            <!--<div class="color-block bg-secondary-1">
                            <div class="split-content job-opening-v1-left">
                                <div>Remote</div>
                                    <div class="job-opening-v1-about-divider">
                                </div>
                                <div>Full Time</div>
                            </div>
                               
                            </div>-->
                            <div class="badge job-opening-v1 text-center my-auto">Role Name</div>
                            <div class="color-content">
                              <h3 class="title job-opening-v1">{{ $product->title }}</h3>
                              <a href="/shop/{{ $discord_store->url }}/{{ Str::title(str_replace(' ', '-', $product->title))}}" class="btn btn-block button-primary job-opening-v1">Get Role</a>
                            </div>

                        </div>
                        @endforeach
                     </div>
                    
                  </div>
                  <div class="color-content-wrapper">
                     <div class="mg-bottom-32px">
                        <h3 class="styleguide-subtitle">Members</h3>
                     </div>
                     <div class="w-layout-grid color-secondary-grid">
                     @foreach($product_roles->where('access', 2) as $product)
                        <div class="color-container">
                            <!--<div class="color-block bg-secondary-1">
                            <div class="split-content job-opening-v1-left">
                                <div>Remote</div>
                                    <div class="job-opening-v1-about-divider">
                                </div>
                                <div>Full Time</div>
                            </div>
                               
                            </div>-->
                            <div class="badge job-opening-v1 text-center my-auto" style="background-color: #9b59b6;">Role Name</div>
                            <div class="color-content">
                            <h3 class="title job-opening-v1">{{ $product->title }}</h3>
                              <!--<p class="paragraph job-opening-v1">{{ $product->description }}</p>-->
                              <a href="/shop/{{ $discord_store->url }}/{{ Str::title(str_replace(' ', '-', $product->title))}}" class="btn btn-block button-primary job-opening-v1">Get Role</a>
                            </div>

                        </div>
                        @endforeach
                     </div>
                  </div>
                  <div class="color-content-wrapper last">
                     <div class="mg-bottom-32px">
                        <h3 class="styleguide-subtitle">Special Access</h3>
                     </div>
                     <div class="w-layout-grid color-neutral-grid">
                     @foreach($product_roles->where('access', 3) as $product)
                        <div class="color-container">
                            <!--<div class="color-block bg-secondary-1">
                            <div class="split-content job-opening-v1-left">
                                <div>Remote</div>
                                    <div class="job-opening-v1-about-divider">
                                </div>
                                <div>Full Time</div>
                            </div>
                               
                            </div>-->
                            <div class="badge job-opening-v1 text-center my-auto" style="background-color: #9b59b6;">Role Name</div>
                            <div class="color-content">
                              <h3 class="title job-opening-v1">{{ $product->title }}</h3>
                              <a href="/shop/{{ $discord_store->url }}/{{ Str::title(str_replace(' ', '-', $product->title))}}" class="btn btn-block button-primary job-opening-v1">Get Role</a>
                            </div>

                        </div>
                        @endforeach
                     </div>
                  </div>
               </div>
            </div>
            
         </div>
      </div>
   </div>
</div>--}}


        
                
                  

@endsection


@section('shopfooter')
<div class="divider integration-page"></div>


<div class="top-content integration-page">
    <div class="container-default w-container">
       <div data-w-id="2c782146-49ba-b762-ba1c-c5a1c8797c1d" style="-webkit-transform:translate3d(0, 20px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 20px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 20px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 20px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);" class="integration-name-main-wrapper">
          <div class="integration-name-wrapper">
                @if($guild)
                      @if($guild->icon == NULL)
                      <img src="https://i.imgur.com/qbVxZbJ.png" alt="" class="image integration-logo">
                      @else
                      <img id="server_icon" src="https://cdn.discordapp.com/icons/{{ $guild->id }}/{{ $guild->icon }}.png?size=256" alt="..." class="image integration-logo">
                      @endif
                  @else
                      <img src="{{asset('store/assets/img/beastlybot-logo4-w.png') }}" alt="" class="image integration-logo">
                  @endif
             <h1 class="title integration-name">{{ $guild->name }}</h1>
          </div>
          <a href="https://" class="category-button integration-page w-button">Discord Group</a>
       </div>
       <p data-w-id="4143eed4-baa7-abec-98fc-4be4d7e0e691" style="-webkit-transform:translate3d(0, 20px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 20px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 20px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 20px, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);" class="paragraph integration-excerpt">
       {{ $discord_store->desc }}
       </p>
    </div>

 </div>

@endsection

@section('scripts')

<script>

function showBlock(blockid){
    $('.tab-block').removeClass('w--current')
    $('.store-block').hide();
    /*if($(this)).data('target-block').val() == blockid){
        $(this).addClass('w--current');
    }*/
    $('#'+blockid).slideDown();
    $('.' + blockid + '-tab').addClass('w--current');
}

function showProductList($product_access){
   $('.show_product_list').removeClass('w--current');
   $('.product_roles_list').slideUp();
   $('#product_roles_list_' + $product_access).slideDown();
   $('#show_product_list_' + $product_access).addClass('w--current');
}
</script>


@endsection