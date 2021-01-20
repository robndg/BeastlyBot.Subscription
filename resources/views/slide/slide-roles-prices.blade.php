<header class="slidePanel-header dual bg-green-600">
    <div class="slidePanel-actions" aria-label="actions" role="group">
        <button id='back-btn' type="button" class="btn btn-icon btn-pure btn-inverse actions-top icon wb-chevron-left"
                aria-hidden="true"
                data-url="/slide-roles-settings/{{ $guild_id }}/{{ $role_id }}"
                data-toggle="slidePanel"></button>
    </div>
    <h1>Subscription Prices</h1>
    <p id="role_name"></p>
</header>
<div class="site-sidebar-tab-content put-long" id="slider-div">
    <div class="tab-pane fade active show">
        <div class="row">
            <div class="col-md-12">
                
                        <div
                            class="list-group-item d-flex flex-row flex-wrap align-items-center justify-content-between">
                            <h5>1 day</h5>

                            <div>
                                <div class="d-flex flex-row flex-wrap align-items-center justify-content-between">
                                    <div class="d-block pl-15">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input id="price_1day" type="text" class="form-control w-100"
                                                   placeholder="0.00"
                                                   value="day" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div
                            class="list-group-item d-flex flex-row flex-wrap align-items-center justify-content-between">
                            <h5>1 week</h5>

                            <div>
                                <div class="d-flex flex-row flex-wrap align-items-center justify-content-between">
                                    <div class="d-block pl-15">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input id="price_1week" type="text" class="form-control w-100"
                                                   placeholder="0.00"
                                                   value="week" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div
                            class="list-group-item d-flex flex-row flex-wrap align-items-center justify-content-between">
                            <h5>1 month</h5>

                            <div>
                                <div class="d-flex flex-row flex-wrap align-items-center justify-content-between">
                                    <div class="d-block pl-15">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input id="price_1month" type="text" class="form-control w-100"
                                                   placeholder="0.00"
                                                   value="month" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div
                            class="list-group-item d-flex flex-row flex-wrap align-items-center justify-content-between">
                            <h5>1 year</h5>

                            <div>
                                <div class="d-flex flex-row flex-wrap align-items-center justify-content-between">
                                    <div class="d-block pl-15">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input id="price_1year" type="text" class="form-control w-100"
                                                   placeholder="0.00"
                                                   value="year" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
             
               
            </div>

        </div>

        <button class="btn put-bottom btn-lg btn-success" onclick="updatePrices()">Save Prices</button>
    </div>
</div>

<script type="text/javascript">
    var guild_id = '{{ $guild_id }}';
    var role_id = '{{ $role_id }}';
    var Global = {};

    // TODO: For now we close the slide but we need to turn off the switcheries
    function updatePrices() {
        Toast.fire({
            title: 'Processing....',
            text: '',
            showCancelButton: false,
            showConfirmButton: false,
            allowOutsideClick: () => !Toast.isLoading(),
            //target: document.getElementById('slider-div')
        });
        Toast.showLoading();
        $.ajax({
            url: '/update_discord_prices',
            type: 'POST',
            data: {
                'price_1_day': $('#price_1day').val(),
                'price_1_week': $('#price_1week').val(),
                'price_1_month': $('#price_1month').val(),
                'price_1_year': $('#price_1year').val(),
                'role_id': role_id,
                'role_name': Global.role_name,
                'guild_id': guild_id,
                _token: '{{ csrf_token() }}'
            },
        }).done(function (msg) {
            if (msg['success']) {
                Toast.fire({
                    title: 'Success!',
                    text: msg['msg'],
                    type: 'success',
                    showCancelButton: false,
                    //target: document.getElementById('slider-div')
                });
            } else {
                Swal.fire({
                    title: 'Oops!',
                    text: msg['msg'],
                    type: 'warning',
                    showCancelButton: false,
                    target: document.getElementById('slider-div')
                });
            }
        });
    }
</script>

@include('partials/clear_script')