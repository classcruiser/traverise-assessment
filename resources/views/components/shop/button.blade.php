@if ($type == 'anchor')
    <a
        href="{{ $href }}"
        title=""
        class="{{ $classes }}"
        {!! $id != '' ? 'id="'. $id .'"' : '' !!}
    >
        {!! $slot !!}
    </a>
@elseif ($type == 'button')
    <button
        {!! isset($btn['type']) && $btn['type'] != '' ? 'type="'. $btn['type'] .'"' : '' !!}
        {!! isset($btn['name']) && $btn['name'] != '' ? 'name="'. $btn['name'] .'"' : '' !!}
        {!! $id != '' ? 'id="'. $id .'"' : '' !!}
        class="{{ $classes }}"
    >
        {!! $slot !!}
    </button>
@endif