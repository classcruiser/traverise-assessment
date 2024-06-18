@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <span class="breadcrumb-item active">Questionnaire</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h4 class="card-title">Questionnaires</h4>
                    <div class="header-elements">
                        <a href="{{ route('tenant.questionnaire.create') }}" title="" class="btn bg-danger">
                            <i class="far fa-plus mr-1"></i> New Questionnaire
                        </a>
                    </div>
                </div>
                <table class="table table-xs table-compact sortable" data-url="{{ route('tenant.addons.sort') }}">
                    <thead>
                        <tr class="bg-grey-700">
                            <th></th>
                            <th class="">Name</th>
                            <th class="">Title</th>
                            <th class="">Type</th>
                            <th class="text-center">Active</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($questionnaires as $questionnaire)
                        <tr class="bg-white" data-id="{{ $questionnaire->id }}">
                            <td class="text-center">
                                <span class="handler cursor-move">
                                    <i class="fal fa-bars fa-fw"></i>
                                </span>
                            </td>
                            <td class="vertical-top">
                                <a href="/questionnaires/{{ $questionnaire->id }}" class="list-icons-item text-danger">
                                    <b>{{ $questionnaire->name }}</b>
                                </a>
                            </td>
                            <td>{{ $questionnaire->title }}</td>
                            <td>{{ $questionnaire->type->name }}</td>
                            <td class="text-center">
                                <i class="far fa-fa fa-{{ $questionnaire->active ? 'check text-success' : 'times text-danger' }}"></i>
                            </td>
                            <td class="text-right">
                                <div class="list-icons">
                                    <a href="{{ route('tenant.questionnaire.show', ['questionnaire' => $questionnaire->id]) }}"
                                       class="list-icons-item text-grey"><i class="icon-pencil7"></i></a>
                                    @can ('delete addon')
                                        <a href="{{ route('tenant.questionnaire.remove', ['questionnaire' => $questionnaire->id]) }}"
                                           class="list-icons-item text-danger confirm-dialog"
                                           data-text="Delete this questionnaire?"><i class="icon-trash"></i></a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

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
    })
</script>
@endsection
