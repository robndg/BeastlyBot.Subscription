<header class="slidePanel-header">
    <div class="slidePanel-actions" aria-label="actions" role="group">
        <button type="button" class="btn btn-icon btn-pure btn-inverse slidePanel-close actions-top icon wb-close"
                aria-hidden="true"></button>
    </div>
    <h1>{{ auth()->user()->getDiscordHelper()->getUsername() }}</h1>
</header>

<!-- nav-tabs -->
<ul class="site-sidebar-nav nav nav-tabs nav-tabs-line" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#sidebar-user" role="tab">
            <i class="icon wb-more-vertical" aria-hidden="true"></i>
            <h5>Roles</h5>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#sidebar-payments" role="tab">
            <i class="icon wb-order" aria-hidden="true"></i>
            <h5>Payments</h5>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#sidebar-details" role="tab">
            <i class="icon wb-user-circle" aria-hidden="true"></i>
            <h5>Info</h5>
        </a>
    </li>
</ul>

<div class="site-sidebar-tab-content put-short tab-content">
    <div class="tab-pane fade active show" id="sidebar-user">
        <div>

            <table id="rolesTable" class="table table-hover" data-plugin="animateList" data-animate="fade"
                   data-child="tr">
                {{--<thead>
                    <tr>
                        <th scope="col">Role</th>
                        <th scope="col">End Period</th>
                        <th scope="col">Delete</th>
                        <th scope="col" class="text-left">Remove</th>
                    </tr>
                </thead>--}}
                <tbody>
                {{--
                @foreach($user->getSubscriptions($guild_id) as $sub)
                    <tr>
                        <td class="cell-300">
                            <h3><span class="badge badge-primary text-left"
                                      style="margin-top: -0.5em;"
                                      id="role_{{ $sub->role_id }}"></span></h3>
                        </td>
                        <td class="cell-150 text-right">
                            <div class="time">{{ gmdate("m-d-Y", $sub->current_period_end) }}</div>
                        </td>
                    </tr>
                @endforeach --}}
                <!-- this is the new stuff I made -->
                @if($subscriptions)
                    @foreach($subscriptions as $sub)

                        @if($sub->plan_special_bool)
                        <tr data-url="/slide-special-roles-settings/{{ $guild_id }}/{{ $sub->role_id }}/{{ $sub->plan_special }}/{{ $useruser()->DiscordOAuth->discord_id }}"
                        data-toggle="slidePanel">
                        @else
                        <tr data-url="/slide-roles-settings/{{ $guild_id }}/{{ $sub->role_id }}"
                        data-toggle="slidePanel">
                        @endif
                        {{-- <td><button type="button" class="btn btn-danger btn-outline"><i class="wb wb-close"></i></button></td>--}}
                            <td colspan="4" >
                                <h3><span class="badge badge-primary text-left"
                                        style="margin-top: -0.5em;"
                                        id="role_">{{ $sub->items->data[0]['plan']->nickname }}</span></h3>
                            </td>
                            <td colspan="2" class="text-right">
                                <div class="time">${{ ($sub->items->data[0]['plan']->amount)/100 }}</div>
                            </td>
                            <td colspan="2" class="text-right">
                                <div class="time">{{ gmdate("m-d-Y", $sub->current_period_end) }}</div>
                            </td>
                            <td class="text-left"><button type="button" class="btn btn-dark"><i class="wb wb-minus"></i> Remove</button></td>
                        </tr>
                    @endforeach
                @endif
                @if($other_plans)
                    @foreach($other_plans as $plan)

                        @if($plan->plan_special_bool)
                        <tr data-url="/slide-special-roles-settings/{{ $guild_id }}/{{ $plan->role_id }}/{{ $plan->plan_special }}/{{ $useruser()->DiscordOAuth->discord_id }}"
                        data-toggle="slidePanel">
                        @else
                        <tr data-url="/slide-roles-settings/{{ $guild_id }}/{{ $plan->role_id }}"
                        data-toggle="slidePanel">
                        @endif
                        {{-- <td><button type="button" class="btn btn-danger btn-outline"><i class="wb wb-close"></i></button></td>--}}
                            <td colspan="4" >
                                <h3><span class="badge badge-primary text-left"
                                        style="margin-top: -0.5em;"
                                        id="role_">{{ $plan->items->data[0]['plan']->nickname }}</span></h3>
                            </td>
                            <td colspan="2" class="text-right">
                                <div class="time">${{ ($plan->items->data[0]['plan']->amount)/100 }}</div>
                            </td>
                            <td colspan="2" class="text-right">
                                <div class="time">{{ gmdate("m-d-Y", $plan->current_period_end) }}</div>
                            </td>
                            <td class="text-left"><button type="button" class="btn btn-dark"><i class="wb wb-minus"></i> Remove</button></td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
            <!-- pagination -->

            <button class="btn put-bottom btn-primary" data-url="/slide-server-member-role-add/{{ $guild_id }}/{{ $useruser()->DiscordOAuth->discord_id }}"
                    data-toggle="slidePanel">Add Role
            </button>

        </div>
    </div>
    <div class="tab-pane fade" id="sidebar-payments">
        <div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th class="cell-200">Invoice</th>
                        <th class="cell-200 responsive-hide">Date</th>
                        <th class="cell-100">Amount</th>
                        {{--                        <th>Role</th>--}}
                        <th class="cell-50">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($invoices as $invoice)
                        <tr data-url="/slide-invoice/{{ $invoice->id }}" data-toggle="slidePanel">
                            <td><a href="javascript:void(0)">#{{ $invoice->number }}</a></td>
                            <td class="responsive-hide">
                                <span class="text-muted"><i class="wb wb-time"></i> {{ gmdate("m-d-Y", $invoice->created) }}</span>
                            </td>
                            <td>${{ number_format($invoice->amount_paid/100, 2, '.', ',') }}</td>
                            <!--<td id="invoice_role_{{ explode('_', $invoice->lines->data[0]->plan->product)[0] }}"></td>-->
                            <td>
                                <div
                                    class="badge badge-table @if($invoice->paid)badge-success @else badge-danger @endif">{{ $invoice->paid ? "Paid" : "Unpaid" }}</div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="sidebar-details">
        <div>
            <div>
                <div class="list-group-item d-flex flex-row flex-wrap align-items-center justify-content-between">
                    <h5>Email</h5>
                    <div>
                        <input type="text" class="form-control" value="{{ $user->getStripeHelper()->getStripeEmail() }}" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function () {
        var guild_id = '{{ $guild_id }}';
        var socket_id = '{{ uniqid() }}';
        @foreach($user->getSubscriptions($guild_id) as $sub)
        socket.emit('get_role_data', [socket_id, guild_id, '{{ $sub->role_id }}']);
        @endforeach

        socket.on('res_role_data_' + socket_id, function (message) {
            var id = message['id'];
            var name = message['name'];
            var color = message['color'];

            $('#role_' + id).text(name);
            $('#role_' + id).css('background-color', color);
        });
    });
</script>

@include('partials/clear_script')
