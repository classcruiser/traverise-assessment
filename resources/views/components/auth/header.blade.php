<div class="relative">
    <div class="absolute left-0 top-0 w-full h-full flex justify-center items-center z-10">
        <a href="{{ route('class.shop.index') }}" title="" class="z-10 block w-[176px] p-3 bg-white rounded border-8 border-sky-100">
            <img src="{{ asset('images/camps/'. tenant('id') .'_logo.jpg') }}" alt="{{ tenant('id') }}" class="block w-full" />
        </a>
    </div>
    <img src="{{ asset('front/'. tenant('id') .'-class-shop.jpg') }}" alt="" class="block w-full h-[250px] object-cover" />
</div>