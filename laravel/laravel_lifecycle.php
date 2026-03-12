<!-- 🚀 Laravel Lifecycle (Request → Response)
Summary

Jab bhi tum Laravel project per request karte ho (jaise button click karna ya URL hit karna), wo request step by step process hoti hai.
Pehle entry point, phir service providers, phir routing aur akhir mai response browser ko wapas chala jata hai.
Is flow ko samajhna developer ki confidence aur control dono barhata hai.

1. Entry Point

Jab user request bhejta hai, sab se pehle wo web server (Apache/Nginx) ko milti hai.

Laravel ka entry point file hota hai public/index.php. Ye Laravel ka front door hai.

Ye file autoloader (Composer ka) load karti hai taake sari classes/files automatically mil saken.

Phir Laravel application ka instance create hota hai (bootstrap/app.php).

Ye instance ek service container hai jo tools aur services hold karta hai (database, routing, queue, etc).

2. HTTP / Console Kernels

Laravel ke 2 kernels hote hain:

HTTP Kernel (jab browser se request aaye).

Console Kernel (jab artisan command chalaye).

Example code:

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);


👉 Breakdown:

$kernel Laravel ka HTTP Kernel banata hai.

Request::capture() current HTTP request ko pakar leta hai (GET/POST, headers, etc).

$kernel->handle($request) request ko middleware, routes aur controllers se guzarta hai aur response generate karta hai.

3. Service Providers

Ye Laravel ka backbone hain.

Saari badi cheezen (database, routing, cache, queue, validation) service providers se bootstrap hoti hain.

Ye list config/app.php mai hoti hai.

Bootstrapping ka process:

Laravel har provider ko instantiate karta hai.

Pehle register() method chalta hai (bindings aur configs register karne ke liye).

Jab sab register ho jate hain to boot() method chalta hai (actual functionality start karne ke liye).

4. Routing

Jab service providers load ho jate hain to request Router ko di jati hai.

Router decide karta hai k request kis controller ya closure route ko jaye gi.

Saath hi saath, middleware lagta hai jo request check karta hai (jaise user login hai ya nahi).

Example:

Agar user login nahi hai → middleware usko login page per redirect karega.

Agar user login hai → route/controller execute hoga.

Phir response middleware se hota hua wapas aata hai.

5. Finishing Up

Controller ka method response return karta hai.

Ye response middleware chain se guzarta hai (agar kuch modification karna ho).

Phir Kernel ka handle() response ko app ko deta hai.

Response ka send() method usko browser mai user ko bhej deta hai.

Aur is tarah Laravel ka poora request-response lifecycle complete hota hai. ✅ -->


┌────────────────────────────────────────────────────────┐
│  1. public/index.php                                   │
│  ───────────────────────────────────────────────────── │
│  → Application bootstrap hoti hai                      │
│  → bootstrap/app.php load hota hai                     │
│  → $app (Illuminate\Foundation\Application) banata hai │
└────────────────────────────────────────────────────────┘
                     │
                     ▼
┌────────────────────────────────────────────────────────┐
│  2. Kernel resolve hota hai                            │
│  ───────────────────────────────────────────────────── │
│  → App\Http\Kernel class load hoti hai                 │
│  → Middleware stack define hota hai (Kernel::$middleware) │
└────────────────────────────────────────────────────────┘
                     │
                     ▼
┌────────────────────────────────────────────────────────┐
│  3. Service Providers Register Phase                   │
│  ───────────────────────────────────────────────────── │
│  → config/app.php se sab providers list hoti hai        │
│  → Har provider ka `register()` call hota hai           │
│     (bindings, singleton, config merge, etc.)           │
│  ❌ Abhi routes, models, middleware active nahi hain     │
└────────────────────────────────────────────────────────┘
                     │
                     ▼
┌────────────────────────────────────────────────────────┐
│  4. Service Providers Boot Phase                       │
│  ───────────────────────────────────────────────────── │
│  → Sab providers ke `boot()` call hote hain             │
│  → Ab sab kuch load ho gaya:                           │
│      ✅ Models available                               │
│      ✅ Observers register ho sakte hain                │
│      ✅ Events & Gates define ho sakte hain             │
│      ✅ Dynamic middleware attach ho sakta hai          │
└────────────────────────────────────────────────────────┘
                     │
                     ▼
┌────────────────────────────────────────────────────────┐
│  5. RouteServiceProvider Boot Phase                    │
│  ───────────────────────────────────────────────────── │
│  → routes/web.php, api.php, console.php load hote hain  │
│  → Route bindings aur model bindings define hote hain   │
│  ✅ Ab Laravel routes ko recognize karta hai             │
└────────────────────────────────────────────────────────┘
                     │
                     ▼
┌────────────────────────────────────────────────────────┐
│  6. Middleware System Ready                            │
│  ───────────────────────────────────────────────────── │
│  → app/Http/Kernel.php ke middleware stack active hota │
│  → Jab request aaye, ye sab middleware sequence mai run │
│    honge (Before → Controller → After)                  │
└────────────────────────────────────────────────────────┘
                     │
                     ▼
┌────────────────────────────────────────────────────────┐
│  7. Controller / Route Action                          │
│  ───────────────────────────────────────────────────── │
│  → Laravel matched route ko controller/action deta hai │
│  → Models yahan load hote hain (lazy-loaded)           │
│  → Business logic chalta hai                           │
└────────────────────────────────────────────────────────┘
                     │
                     ▼
┌────────────────────────────────────────────────────────┐
│  8. Response Pipeline                                  │
│  ───────────────────────────────────────────────────── │
│  → Response middleware se wapas jaata hai              │
│  → Final response browser/user ko milta hai            │
└────────────────────────────────────────────────────────┘


🔹 register() → prepare container
🔹 boot() → runtime setup (observers, routes, events)
🔹 routes → inside boot
🔹 middleware → active after boot
🔹 models → load on demand (lazy)