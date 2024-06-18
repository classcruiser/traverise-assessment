<div class="h-screen fixed top-0 right-0 w-[90%] md:w-[460px] bg-white z-[9998] text-sm shadow-xl drawer-hidden" id="drawer">
    <a href="javascript:" title="Toggle overview" class="drawer-toggle w-12 h-12 md:w-14 md:h-14 bg-teal-300 text-white rounded flex justify-center items-center absolute p-2 left-[-46px] md:left-[-56px] top-[48vh] z-[9999]">
        <i class="far fa-shopping-cart fa-fw text-lg md:text-2xl"></i>
    </a>
    <div class="px-10 py-10 text-center border-b border-gray-100 space-y-1">
        <h2 class="block text-2xl font-bold">{{ $profile->title }}</h2>
    </div>
    <div class="w-full border-t border-gray-100 py-6 px-10">
        <span class="block uppercase mb-4 tracking-wider text-xs text-gray-400">CLASS</span>
        @foreach ($session['sessions'] as $class)
            <div class="py-1 flex justify-between items-start">
                <div class="max-w-[300px]">
                    <span>{{ $class['quantity'] }}x {{ $class['name'] }}</span>
                </div>
                <div class="text-base min-w-[75px] flex justify-end">
                    <span>&euro; {{ $class['price'] * $class['quantity'] }}</span>
                    @if ($step == 2)
                        <a href="/book-class/remove-session/{{ $class['id'] }}" title=""><i class="fa fa-times ml-1 text-rose-600"></i></a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    @if ($session['addons']->count())
        <div class="w-full border-t border-gray-100 py-6 px-10">
            <span class="block uppercase mb-4 tracking-wider text-xs text-gray-400">ADD ONS</span>
            @foreach ($session['addons'] as $addon)
                <div class="py-1 flex justify-between items-start">
                    <div>
                        <span>{{ $addon['amount'] }}x {{ $addon['name'] }}</span>
                    </div>
                    <span class="text-base">
                        &euro; {{ $addon['price'] * $addon['amount'] }}
                        @if ($step == 2)
                            <a href="/book-class/remove-addon/{{ $addon['class_addon_id'] }}" title=""><i class="fa fa-times ml-1 text-rose-600"></i></a>
                        @endif
                    </span>
                </div>
            @endforeach
        </div>
    @endif
    <div class="w-full border-t border-gray-100 py-6 px-10">
        <span class="block uppercase mb-4 tracking-wider text-xs text-gray-400">COMMENTS</span>
        <textarea name="comments" class="w-full border border-gray-200 rounded text-sm leading-relaxed" rows="5" placeholder="Enter if you have any comments">{{ $session['class.comments'] ?? '' }}</textarea>
        <div class="flex justify-start items-center mt-4">
            <button class="inline-block bg-gray-100 text-gray-700 rounded py-2 px-4 text-sm font-bold hover:bg-gray-200 transition-all" id="save-comment">
                Save
            </button>
            <span class="text-sm text-green-600 ml-2 hidden" id="comment-saved"><i class="fal fa-check mr-1"></i> Saved</span>
        </div>
    </div>
    <div class="w-full border-t border-gray-100 py-6 px-10">
        <div class="py-1 flex justify-between items-center">
            <span>TOTAL</span>

            <span class="text-2xl font-bold text-gray-700">&euro; {{ number_format(\App\Services\Classes\ShopService::getTotalPrice()) }}</span>
        </div>
    </div>
</div>