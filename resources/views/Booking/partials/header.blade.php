@if (strtoupper(config('app.env')) != 'PRODUCTION')
    <div class="env">
        You are in <strong>{{ strtoupper(config('app.env')) }}</strong> environment.
    </div>
@endif
<div class="navbar navbar-dark navbar-kima navbar-expand-md border-0">
    <div class="navbar-brand">
        <a href="/dashboard" class="d-inline-block">
            <img src="/images/simplelogo-light.png" alt="{{config('app.name')}}" />
        </a>
    </div>
    <div class="d-md-none">
        <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbar-mobile" aria-expanded="false">
            <i class="icon-menu7"></i>
        </button>
    </div>
    <div class="navbar-collapse collapse" id="navbar-mobile">
        <ul class="navbar-nav font-size-xs">
            @if (tenant('plan') != 'events')
                <li class="nav-item">
                    <a href="{{route('tenant.dashboard')}}" title="" class="navbar-nav-link {!! request()->is('dashboard') ? 'active' : '' !!}">
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('tenant.bookings')}}" title="" class="navbar-nav-link {!! request()->is('bookings*') && !request()->is('bookings/draft') ? 'active' : '' !!}">
                        <span>Bookings</span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a href="/calendar" title="" class="navbar-nav-link dropdown-toggle {!! request()->is('calendar*') && !request()->has('surfschool') ? 'active' : '' !!}" data-toggle="dropdown" data-hover="dropdown">
                        <span>Calendar</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right font-size-sm">
                        @foreach ($menu_camps as $camp)
                            @if (auth()->user()->allowed_camps_decoded->contains($camp->id) || auth()->user()->role == 'MASTER')
                                <a class="dropdown-item" href="/calendar/{{ $camp->id }}">{{ $camp->name }}</a>
                            @endif
                        @endforeach
                    </div>
                </li>
                <li>
                    <a href="{{route('tenant.payments')}}" title="" class="navbar-nav-link {!! request()->is('payments*') ? 'active' : '' !!}">
                        <span>Payments</span>
                    </a>
                </li>
                @can('view guest')
                    <li>
                        <a href="{{ route('tenant.guests') }}" title="" class="navbar-nav-link {!! request()->is('guests*') ? 'active' : '' !!}">
                            <span>Guests</span>
                        </a>
                    </li>
                @endcan
            @endif
            <li class="nav-item dropdown">
                @if (!auth()->user()->hasRole('Agent'))
                    <a href="javascript:" class="navbar-nav-link dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">
                        <span>Settings</span>
                    </a>
                @endif
                <div class="dropdown-menu dropdown-menu-right font-size-xs">
                    @if (tenant('plan') != 'events')
                        @canany(['add camp', 'edit camp', 'delete camp'])
                            <a class="dropdown-item" href="/camps"><i class="far fa-fw mr-2 fa-home"></i> Camps</a>
                        @endcanany
                        @canany(['add room', 'edit room', 'delete room'])
                            <a class="dropdown-item" href="/rooms"><i class="far fa-fw mr-2 fa-bed"></i> Room Categories</a>
                            <a class="dropdown-item" href="/rooms/threshold"><i class="far fa-fw mr-2 fa-percent"></i> Rooms Threshold</a>
                        @endcanany
                        @canany(['add camp', 'edit camp', 'delete camp', 'add room', 'edit room', 'delete room'])
                            <div class="dropdown-divider"></div>
                        @endcanany
                        @canany(['add addon', 'edit addon', 'delete addon'])
                            <a class="dropdown-item" href="/addons"><i class="far fa-fw mr-2 fa-gift"></i> Extras / Addons</a>
                            <a class="dropdown-item" href="/questionnaires"><i class="far fa-fw mr-2 fa-comments-question-check"></i> Questionnaires</a>
                        @endcanany
                        @canany(['add automated email', 'edit automated email', 'delete automated email'])
                            <a class="dropdown-item" href="/automated-emails"><i class="far fa-fw mr-2 fa-envelope-open"></i> Automated Emails</a>
                        @endcanany
                        @can('manage blacklist')
                            <a class="dropdown-item" href="/blacklist"><i class="far fa-fw mr-2 fa-user-slash"></i> Blacklist</a>
                        @endcan
                        @can('manage special package')
                            <a class="dropdown-item" href="/special-packages"><i class="far fa-fw mr-2 fa-box-alt"></i> Special Packages</a>
                        @endcan
                        @can('manage agent')
                            <a class="dropdown-item" href="/agents"><i class="far fa-fw mr-2 fa-user-alt"></i> Agents</a>
                        @endcan
                        @can('manage taxes')
                            <a class="dropdown-item" href="{{ route('tenant.taxes') }}"><i class="far fa-fw mr-2 fa-coins"></i> Tax</a>
                        @endcan
                        @canany(['manage voucher', 'manage special offer'])
                            <div class="dropdown-divider"></div>
                        @endcanany
                        @can('manage voucher')
                            <a class="dropdown-item" href="/vouchers"><i class="far fa-fw mr-2 fa-credit-card"></i> Vouchers</a>
                        @endcan
                        @can('manage special offer')
                            <a class="dropdown-item" href="/special-offers"><i class="far fa-fw mr-2 fa-dollar-sign"></i> Special Offers</a>
                        @endcan
                        @canany(['manage voucher', 'manage special offer'])
                            <div class="dropdown-divider"></div>
                        @endcanany
                        @canany(['add user', 'edit user', 'delete user'])
                            <a class="dropdown-item" href="/users"><i class="far fa-fw mr-2 fa-users"></i> Users</a>
                        @endcanany
                        @can('manage roles')
                            <a class="dropdown-item" href="/roles-and-permissions"><i class="far fa-fw mr-2 fa-key"></i> Role & Permissions</a>
                        @endcan
                        @canany(['add document', 'edit document', 'delete document'])
                            <a class="dropdown-item" href="{{route('tenant.documents')}}"><i class="far fa-fw mr-2 fa-file"></i> Documents</a>
                        @endcanany
                        @can('manage appearances')
                            <a class="dropdown-item" href="{{route('tenant.appearances')}}"><i class="far fa-fw mr-2 fa-window"></i> Appearances</a>
                        @endcan
                        @can('edit profile')
                            <a class="dropdown-item" href="{{route('tenant.profile')}}"><i class="far fa-fw mr-2 fa-user-tie"></i> Profile</a>
                        @endcan
                    @endif
                </div>
            </li>
        </ul>

        <ul class="navbar-nav ml-xl-auto font-size-xs">
            @if (tenant('plan') != 'events')
                <li class="nav-item">
                    <a href="{{route('tenant.bookings.trash')}}" class="navbar-nav-link {!! request()->is('bookings/trash') ? 'active' : '' !!}">
                        <i class="fa fa-fw fa-trash"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('tenant.bookings.pending')}}" class="navbar-nav-link {!! request()->is('bookings/pending') ? 'active' : '' !!}">
                        <i class="fa fa-fw fa-exclamation-circle"></i>
                        <span class="badge badge-pill bg-warning-400 ml-auto ml-xl-0">{{$total_pendings}}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('tenant.bookings.draft')}}" class="navbar-nav-link {!! request()->is('bookings/draft') ? 'active' : '' !!}">
                        <i class="fa fa-fw fa-pencil"></i>
                        @if(auth()->user()?->role_id != 4)
                            <span class="badge badge-pill bg-warning-400 ml-auto ml-xl-0">{{$total_drafts}}</span>
                        @endif
                    </a>
                </li>
            @endif
            <li class="nav-item">
                <a href="/auth/logout" class="navbar-nav-link">
                    <i class="far fa-sign-out mr-1"></i>
                    <span>{{auth()->user()?->name}}</span>
                </a>
            </li>
        </ul>
    </div>
</div>
