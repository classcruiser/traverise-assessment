@extends('Classes.app')

@section('content')
<div class="w-screen min-h-screen bg-gray-50 relative">
    @include('Classes.shop.drawer')
    <x-shop.container>
        <x-shop.header />
        <x-shop.heading step="2">
            <div class="py-8 px-6">
                
                @if (session()->has('message'))
                    <div class="mb-6 p-4 bg-teal-50 border border-teal-100 text-teal-600 text-sm leading-relaxed">{{ session('message') }}</div>
                @endif

                <div class="grid grid-cols-12 gap-6">
                    @forelse ($addons as $addon)
                        <div class="col-span-6 md:col-span-3">
                            <img
                                class="block object-contain w-full aspect-square"
                                src="{{ asset('images/class-addons/'. tenant('id') .'_addon_'. $addon->id .'.jpg?'. date('Ymd')) }}"
                                alt="{{ $addon->name }}"
                            />
                            <div class="flex justify-between items-center my-4">
                                <h3 class="text-base font-bold">{{ $addon->name }}</h3>
                                <span>&euro; {{ $addon->base_price }} / {{ $addon->unit_name }}</span>
                            </div>

                            <a href="/book-class/add-addon/{{ $addon->id }}" title="" class="bg-teal-50 text-gray-500 rounded text-xs uppercase py-2 px-3 w-full block text-center hover:bg-gray-100 transition-all">
                                ADD <i class="fal fa-plus ml-1"></i>
                            </a>
                        </div>
                    @empty
                        <div class="col-span-12">There is currently no add ons for this class.</div>
                    @endforelse
                </div>

                <div class="mt-16 flex justify-between">
                    <x-shop.button href="/book-class{{ request()->has('soft_op') ? '?soft_op='. request('soft_op') : '' }}" style="secondary">
                        <i class="ml-1 fal fa-arrow-left"></i> PREVIOUS
                    </x-shop.button>
                    <x-shop.button href="/book-class/profile{{ request()->has('soft_op') ? '?soft_op='. request('soft_op') : '' }}" style="primary">
                        NEXT <i class="ml-1 fal fa-arrow-right"></i>
                    </x-shop.button>
                </div>
            </div>
        </x-shop.heading>
    </x-shop.container>
</div>
@endsection