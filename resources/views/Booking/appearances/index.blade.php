@extends('Booking.app', ['tailwind' => true])

@section('content')
    <style type="text/css" media="screen">
    .bg-body-preview {
        background-color: {{$preview ? $preview_data['bg_color'] : $settings->bg_color}};
    }
    .bg-custom-primary {
        background-color: {{ $preview ? $preview_data['primary_color'] : $settings->primary_color }};
    }
    .bg-custom-secondary {
        background-color: {{ $preview ? $preview_data['secondary_color'] : $settings->secondary_color }};
    }
    .navi li span { color: #444 }
    .navi li.navi-active span {
        border-color: {{ $preview ? $preview_data['accent_color'] : $settings->accent_color }};
        color: {{ $preview ? $preview_data['accent_color'] : $settings->accent_color }};
    }
    .navi li.navi-complete:after {
        background-color: {{ $preview ? $preview_data['accent_color'] : $settings->accent_color }};
    }
    .btn-custom {
        background-color: {{ $preview ? $preview_data['accent_color'] : $settings->accent_color }};
    }
    .text-custom, .link-custom, .normal-text a {
        color: {{ $preview ? $preview_data['accent_color'] : $settings->accent_color }};
    }
    .text-custom:hover, .link-custom:hover, .normal-text a:hover {
        color: {{ $preview ? $preview_data['accent_color'] : $settings->accent_color }};
    }
    .border-danger {
        border-color: {{ $preview ? $preview_data['accent_color'] : $settings->accent_color }};
    }
    </style>

    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{route('tenant.dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                <span class="breadcrumb-item">Settings</span>
                <span class="breadcrumb-item active">Appearances</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content">
        <div class="content-wrapper container">
            <div class="content">
                @if(session()->has('messages'))
                    <div class="alert bg-green-400 text-white alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        <i class="fa fa-check-circle mr-1"></i> {{session('messages')}}
                    </div>
                @endif
                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h4 class="card-title"><i class="fal fa-window fa-fw"></i> Appearances</h4>
                    </div>
                    <div class="card-body border-top-1 border-alpha-grey pt-3">
                        <form action="{{route('tenant.appearances.update')}}" method="post" enctype="multipart/form-data">
                            <div class="w-full grid grid-cols-12 gap-4">
                                <div class="col-span-6">
                                    <label class="font-bold uppercase text-xs">Title</label>
                                    <input type="text" name="title" class="form-control" value="{{$preview ? $preview_data['title'] : $settings->title}}">
                                </div>
                                <div class="col-span-6">
                                    <label class="font-bold uppercase text-xs">Sub title</label>
                                    <input type="text" name="short_description" class="form-control" value="{{$preview ? $preview_data['short_description'] : $settings->short_description}}">
                                </div>
                                <div class="col-span-12">
                                    <label class="font-bold uppercase text-xs">Address</label>
                                    <textarea name="address" class="form-control" rows="3">{{$preview ? $preview_data['address'] : $settings->address}}</textarea>
                                </div>
                                <div class="col-span-3">
                                    <label class="font-bold uppercase text-xs">Contact person</label>
                                    <input type="text" name="contact_person" class="form-control" value="{{$preview ? $preview_data['contact_person'] : $settings->contact_person}}">
                                </div>
                                <div class="col-span-3">
                                    <label class="font-bold uppercase text-xs">Contact email</label>
                                    <input type="text" name="contact_email" class="form-control" value="{{$preview ? $preview_data['contact_email'] : $settings->contact_email}}">
                                </div>
                                <div class="col-span-3">
                                    <label class="font-bold uppercase text-xs">Contact phone number</label>
                                    <input type="text" name="phone_number" class="form-control" value="{{$preview ? $preview_data['phone_number'] : $settings->phone_number}}">
                                </div>
                                <div class="col-span-3"></div>
                                <div class="col-span-3">
                                    <label class="font-bold uppercase text-xs">Background color</label>
                                    <input data-jscolor="{ preset: 'dark', closeButton: true, closeText: 'OK' }" value="{{$preview ? $preview_data['bg_color'] : $settings->bg_color}}" name="bg_color" class="form-control" />
                                </div>
                                <div class="col-span-3">
                                    <label class="font-bold uppercase text-xs">Accent color</label>
                                    <input data-jscolor="{ preset: 'dark', closeButton: true, closeText: 'OK' }" value="{{$preview ? $preview_data['accent_color'] : $settings->accent_color}}" name="accent_color" class="form-control" />
                                </div>
                                <div class="col-span-3">
                                    <label class="font-bold uppercase text-xs">Primary color</label>
                                    <input data-jscolor="{ preset: 'dark', closeButton: true, closeText: 'OK' }" value="{{$preview ? $preview_data['primary_color'] : $settings->primary_color}}" name="primary_color" class="form-control" />
                                </div>
                                <div class="col-span-3">
                                    <label class="font-bold uppercase text-xs">Secondary color</label>
                                    <input data-jscolor="{ preset: 'dark', closeButton: true, closeText: 'OK' }" value="{{$preview ? $preview_data['secondary_color'] : $settings->secondary_color}}" name="secondary_color" class="form-control" />
                                </div>
                                <div class="col-span-6">
                                    <label class="font-bold uppercase text-xs">Header picture</label>
                                    <input type="file" name="file" class="form-control">
                                    @if($preview && isset($preview_data['file']))
                                        <input type="hidden" name="preview_file" value="{{$preview_data['file']}}" />
                                    @endif
                                </div>
                                <div class="col-span-3">
                                    <label class="font-bold uppercase text-xs">Picture Position (Horizontal)</label>
                                    <select name="bg_pos_horizontal" class="border border-gray-100 py-1 px-2 rounded text-sm w-full">
                                        <option value="left" {{$preview ? ($preview_data['bg_pos_horizontal'] == 'left' ? 'selected' : '') : ($settings['bg_pos_horizontal'] == 'left' ? 'selected' : '')}}>Left</option>
                                        <option value="center" {{$preview ? ($preview_data['bg_pos_horizontal'] == 'center' ? 'selected' : '') : ($settings['bg_pos_horizontal'] == 'center' ? 'selected' : '')}}>Center</option>
                                        <option value="right"{{$preview ? ($preview_data['bg_pos_horizontal'] == 'right' ? 'selected' : '') : ($settings['bg_pos_horizontal'] == 'right' ? 'selected' : '')}}>Right</option>
                                    </select>
                                </div>
                                <div class="col-span-3">
                                    <label class="font-bold uppercase text-xs">Picture Position (Vertical)</label>
                                    <select name="bg_pos_vertical" class="border border-gray-100 py-1 px-2 rounded text-sm w-full">
                                        <option value="top" {{$preview ? ($preview_data['bg_pos_vertical'] == 'top' ? 'selected' : '') : ($settings['bg_pos_vertical'] == 'top' ? 'selected' : '')}}>Top</option>
                                        <option value="center" {{$preview ? ($preview_data['bg_pos_vertical'] == 'center' ? 'selected' : '') : ($settings['bg_pos_vertical'] == 'center' ? 'selected' : '')}}>Middle</option>
                                        <option value="bottom" {{$preview ? ($preview_data['bg_pos_vertical'] == 'bottom' ? 'selected' : '') : ($settings['bg_pos_vertical'] == 'bottom' ? 'selected' : '')}}>Bottom</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4 flex justify-center gap-2">
                                @csrf
                                <button class="btn btn-outline-secondary uppercase font-bold" name="preview">preview</button>
                                <button class="btn btn-danger uppercase font-bold" name="submit">save</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h4 class="card-title"><i class="fal fa-magnifying-glass fa-fw"></i> Preview</h4>
                    </div>
                    <div class="card-body p-0 bg-body-preview">

                        <!-- preview -->
                        <div class="w-full relative min-h-[400px]">
                            <div class="header fixed sm:absolute with-overlay-bottom h-[400px] left-0 top-0 w-full" style="background: url({{$preview && isset($preview_data['file']) ? '/bucket/'. $preview_data['file'] : url('bucket/'. tenant('id') .'.jpg')}}) {{$preview ? $preview_data['bg_pos_horizontal'] : $settings->bg_pos_horizontal}} {{ $preview ? $preview_data['bg_pos_vertical'] : $settings->bg_pos_vertical}} no-repeat; background-size: cover;">
                                &nbsp;
                            </div>

                            <div class="container relative z-30 mt-16">
                                <div class="pt-4 px-4 block sm:flex justify-between items-end w-full">
                                    <div class="px-3">
                                        <h1 class="text-white text-3xl font-bold">{{$preview ? $preview_data['title'] : $settings->title}}</h1>
                                        <p class="text-white opacity-80">{!! $preview ? $preview_data['short_description'] : ($settings->short_description ?? 'Sub title') !!}</p>
                                    </div>
                                    <div class="px-3 text-right text-white opacity-80 block">
                                        <p class="leading-relaxed">
                                            <i class="fa fa-user mr-1"></i> {{$preview ? $preview_data['contact_person'] : ($settings->contact_person ?? 'Contact Person')}}<br />
                                            {!! $preview ? nl2br($preview_data['address']) : ($settings->address ? nl2br($settings->address) : 'Address') !!}<br />
                                            <i class="fa fa-envelope mr-1"></i> {!! $preview ? $preview_data['contact_email'] : ($settings->contact_email ?? 'Contact email') !!}<br />
                                            <i class="fa fa-phone mr-1"></i> {!! $preview ? $preview_data['phone_number'] : ($settings->phone_number ?? 'Phone number') !!}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="page-content relative z-30">
                                <div class="content-wrapper container">
                                    <div class="content">
                                        <div class="card border-0 rounded rounded-bottom-0">
                                            <div class="card-body bg-custom-primary py-2 px-2">
                                                <div class="flex flex-col md:flex-row justify-center items-center space-y-2 md:space-y-0 space-x-0 md:space-x-2">
                                                    <div class="form-group mb-0 w-full md:w-[260px] mx-1">
                                                        <div class="input-group input-cal-step2 w-full">
                                                            <span class="input-group-prepend mobile-hide">
                                                                <span class="input-group-text"><i class="icon-calendar22"></i></span>
                                                            </span>
                                                            <input id="datepicker" name="dates" type="text" value="{{date('d M Y')}}" class="form-control" />
                                                        </div>
                                                    </div>
                                                    <div class="form-group mb-0 ml-0 md:ml-2 w-full md:w-[170px] mx-1">
                                                        <div class="input-group input-guest-step2 w-full">
                                                            <span class="input-group-prepend mobile-hide">
                                                                <span class="input-group-text"><i class="icon-users"></i></span>
                                                            </span>
                                                            <select name="guest" class="custom-select mobile-radius">
                                                                @for($i = 1; $i <= 10; $i++)
                                                                    <option value="{{$i}}">{{$i}} {{Str::plural('person', $i)}}</option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group ml-0 md:ml-2 mb-0 w-full md:w-[160px] mx-1">
                                                        @csrf
                                                        <button class="btn btn-custom check_duration w-full text-white" data-minimum="1">UPDATE</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body bg-custom-secondary py-2 mobile-navi">
                                                <div class="navi">
                                                    <ul>
                                                        <li class="navi-active navi-complete">
                                                            <span><i class="fa fa-check"></i></span>
                                                        </li>
                                                        <li class="navi-active navi-complete">
                                                            <span><i class="fa fa-check"></i></span>
                                                        </li>
                                                        <li class="navi-active">
                                                            <span>3</span>
                                                        </li>
                                                        <li>
                                                            <span>4</span>
                                                        </li>
                                                        <li>
                                                            <span>5</span>
                                                        </li>
                                                        <li>
                                                            <span>6</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">

                                            <div class="col-md-8">
                                                <div class="section-title bg-custom-primary text-uppercase px-3 rounded-1 font-size-lg">1 - LOCATION</div>
                                                <div class="section-title bg-custom-primary text-uppercase px-3 rounded-1 font-size-lg">2 - ACCOMMODATION</div>
                                                <div class="section-body active">

                                                    <select name="room_id" class="form-control" id="location-select">
                                                        <option value="">Select room category</option>
                                                    </select>

                                                    <div class="d-flex justify-content-between mt-3">
                                                        <a class="btn bg-grey text-uppercase font-size-lg btn-lg" href="#">BACK</a>
                                                        <a class="btn btn-custom text-uppercase font-size-lg btn-lg" href="#">NEXT STEP</a>
                                                    </div>

                                                </div>

                                                <div class="section-title bg-custom-primary text-uppercase px-3 rounded-1 font-size-lg">3 - ADD-ONS</div>
                                                <div class="section-title bg-custom-primary text-uppercase px-3 rounded-1 font-size-lg">4 - YOUR DETAILS</div>
                                                <div class="section-title bg-custom-primary text-uppercase px-3 rounded-1 font-size-lg">1 - CONFIRM BOOKING</div>
                                                <div class="section-title bg-custom-primary text-uppercase px-3 rounded-1 font-size-lg">1 - ENJOY YOUR TRIP</div>
                                            </div>

                                            <div class="col-md-4">

                                                <div class="card">
                                                    <div class="card-header bg-transparent text-center sidebar-title text-gray-800">
                                                        <b>{{$location->name}}</b>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="text-muted d-flex justify-content-between align-items-center text-uppercase font-size-sm">
                                                            <span>Check In</span>
                                                            <span>Check Out</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center text-uppercase text-lg">
                                                            <span class="text-gray-800"><b>{{date('d M Y')}}</b></span>
                                                            <i class="fal fa-arrow-right fa-fw text-muted"></i>
                                                            <span class="text-gray-800"><b>{{now()->addDays(6)->format('d M Y')}}</b></span>
                                                        </div>
                                                        <div class="text-center mt-2">
                                                            <a href="#" title="" class="btn btn-custom btn-sm text-uppercase py-0">
                                                                change
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end preview -->

                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

@section('scripts')
    <script>
    tippy('.tippy', {
        content: 'Tooltip',
        arrow: true,
    });
    </script>
@endsection
