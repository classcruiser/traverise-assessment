<div class="section-title bg-custom-primary text-uppercase px-3 rounded-1 font-size-lg">1 - Location</div>
@if ($step == 1)
  <div class="section-body {{ $step == 1 ? 'active' : '' }}">
    <form action="/book-now" method="post">
      <select name="location_id" class="form-control" id="location-select">
        @foreach ($locations as $location)
          <option value="{{ $location->id }}">{{ $location->name }}</option>
        @endforeach
      </select>

      @foreach ($locations as $location)
        <div class="mt-3 position-relative cover-image cover-{{ $location->id }} {{ $loop->index == 0 ? 'cover-active' : '' }}">
          <div class="position-absolute bottom-0 left-0 w-100 cover-title p-3">
            <span>{{ $location->name }}</span>
          </div>
          <img class="img-fluid rounded" src="{{ asset('images/camps/'. tenant('id') .'_camp_'. $location->id .'.jpg') }}" alt="{{ $location->name }}" />
        </div>
        <div class="p-3 normal-text">
          {!! $location->description !!}
        </div>
      @endforeach

      @csrf
      <div class="d-flex justify-content-end mt-3">
        <button class="btn btn-custom text-uppercase font-size-lg btn-lg">CONTINUE</button>
      </div>
    </form>
  </div>
@endif
