@extends('layouts.app')

@section('title', 'Gallery')

@section('content')
    <div class="page-head">
        <div>
            <h1>Photo Gallery</h1>
            <p>{{ $photos->count() }} {{ Str::plural('photo', $photos->count()) }}, newest first</p>
        </div>
        <a href="{{ route('photos.create') }}" class="btn btn-primary">+ Upload photo</a>
    </div>

    @if ($photos->isEmpty())
        <div class="empty">
            <h2>No photos yet</h2>
            <p>Your gallery is empty. Upload your first photo to get started.</p>
            <a href="{{ route('photos.create') }}" class="btn btn-primary">Upload a photo</a>
        </div>
    @else
        <div class="masonry">
            @foreach ($photos as $photo)
                <figure class="card">
                    <button type="button" class="open"
                            data-full="{{ $photo->url }}"
                            data-caption="{{ $photo->caption }}">
                        <img src="{{ $photo->url }}" alt="{{ $photo->caption }}" loading="lazy">
                    </button>

                    <figcaption class="overlay">
                        <h3>{{ $photo->caption }}</h3>
                        <p class="meta">
                            {{ $photo->original_name }} · {{ \Illuminate\Support\Number::fileSize($photo->size) }}
                            · {{ $photo->created_at->diffForHumans() }}
                        </p>
                        <form action="{{ route('photos.destroy', $photo) }}" method="POST"
                              onsubmit="return confirm('Delete this photo?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </figcaption>
                </figure>
            @endforeach
        </div>

        <dialog id="lightbox" class="lightbox">
            <button type="button" class="close" id="lightbox-close" aria-label="Close">&times;</button>
            <figure>
                <img id="lightbox-img" alt="">
                <figcaption id="lightbox-cap"></figcaption>
            </figure>
        </dialog>
    @endif
@endsection

@push('scripts')
<script>
    const box = document.getElementById('lightbox');
    if (box) {
        const img = document.getElementById('lightbox-img');
        const cap = document.getElementById('lightbox-cap');

        document.querySelectorAll('.open').forEach(btn => {
            btn.addEventListener('click', () => {
                img.src = btn.dataset.full;
                img.alt = btn.dataset.caption;
                cap.textContent = btn.dataset.caption;
                box.showModal();
            });
        });

        box.addEventListener('click', e => {
            if (e.target === box || e.target.id === 'lightbox-close') box.close();
        });
    }
</script>
@endpush