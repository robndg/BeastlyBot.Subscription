    
  <header class="slidePanel-header bg-primary-600">
    <div class="slidePanel-actions" aria-label="actions" role="group">
        <button type="button" class="btn btn-icon btn-pure btn-inverse slidePanel-close actions-top icon wb-close"
        aria-hidden="true"></button>
    </div>
    <h1>Send Ticket</h1>
</header>
<div class="site-sidebar-tab-content tab-content">
    <div class="tab-pane fade active show" id="sidebar-help">
        @if (session('status'))
           <div class="alert alert-success">
               {{ session('status') }}
           </div>
        @endif
        <div>
        <form role="form" method="POST" action="{{ route('newTicketPost') }}">
        {!! csrf_field() !!}
            <div class="form-group pb-0 mb-5">
              <div class="input-group input-group-lg">
                <div class="input-group-prepend">
                  <span class="input-group-text">?</span>
                </div>
                <input type="text" class="form-control" id="ticket-title" name="title" placeholder="Message title...">
              </div>
            </div>

            <div class="form-group pb-0 mb-5">
              <div class="input-group input-group-lg">
                <div class="input-group-prepend">
                  <span class="input-group-text">?</span>
                </div>
                <select id="category" type="category" class="form-control" name="category">
                    <option value="">Select Category</option>
                    @foreach ($categories as $category)
                        @if(isset($type))
                        <option value="{{ $category->id }}" @if($category->id == $type)selected @endif>{{ $category->name }}</option>
                        @else
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endif
                    @endforeach
                </select>
              </div>
            </div>

            <div class="form-group pb-0 mb-5">

                <textarea rows="6" id="message" class="form-control" name="message"></textarea>

            </div>

            <button type="submit" class="btn btn-block btn-dark">Send</button>

            
         </form>
         
      </div>

    </div>

</div>

<script>

    if((!window.location.href.includes('guide-opened')) && window.location.href.includes('server')) {
        setTimeout(function(){
           // set variables
        },100)
    };

</script>


@include('partials/clear_script')