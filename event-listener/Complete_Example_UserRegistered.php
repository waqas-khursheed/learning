<?php

// ============================================================================
// DOOSRA COMPLETE REAL EXAMPLE — USER REGISTRATION FLOW
// ============================================================================

// Pehla example (Order Placed) mein har cheez ALAG file mein thi.
// Ye DOOSRA example jaan-boojh kar EK FILE mein diya gaya hai —
// taake tumhe PURA FLOW EK NAZAR mein, START SE END TAK dikhe.

// Real Life Scenario:
// User signup form submit karta hai → Account banta hai → Event fire hota hai
// → 3 ALAG kaam automatically hote hain: Welcome Email, Admin Notification, Referral Bonus


// ============================================================================
// STEP 1 — CONTROLLER (jahan se sab shuru hota hai)
// ============================================================================

namespace App\Http\Controllers;

use App\Events\UserRegistered;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        // ASAL "EVENT HONA" YAHAN HAI — User database mein bana:
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Ab Laravel ko batao "User Register ho gaya hai":
        UserRegistered::dispatch($user);

        return redirect('/dashboard')->with('message', 'Welcome! Account ban gaya');
    }
}


// ============================================================================
// STEP 2 — EVENT (sirf elaan/announcement, koi logic nahi)
// ============================================================================

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRegistered
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User $user
    ) {}
}


// ============================================================================
// STEP 3 — TEEN ALAG LISTENERS (Ek Event, Teen Independent Kaam)
// ============================================================================

namespace App\Listeners;

use App\Events\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

// LISTENER 1 — Naye user ko welcome email bhejna
class SendWelcomeEmail implements ShouldQueue
{
    public function handle(UserRegistered $event): void
    {
        Mail::to($event->user->email)->send(
            new \App\Mail\WelcomeMail($event->user)
        );
    }
}

// LISTENER 2 — Admin ko notify karna ("naya user aaya hai")
class NotifyAdminOfNewUser implements ShouldQueue
{
    public function handle(UserRegistered $event): void
    {
        $admin = \App\Models\User::where('role', 'admin')->first();

        Notification::send($admin, new \App\Notifications\NewUserJoined($event->user));
    }
}

// LISTENER 3 — Agar koi referral code use hua ho to bonus dena
// (NOT queued — ye turant chalna chahiye, kyunke bonus turant credit hona zaroori hai)
class CreditReferralBonus
{
    public function handle(UserRegistered $event): void
    {
        if ($event->user->referred_by) {
            $referrer = \App\Models\User::find($event->user->referred_by);
            $referrer?->increment('wallet_balance', 500);
        }
    }
}


// ============================================================================
// STEP 4 — REGISTRATION (AppServiceProvider::boot() mein, Laravel 11/12 style)
// ============================================================================

namespace App\Providers;

use App\Events\UserRegistered;
use App\Listeners\CreditReferralBonus;
use App\Listeners\NotifyAdminOfNewUser;
use App\Listeners\SendWelcomeEmail;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(UserRegistered::class, SendWelcomeEmail::class);
        Event::listen(UserRegistered::class, NotifyAdminOfNewUser::class);
        Event::listen(UserRegistered::class, CreditReferralBonus::class);
    }
}


// ============================================================================
// PURA FLOW — EK NAZAR MEIN
// ============================================================================

//   User signup form submit
//         │
//         ▼
//   RegisterController::store()
//         │  User::create([...])  → DB mein save
//         │  UserRegistered::dispatch($user)
//         ▼
//   Event: UserRegistered  ("User register ho gaya" ka elaan)
//         │
//         ├──────────────┬──────────────────┬─────────────────────┐
//         ▼              ▼                  ▼
//   SendWelcomeEmail   NotifyAdminOfNewUser   CreditReferralBonus
//   (queue mein jata)  (queue mein jata)       (turant chalta hai)
//         │              │                     │
//         ▼              ▼                     ▼
//   Email bheja    Admin ko notification    Referrer ko bonus mila
//
//   Controller ne user ko TURANT redirect kar diya — usay in 3 kaamon
//   ka wait NAHI karna para (kyunke 2 listeners queue mein chale gaye)


// ============================================================================
// IS EXAMPLE SE KYA SEEKHA? (REVISION SUMMARY)
// ============================================================================

// 1. Controller ka kaam sirf itna hai: "User banao, Event fire karo, Done."
//    Email, Notification, Bonus — koi bhi logic Controller mein NAHI hai.

// 2. Ek Event, MULTIPLE listeners — sab APNA APNA, INDEPENDENT kaam karte hain.
//    Koi listener ek dusre ke baare mein nahi janta.

// 3. Kuch Listeners QUEUE hote hain (background, fast), kuch NAHI hote
//    (jab turant hona ZAROORI ho, jaise bonus credit).

// 4. Kal agar "SendSmsOnRegister" naya listener add karna ho, bas:
//    a) Naya Listener class banao
//    b) AppServiceProvider mein Event::listen(...) add karo
//    Controller ka code TOUCH TAK NAHI karna parega.
