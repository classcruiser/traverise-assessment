@extends('Booking.app')

@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" integrity="sha256-Uv9BNBucvCPipKQ2NS9wYpJmi8DTOEfTA/nH2aoJALw=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css" integrity="sha256-aa0xaJgmK/X74WM224KMQeNQC2xYKwlAt08oZqjeF0E=" crossorigin="anonymous" />
    <div class="navbar navbar-expand-md navbar-dark bg-grey navbar-static border-0">
        <div class="text-center d-md-none w-100">
            <button type="button" class="navbar-toggler dropdown-toggle" data-toggle="collapse" data-target="#navbar-second">
                <i class="icon-unfold mr-1"></i>
                Reports
            </button>
        </div>
        <div class="navbar-collapse collapse" id="navbar-second">
            <ul class="navbar-nav font-size-xs">
                @can('view general report')
                    <li class="nav-item">
                        <a href="/reports" class="navbar-nav-link {{$page == 'general' ? 'active' : ''}}">General Overview</a>
                    </li>
                @endcan
                @can('view general report')
                    <li class="nav-item">
                        <a href="/reports/income" class="navbar-nav-link {{$page == 'income' ? 'active' : ''}}">Income</a>
                    </li>
                @endcan
                @can('view daily report')
                    <li class="nav-item">
                        <a href="/reports/daily" class="navbar-nav-link {{$page == 'daily' ? 'active' : ''}}">Daily Overview</a>
                    </li>
                @endcan
                @can('view monthly report')
                    <li class="nav-item">
                        <a href="/reports/monthly" class="navbar-nav-link {{$page == 'monthly' ? 'active' : ''}}">Monthly Overview</a>
                    </li>
                @endcan
                @can('view yearly report')
                    <li class="nav-item">
                        <a href="/reports/yearly" class="navbar-nav-link {{$page == 'yearly' ? 'active' : ''}}">Yearly Overview</a>
                    </li>
                @endcan
                <li class="nav-item">
                    <a href="/reports/accommodation" class="navbar-nav-link {{$page == 'accommodation' ? 'active' : ''}}">Accommodation</a>
                </li>
                <li class="nav-item">
                    <a href="/reports/addons" class="navbar-nav-link {{$page == 'addons' ? 'active' : ''}}">Add-ons</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="/dashboard" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                <span class="breadcrumb-item">Reports</span>
                <span class="breadcrumb-item active">{{$current_page}}</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    @include('Booking.partials.reports.'. $page)
@endsection
