<div class="card-body">
    <h4>Terms and Conditions Template</h4>

    <form action="javascript:" method="post" id="terms-form">
        <textarea name="terms" class="frl form-control">{!! $location->terms !!}</textarea>
    </form>
</div>

@can ('edit camp')
    <div class="card-body">
        <div class="text-right">
            @csrf
            <input type="hidden" name="room_id" id="camp_id" value="{{ $location->id }}" />
            <button class="btn bg-danger update-terms-template">Update Template</button>
        </div>
    </div>
@endcan
