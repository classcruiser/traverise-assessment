<div class="p-3">
  <div class="d-flex justify-content-between align-items-center">
    <div class="select-year d-flex justify-content-start align-items-center">
      <div class="mr-3">SELECT YEAR</div>
      <div>
        <select class="form-control form-control-sm select-no-search pricing_calendar_year" name="apply_to" data-container-css-class="select-sm">
          @for ($i = (intVal(date('Y')) - 2); $i <= (intVal(date('Y')) + 8); $i++)
            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
          @endfor
        </select>
      </div>
    </div>

    <div class="select-period d-flex justify-content-end align-items-center">
      @can ('save setting')
        <div style="width: 220px">
          <div class="input-group">
            <span class="input-group-prepend">
              <span class="input-group-text"><i class="icon-calendar22"></i></span>
            </span>
            <input type="text" class="form-control daterange-empty" placeholder="select dates" id="cal-dates" value="{{ '01' .'.'. date('m') .'.'. $year .' - 10' .'.'. date('m') .'.'. $year }}"> 
          </div>
        </div>
        <button class="btn btn-labeled btn-labeled-left bg-slate ml-1 btn-sm cal-action" data-action="update" data-room-id="{{ $room->id }}">
          <b><i class="icon-pencil5"></i></b> Update
        </button>
        <button class="btn btn-labeled btn-labeled-left bg-danger ml-1 btn-sm cal-action" data-action="full" data-room-id="{{ $room->id }}">
          <b><i class="icon-cross"></i></b> Full
        </button>
        <button class="btn btn-labeled btn-labeled-left bg-secondary ml-1 btn-sm cal-action" data-action="block" data-room-id="{{ $room->id }}">
          <b><i class="icon-blocked"></i></b> Block
        </button>
        <button class="btn btn-labeled btn-labeled-left bg-success ml-1 btn-sm cal-action" data-action="restore" data-room-id="{{ $room->id }}">
          <b><i class="icon-reload-alt"></i></b> Restore
        </button>
      @endcan
    </div>
  </div>

  <div class="mt-3">
    <table class="pricing-calendar">
      <thead>
        <tr>
          <th style="width: 80px"></th>
          <th class="header tippy" title="" data-tippy-content="Monday">M</th>
          <th class="header tippy" title="" data-tippy-content="Tuesday">T</th>
          <th class="header tippy" title="" data-tippy-content="Wednesday">W</th>
          <th class="header tippy" title="" data-tippy-content="Thursday">T</th>
          <th class="header tippy" title="" data-tippy-content="Friday">F</th>
          <th class="header tippy sat" title="" data-tippy-content="Saturday">S</th>
          <th class="header tippy sun" title="" data-tippy-content="Sunday">S</th>
          <th class="header tippy" title="" data-tippy-content="Monday">M</th>
          <th class="header tippy" title="" data-tippy-content="Tuesday">T</th>
          <th class="header tippy" title="" data-tippy-content="Wednesday">W</th>
          <th class="header tippy" title="" data-tippy-content="Thursday">T</th>
          <th class="header tippy" title="" data-tippy-content="Friday">F</th>
          <th class="header tippy sat" title="" data-tippy-content="Saturday">S</th>
          <th class="header tippy sun" title="" data-tippy-content="Sunday">S</th>
          <th class="header tippy" title="" data-tippy-content="Monday">M</th>
          <th class="header tippy" title="" data-tippy-content="Tuesday">T</th>
          <th class="header tippy" title="" data-tippy-content="Wednesday">W</th>
          <th class="header tippy" title="" data-tippy-content="Thursday">T</th>
          <th class="header tippy" title="" data-tippy-content="Friday">F</th>
          <th class="header tippy sat" title="" data-tippy-content="Saturday">S</th>
          <th class="header tippy sun" title="" data-tippy-content="Sunday">S</th>
          <th class="header tippy" title="" data-tippy-content="Monday">M</th>
          <th class="header tippy" title="" data-tippy-content="Tuesday">T</th>
          <th class="header tippy" title="" data-tippy-content="Wednesday">W</th>
          <th class="header tippy" title="" data-tippy-content="Thursday">T</th>
          <th class="header tippy" title="" data-tippy-content="Friday">F</th>
          <th class="header tippy sat" title="" data-tippy-content="Saturday">S</th>
          <th class="header tippy sun" title="" data-tippy-content="Sunday">S</th>
          <th class="header tippy" title="" data-tippy-content="Monday">M</th>
          <th class="header tippy" title="" data-tippy-content="Tuesday">T</th>
          <th class="header tippy" title="" data-tippy-content="Wednesday">W</th>
          <th class="header tippy" title="" data-tippy-content="Thursday">T</th>
          <th class="header tippy" title="" data-tippy-content="Friday">F</th>
          <th class="header tippy sat" title="" data-tippy-content="Saturday">S</th>
          <th class="header tippy sun" title="" data-tippy-content="Sunday">S</th>
          <th class="header tippy" title="" data-tippy-content="Monday">M</th>
          <th class="header tippy" title="" data-tippy-content="Tuesday">T</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($cal as $c)
          <tr>
            <td>{{ $c['month'] }}</td>
            @if ($c['offset'] > 0)
              @for ($i = 0; $i < $c['offset']; $i++)
                <td></td>
              @endfor
            @endif
            @foreach ($c['dates'] as $date)
              <td class="{{ $date['sun'] ? 'sun ' : '' }}{{ $date['sat'] ? 'sat' : '' }}">
                <div class="tippy {{ $date['css_class'] }}" title="TEST" data-tippy-content="{{ $date['season'] != 'BLOCK' && $date['season'] != 'FULL' ? '&euro;'. $date['price'] .' / guest' : $date['season'] }}">
                  <span>{{ $date['date'] }}</span>
                </div>
              </td>
            @endforeach
            @if ($c['onset'] > 0)
              @for ($i = 0; $i < $c['onset']; $i++)
                <td></td>
              @endfor
            @endif
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-2 py-2 d-flex align-items-center justify-content-end">
    @foreach ($season_legends as $legend)
      <div class="ml-3 mr-1 legend {{ $legend->first()->season_type }}">{{ $legend->first()->season_type }}</div>
      @if ($legend->first()->season_type != 'BLOCK' && $legend->first()->season_type != 'FULL')
        <div class="legend-text">&euro;{{ $legend->first()->price }} / Guest</div>
      @endif
    @endforeach
  </div>
</div>