<?php


// 🔹 Migration Commands
// 1. Run New Migrations
php artisan migrate
// Jo migrations abhi tak run nahi hui, unko run karega.

// 2. Rollback Migrations

php artisan migrate:rollback
// Last batch ke migrations rollback karega (down() run karke).
// Extra options:
// Sirf 1 migration rollback:
php artisan migrate:rollback --step=1
// 2 ya zyada rollback:
php artisan migrate:rollback --step=3

// 3. Reset All Migrations
php artisan migrate:reset
// Sab migrations rollback kar dega (sab tables drop ho jayenge).

// 4. Refresh Migrations

php artisan migrate:refresh

// Sab migrations rollback karega, phir dobara run karega.

// Options:
php artisan migrate:refresh --seed
// Rollback + migrate + database seeding bhi karega.

// 5. Fresh Migrations
php artisan migrate:fresh
// Sab tables drop karke dobara sab migrations run karega.
php artisan migrate:fresh --seed
// Fresh migrate ke saath seeding bhi karega.

// 6. Status Check
php artisan migrate:status

// Dekhne ke liye kaun si migrations run ho chuki hain aur kaun si pending hain.

// 7. Rollback One Migration (Specific)
php artisan migrate:rollback --path=/database/migrations/2025_09_24_000000_create_users_table.php

//  Sirf ek specific migration rollback karega.

// 8. Run One Migration (Specific)
php artisan migrate --path=/database/migrations/2025_09_24_000000_create_users_table.php

//  Sirf ek specific migration run karega.

// 9. Pretend (Dry Run)
php artisan migrate --pretend

// Queries run nahi karega, sirf show karega ke kaun si SQL queries chalengi.

// 10. Fresh Database with Drop Views/Types (PostgreSQL)
php artisan migrate:fresh --drop-views
php artisan migrate:fresh --drop-types

// Views ya custom types bhi drop karega.


// | Command                       | Kaam                             |
// | ----------------------------- | -------------------------------- |
// | `migrate`                     | New migrations run karta hai     |
// | `migrate:rollback`            | Last batch rollback              |
// | `migrate:reset`               | Sab rollback                     |
// | `migrate:refresh`             | Sab rollback + dobara run        |
// | `migrate:fresh`               | Drop all tables + run migrations |
// | `migrate:status`              | Migration status check           |
// | `migrate --path=...`          | Specific migration run           |
// | `migrate:rollback --path=...` | Specific migration rollback      |



🔹 1. php artisan migrate:refresh
// Kaam:
// Pehle sab migrations ka rollback (down()) chalata hai.
// Uske baad unhi migrations ka dobara migrate (up()) chalata hai.

// Example:
php artisan migrate:refresh

// Process:
// rollback all migrations → run all migrations again
// Agar tumhare down() methods sahi likhe hue hain, to tables sahi se delete/revert ho jate hain aur dobara ban jate hain.

// Faida:
// Testing ke liye jab tum check karna chahte ho ke tumhare down() aur up() methods properly kaam kar rahe hain.
// Useful jab tumhe rollback behavior bhi verify karna ho.

🔹 2. php artisan migrate:fresh
// Kaam:
// Database ke sabhi tables ko forcefully drop kar deta hai (chahe down() likha ho ya na ho).
// Phir sab migrations ka up() run karta hai.

// Example:
php artisan migrate:fresh
// Process:
// drop all tables directly → run all migrations again
// Ye down() methods ko completely ignore karta hai.

// Faida:
// Development me fast testing ke liye jab tumhe sirf naya clean database banana ho.
// Agar tumne down() methods likhe hi nahi hain ya galat likhe hain to bhi ye safe hai.
// Clean slate se shuru karne ke liye sabse simple command.

// | Feature                | `refresh` (Rollback + Migrate)  | `fresh` (Drop + Migrate) |
// | ---------------------- | ------------------------------- | ------------------------ |
// | Tables delete kaise?   | `down()` method se rollback     | Direct sab tables drop   |
// | Down() method zaroori? | ✅ Haan                          | ❌ Nahi                   |
// | Speed                  | Thoda slow (rollback + migrate) | Fast (drop + migrate)    |
// | Best use-case          | Jab rollback test karna ho      | Jab clean slate chahiye  |

// Simple Rule of Thumb
// Development me quick reset chahiye → fresh use karo.
// Rollback bhi test karna ho (e.g. production-like behavior) → refresh use karo.



// Scenario:
// tumhare paas users table already hai aur usme naya column phone add karna hai.

// Step 1: migration banao
// command chalao:
// php artisan make:migration add_phone_to_users_table --table=users

// is se ek nayi migration file banegi database/migrations/ folder ke andar.
// Step 2: migration file edit karo
// us file ke andar kuch aisa code likhna hoga:

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }
};

// yahan kya ho raha hai:
// up() → jab migrate chalay ga to users table me ek phone column add ho jayega.
// down() → agar rollback karoge to phone column delete ho jayega.

// Step 3: migrate chalao
php artisan migrate
// ab tumhare users table me phone column aa gaya hoga.

// Summary 
// agar tumhe table me naya column add karna hai to php artisan make:migration add_xxx_to_table --table=tableName chalao
// Schema::table use karke column add karo up() method me
// down() me dropColumn likho taki rollback bhi kaam kare
// migrate command run karo aur column DB me aa jayega



// Laravel Migration Column Types 
// Laravel me migration ke through DB table banate waqt Blueprint $table ka use hota hai. iske andar bohot saare column types available hain.

// 🔹 String / Text Types
$table->string('name', 255);   // varchar(255)
$table->char('code', 10);      // char(10)
$table->text('description');   // TEXT
$table->mediumText('bio');     // MEDIUMTEXT
$table->longText('content');   // LONGTEXT
//  use case: naam, chhoti chhoti strings ke liye string, lambi text ke liye text / longText.

// 🔹 Numbers (Integer / Decimal)
$table->integer('age');                   // int
$table->bigInteger('views');              // bigint
$table->tinyInteger('status');            // tinyint
$table->smallInteger('rank');             // smallint
$table->unsignedInteger('points');        // unsigned int
$table->unsignedBigInteger('likes');      // unsigned bigint
$table->decimal('price', 8, 2);           // decimal(8,2)
$table->float('rating', 3, 2);            // float(3,2)
$table->double('balance', 15, 8);         // double(15,8)


// use case:
// integer = normal number
// bigInteger = bohot bada number (IDs ke liye)
// decimal/float/double = paisa ya percentage ke liye

// 🔹 Boolean
$table->boolean('is_active')->default(true);

// true/false values ke liye

// 🔹 Date / Time Columns
$table->date('birth_date');               // YYYY-MM-DD
$table->dateTime('published_at');         // date + time
$table->timestamp('created_at');          // timestamp
$table->time('start_time');               // HH:MM:SS
$table->year('founded_year');             // YYYY
//  use case: dates, times, logs

// 🔹 JSON
$table->json('meta');         // json field
$table->jsonb('settings');    // jsonb (PostgreSQL)


//  arrays ya object data store karne ke liye

// 🔹 Enum & Set
$table->enum('status', ['pending', 'approved', 'rejected']);
// fixed values ka set

// 🔹 Binary / UUID
$table->binary('file_data');   // binary blob
$table->uuid('uuid');          // unique UUID string

// 🔹 Foreign Keys / Relations
$table->foreignId('user_id')->constrained()->onDelete('cascade');
// same as: unsignedBigInteger + foreign key constraint

// 🔹 Miscellaneous
$table->ipAddress('visitor_ip');      // IP Address
$table->macAddress('device_mac');     // MAC Address
$table->geometry('location');         // GIS data (MySQL/Postgres)
$table->rememberToken();              // 100 char token for auth
$table->timestamps();                 // created_at + updated_at
$table->softDeletes();                // deleted_at (for soft delete)

//  Example Migration
public function up(): void
{
    Schema::create('examples', function (Blueprint $table) {
        $table->id();                                // bigint auto-increment
        $table->string('name');                      // varchar
        $table->text('description')->nullable();     // text
        $table->integer('age')->default(0);          // int
        $table->decimal('price', 8, 2)->nullable();  // decimal
        $table->boolean('is_active')->default(true); // boolean
        $table->date('birth_date')->nullable();      // date
        $table->json('meta')->nullable();            // json
        $table->enum('status', ['draft','live']);    // enum
        $table->timestamps();                        // created_at, updated_at
        $table->softDeletes();                       // deleted_at
    });
}

// Summary 
// string/text → chhoti ya lambi text values
// integer/decimal/float → numbers
// boolean → true/false

// json → array/object store karna
// enum → fixed values
// timestamps/softDeletes → created_at, updated_at, deleted_at

// | Type       | Range (Signed)                  | Range (Unsigned)      |
// | ---------- | ------------------------------- | --------------------- |
// | `TINYINT`  | -128 to 127                     | 0 to 255              |
// | `SMALLINT` | -32,768 to 32,767               | 0 to 65,535           |
// | `INT`      | -2,147,483,648 to 2,147,483,647 | 0 to 4,294,967,295    |
// | `BIGINT`   | -9 quintillion … to +9…         | 0 to 18 quintillion … |

// Simple rule 
// agar column me negative numbers kabhi nahi aane wale (jaise id, views, likes, amount), to unsigned use karo.
// agar kabhi minus ki zarurat hai (jaise balance jo negative bhi ho sakta hai), to normal integer rakho.