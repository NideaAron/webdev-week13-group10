@extends('layouts.app')

@section('title', 'Upload')

@section('content')
    <div class="form-card">
        <h1>Upload a photo</h1>
        <p class="sub">Add an image and a caption. Both are required.</p>

        <form action="{{ route('photos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="field">
                <label class="lbl" for="caption">Caption</label>
                <input type="text" id="caption" name="caption" value="{{ old('caption') }}"
                       placeholder="Write a caption..." class="@error('caption') is-invalid @enderror">
                @error('caption')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <span class="lbl" style="display:block;font-size:.9rem;font-weight:600;margin-bottom:.45rem;">Image</span>
                <label for="photo" class="dropzone @error('photo') is-invalid @enderror">
                    <img id="preview" alt="Preview" hidden>
                    <strong id="file-name">Click to choose an image</strong>
                    <span id="file-hint">JPG, PNG, GIF or WEBP, up to 2 MB</span>
                    <input type="file" id="photo" name="photo">
                </label>
                @error('photo')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">Upload</button>
                <a href="{{ route('photos.index') }}" class="link-muted">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    const input = document.getElementById('photo');
    const preview = document.getElementById('preview');
    const fileName = document.getElementById('file-name');
    const fileHint = document.getElementById('file-hint');

    input.addEventListener('change', () => {
        const file = input.files[0];
        if (!file) {
            preview.hidden = true;
            fileName.textContent = 'Click to choose an image';
            fileHint.hidden = false;
            return;
        }
        fileName.textContent = file.name;
        fileHint.hidden = true;
        preview.hidden = false;
        preview.src = URL.createObjectURL(file);
    });

    // A fake image (e.g. renamed .txt) cannot be previewed; the server will refuse it.
    preview.addEventListener('error', () => { preview.hidden = true; });
</script>
@endpush