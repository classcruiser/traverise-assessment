<div class="flex h-screen overflow-hidden">

    @include('partials.sidebar')

    <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">

        <!-- Site header -->
        @include('partials.header')

        <main>
            {{ $slot }}
        </main>

    </div>

</div>
