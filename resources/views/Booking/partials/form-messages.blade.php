@if (session()->has('messages'))
    <div class="alert bg-green-400 text-white alert-dismissible">
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        <i class="fa fa-check-circle mr-1"></i> {{ session('messages') }}
    </div>
@endif
