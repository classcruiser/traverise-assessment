<div id="popup-wrapper" class="w-screen h-screen bg-black bg-opacity-80 fixed top-0 left-0 overflow-y-auto z-[99999] flex justify-center items-center transition-all opacity-0 pointer-events-none">
    <span id="popup-loading" class="hidden"><i class="fad fa-spin fa-spinner-third text-white text-7xl"></i></span>

    <div id="popup-content" class="hidden w-full md:w-2/4 mx-4 md:mx-0 rounded-sm">
        <div class="text-xl text-gray-800 p-3 bg-white flex justify-between items-center">
            <span id="popup-title">Document Title</span>
            <a href="#" title="" class="text-gray-900" popup-close><i class="fal fa-times text-3xl"></i></a>
        </div>
        <div id="popup-body" class="p-3 border-t border-gray-100 leading-relaxed bg-white text-gray-800 max-h-[85vh] md:max-h-[90vh] overflow-y-auto normal-text">
        </div>
    </div>
</div>
