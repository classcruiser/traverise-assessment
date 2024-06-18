@if (session('message'))
    <div class="rounded py-2 px-3 bg-blue-100 border border-blue-200 text-blue-600 text-sm">
        <i class="fa fa-check fa-fw"></i> {{ session('message') }}
    </div>
@endif
