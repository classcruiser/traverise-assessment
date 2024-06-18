<div>
    <!-- create a white bar -->
    <div class="w-full bg-[#82bbb3] flex justify-end items-center text-xs">
        <a href="{{ route('guest.account') }}" class="block text-gray-800 p-3 font-bold"><i class="fal fa-user mr-1"></i> My Account</a>
        @if (auth()->guard('guest')->check())
            <a href="{{ route('guest.logout') }}" class="block text-gray-800 p-3 font-bold"><i class="fal fa-sign-out mr-1"></i> Logout</a>
        @endif
    </div>
    <div class="relative">
        <img src="{{ asset('front/'. tenant('id') .'-class-shop.jpg') }}" alt="" class="block w-full h-[200px] object-cover" />
    </div>
</div>