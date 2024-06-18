@extends('Booking.app')

@section('content')

<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="/dashboard" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <span class="breadcrumb-item"><a href="/documents" title="" class="text-grey">Documents</a></span>
            <span class="breadcrumb-item active">Edit Document</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h4 class="card-title">Edit Document</h4>
                    <div class="header-elements">
                        <a href="/documents" title="" class="btn btn-link text-danger">
                            <i class="far fa-angle-left mr-1"></i> Return
                        </a>
                    </div>
                </div>
                <form action="/documents/{{ $document->id }}" method="post">
                    <div class="card-body border-top-1 border-alpha-grey pt-3">
                        @include('Booking.partials.form-messages')
                        @include('Booking.partials.form-errors')
                        <div class="row">
                            <div class="col-sm-4"><h6>Details</h6></div>
                            <div class="col-sm-8">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>* Name</label>
                                            <input type="text" name="name" placeholder="Name" class="form-control" required value="{{ $document->name }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>* Title</label>
                                            <input type="text" name="title" placeholder="Title" class="form-control" required value="{{ $document->title }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Slug</label>
                                            <input type="text" name="slug" placeholder="Leave blank to generate automatically from the document name" class="form-control" value="{{ $document->slug }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>* Location</label>
                                            <select name="position" class="form-control">
                                                <option value="sidebar" {{ $document->position == 'sidebar' ? 'selected' : '' }}>Room - Sidebar</option>
                                                <option value="terms-and-conditions" {{ $document->position == 'terms-and-conditions' ? 'selected' : '' }}>Room - Term and conditions field</option>
                                                <option value="classes-terms-and-conditions" {{ $document->position == 'classes-terms-and-conditions' ? 'selected' : '' }}>Classes - Term and conditions field</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>* Open as</label>
                                            <select name="popup" class="form-control">
                                                <option value="1" {{ boolval($document->popup) ? 'selected' : '' }}>Popup</option>
                                                <option value="0" {{ !boolval($document->popup) ? 'selected' : '' }}>Normal page</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Content</label>
                                            <textarea name="content" class="frl form-control">{!! $document->content !!}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @can ('edit document')
                        <div class="card-body">
                            <div class="text-right">
                                @csrf
                                <input type="hidden" name="id" value="{{ $document->id }}" />
                                <button class="btn bg-danger" type="submit">Update Document</button>
                            </div>
                        </div>
                    @endcan
                </form>
            </div>
        </div>

    </div>
</div>

@endsection


@section('scripts')
<script>
$(document).ready(function() {
    $('textarea.frl').froalaEditor({
        charCounterCount: false,
        toolbarButtons: ['bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript', '|', 'paragraphFormat', 'align', 'formatOL', 'formatUL', 'insertLink', 'insertHR', 'insertTable', 'html'],
        heightMin: 400,
        heightMax: 800
    })
});
</script>
@endsection