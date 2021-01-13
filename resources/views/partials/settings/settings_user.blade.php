
<div class="{{ Request::is('account/settings') ? 'col-xxl-3 col-lg-12' : 'col-12 col-lg-8 offset-lg-2' }} order-2 order-md-1 mx-auto">
    <!-- basic user settings -->
    <div class="p-30 text-center">
        <a class="avatar avatar-xxl" href="javascript:void(0)">
            <img
                 src="{{ auth()->user()->getDiscordHelper()->getAvatar() }}"
                 alt="...">
        </a>
        <h5>{{ auth()->user()->getDiscordHelper()->getUsername() }}</h5>
        <!--<button type="button" class="btn btn-block btn-dark" id="dark-day" onClick="changeNightMode();">Dark/Day</button>-->
        <button type="button" class="btn btn-block btn-dark" onclick="window.location.href = '/logout';">Log out</button>
    </div>

    <div class="p-30 text-center">
        <div class="row">

            <div class="col-6">
                @if(auth()->user()->canAcceptPayments())
                    @if(auth()->user()->usingPayPal() || ! auth()->user()->usingStripe())
                        <a href="/account/set-payment-processor?id=1" class="btn btn-primary">Use Stripe</a>
                    @elseif(auth()->user()->usingStripe())
                        <a href="#" class="btn btn-primary">Using Stripe</a>
                    @endif
                @else
                    <a href="{{ StripeHelper::getConnectURL() }}" class="btn btn-primary">Connect Stripe</a>
                @endif
            </div>

            <div class="col-6">
                @if(auth()->user()->getPayPalAccount() !== null)
                    @if(auth()->user()->usingStripe() || ! auth()->user()->usingPayPal())
                        <a href="/account/set-payment-processor?id=2" class="btn btn-primary">Use PayPal</a>
                    @elseif(auth()->user()->usingPayPal())
                        <a href="#" class="btn btn-primary">Using PayPal</a>
                    @endif
                @else
                    <a href="{{ PayPalHelper::getConnectUrl() }}" class="btn btn-primary">Connect PayPal</a>
                @endif
            </div>
        </div>

    </div>
</div>



