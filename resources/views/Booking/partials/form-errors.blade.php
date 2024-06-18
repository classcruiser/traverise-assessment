@if ($errors->any())
<div class="alert bg-danger-400 text-white alert-dismissible">
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
