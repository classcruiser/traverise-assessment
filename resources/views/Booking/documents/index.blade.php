@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <span class="breadcrumb-item active">Documents</span>
        </div>
        
        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h4 class="card-title"><i class="fal fa-file fa-fw"></i> Documents</h4>
                    <div class="header-elements">
                        @can ('add document')
                            <a href="{{ route('tenant.documents.create') }}" title="" class="btn bg-danger">
                                <i class="far fa-plus mr-1"></i> New Document
                            </a>
                        @endcan
                    </div>
                </div>
                <table class="table table-xs table-compact sortable" data-url="{{ route('tenant.documents.sort') }}">
                    <thead>
                        <tr class="bg-grey-700">
                            <th></th>
                            <th width="30%">Name</th>
                            <th width="30%">Title</th>
                            <th width="20%">Type</th>
                            <th class="text-center">Popup</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($documents as $document)
                        <tr class="bg-white" data-id="{{ $document->id }}">
                            <td class="text-center"><span class="handler cursor-move"><i class="fal fa-bars fa-fw"></i></span></td>
                            <td class="vertical-top">
                                <a href="{{ route('tenant.documents.show', ['id' => $document->id]) }}" class="list-icons-item text-danger"><b>{{ $document->name }}</b></a>
                            </td>
                            <td>{{ $document->title }}</td>
                            <td class="text-uppercase">{{ $document->position }}</td>
                            <td class="text-center"><i class="fa fa-{{ $document->popup ? 'check text-success' : 'times text-danger'  }} fa-fw" /></td>
                            <td class="text-right">
                                <div class="list-icons">
                                    <a href="{{ route('tenant.documents.show', ['id' => $document->id]) }}" class="list-icons-item text-grey"><i class="icon-pencil7"></i></a>
                                    @can ('delete document')
                                        <a href="{{ route('tenant.documents.delete', ['id' => $document->id]) }}" class="list-icons-item text-danger confirm-dialog" data-text="Delete this document?"><i class="icon-trash"></i></a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="5">You have no document</td>
                            </tr>
                        @endforelse
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
    });
</script>
@endsection
