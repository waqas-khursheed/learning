<?php

/**
 * ============================================================================
 *                    AWS S3 — MUKAMMAL GUIDE
 *          Laravel ke sath S3 Bucket Setup aur Istemal
 * ============================================================================
 */


// =============================================================================
// 1. S3 KYA HAI?
// =============================================================================

/*
 * S3 = Simple Storage Service
 *
 * S3 AWS ki cloud storage service hai jahan aap files rakh sakte hain.
 *
 * AASAN MISAAL:
 * ─────────────
 *   Google Drive = Personal files ke liye cloud storage
 *   AWS S3       = Applications ke liye cloud storage (images, videos, backups, etc.)
 *
 * KHAS BAATEIN:
 *   - 99.999999999% durability (11 nines — data KABHI nahi khota)
 *   - Unlimited storage (jitna chahein rakhho)
 *   - Pay per use (sirf utna do jitna use karo)
 *   - Globally accessible (duniya mein kahin se bhi access karo)
 *   - CDN ke sath integrate (CloudFront)
 *
 *
 * S3 KI BUNIYADI ISTILAHAAT:
 * ──────────────────────────
 *   Bucket  = Folder (top-level container — jaise ek drawer)
 *   Object  = File (koi bhi file — image, video, PDF, backup)
 *   Key     = File ka path/name (maslan: "uploads/images/photo.jpg")
 *   Region  = Jahan data physically stored hai
 */


// =============================================================================
// 2. S3 BUCKET BANANA
// =============================================================================

/*
 * QADAM 1: AWS Console → S3 → "Create bucket"
 *
 * QADAM 2: Configure karein:
 *
 *   Bucket name: my-laravel-app-storage
 *   ⚠️ Bucket name GLOBALLY UNIQUE hona chahiye (duniya mein kisi ka bhi same nahi)
 *   ⚠️ Sirf lowercase, numbers, hyphens — spaces ya uppercase NAHI
 *
 *   Region: ap-south-1 (Mumbai) — apne server ke qareeb
 *
 *
 * QADAM 3: Access Settings:
 *
 *   ┌──────────────────────────────────────────────────────────────────┐
 *   │  Setting                          │  Kya Karein                  │
 *   ├──────────────────────────────────────────────────────────────────┤
 *   │  Block ALL public access          │  ✅ ON rakhein (Default)     │
 *   │  Bucket Versioning                │  Enable (files ka version)   │
 *   │  Default encryption               │  SSE-S3 (Amazon managed)     │
 *   └──────────────────────────────────────────────────────────────────┘
 *
 *
 * BUCKET KI QISMAIN:
 * ──────────────────
 *
 * 1) PRIVATE BUCKET (Sab se aam — TAVSIYA):
 *    - By default sab buckets private hain
 *    - Sirf aap ki application access kar sakti hai
 *    - User uploads, backups, sensitive files ke liye
 *    - Access: IAM Roles ya Signed URLs se
 *
 * 2) PUBLIC BUCKET:
 *    - Duniya mein koi bhi access kar sakta hai
 *    - Static website hosting ke liye
 *    - Public assets (CSS, JS, images) ke liye
 *    - ⚠️ BOHOT EHTIYAT SE USE KAREIN — data leak ho sakta hai
 *
 * 3) WEBSITE HOSTING BUCKET:
 *    - Static websites host karne ke liye
 *    - React/Vue SPA deploy karne ke liye
 *    - CloudFront ke sath use hota hai
 */


// =============================================================================
// 3. S3 STORAGE CLASSES (Qeemat ke Hisab se)
// =============================================================================

/*
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │  Storage Class          │  Kab Use Karein              │  Qeemat       │
 * ├─────────────────────────────────────────────────────────────────────────┤
 * │  S3 Standard            │  Aksar access hone wali files│  $$$$         │
 * │  S3 Intelligent-Tiering │  Access pattern na pata ho   │  $$$          │
 * │  S3 Standard-IA         │  Kabhi kabhar access         │  $$           │
 * │  S3 One Zone-IA         │  Kam ahem, kabhi kabhar      │  $            │
 * │  S3 Glacier Instant     │  Archive, foran chahiye      │  $            │
 * │  S3 Glacier Flexible    │  Archive, 3-5 ghante baad    │  ¢            │
 * │  S3 Glacier Deep Archive│  Saalon ka archive           │  ¢¢           │
 * └─────────────────────────────────────────────────────────────────────────┘
 *
 * LARAVEL KE LIYE:
 *   - User uploads (images, docs)     → S3 Standard
 *   - Purane invoices/receipts        → S3 Standard-IA
 *   - Database backups (monthly)      → S3 Glacier Flexible
 *   - Legal documents (saalon rakhne) → S3 Glacier Deep Archive
 *
 *
 * LIFECYCLE RULES:
 * ────────────────
 * Khud-ba-khud files ko sasta storage mein le jao:
 *
 *   Rule 1: 30 din baad → Standard se Standard-IA mein
 *   Rule 2: 90 din baad → Standard-IA se Glacier mein
 *   Rule 3: 365 din baad → Glacier se Deep Archive mein
 *   Rule 4: 7 saal baad → Delete kar do
 *
 *   AWS Console → S3 → Bucket → Management → Lifecycle rules
 */


// =============================================================================
// 4. IAM USER BANANA S3 KE LIYE
// =============================================================================

/*
 * ⚠️ AWS root account kabhi application mein mat use karo!
 *    Alag IAM user banayen sirf S3 access ke liye.
 *
 * QADAM 1: AWS Console → IAM → Users → "Create user"
 *
 * QADAM 2: User details:
 *   - Username: laravel-s3-user
 *   - Access type: Programmatic access (API key milega)
 *
 * QADAM 3: Policy attach karein:
 *
 *   Option A: AWS managed policy:
 *   - AmazonS3FullAccess (poora S3 access — testing ke liye)
 *
 *   Option B: Custom policy (TAVSIYA — sirf zaruri permission):
 *
 *   {
 *       "Version": "2012-10-17",
 *       "Statement": [
 *           {
 *               "Effect": "Allow",
 *               "Action": [
 *                   "s3:PutObject",
 *                   "s3:GetObject",
 *                   "s3:DeleteObject",
 *                   "s3:ListBucket"
 *               ],
 *               "Resource": [
 *                   "arn:aws:s3:::my-laravel-app-storage",
 *                   "arn:aws:s3:::my-laravel-app-storage/*"
 *               ]
 *           }
 *       ]
 *   }
 *
 * QADAM 4: Access Key haasil karein:
 *   - Access Key ID: AKIA...
 *   - Secret Access Key: wJalrX...
 *   - ⚠️ Secret key sirf EK BAAR dikhegi! Mehfooz rakhein!
 *   - ⚠️ Git mein KABHI commit mat karo!
 *
 *
 * EC2 KE LIYE BEHTAR TAREEQA — IAM ROLE:
 * ───────────────────────────────────────
 *   - EC2 par IAM Role attach karo
 *   - Access keys ki zaroorat NAHI rehti
 *   - Zyada mehfooz — keys leak hone ka khatra nahi
 *
 *   1. IAM → Roles → Create role
 *   2. AWS Service → EC2 select karo
 *   3. AmazonS3FullAccess (ya custom policy) attach karo
 *   4. EC2 instance → Actions → Security → Modify IAM role
 *   5. Apna role select karo
 *
 *   Ab Laravel .env mein AWS_ACCESS_KEY_ID aur AWS_SECRET_ACCESS_KEY
 *   ki zaroorat NAHI — SDK khud-ba-khud role se credentials lega!
 */


// =============================================================================
// 5. LARAVEL MEIN S3 SETUP
// =============================================================================

/*
 * QADAM 1: Package install karo:
 *
 *   composer require league/flysystem-aws-s3-v3 "^3.0"
 */

// QADAM 2: .env mein add karo:
/*
 *   AWS_ACCESS_KEY_ID=AKIA...your-key
 *   AWS_SECRET_ACCESS_KEY=wJalrX...your-secret
 *   AWS_DEFAULT_REGION=ap-south-1
 *   AWS_BUCKET=my-laravel-app-storage
 *   AWS_URL=https://my-laravel-app-storage.s3.ap-south-1.amazonaws.com
 *   AWS_USE_PATH_STYLE_ENDPOINT=false
 */

// QADAM 3: config/filesystems.php mein dekho (pehle se hota hai):
$s3Config = [
    's3' => [
        'driver' => 's3',
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION'),
        'bucket' => env('AWS_BUCKET'),
        'url'    => env('AWS_URL'),
        'endpoint' => env('AWS_ENDPOINT'),
        'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
        'throw' => false,
    ],
];


// =============================================================================
// 6. LARAVEL MEIN S3 USE KARNA (Code Examples)
// =============================================================================

use Illuminate\Support\Facades\Storage;

// ── FILE UPLOAD KARNA ──

// Controller mein file upload:
class FileUploadController extends Controller
{
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        // S3 par upload karo — "uploads/images" folder mein
        $path = $request->file('image')->store('uploads/images', 's3');

        // $path = "uploads/images/abc123xyz.jpg"

        // URL haasil karo
        $url = Storage::disk('s3')->url($path);

        // Database mein path save karo
        $user = auth()->user();
        $user->avatar = $path;
        $user->save();

        return response()->json([
            'message' => 'Image kamyabi se upload ho gayi',
            'path' => $path,
            'url' => $url,
        ]);
    }
}


// ── CUSTOM NAAM SE UPLOAD ──

// Apni marzi ka naam aur folder
$path = $request->file('document')->storeAs(
    'uploads/documents/' . auth()->id(),   // Folder: uploads/documents/USER_ID/
    'contract-2024.pdf',                    // File name
    's3'                                    // Disk
);


// ── FILE PADHNA / DOWNLOAD KARNA ──

// File maujood hai ya nahi
$exists = Storage::disk('s3')->exists('uploads/images/photo.jpg');

// File content padhna
$content = Storage::disk('s3')->get('uploads/documents/report.pdf');

// File download response
return Storage::disk('s3')->download('uploads/documents/report.pdf', 'my-report.pdf');


// ── FILE DELETE KARNA ──

Storage::disk('s3')->delete('uploads/images/old-photo.jpg');

// Kai files ek sath delete
Storage::disk('s3')->delete([
    'uploads/images/photo1.jpg',
    'uploads/images/photo2.jpg',
]);


// ── TEMPORARY URL (SIGNED URL) — Private files ke liye ──

// URL jo 30 minute baad expire ho jaye
$tempUrl = Storage::disk('s3')->temporaryUrl(
    'uploads/private/secret-doc.pdf',
    now()->addMinutes(30)
);
// Yeh URL sirf 30 minute tak kaam karega — uske baad 403 error


// ── FOLDER KI SAARI FILES ──

$files = Storage::disk('s3')->files('uploads/images');        // Sirf is folder ki
$allFiles = Storage::disk('s3')->allFiles('uploads');         // Sub-folders samet

$directories = Storage::disk('s3')->directories('uploads');   // Sub-folders ki list


// ── FILE COPY / MOVE ──

Storage::disk('s3')->copy('old/path/file.jpg', 'new/path/file.jpg');
Storage::disk('s3')->move('temp/upload.jpg', 'permanent/photo.jpg');


// ── FILE VISIBILITY (Public / Private) ──

// Public banana (koi bhi URL se access kar sake)
Storage::disk('s3')->setVisibility('uploads/images/photo.jpg', 'public');

// Private banana (sirf signed URL se access)
Storage::disk('s3')->setVisibility('uploads/images/photo.jpg', 'private');


// =============================================================================
// 7. PRACTICAL EXAMPLE: USER AVATAR UPLOAD SYSTEM
// =============================================================================

class AvatarController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = auth()->user();

        // Purana avatar delete karo (agar hai)
        if ($user->avatar) {
            Storage::disk('s3')->delete($user->avatar);
        }

        // Naya avatar upload karo
        $path = $request->file('avatar')->store(
            'avatars/' . $user->id,
            's3'
        );

        // Public banayen taake image directly load ho sake
        Storage::disk('s3')->setVisibility($path, 'public');

        $user->update(['avatar' => $path]);

        return response()->json([
            'message' => 'Avatar update ho gaya',
            'avatar_url' => Storage::disk('s3')->url($path),
        ]);
    }

    public function getAvatarUrl(User $user): string
    {
        if (!$user->avatar) {
            return asset('images/default-avatar.png');
        }

        // Private file ke liye temporary URL
        return Storage::disk('s3')->temporaryUrl(
            $user->avatar,
            now()->addHours(1)
        );
    }
}


// =============================================================================
// 8. S3 BUCKET POLICIES
// =============================================================================

/*
 * PRIVATE BUCKET POLICY (Sirf CloudFront access kare):
 * ────────────────────────────────────────────────────
 *
 *   {
 *       "Version": "2012-10-17",
 *       "Statement": [
 *           {
 *               "Sid": "AllowCloudFrontAccess",
 *               "Effect": "Allow",
 *               "Principal": {
 *                   "Service": "cloudfront.amazonaws.com"
 *               },
 *               "Action": "s3:GetObject",
 *               "Resource": "arn:aws:s3:::my-laravel-app-storage/*",
 *               "Condition": {
 *                   "StringEquals": {
 *                       "AWS:SourceArn": "arn:aws:cloudfront::ACCOUNT_ID:distribution/DIST_ID"
 *                   }
 *               }
 *           }
 *       ]
 *   }
 *
 *
 * CORS CONFIGURATION (Frontend se seedha upload ke liye):
 * ─────────────────────────────────────────────────────
 *   S3 → Bucket → Permissions → CORS
 *
 *   [
 *       {
 *           "AllowedHeaders": ["*"],
 *           "AllowedMethods": ["GET", "PUT", "POST", "DELETE"],
 *           "AllowedOrigins": ["https://yourdomain.com"],
 *           "ExposeHeaders": ["ETag"],
 *           "MaxAgeSeconds": 3600
 *       }
 *   ]
 */


// =============================================================================
// 9. S3 BEST PRACTICES
// =============================================================================

/*
 * ✅ Hamesha private bucket rakhein, CloudFront ya Signed URLs se serve karo
 * ✅ IAM Role use karo EC2 ke liye (access keys se behtar)
 * ✅ Lifecycle rules lagao purani files ko sasta storage mein le jane ke liye
 * ✅ Versioning enable karo (ghalti se delete hone se bacho)
 * ✅ Encryption enable karo (data at-rest encrypted rahe)
 * ✅ File size limit lagao upload par
 * ✅ File type validation karo (sirf allowed formats)
 * ✅ Unique file names use karo (UUID ya timestamp)
 *
 * ❌ S3 bucket public mat rakhho (jab tak zaroorat na ho)
 * ❌ Access keys code ya git mein mat daalein
 * ❌ Root account credentials mat use karo
 * ❌ Bina validation ke koi bhi file upload mat hone do
 */
