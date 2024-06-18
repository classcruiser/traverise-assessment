<div id="modal_add_multi_pass" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-grey-800">
                <h5 class="modal-title">Add Multi Pass</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ route('tenant.classes.bookings.multi_pass.store', [ 'ref' => $booking->ref ]) }}" class="form-horizontal" method="post">
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">Multi Pass</label>
                        <div class="col-sm-9">
                            <select class="form-control select-no-search" name="multi_pass">
                                <option selected disabled>Select a pass</option>
                                @foreach ($multiPasses as $pass)
                                    <option value="{{ $pass['id'] }}" {{ ($pass['multi_pass']['type'] === 'SESSION' && $pass['remaining'] < $booking->guests->count()) ? 'disabled' : '' }}>{{ $pass['multi_pass']['name'] }} - REMAINING: {{ $pass['remaining_text'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    {!! csrf_field() !!}
                    <button type="button" class="btn btn-link text-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-danger">Add Multi Pass</button>
                </div>
            </form>
        </div>
    </div>
</div>
