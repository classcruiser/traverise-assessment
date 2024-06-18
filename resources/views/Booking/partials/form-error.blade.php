@if (session()->has('error'))
    <div class="alert bg-danger-400 text-white alert-dismissible">
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        <i class="fa fa-exclamation-triangle mr-1"></i> {{ session('error') }}
    </div>
@endif
