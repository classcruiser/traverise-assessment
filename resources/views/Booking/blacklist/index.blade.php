@extends('Booking.app')

@section('content')

@include('Booking.partials.blacklist.add-entry')

@include('Booking.partials.blacklist.edit-entry')

<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
  <div class="d-flex">
    <div class="breadcrumb">
      <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
      <span class="breadcrumb-item">Settings</span>
      <span class="breadcrumb-item active">Blacklist</span>
    </div>

    <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
  </div>
</div>

<div class="page-content">
  <div class="content-wrapper container">
    <div class="content">
      @if (session()->has('messages'))
        <div class="alert bg-green-400 text-white alert-dismissible">
          <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          <i class="fa fa-check-circle mr-1"></i> {{ session('messages') }}
        </div>
      @endif
      <div class="card">
        <div class="card-header header-elements-inline">
          <h4 class="card-title">Blacklist: {{ $blacklist->count() }} entries</h4>
          @can ('add setting')
            <div class="header-elements">
              <a href="#" title="" data-toggle="modal" data-target="#modal-add-blacklist" class="btn bg-danger">
                <i class="far fa-plus mr-1"></i> New Entry
              </a>
            </div>
          @endcan
        </div>
        <table class="table table-xs table-compact">
          <thead>
            <tr class="bg-grey-700">
              <th>Guest Name</th>
              <th>Email</th>
              <th style="width: 400px">Notes</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($blacklist as $list)
              <tr>
                <td><a href="#" data-id="{{ $list->id }}" class="list-icons-item text-danger edit-blacklist"><b>{{ $list->fname }} {{ $list->lname }}</b></a></td>
                <td>{{ $list->email }}</td>
                <td>{{ $list->notes }}</td>
                <td class="text-right">
                  <div class="list-icons">
                    <a href="#" data-id="{{ $list->id }}" class="list-icons-item text-slate edit-blacklist"><i class="icon-pencil7"></i></a>
                    @can ('delete setting')
                      <a href="{{ route('tenant.blacklist.remove', [ 'id' => $list->id ]) }}" class="list-icons-item text-danger confirm-dialog" data-text="Delete this entry?"><i class="icon-trash"></i></a>
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