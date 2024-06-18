@extends('Booking.main', ['bootstrap' => false, 'tailwind' => true])

@section('content')
<div class="min-w-screen min-h-screen bg-[#F4F4F9]">
    <div class="container mx-auto max-w-2xl">
        <div class="flex flex-wrap">
            <div class="w-full">
                <div class="bg-white shadow-sm rounded px-8 pt-6 pb-8 mb-4 text-sm leading-relaxed">
                    <h4 class="uppercase text-gray-500 text-xs tracking-wider mb-1">REF: {{ $ref }}</h4>
                    <h1 class="text-2xl font-bold mb-4">Questionnaire</h1>
                    @if (request()->has('success'))
                        <p class="text-gray-700 mb-4">Thank you for taking your time to answer the questions.<br />We wish you a safe travel to Vieux-Boucau and are looking forward to meeting you soon!</p>
                        <p class="text-gray-700">
                            Best regards,<br />Uli & the Team of the Atlantic Surf Lodge
                        </p>
                    @else
                        <p class="text-gray-700 mb-4">Thanks for taking some minutes.<br />Please answer the questions below for the best time of the year.</p>
                        @if ($errors->any())
                            @foreach ($errors->all() as $error)
                                <p class="text-red-500 mb-1"><i class="fa fa-exclamation-triangle mr-1"></i> {{ $error }}</p>
                            @endforeach
                        @endif
                        <form method="POST" action="{{ route('questionnaire.old-bookings.store', ['ref' => $booking->ref]) }}">
                            @csrf
                            @foreach ($booking->guests as $booking_guest)
                                <h3 class="font-bold text-base mb-4">Guest {{ $loop->iteration }} : {{ $booking_guest->details->full_name }}</h3>
                                <div class="mb-4">
                                    <label class="block text-gray-700 font-bold mb-2" for="name">
                                        Surflevel <span class="text-red-600">*</span>
                                    </label>
                                    <!-- create 3 radio with label of Beginner, Intermediate and Advanced -->
                                    <label class="flex justify-start items-center">
                                        <input type="radio" name="guest[{{ $booking_guest->id }}][surflevel]" value="Beginner" required class="block" @checked(old('guest.'. $booking_guest->id .'.surflevel') == 'Beginner') />
                                        <span class="block ml-2">Beginner</span>
                                    </label>
                                    <label class="flex justify-start items-center my-1">
                                        <input type="radio" name="guest[{{ $booking_guest->id }}][surflevel]" value="Intermediate" required class="block" @checked(old('guest.'. $booking_guest->id .'.surflevel') == 'Intermediate') />
                                        <span class="block ml-2">Intermediate</span>
                                    </label>
                                    <label class="flex justify-start items-center">
                                        <input type="radio" name="guest[{{ $booking_guest->id }}][surflevel]" value="Advanced" required class="block" @checked(old('guest.'. $booking_guest->id .'.surflevel') == 'Advanced') />
                                        <span class="block ml-2">Advanced</span>
                                    </label>
                                </div>
                                <!-- now create the same radio for nutrition with three options: Regular, Vegan, and Vegetarian -->
                                <div class="mb-4">
                                    <label class="block text-gray-700 font-bold mb-2" for="name">
                                        Nutrition <span class="text-red-600">*</span>
                                    </label>
                                    <label class="flex justify-start items-center">
                                        <input type="radio" name="guest[{{ $booking_guest->id }}][nutrition]" value="Regular" required class="block" @checked(old('guest.'. $booking_guest->id .'.nutrition') == 'Regular') />
                                        <span class="block ml-2">Regular</span>
                                    </label>
                                    <label class="flex justify-start items-center my-1">
                                        <input type="radio" name="guest[{{ $booking_guest->id }}][nutrition]" value="Vegan" required class="block" @checked(old('guest.'. $booking_guest->id .'.nutrition') == 'Vegan') />
                                        <span class="block ml-2">Vegan</span>
                                    </label>
                                    <label class="flex justify-start items-center">
                                        <input type="radio" name="guest[{{ $booking_guest->id }}][nutrition]" value="Vegetarian" required class="block" @checked(old('guest.'. $booking_guest->id .'.nutrition') == 'Vegetarian') />
                                        <span class="block ml-2">Vegetarian</span>
                                    </label>
                                </div>
                                <!-- create a single text field with label Medical info -->
                                <div class="mb-4">
                                    <label class="block text-gray-700 font-bold mb-2" for="name">
                                        Medical info (let us know what to take care of)
                                    </label>
                                    <!-- use textarea instead of input -->
                                    <textarea name="guest[{{ $booking_guest->id }}][medical_info]" rows="6" class="text-sm shadow appearance-none border border-gray-200 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('guest.'. $booking_guest->id .'.medical_info') }}</textarea>
                                </div>
                                <div class="mb-4 border-b border-gray-100 pb-8">
                                    <label class="block text-gray-700 font-bold mb-2" for="name">
                                        How do you plan your arrival? Please let us know your details:
                                    </label>
                                    <!-- use textarea instead of input -->
                                    <textarea name="guest[{{ $booking_guest->id }}][arrival]" rows="6" class="text-sm shadow appearance-none border border-gray-200 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('guest.'. $booking_guest->id .'.arrival') }}</textarea>
                                </div>
                            @endforeach
                            <!-- create submit button -->
                            <button class="bg-sky-500 hover:bg-sky-600 transition text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                                Submit
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection