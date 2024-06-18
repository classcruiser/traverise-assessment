<div class="card-body">
    <h4>Room pictures</h4>
    
    <div class="form-group row">
        <label class="col-form-label col-lg-2">Drop pictures to the grey box to upload</label>
        <div class="col-lg-10">
            <form method="post" action="{{ route('tenant.rooms.upload', [ 'id' => $room->id ]) }}" enctype="multipart/form-data" class="dropzone-upload">
                @csrf
            </form>

            <div class="mt-4">
                <p><b>CURRENT PICTURE</b></p>
            </div>
            @if (!$picture)
                <em>No picture uploaded yet</em>
            @else
                <img src="{{ $picture }}?{{ time() }}" alt="" class="d-block w-100" />
            @endif

            <br />
            <em>Dimension: 800px (W) x 500px (H). File size: under 500 KB. Extension: JPG only</em>

            <div class="mt-4">
                <p><b>OTHER PICTURES</b></p>

                <div class="backend-gallery">
                    @forelse ($gallery_files as $file)
                        <div class="backend-gallery-file">
                            <a href="{{ route('tenant.rooms.deletePicture', ['id' => $room->id, 'filename' => $file->getFilename()]) }}" title="" class="confirm-dialog tippy" data-tippy-content="Delete picture" data-text="Delete this picture?">
                                <i class="fa fa-trash fa-fw"></i>
                            </a>
                            <a href="{{ route('tenant.rooms.setMainPicture', ['id' => $room->id, 'filename' => $file->getFilename()]) }}" title="" class="tippy" data-tippy-content="Set as main picture">
                                <i class="fa fa-thumbtack fa-fw {{ $room->featured_image == $file->getFilename() ? 'text-danger' : '' }}"></i>
                            </a>
                            <img src="{{ asset('images/rooms/'. $room->id .'/'. $file->getfilename()) }}" alt="" class="d-block w-100 h-100 object-contain" />
                        </div>
                    @empty
                        <em>No other pictures uploaded yet</em>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>