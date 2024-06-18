<div>
    @foreach ($headings as $index => $heading)
        <a href="{{ $step >= $index ? $heading['url'] : '#' }}" title="" class="step">{{ $index .'. '. $heading['label'] }}</a>
        {{ $step == $index ? $slot : '' }}
    @endforeach
</div>