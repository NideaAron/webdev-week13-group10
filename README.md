
# Week 13 – Photo Gallery (Supabase Storage)

A Laravel Photo Gallery application that allows users to upload images with captions, display photos, and delete uploaded files. This project uses Laravel, Supabase Storage, and PostgreSQL.

**Group:** Group 10

## What It Does

- Uploads an image with a caption.
- Accepts real JPG, JPEG, and PNG images only.
- Limits uploaded images to 2 MB.
- Stores images in a private Supabase Storage bucket named `gallery`.
- Saves the caption, file path, original filename, and file size in the PostgreSQL `photos` table.
- Displays private images through Laravel.
- Deletes both the database record and the corresponding Storage file.

## Storage Configuration

- **Bucket Name:** `gallery`
- **Bucket Visibility:** Private
- **Maximum File Size:** 2 MB
- **Allowed MIME Types:** `image/png` and `image/jpeg`
- **Storage Provider:** Supabase Storage
- **Database:** PostgreSQL

## Storage Policies

The following policies were configured for the `gallery` bucket:

1. **Upload Policy** – Authenticated users can upload files only inside their own UUID folder.
2. **View Policy** – Authenticated users can view only files inside their own folder.
3. **Delete Policy** – Authenticated users can delete only files inside their own folder.

The policies also restrict filenames to `.png`, `.jpg`, and `.jpeg` extensions. Anonymous visitors are not allowed to access the private bucket.

## How to Run

Install the project dependencies:

```bash
composer install
```

Create your environment file:

```bash
copy .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Configure your Supabase PostgreSQL database and Supabase Storage credentials in your `.env` file.

The Supabase Storage configuration uses these environment variables:

```env
SUPABASE_S3_ENDPOINT=
SUPABASE_S3_REGION=
SUPABASE_S3_ACCESS_KEY_ID=
SUPABASE_S3_SECRET_ACCESS_KEY=
SUPABASE_S3_BUCKET=gallery
```

Run the database migrations:

```bash
php artisan migrate
```

Start the Laravel development server:

```bash
php artisan serve
```

Open the application:

[Open Photo Gallery](http://127.0.0.1:8000/photos)

**Note:** Do not upload your `.env` file or expose your database password and Supabase credentials in GitHub.

## Routes

| Method | URL | Purpose |
|---|---|---|
| GET | `/photos` | Display gallery |
| GET | `/photos/create` | Display upload form |
| GET | `/photos/{photo}/image` | Display private image |
| POST | `/photos` | Upload a photo |
| DELETE | `/photos/{photo}` | Delete a photo and its file |

## Questions

### 1. What is the difference between a Storage Bucket and Storage Policies?

A Storage Bucket is where uploaded files are stored. In this activity, the `gallery` bucket stores the uploaded images.

Storage Policies control who can upload, view, and delete files. They help protect files by allowing only authorized users to access their own files.

### 2. Why can Laravel upload files to the `gallery/` folder even if no policy mentions that folder?

Laravel uses server-side Supabase S3 credentials configured in the `.env` file. These credentials have privileged access to Storage and can bypass the user-level Row Level Security policies.

That is why Laravel can upload files to the `gallery/` folder even though the authenticated-user policies require UUID-based folders.

### 3. Why should we not use `DELETE FROM storage.objects` to delete files?

Supabase Storage manages the actual files and their metadata. Deleting records directly from `storage.objects` using SQL may leave the actual files behind or cause inconsistencies.

We should use the Supabase Storage API or Laravel Storage methods to properly delete files.

### 4. Why do we keep the image path in PostgreSQL and not the image itself?

PostgreSQL is good for storing organized information such as captions, file paths, and file sizes. Image files are better stored in Supabase Storage because they can be large.

The database only stores the file path, which Laravel uses to retrieve and display the image.

### 5. Why do we not use the original filename to store the file?

The original filename may be duplicated or contain spaces and special characters. Using generated filenames helps prevent conflicts and makes file storage safer.

We still save the original filename in the `original_name` column for reference.

### 6. What would be left behind if we deleted only the database row?

The actual image file would remain in Supabase Storage without a database record pointing to it. This is called an orphan file.

That is why our Laravel delete function removes both the database record and the stored image.

## Testing Results

| Test | Result |
|---|---|
| Valid JPG/PNG upload | Successful |
| Wrong file type | Rejected |
| Image larger than 2 MB | Rejected |
| Display private image | Successful |
| Delete uploaded image | Successful |
| Storage file after deletion | Removed |
| Database record after deletion | Removed |

---

# Week 14 - Supabase API and Database Connection Testing

This activity demonstrates how Laravel connects to Supabase through the Data API and a direct PostgreSQL connection.

## Database Configuration

The `notices` table contains the following fields:

- `id` - Unique notice ID
- `title` - Notice title (5 to 100 characters)
- `body` - Notice content
- `is_published` - Publication status
- `user_id` - Owner of the notice
- `created_at` - Date and time created

Row Level Security (RLS) is enabled on the table.

## Database Grants and Policies

The following permissions were configured:

| Role | SELECT | INSERT | UPDATE | DELETE |
|---|---|---|---|---|
| anon | Yes | No | No | No |
| authenticated | Yes | Yes | No | No |

The following RLS policies were applied:

1. Visitors can read published notices only.
2. Authenticated users can read published notices and their own unpublished notices.
3. Authenticated users can insert notices only using their own user ID.

A database constraint also requires notice titles to contain between 5 and 100 characters.

## REST API Testing Results

| Test | HTTP Status | Result |
|---|---:|---|
| Visitor reads notices | 200 | Successful |
| Filtered and sorted read | 200 | Successful |
| Visitor attempts to insert | 401 | Blocked - missing INSERT grant |
| User inserts own notice | 201 | Successful |
| User inserts using another user's ID | 403 | Blocked - RLS policy |
| User inserts a title shorter than 5 characters | 400 | Blocked - database constraint |
| Authenticated user attempts DELETE | 403 | Blocked - missing DELETE grant |

## Laravel Connection Testing

Two Laravel routes were created:

| Method | Route | Purpose |
|---|---|---|
| GET | `/week14/notices` | Retrieves published notices through the Supabase Data API |
| GET | `/week14/database-test` | Tests the direct PostgreSQL connection |

The Data API route uses the Supabase URL and publishable key from environment configuration. It retrieves published notices and reports API errors.

The direct PostgreSQL route uses Laravel's configured PostgreSQL connection through the Supabase Session Pooler. It returns the database name and server time. SSL is required for the connection.

## Environment Configuration

The following environment variables are used:

```env
SUPABASE_URL=
SUPABASE_PUBLISHABLE_KEY=
DB_SSLMODE=require
```

Actual credentials are stored in the private `.env` file and are not included in the repository. The `.env.example` file contains placeholders instead of actual credentials.

## Questions

### 1. What is the difference between a GRANT, an RLS policy, and a database constraint?

GRANT controls which database actions a role is allowed to perform, such as SELECT and INSERT. RLS policies control which rows a user can access or insert. Database constraints enforce rules on the data itself, such as requiring a title to have between 5 and 100 characters.

### 2. Why can a visitor read only published notices while a direct PostgreSQL connection can see more?

The visitor uses the Supabase Data API with the anonymous role, so the configured grants and RLS policies restrict the returned rows to published notices.

The direct PostgreSQL connection uses the configured server-side database credentials for the privileged `postgres` database role, not the visitor's Supabase Auth identity. This connection has different database privileges from the visitor's API access and can access data beyond what the visitor can read through the Data API.

### 3. What type of direct database connection does Laravel use, and why?

Laravel uses a direct PostgreSQL connection through the Supabase Session Pooler. It is useful for server-side database operations and SQL queries. SSL is required to encrypt the database connection.

## What I Learned

I learned how database grants, RLS policies, and constraints work together to protect data. I also learned how to test Supabase REST API requests using different user roles and understand why some requests are blocked. One confusing part was the difference between accessing data through the Data API and connecting directly to PostgreSQL. I would like to explore more about authentication, database permissions, and secure API development.
