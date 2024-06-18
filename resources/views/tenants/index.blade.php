@extends('app')

@section('content')
<x-app>
    <div class="px-8 py-8 w-full mx-auto">

        <!-- Page header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-5">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Tenants</h1>
            </div>

            <a href="/tenants/new" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
                <i class="fal fa-plus fa-fw"></i>
                <span class="ml-2">Add Tenant</span>
            </a>

        </div>

        <div class="bg-white shadow-sm rounded-sm border border-gray-200 mb-8">
            <header class="px-5 py-4">
                <h2 class="font-semibold text-gray-800">All Tenants <span class="text-gray-400 font-medium">{{ $total_tenants }}</span></h2>
            </header>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="general">
                    <thead>
                        <tr>
                            <th><div class="font-semibold text-left">Identifier</div></th>
                            <th><div class="font-semibold text-left">Name</div></th>
                            <th><div class="font-semibold text-left">Email</div></th>
                            <th><div class="font-semibold text-left">Status</div></th>
                            <th><div class="font-semibold text-left">Stripe ID</div></th>
                            <th><div class="font-semibold text-left">Created</div></th>
                            <th><span class="sr-only">Menu</span></th>
                        </tr>
                    </thead>
                    @foreach ($tenants as $tenant)
                        <tbody class="text-sm">
                            <!-- Row -->
                            <tr>
                                <td>
                                    <div class="font-medium text-red-500">
                                        <a href="//{{ $tenant->id }}.{{ env('APP_URI') }}/dashboard" target="_blank" rel="nofollow">
                                            {{ $tenant->id }}
                                            <i class="fad fa-external-link fa-fw"></i>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <div class="font-medium text-gray-800">{{ $tenant->full_name }}</div>
                                    <div class="text-gray-600 text-xs">{{ $tenant->company }}</div>
                                </td>
                                <td>
                                    <div class="text-left">{{ $tenant->email }}</div>
                                </td>
                                <td>
                                    <div class="inline-flex font-medium text-xs uppercase {{ $tenant->is_active ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} rounded-full text-center px-2.5 py-0.5">
                                        {{ $tenant->is_active ? 'ACTIVE' : 'INACTIVE' }}
                                    </div>
                                </td>
                                <td>
                                    @if (!$tenant->stripe_account_id)
                                        <a href="{{ route('tenants.connect-stripe', ['tenant' => $tenant->id]) }}" title="" class="text-pink-600 font-bold text-xs">CONNECT NOW <i class="fa fa-exclamation-triangle ml-1"></i></a>
                                    @else
                                        <code class="text-xs">{{ $tenant->stripe_account_id }}</code>
                                        @if ($tenant->stripe_onboarding_process)
                                            <a href="{{ route('tenants.onboarding-stripe', ['tenant' => $tenant->id]) }}" title="" class="text-orange-600 ml-1">
                                                <i class="fas fa-link-slash"></i>
                                            </a>
                                        @else
                                            <span class="text-green-600"><i class="fas fa-link ml-1"></i></span>
                                        @endif
                                        <a href="{{ route('tenants.delete-stripe', ['tenant' => $tenant->id]) }}" title="" class="text-red-400 ml-1" onClick="return window.confirm('Delete Stripe account?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-left">{{ $tenant->created_at->format('M d, Y H:i A') }}</div>
                                </td>
                                <td class="w-px">
                                    <div class="flex items-center">
                                        <a href="/tenants/{{ $tenant->id }}" title="" class="text-light-blue-500 ml-2"><i class="fas fa-pencil"></i></a>
                                        <a href="/tenants/{{ $tenant->id }}/delete" title="" class="text-red-400 ml-2" onClick="return window.confirm('Delete tenant?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    @endforeach
                </table>

            </div>
        
        </div>

    </div>
</x-app>
@endsection
