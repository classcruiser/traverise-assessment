@extends('Booking.app')

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i>
                    Home</a>
                <span class="breadcrumb-item">Settings</span>
                <a href="{{ route('tenant.questionnaire') }}" title="" class="breadcrumb-item">Questionnaires</a>
                <span class="breadcrumb-item active">Add new questionnaire</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content" id="questionnaire">
        <div class="content-wrapper container">
            <div class="content">
                <div class="card">
                    <div class="card-header bg-transparent header-elements-inline">
                        <h4 class="card-title">Add questionnaire</h4>
                        <div class="header-elements">
                            <a href="{{ route('tenant.questionnaire') }}" title="" class="btn btn-link text-danger">
                                <i class="far fa-angle-left mr-1"></i> Return
                            </a>
                        </div>
                    </div>

                    <form action="{{ route('tenant.questionnaire.insert') }}" method="post" id="new-questionnaire">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4"><h6>Details</h6></div>
                                <div class="col-sm-8">

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>* Name</label>
                                                <input type="text" name="name" placeholder="Name" class="form-control" value="{{ old('name') }}" required>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>* Title</label>
                                                <input type="text" name="title" placeholder="Title" class="form-control" value="{{ old('title') }}" required>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>* Type</label>
                                                <select class="custom-select"
                                                        name="type_id"
                                                        required
                                                        v-model="selectedType"
                                                >
                                                    <option disabled>Select Questionnaire type
                                                    </option>
                                                    @foreach ($types as $type)
                                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12" id="answers" v-if="selectedTypeName != 'input'">
                                            <button class="btn bg-danger-400 btn-sm rounded mb-2"
                                                    @click.prevent="addAnswer()">
                                                Add Answer <i class="fal fa-comments-question-check ml-1"></i>
                                            </button>
                                            <div class="form-group">
                                                <label>* Answers</label>

                                                <div class="input-group mb-3" v-for="(answer, key) in answers">
                                                    <input type="text" v-model="answers[key]" name="answers[]"
                                                           class="form-control">
                                                    <div class="input-group-append" @click="removeAnswer(key)">
                                                    <span class="input-group-text text-danger">
                                                        <i class="icon-trash"></i>
                                                    </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label>&nbsp;</label>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" name="active"
                                                               class="custom-control-input" id="form-active">
                                                        <label class="custom-control-label"
                                                               for="form-active">Active</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="text-right">
                                    @csrf
                                    <button class="btn bg-danger" type="submit">Submit</button>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    window.types = @json($types);
</script>

<script src="{{ asset('js/questionnaire.js') }}"></script>
@endsection
