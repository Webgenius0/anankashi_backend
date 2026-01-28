<form method="POST" action="{{ route('admin.news.update', $news->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <h5 class="text-center">Edit News</h5>

    <div class="mb-3">
        <label>News Title</label>
        <input type="text" class="form-control" name="news_title"
               value="{{ old('news_title', $news->news_title) }}">
    </div>

    <div class="mb-3">
        <label>Type</label>
        <input type="text" class="form-control" name="type"
               value="{{ old('type', $news->type) }}">
    </div>

    <div class="mb-3">
        <label>Short Description</label>
        <textarea class="form-control summernote" name="short_description">{{ old('short_description', $news->short_description) }}</textarea>
    </div>

    <div class="mb-3">
        <label>Thumbnail</label>
        <input type="file" class="form-control" name="thumbnail">
        @if($news->thumbnail)
            <img src="{{ asset($news->thumbnail) }}" width="120" class="mt-2">
        @endif
    </div>

    <h5 class="mt-4 text-center">News Details</h5>

    <div id="news-details-wrapper">
        @foreach($news->details as $index => $detail)
        <div class="news-detail-row border p-3 mb-3">

            <input type="hidden" name="details[{{ $index }}][id]" value="{{ $detail->id }}">

            <div class="d-flex justify-content-between mb-2">
                <h6>Detail</h6>
                <button type="button" class="btn btn-danger btn-sm remove-detail">Remove</button>
            </div>

            <div class="mb-3">
                <label>Detail Title</label>
                <input type="text" name="details[{{ $index }}][title]" class="form-control"
                       value="{{ $detail->title }}">
            </div>

            <div class="mb-3">
                <label>Description</label>
                <textarea name="details[{{ $index }}][description]" class="form-control summernote">{{ $detail->description }}</textarea>
            </div>

            <div class="mb-3">
                @foreach($detail->images as $img)
                    <img src="{{ asset($img->image) }}" width="100" class="me-2 mb-2">
                @endforeach
            </div>

            <div class="images-wrapper">
                <div class="input-group mb-2">
                    <input type="file" name="details[{{ $index }}][images][]" class="form-control">
                    <button type="button" class="btn btn-success add-image">+</button>
                </div>
            </div>

        </div>
        @endforeach
    </div>

    <button type="button" class="btn btn-secondary mb-3" id="add-detail">
        Add News Detail
    </button>

    <div>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>

<script>
let detailIndex = {{ $news->details->count() }};

$('#add-detail').click(function () {
    let html = `
    <div class="news-detail-row border p-3 mb-3">
        <div class="d-flex justify-content-between mb-2">
            <h6>Detail</h6>
            <button type="button" class="btn btn-danger btn-sm remove-detail">Remove</button>
        </div>

        <div class="mb-3">
            <label>Detail Title</label>
            <input type="text" name="details[${detailIndex}][title]" class="form-control">
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="details[${detailIndex}][description]" class="form-control"></textarea>
        </div>

        <div class="images-wrapper">
            <div class="input-group mb-2">
                <input type="file" name="details[${detailIndex}][images][]" class="form-control">
                <button type="button" class="btn btn-success add-image">+</button>
            </div>
        </div>
    </div>`;
    $('#news-details-wrapper').append(html);
    detailIndex++;
});

$(document).on('click', '.remove-detail', function () {
    $(this).closest('.news-detail-row').remove();
});

$(document).on('click', '.add-image', function () {
    let name = $(this).prev('input').attr('name');
    let html = `
        <div class="input-group mb-2">
            <input type="file" name="${name}" class="form-control">
            <button type="button" class="btn btn-danger remove-image">-</button>
        </div>`;
    $(this).closest('.images-wrapper').append(html);
});

$(document).on('click', '.remove-image', function () {
    $(this).closest('.input-group').remove();
});
</script>
