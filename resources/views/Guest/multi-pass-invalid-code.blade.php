@extends('Classes.app')

@section('content')
    <div class="w-screen min-h-screen bg-gray-50 relative flex justify-center items-center">
        <div class="w-full md:max-w-[480px] m-6 bg-white p-6 shadow-sm text-sm">
            <img src="{{ asset('images/camps/'. tenant('id') .'_logo.jpg') }}" alt="" class="block mx-auto max-w-[200px] mb-6"/>
            <!-- TODO : take from backend -->
            <h1 class="text-center text-xl font-bold text-gray-700 mb-4">Dieser Code wurde nicht gefunden oder wurde bereits verwendet.</h1>
        </div>
    </div>
