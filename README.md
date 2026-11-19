# Week 13 - Photo Gallery (File/Image Upload Module)

A small Laravel app where a person uploads one image with a caption, sees all photos in a gallery (newest first), and can delete a photo. Built with Laravel and PostgreSQL.

**Group:** Group 10

## What it does

- Upload form with one image and a caption (both required)
- Accepts only real images (JPG, PNG, GIF, WEBP) up to 2 MB
- Stores the image on the `public` disk with a generated name
- Saves the caption, path, original file name and size in the `photos` table
- Gallery page shows all photos, newest first, with captions
- Deleting a photo removes both the database row and the file

## How to run

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Create a PostgreSQL database, then set your own database name, username and password in `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=week13_gallery
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Then run:

```bash
php artisan migrate:fresh
php artisan storage:link
php artisan serve
```

Open `http://127.0.0.1:8000/photos`.

Note: `.env` is not included in the submission, and `.env.example` has no real password.

## Routes

| Method | URL | Purpose |
|---|---|---|
| GET | `/photos` | Gallery page |
| GET | `/photos/create` | Upload form |
| POST | `/photos` | Save a new photo |
| DELETE | `/photos/{photo}` | Delete a photo (row and file) |

## Questions

### 1. Why do we keep the path in PostgreSQL and not the file itself?

The database is good at storing small, organized data like the caption, the path and the size, and at searching and sorting it. Image files are big, and putting them inside the table would make the database heavy and slow. So the file stays in the storage folder (`storage/app/public/photos`), and the table only keeps its path. The path tells the app where to find the file when it needs to show the photo on the page.

### 2. Why do we not use the original file name to store the file?

The original name comes from the person's computer, so we can't trust it. Two people can upload files with the same name, like `image.png`, and the second one would overwrite the first. The name can also have spaces, strange symbols or even a dangerous name that causes problems. That's why Laravel generates a random unique name for each file. We still keep the original name in the `original_name` column, so we know what the file was called before.

### 3. What would be left behind if we deleted only the database row?

The image file would stay in the storage folder with nothing pointing to it. It becomes an orphan file: it takes up disk space, the app can't see it in the gallery, and nobody can delete it from the page anymore. Over time these files pile up. That's why our delete code removes both the row and the file, so the folder and the table always agree.