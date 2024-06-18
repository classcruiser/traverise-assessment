<form method="post" action="{{ route('tenant.camps.upload', $location->id) }}" enctype="multipart/form-data">
    <div class="card-body">
        <h4>Camp picture</h4>
        
        <div class="form-group row">
            <label class="col-form-label col-lg-2">Select picture</label>
            <div class="col-lg-10">
                <div class="custom-file mb-3">
                    <input type="file" class="" name="file">
                </div>

                <p><b>CURRENT PICTURE</b></p>
                @if (!$picture)
                    <em>No picture uploaded yet</em>
                @else
                    <img src="{{ $picture }}?{{ time() }}" alt="" class="d-block w-100" />
                @endif

                <br />
                <em>Dimension: 800px (W) x 500px (H). File size: under 500 KB. Extension: JPG only</em>
            </div>
        </div>
    </div>
    @can ('edit camp')
        <div class="card-body">
            <div class="text-right">
                @csrf
                <button class="btn bg-danger" type="submit" name="submit">Submit</button>
            </div>
        </div>
    @endcan
</form>
