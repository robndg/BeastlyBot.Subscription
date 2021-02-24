@extends('layouts.store')

{{-- @section('title', 'Guild Product') --}}
@section('metadata')
    <title>{{ $guild->name }}: {{ $product_role->title }}</title>
    <meta content="Subscribe to {{ $role->name }}{{' Role. '}}{{ $product_role->description ?? '' }}" name="description">
    <meta content="{{ $guild->name }}: {{ $product_role->title }}" property="og:title">
    <meta content="Subscribe to {{ $role->name }}{{' Role. '}}{{ $product_role->description ?? '' }}" property="og:description">
    <meta content="{{ $guild->name }}: {{ $product_role->title }}" property="twitter:title">
    <meta content="Subscribe to {{ $role->name }}{{' Role. '}}{{ $product_role->description ?? '' }}" property="twitter:description">
    <meta name="keywords" content="{{ $guild->name }}, {{ $role->name }}, Discord, Shop, BeastlyBot"> <!-- server name -->
    <meta name="author" content="BeastlyBot.com">
@endsection

@section('content')

<div class="section plan-page">
            <div class="container-default w-container">
                <div class="plan-page-wrapper">
                    <div data-w-id="25f4aa3e-26f7-33b6-7fcc-1a6d9052ad8e" style="transform: translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg, 0deg); opacity: 1; transform-style: preserve-3d;" class="split-content plan-left">
                        <div class="plan-page-title-wrapper">
                            <h1 class="title plan-page">{{ $product_role->title }}</h1>
                            <h1 class="title plan-page last">Role</h1>
                        </div>
                        <p class="paragraph plan-page-excerpt">{{ $product_role->description }}</p>
                        <div class="w-layout-grid plan-page-features-grid">
                            @if($product_role->access == 1 || $product_role->access > 1) {{-- TODO if guild access (1) has max subscribers then cannot subscribe --}}
                            <div class="plan-feature-wrapper"><img src="{{ asset('store/assets/img/5fcfddefc8a7a0aaec7ed4af_icon-check-software-ui-kit.svg') }}" alt="" class="image icon-plan-feature">
                                <div>Instant Invite to {{ $guild->name }}</div>
                            </div>
                            @endif
                            <div class="plan-feature-wrapper"><img src="{{ asset('store/assets/img/5fcfddefc8a7a0aaec7ed4af_icon-check-software-ui-kit.svg') }}" alt="" class="image icon-plan-feature">
                                <div>Have <span class="badge badge-primary badge-lg font-size-18 text-white" style="background-color: #{{ dechex($role->color) }}"><i class="icon-discord mr-2"></i> <span>{{ $role->name }}</span></span> badge in chat</div> <!-- badge or role? -->
                            </div>
                            @if($product_role->max_sales != NULL)
                            <div class="plan-feature-wrapper"><img src="{{ asset('store/assets/img/5fcfddefc8a7a0aaec7ed4af_icon-check-software-ui-kit.svg') }}" alt="" class="image icon-plan-feature">
                                <div>Up to {{ $product_role->max_sales }} Subscribers Only</div>
                            </div>
                            @endif
                            {{-- Store stats if over certain members display --}}
                            {{--<div class="plan-feature-wrapper"><img src="{{ asset('store/assets/img/5fcfddefc8a7a0aaec7ed4af_icon-check-software-ui-kit.svg') }}" alt="" class="image icon-plan-feature">
                                <div>Over 100 Members Subscribed</div>
                            </div>--}}
                            <div class="plan-feature-wrapper"><img src="{{ asset('store/assets/img/5fcfddefc8a7a0aaec7ed4af_icon-check-software-ui-kit.svg') }}" alt="" class="image icon-plan-feature">
                                <div>Verified Checkout with Stripe</div>
                            </div>
                        </div>
                        <div class="rich-text w-richtext">
                            <h2>What's {{ $guild->name }} guild about?</h2>
                            <p>{{ $guild->description }}</p>
                            <h3>Is this the right plan for me?</h3>
                            <p><a href="#">View <strong>{{ $guild->name }}</strong> entire store</a> too see what's best for you. Unsubscribe anytime by leaving the guild or in the <a href="#">members area</a>.</p>
                        </div>
                    </div>
                    <div data-w-id="a66d09ec-e220-d00d-2701-d2cbf043e090" style="transform: translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg, 0deg); opacity: 1; transform-style: preserve-3d;" class="split-content plan-right">
                        <div class="card add-to-cart-plan">
                            <div class="title card-add-to-cart-plan">Subscribe @if(Carbon\Carbon::createFromTimestamp(strtotime($product_role->start_date)) > Carbon\Carbon::now()){{'in'}} <span id="datecounter"></span> @else{{"Today"}}@endif</div>
                            <p class="paragraph card-add-to-cart-plan">Select your plan duration below. We have plans for 1 week, 1 month and 1 year.</p>
                            <div class="card-add-to-cart-plan-wrapper">
                                <div data-node-type="commerce-add-to-cart-form" data-commerce-sku-id="5fcfd70f3cb027385cf3b4f6" data-loading-text="Adding to cart..." data-commerce-product-id="5fcfd70dc7ca592fda36334d" class="w-commerce-commerceaddtocartform card-add-to-cart-plan-default-state"
                                    data-wf-atc-loading="">
                                    <div role="group" data-wf-sku-bindings="%5B%7B%22from%22%3A%22f_sku_values_3dr%22%2C%22to%22%3A%22opionValues%22%7D%5D" data-commerce-product-sku-values="%7B%22f72071673c70423b378004278be06033%22%3A%22aa4295942540ff24f674bbd0d11c17c6%22%7D"
                                        data-node-type="commerce-add-to-cart-option-list" data-commerce-product-id="5fcfd70dc7ca592fda36334d" data-preselect-default-variant="false">
                                        <div role="group">
                                            <div class="select-wrapper card-add-to-cart-plan">
                                                <select id="select-interval" data-change="select-interval" data-target="data-price-format" data-node-type="commerce-add-to-cart-option-select" data-commerce-option-set-id="f72071673c70423b378004278be06033" class="select card-add-to-cart-plan w-select"
                                                    required="">
                                                    <option selected="" value="">Select Plan Duration</option>
                                                    @foreach($product_prices as $price) <!-- TODO Rob: going to move prices to ajax popup with edit fields, checking relationship -->
                                                        <option value="{{ $price->interval }}" id="target-select-price-{{$price->interval}}" data-target="select-interval" data-price-id="{{ $price->id }}" data-price="{{ $price->price }}" data-price-format="{{ number_format(($price->price/100),2) }}">1 {{ $price->interval }}</option>
                                                    @endforeach
                                                    </select>
                                                </div>
                                        </div>
                                    </div>
                                    <div data-wf-sku-bindings="%5B%7B%22from%22%3A%22f_price_%22%2C%22to%22%3A%22innerHTML%22%7D%5D" class="card-add-to-cart-plan-price"><span id="selected-price-show">$ 169.00 USD</span></div>
                                    <button type="button" onclick="beginCheckout()" data-node-type="commerce-add-to-cart-button" data-loading-text="Adding role..." value="Subscribe" class="w-commerce-commerceaddtocartbutton button-primary full-width white"></button>
                                    <button type="button" data-node-type="commerce-buy-now-button" data-default-text="Buy now" data-subscription-text="Subscribe now" class="w-commerce-commercebuynowbutton button-secondary buy-now w-dyn-hide">Buy now</button>
                                </div>
                                <div style="display:none" class="w-commerce-commerceaddtocartoutofstock empty-state small plan">
                                    <div>This product is out of stock.</div>
                                </div>
                                <div data-node-type="commerce-add-to-cart-error" style="display:none" class="w-commerce-commerceaddtocarterror">
                                    <div data-node-type="commerce-add-to-cart-error" data-w-add-to-cart-quantity-error="Product is not available in this quantity." data-w-add-to-cart-general-error="Something went wrong when adding this item to the cart."
                                        data-w-add-to-cart-mixed-cart-error="You can’t purchase another product with a subscription." data-w-add-to-cart-buy-now-error="Something went wrong when trying to purchase this item." data-w-add-to-cart-checkout-disabled-error="Checkout is disabled on this site."
                                        data-w-add-to-cart-select-all-options-error="Please select an option in each set.">Product is not available in this quantity.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

@endsection

@section('scripts')
@if(Carbon\Carbon::createFromTimestamp(strtotime($product_role->start_date)) > Carbon\Carbon::now())
<script>
// Set the date we're counting down to
var countDownDate = new Date("{{ $product_role->start_date }}").getTime(); 
//new Date($('#start_date').val() + "T" + $('#start_time').val())

// Update the count down every 1 second
var x = setInterval(function() {

  // Get today's date and time
  var now = new Date().getTime();

  // Find the distance between now and the count down date
  var distance = countDownDate - now;

  // Time calculations for days, hours, minutes and seconds
  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  var seconds = Math.floor((distance % (1000 * 60)) / 1000);

  // Display the result in the element with id="demo"
  var days_str = days + "d"
  if(days == 0){
    days_str = ""
  }
  document.getElementById("datecounter").innerHTML = days_str + hours + "h "
  + minutes + "m " + seconds + "s ";

  // If the count down is finished, write some text
  if (distance < 0) {
    clearInterval(x);
    console.log("Subscribe Today");
    //document.getElementById("demo").innerHTML = "EXPIRED";
  }
}, 1000);
</script>
@endif

<script>

var selectedPriceId = null; // use this on subscribe to get price id->role_id->owner in StoreCustomerController.php

$(document).on('change', '[data-change="select-interval"]', function (e) {
    const interval = $(this).val()
    console.log(interval)
    if(interval != "") {
        const price_id = $('#target-select-price-' + interval).attr('data-price-id');
        console.log(price_id)
        selectedPriceId = price_id;
        const price_str = $('#target-select-price-' + interval).attr('data-price-format');
        console.log(price_str)
        console.log($(this))
        $('#selected-price-show').html('$ ' + price_str + ' USD');
        // set var for selected
    }else{
        $('#selected-price-show').html('');
    }
});


function beginCheckout() {
        Swal.fire({
            title: 'Processing...',
            text: '',
            showCancelButton: false,
            showConfirmButton: false,
            allowOutsideClick: () => !Swal.isLoading(),
            target: document.getElementById('slider-div')
        });
        Swal.showLoading();
        var process_url = '/bknd00/setup-order';
     
        $.ajax({
            url: process_url,
            type: 'POST',
            data: {
           
                'price-id': selectedPriceId,

                _token: '{{ csrf_token() }}'
            },
        }).done(function (msg) {
            if (msg['success']) {
                swal.close();
                console.log(msg['msg']);
                
                var stripeconnected = Stripe("{{ env('STRIPE_CLIENT_PUBLIC_TEST') }}", {
                    stripeAccount: "{{ $processor->processor_id }}"
                });
                var stripeid = msg['msg'];
                stripeconnected.redirectToCheckout({
                    sessionId: stripeid
                }).then(function (result) {
                // If `redirectToCheckout` fails due to a browser or network
                // error, display the localized error message to your customer
                       // console.log(result.error.message)
                });
                /*stripe.redirectToCheckout({
                    sessionId: msg['msg']
                    
                }).then(function (result) {
                    // If `redirectToCheckout` fails due to a browser or network
                    // error, display the localized error message to your customer
                    // using `result.error.message`.
                    alert(result.error.message);
                });*/
            } else {
                Swal.fire({
                    title: 'Failure',
                    text: msg['msg'],
                    showCancelButton: false,
                    showConfirmButton: true,
                    target: document.getElementById('slider-div')
                });
            }
        });
    }


</script>
@endsection