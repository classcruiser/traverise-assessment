@extends('Booking.main', ['tailwind' => true, 'bootstrap' => false])

@section('content')
<div class="min-h-screen w-full relative" style="background-color: {{ $profile->bg_color }}">
	<div class="w-full md:w-[720px] mx-auto py-0 md:py-10">
		@if (!$has_checked_in)
			<div class="border border-solid border-red-200 bg-red-50 p-3 text-red-700 text-lg mb-4">
				<i class="fa fa-exclamation-triangle mr-1"></i> This guest has checked in before at {{ $booking->checked_in_at->format('d.m.Y H:i') }}
			</div>
		@endif
		@if ($booking->payment->status != 'COMPLETED')
		<div class="p-5 md:p-0">
		    <div class="bg-red-50 border border-solid border-red-200 p-5 text-red-700">
                <i class="fa fa-exclamation-triangle mr-1"></i> This booking has not been fully paid yet.
            </div>
		</div>
		@endif
		@if ($booking->payment->status == 'COMPLETED')
    		<div class="border-t-4 border-solid" style="border-color: {{ $profile->accent_color }}; background-color: {{ $profile->secondary_color }}">
    			<div class="flex justify-between">
    				<h1 class="text-lg py-4 px-5">{{ $profile->title }} booking details</h1>
    				<div class="py-4 px-5 relative">
    					<span class="absolute right-5 -bottom-4 rounded bg-gray-900 text-white text-xl inline-block px-1">
    						<i class="fa fa-fw fa-angle-down"></i>
    					</span>
    				</div>
    			</div>
    			<div class="bg-gray-50">
    				<div class="grid grid-cols-12 gap-4 py-5 px-5">
    					<div class="col-span-12 md:col-span-6">
    						<span class="block uppercase text-xs tracking-wide text-gray-400">BOOKING REFERENCE NUMBER</span>
    						<span class="text-2xl">
    							{{ $booking->ref }}
    						</span>
    					</div>
    					<div class="col-span-12 md:col-span-6">
    						<span class="block uppercase text-xs tracking-wide text-gray-400">LOCATION</span>
    						<span class="text-2xl">
    							{{ $booking->location->name }}
    						</span>
    					</div>
    					<div class="col-span-6">
    						<span class="block uppercase text-xs tracking-wide text-gray-400">STAY</span>
    						<span class="text-2xl">
    							{{ $booking->check_in->format('d.m.Y') }} <i class="fal fa-arrow-right mx-2"></i> {{ $booking->check_out->format('d.m.Y') }}
    						</span>
    					</div>
    				</div>
    			</div>
    			@foreach ($booking->guests as $guest)
    				<div class="bg-white border-b border-gray-400">
    					<div class="py-5 px-5">
    						<span class="block uppercase text-xs tracking-wide text-gray-400">GUEST NAME</span>
    						<span class="text-2xl">
    							{{ $guest->details->full_name }}
    						</span>
    					</div>

    					<table class="table w-full text-sm">
    						<thead>
    							<tr>
    								<th class="bg-gray-50 text-left py-3 px-6 uppercase">Accommodation / Addons</th>
    								<th class="bg-gray-50 text-right py-3 px-6 uppercase">Amount</th>
    							</tr>
    						</thead>
    						<tbody>
    							@foreach ($guest->rooms as $room_info)
    								<tr>
    									<td class="py-3 px-6">{{ $room_info->room->room->name .' - '. $room_info->room->subroom->name }} {!! $room_info->room->is_private ? '<i class="fa fa-fw fa-lock tippy" data-tippy-content="Private Booking"></i>' : '' !!}</td>
    									<td class="py-3 px-6 text-right">{{ $room_info->room->days }} days / {{ $room_info->room->nights }} nights</td>
    								</tr>
    								@if ($room_info->room->addons->count() > 0)
    									@foreach ($room_info->room->addons as $addon)
    										<tr>
    											<td class="py-3 px-6">{{ $addon->details->name }}</td>
    											<td class="py-3 px-6 text-right">{{ $addon->amount }} {{ \Illuminate\Support\Str::plural($addon->details->unit_name, $addon->amount) }}</td>
    										</tr>
    									@endforeach
    								@endif
    							@endforeach
    						</tbody>
    					</table>
    				</div>
    			@endforeach
    		</div>
        @endif
	</div>
</div>
@endsection
