@extends('app')

@section('content')
<main class="bg-white">

    <div class="relative flex">

        <!-- Content -->
        <div class="w-full md:w-1/2">

            <div class="min-h-screen h-full flex flex-col justify-center items-center">

                <div class="w-[28rem] mx-auto px-4 py-8">
    
                    <h1 class="text-3xl text-gray-800 font-bold mb-6">Login to Traverise</h1>
                    <!-- Form -->
                    <form action="/" method="post">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-1" for="email">Email Address</label>
                                <input id="email" class="form-input w-full" name="email" type="email" placeholder="Email address" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1" for="password">Password</label>
                                <input id="password" class="form-input w-full" name="password" type="password" placeholder="Password" autocomplete="on" />
                            </div>
                        </div>
                        <div class="flex items-center justify-end mt-6">
                            <button class="btn bg-indigo-500 hover:bg-indigo-600 text-white ml-3" type="submit" name="submit">Sign In</button>
                        </div>
                        @csrf
                    </form>
    
                </div>

            </div>

        </div>

        <!-- Image -->
        <div class="hidden md:block absolute top-0 bottom-0 right-0 md:w-1/2" aria-hidden="true">
            <img class="object-cover object-center w-full h-full" src="/images/auth.jpg" alt="Authentication image" />
        </div>

    </div>

</main>
@endsection
