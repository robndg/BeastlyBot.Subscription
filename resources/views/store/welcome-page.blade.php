@extends('layouts.store')

@section('metadata')

@endsection

@section('content')
<div class="utility-page-wrap" style="padding-top:0px;padding-bottom:0px;min-height:600px;">
	<div data-w-id="5e86ada79942c1e4247fd4c700000000000b" style="transform: translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg, 0deg); transform-style: preserve-3d; opacity: 1;" class="utility-page-content-password w-password-page w-form">
		<div class="utility-page-form w-password-page">
			<div class="image-wrapper icon-password">
            @if($discord_store->live == 1 || $owner)
            <img src="{{ $store_settings->store_image }}" alt="" class="image icon-password" style="border-radius: 100%;width: 90px;">
            @else
            <img src="https://assets.website-files.com/5fbd60e9c0e04c6e2ff0c2e0/5fdd0733571ea0cc72edbc98_locked-icon.svg" alt="" class="image icon-password">
            @endif
			</div>
			<h2>{{ $store_settings->store_name }}</h2>
            @if($discord_store->live == 1 || $owner)
			<p class="password">@if($store_settings->members_only) {{'Our store is for members only.'}} @else {{'Welcome please login with Discord to access our store.'}} @endif</p>
            @endif
			{{--<input type="password" autofocus="true" maxlength="256" name="pass" placeholder="Enter your password" class="input password w-password-page w-input">--}}
			
            @if($discord_store->live == 1 || $owner)<a href="/shop/{{ $store_settings->url_slug }}" value="Enter Now" data-wait="Please wait..." class="button-primary full-width w-password-page w-button">Enter Now</a>@endif
			<div class="">
				<div>@if($discord_store->live == 0){{'Store is not open yet, please come back later.'}}@endif</div>
			</div>
			{{--<div style="display:none" class="w-password-page w-embed w-script">
				<input type="hidden" name="path" value="<%WF_FORM_VALUE_PATH%>">
				<input type="hidden" name="page" value="<%WF_FORM_VALUE_PAGE%>">
			</div>
			<div style="display:none" class="w-password-page w-embed w-script">
				<script type="application/javascript">
					(function _handlePasswordPageOnload() {
						  if (/[?&]e=1(&|$)/.test(document.location.search)) {
						    document.querySelector('.w-password-page.w-form-fail').style.display = 'block';
						  }
						})()
				</script>
			</div>--}}
		</div>
	</div>
</div>

@endsection