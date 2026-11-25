<?php
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PhotoController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/photos');
Route::get('/photos', [PhotoController::class, 'index'])->name('photos.index');
Route::get('/photos/create', [PhotoController::class, 'create'])->name('photos.create');
Route::get('/photos/{photo}/image', [PhotoController::class, 'image'])->name('photos.image');
Route::post('/photos', [PhotoController::class, 'store'])->name('photos.store');
Route::delete('/photos/{photo}', [PhotoController::class, 'destroy'])->name('photos.destroy');
// WEEK 14 - Supabase API Connection Test

Route::get('/week14/notices', function () {

    $response = Http::withHeaders([
        'apikey' => config('services.supabase.key'),
        'Accept' => 'application/json',
    ])->get(
        rtrim(config('services.supabase.url'), '/') . '/rest/v1/notices',
        [
            'select' => 'id,title,body,is_published,user_id,created_at',
            'is_published' => 'eq.true',
            'order' => 'created_at.desc',
        ]
    );

    $response->throw();

    return response()->json([
        'success' => true,
        'notices' => $response->json(),
    ]);
});

Route::get('/week14/database-test', function () {

    $result = DB::connection('pgsql')->selectOne(
        'SELECT current_database() AS database_name, NOW() AS server_time'
    );

    return response()->json([
        'success' => true,
        'database_name' => $result->database_name,
        'server_time' => $result->server_time,
    ]);
});