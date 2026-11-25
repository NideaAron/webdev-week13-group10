<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhotoRequest;
use App\Models\Photo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function index()
    {
        $photos = Photo::latest()->get();

        return view('photos.index', compact('photos'));
    }

    public function create()
    {
        return view('photos.create');
    }

    public function store(StorePhotoRequest $request)
    {
        $file = $request->file('photo');
        $originalName = $file->getClientOriginalName();
        $size = $file->getSize();

        // Generated name, public disk named on purpose
        $path = $file->store('gallery', 'supabase');

        if ($path === false) {
            return back()->withInput()
                ->withErrors(['photo' => 'The file could not be saved. Please try again.']);
        }

        try {
            Photo::create([
                'caption'       => $request->validated('caption'),
                'path'          => $path,
                'original_name' => $originalName,
                'size'          => $size,
            ]);
        } catch (\Throwable $e) {
            // Do not leave a file with no row
            Storage::disk('supabase')->delete($path);
            throw $e;
        }

        return redirect()->route('photos.index')->with('status', 'Photo uploaded.');
    }
public function image(Photo $photo)
{
    $disk = Storage::disk('supabase');

    abort_unless($disk->exists($photo->path), 404);

    return response($disk->get($photo->path))
        ->header(
            'Content-Type',
            $disk->mimeType($photo->path) ?? 'application/octet-stream'
        )
        ->header('Cache-Control', 'private, max-age=3600');
}
    public function destroy(Photo $photo)
    {
        try {
            DB::transaction(function () use ($photo) {
                $photo->delete();

                if (! Storage::disk('supabase')->delete($photo->path)) {
                    throw new \RuntimeException('File could not be deleted.');
                }
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['delete' => 'The photo could not be deleted.']);
        }

        return redirect()->route('photos.index')->with('status', 'Photo deleted.');
    }
}