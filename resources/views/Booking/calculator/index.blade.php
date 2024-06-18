@extends('Booking.app')

@section('content')

@include('Booking.partials.popup-calculator')

<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
  <div class="d-flex">
    <div class="breadcrumb">
      <a href="/dashboard" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
      <span class="breadcrumb-item active">Price Calculator</span>
    </div>

    <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
  </div>
</div>

<div class="page-content App-Calculator">
  <div class="content-wrapper container">
    <div class="content">
      <div class="mb-2 py-1 px-2 d-flex justify-content-end align-items-center">
        <h4 class="m-0 mr-auto"><i class="fal fa-fw fa-calculator mr-1"></i> Price Calculator</h4>
      </div>

      <div class="card">
        <div class="card-body">
          <h5>Rooms</h5>
          <div class="d-flex justify-content-start align-items-center">
            
            <div class="form-group">
              <div style="width: 180px;" class="mr-2">
                <select class="form-control" name="location" v-model="camp" @change="updateRoom">
                  <option disabled :value="null">CAMP</option>
                  <option v-if="camps" v-for="camp in camps" v-key="camp.id" :value="camp.id">@{{ camp.name }}</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <div style="width: 280px;" class="mr-2">
                <select class="form-control" name="room_id" v-model="room" @change="checkPrivate">
                  <option disabled value="null">ROOM</option>
                  <option v-if="rooms" v-for="room in rooms" v-key="room.id" :value="room.id">@{{ room.name }}</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <div style="width: 200px;" class="mr-2">
                <input type="text" class="form-control daterange-empty" v-model="dates" placeholder="DATES" />
              </div>
            </div>

            <div class="form-group">
              <div style="width: 200px; height: 20px">
                <div class="form-check form-check-inline mr-1">
                  <label class="form-check-label">
                    <input type="checkbox" id="keep-price" class="form-check-input-styled" :disabled="!allowPrivate" data-fouc v-model="privateBooking">
                    <span>Private booking</span>
                  </label>
                </div>
              </div>
            </div>

            <div class="form-group ml-auto ">
              <div class="text-right">
                <button class="btn bg-danger" @click.prevent="calculateRoom" :disabled="isLoading || isBlank">
                  <span v-if="!isLoading"><b>CALCULATE</b></span>
                  <span v-else><i class="fal fa-fw fa-spin fa-spinner-third"></i></span>
                </button>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <h5>Results</h5>
            <div>
              <button v-if="results.length > 0" class="btn bg-grey-600 text-uppercase" @click="showOfferTemplate"><b>Offer Template</b></button>
            </div>
          </div>
          <div v-if="results && results.length <= 0" class="d-flex justify-content-start align-items-center">
            <div class="d-flex flex-column align-items-center justify-content-center w-100 text-grey-300">
              <i class="far fa-fw fa-search fa-4x text-grey-300 mb-2"></i>
              Search something first
            </div>
          </div>
          <div v-else>
            <div v-for="(r, i) in results" v-key="i" class="pc-result mb-3">
              <div class="d-flex justify-content-between align-items-center">
                <a href="#" title="" class="pc-title py-2" data-toggle="collapse" :data-target="`#pc-${i}`">
                  @{{ r.title }}
                  <span v-show="r.private_booking"><i class="fa ml-1 fa-lock tippy" data-tippy-content="Private booking"></i></span>
                </a>
                <a href="#" title="" class="text-danger" @click.prevent="removeResult(i)"><i class="far fa-fw fa-times"></i></a>
              </div>
              <div class="pc-details collapse show" :id="`pc-${i}`">
                <table>
                  <tr>
                    <th>Duration</th>
                    <th>Available Beds</th>
                    <th class="text-right">Basic Price</th>
                    <th class="text-right">Surcharge</th>
                    <th class="text-right">Total Price</th>
                  </tr>
                  <tr>
                    <td>@{{ r.duration }} nights</td>
                    <td>@{{ r.available_beds }} beds</td>
                    <td class="text-right">&euro;@{{ r.basic_price }}</td>
                    <td class="text-right">&euro;@{{ r.surcharge }}</td>
                    <td class="text-right"><b>&euro;@{{ r.total_price }}</b></td>
                  </tr>
                  <tr>
                    <td colspan="5" class="p-0">
                      <a href="#" title="" data-toggle="collapse" class="pc-details-toggle" :data-target="`#pc-details-${i}`"><i class="fal fa-angle-down mr-1"></i> Days breakdown</a>
                    </td>
                  </tr>
                  <tr :id="`pc-details-${i}`" class="collapse show">
                    <td colspan="5" class="p-0">
                      <table>
                        <tr>
                          <th width="20%">Date</th>
                          <th width="10%">Season</th>
                          <th width="10%">Free Beds</th>
                          <th width="20%" class="text-right">Basic Price</th>
                          <th width="20%" class="text-right">Progressive Price</th>
                          <th width="20%" class="text-right">Subtotal</th>
                        </tr>
                        <tr v-for="(rate, index) in r.details" :key="index">
                          <td>@{{ rate.date }}</td>
                          <td>@{{ rate.season }}</td>
                          <td>@{{ rate.beds }} beds</td>
                          <td class="text-right">&euro;@{{ rate.price }}</td>
                          <td class="text-right">&euro;@{{ rate.progressive_price }}</td>
                          <td class="text-right">&euro;@{{ rate.subtotal }}</td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </div>
            </div>

            <div class="d-flex justify-content-end">
              <button class="btn bg-grey-600 text-uppercase" @click="showOfferTemplate"><b>Offer Template</b></button>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

@endsection

@section('json')
<script>
const CAMPS_DATA = @json($locations);
</script>
@endsection
@section('scripts')
<script>
tippy('.tippy', {
  content: 'Tooltip',
  arrow: true,
})
</script>
@endsection