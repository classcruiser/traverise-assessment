@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
  <div class="d-flex">
    <div class="breadcrumb">
      <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
      <a href="{{ route('tenant.schedule') }}" class="breadcrumb-item active">Transfers</a>
    </div>

    <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
  </div>
</div>

<div class="page-content">
  <div class="content-wrapper">
    <div class="content">

      <div class="row arriving-guest-container">
        <div class="col-12" id="arriving-guest-app">
          <div class="mb-2 py-1 px-2 d-flex justify-content-end align-items-center">
            <h4 class="m-0 mr-auto"><i class="fal fa-fw fa-sign-in-alt mr-1"></i> Pickup Schedule</h4>
            <div style="width: 200px">
              <select
                v-model="camp"
                class="form-control form-control-sm"
                data-container-css-class="select-sm"
                id="arrive-camp"
              >
                <option value="all">All locations</option>
                @foreach ($locations as $location)
                  <option value="{{ $location->id }}">{{ $location->name }}</option>
                @endforeach
              </select>
            </div>
            <div style="width: 220px" class="ml-1">
                <div class="input-group">
                    <span class="input-group-prepend">
                        <span class="input-group-text"><i class="icon-calendar22"></i></span>
                    </span>
                    <input type="text" @focus="onFocus()" class="form-control form-control-sm date-range" name="dates" id="daterange-arrival" value="{{ $dates['start'] .' - '. $dates['end'] }}" placeholder="select dates" v-model="defaultArrivalDate" />
                </div>
            </div>
            <button class="btn btn-danger btn-sm ml-1" @click="updateArrivingGuest()">OK</button>
          </div>
          <div class="card">
            <div class="card-body py-0 px-0">
              <table class="table table-xs table-compact" id="arriving-guest">
                <thead>
                  <tr class="bg-grey-700">
                    <th></th>
                    <th class="two wide">Ref</th>
                    <th class="two wide">Customer</th>
                    <th>WA Number</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Guests</th>
                    <th class="two wide">Location</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Transfer (pick-up)</th>
                    <th>Time</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in items" :class="(item.transfer_info == 'TBA' || item.transfer_info == '') ? 'flight-empty' : ''">
                    <td><span v-if="(item.transfer_info == 'TBA' || item.transfer_info == '')"><i class="fa fa-fw fa-exclamation-triangle text-danger"></i></span><span v-else><i class="far fa-fw fa-check text-success"></i></span></td>
                    <td><a :href="`/bookings/${item.ref}`" title="" class="text-danger"><b>@{{ item.ref }}</b></a></td>
                    <td><a :href="`/guest/${item.guest_link}`" title="" class="text-danger"><b>@{{ item.guest_name }}</b></a></td>
                    <td><a :href="`https://wa.me/${item.phone}`" title="" class="text-grey-800"><b>+@{{ item.phone }}</b></a></td>
                    <td class="text-center"><span :class="`badge badge-pill ${item.status_badge}`"><b>@{{ item.payment_status }}</b></span></td>
                    <td class="text-center">@{{ item.guests_count }}</td>
                    <td><span class="tippy" :data-tippy-content="item.rooms_name"><b>@{{ item.location }}</b></td>
                    <td>@{{ item.check_in }}</td>
                    <td>@{{ item.check_out }}</td>
                    <td><div v-html="item.transfer_info"></div></td>
                    <td><div v-html="item.transfer_time"></div></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <br />

      <div class="row departure-guest-container">
        <div class="col-12" id="departure-guest-app">
          <div class="mb-2 py-1 px-2 d-flex justify-content-end align-items-center">
            <h4 class="m-0 mr-auto"><i class="fal fa-fw fa-sign-out-alt mr-1"></i> Dropoff Schedule</h4>
            <div style="width: 200px">
              <select
                v-model="camp"
                class="form-control form-control-sm"
                data-container-css-class="select-sm"
                id="departure-camp"
              >
                <option value="all">All locations</option>
                @foreach ($locations as $location)
                  <option value="{{ $location->id }}">{{ $location->name }}</option>
                @endforeach
              </select>
            </div>
            <div style="width: 220px" class="ml-1">
                <div class="input-group">
                    <span class="input-group-prepend">
                        <span class="input-group-text"><i class="icon-calendar22"></i></span>
                    </span>
                    <input type="text" @focus="onFocus()" class="form-control form-control-sm date-range" id="daterange-departure" name="dates" value="{{ $dates['start'] .' - '. $dates['end'] }}" placeholder="select dates" v-model="defaultDepartureDate" />
                </div>
            </div>
            <button class="btn btn-danger btn-sm ml-1" @click="updateDepartureGuest()">OK</button>
          </div>
          <div class="card">
            <div class="card-body py-0 px-0">
              <table class="table table-xs table-compact" id="departure-guest">
                <thead>
                  <tr class="bg-grey-700">
                    <th></th>
                    <th class="two wide">Ref</th>
                    <th class="two wide">Customer</th>
                    <th>WA Number</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Guests</th>
                    <th class="two wide">Location</th>
                    <th>Check Out</th>
                    <th>Transfer (drop-off)</th>
                    <th>Time</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in items" :class="(item.transfer_info == 'TBA' || item.transfer_info == '') ? 'flight-empty' : ''">
                    <td><span v-if="(item.transfer_info == 'TBA' || item.transfer_info == '')"><i class="fa fa-fw fa-exclamation-triangle text-danger"></i></span><span v-else><i class="far fa-fw fa-check text-success"></i></span></td>
                    <td><a :href="`/bookings/${item.ref}`" title="" class="text-danger"><b>@{{ item.ref }}</b></a></td>
                    <td><a :href="`/guest/${item.guest_link}`" title="" class="text-danger"><b>@{{ item.guest_name }}</b></a></td>
                    <td><a :href="`https://wa.me/${item.phone}`" title="" class="text-grey-800"><b>+@{{ item.phone }}</b></a></td>
                    <td class="text-center"><span :class="`badge badge-pill ${item.status_badge}`"><b>@{{ item.payment_status }}</b></span></td>
                    <td class="text-center">@{{ item.guests_count }}</td>
                    <td><span class="tippy" :data-tippy-content="item.rooms_name"><b>@{{ item.location }}</b></td>
                    <td>@{{ item.check_out }}</td>
                    <td><div v-html="item.transfer_info"></div></td>
                    <td><div v-html="item.transfer_time"></div></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
window.defaultArrivalDate = '{{ $dates['start'] .' - '. $dates['end'] }}';
window.defaultDepartureDate = '{{ $dates['start'] .' - '. $dates['end'] }}';
window.scheduleURL = 'transfer-guests';
</script>
@endsection
