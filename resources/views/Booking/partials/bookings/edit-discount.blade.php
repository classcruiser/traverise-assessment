<div id="{{ $modal_id }}" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-grey-800">
                <h5 class="modal-title">Edit Discount</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ route('tenant.bookings.updateDiscount', [ 'ref' => $booking->ref, 'discount_id' => $discount->id ]) }}" class="form-horizontal" method="post">
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">Discount name</label>
                        <div class="col-sm-9">
                            <input type="text" name="name" class="form-control form-control-sm" placeholder="Name / description" value="{{ $discount->name }}"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">Discount type</label>
                        <div class="col-sm-9">
                            <select class="form-control select-no-search" name="discount_type">
                                <option value="Percent" {{ $discount->type == 'Percent' ? 'selected' : '' }}>Percent</option>
                                <option value="Fixed" {{ $discount->type == 'Fixed' ? 'selected' : '' }}>Fixed</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">Apply to</label>
                        <div class="col-sm-9">
                            <select class="form-control select-no-search" name="apply_to">
                                <option value="ROOM" {{ $discount->apply_to == 'ROOM' ? 'selected' : '' }}>Room price only</option>
                                <option value="ALL" {{ $discount->apply_to == 'ALL' ? 'selected' : '' }}>Full price</option>
                            </select>
                            <div class="form-text text-muted">Only if you select Percent discount type</div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">Discount value</label>
                        <div class="col-sm-9">
                            <input type="text" name="discount_value" class="form-control form-control-sm" placeholder="0.0" required style="width: 80px" value="{{ $discount->value }}" />
                        </div>
                    </div>

                </div>

                @can ('edit booking')
                    <div class="modal-footer">
                        @csrf
                        <a href="{{ route('tenant.bookings.removeDiscount', [ 'ref' => $booking->ref, 'discount_id' => $discount->id ]) }}" title="" class="mr-auto btn bg-grey-300 confirm-dialog" data-text="Remove discount ?">Remove Discount</a>
                        <button type="button" class="btn btn-link text-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-danger">Update Discount</button>
                    </div>
                @endif

            </form>
        </div>
    </div>
</div>
