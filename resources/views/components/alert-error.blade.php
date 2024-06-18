@if ($errors->count())
    <div class="rounded py-2 px-3 bg-red-100 border border-red-200 text-red-600 text-sm">
        <b><i class="fa fa-exclamation-triangle fa-fw"></i> Error found:</b>
        @foreach ($errors->all() as $message)
            <p>{{ $message }}</p>
        @endforeach
    </div>
@endif
