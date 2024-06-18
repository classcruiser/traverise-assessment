@extends('Classes.app')

@section('content')
    <div class="w-screen min-h-screen bg-gray-50 relative flex justify-center items-center">
        <div class="w-full md:max-w-[480px] m-6 bg-white p-6 shadow-sm text-sm">
            <img src="{{ asset('images/camps/'. tenant('id') .'_logo.jpg') }}" alt="" class="block mx-auto max-w-[200px] mb-6"/>
            <!-- TODO : take from backend -->
            <h1 class="text-center text-xl font-bold text-gray-700 mb-4">Danke für deine Buchung!</h1>
            <p class="text-gray-600 text-center">
                In kürze erhältst du eine E-Mail mit einer PDF-Rechnung inkl. QR Code.
                <br />
                Bitte beachte, dass dies je nach Zahlungsmethode etwas Zeit in Anspruch nehmen.
            </p>
            <p class="text-gray-600 text-center mt-6">#RR-Family </p>

            <div class="mt-8">
                <a href="{{ route('class.shop.index') }}" class="w-full block text-center bg-teal-300 text-white rounded-sm text-sm font-bold py-3 px-5">Surf Calendar</a>
            </div>
        </div>
    </div>
