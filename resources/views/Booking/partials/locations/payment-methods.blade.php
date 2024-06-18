<div class="card-body">
    <h4>Payment Methods</h4>

    <form action="javascript:" method="post" id="bank-form">
        <div class="custom-control custom-checkbox">
            <input type="checkbox" name="bank_transfer" class="custom-control-input" {{$location->bank_transfer ? 'checked' : ''}} id="form-bank" toggler data-target="#bank-transfer-text">
            <label class="custom-control-label" for="form-bank">Enable bank transfer payment</label>
        </div>
        <div class="mt-3 {{$location->bank_transfer ? '' : 'hidden'}}" id="bank-transfer-text">
            <label>Bank Transfer instructions</label>
            <textarea name="bank_transfer_text" class="frl form-control">{!! $location->bank_transfer_text !!}</textarea>
        </div>
    </form>
</div>

@can('edit camp')
    <div class="card-body">
        <div class="text-right">
            @csrf
            <input type="hidden" name="room_id" id="camp_id" value="{{$location->id}}" />
            <button class="btn bg-danger update-bank-transfer">Update</button>
        </div>
    </div>
@endcan