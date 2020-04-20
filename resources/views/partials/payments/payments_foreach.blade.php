@foreach($invoices as $invoice)
<a class="list-group-item flex-column align-items-start" href="javascript:void(0)"
    data-url="/slide-invoice/{{ $invoice['id'] }}" data-toggle="slidePanel">
    <span class="badge badge-pill text-capitalize @if($invoice['status'] === 'paid')badge-success @else badge-secondary @endif">{{ $invoice['status'] }}</span> 
    <span class="badge badge-pill badge-primary badge-outline mr-2 hidden-sm-down">#{{ $invoice['number'] }}</span>
    <span class="badge badge-first badge-pill badge-primary mr-15"><i class="wb wb-arrow-down"></i></span>
    <div><p class="desc">{{ $invoice['lines']['data'][0]['description'] }}</p></div>
</a> 
@endforeach    