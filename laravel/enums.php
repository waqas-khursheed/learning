<?php

// Enum ki Definition

// Enum (Enumeration) ek special data type hai jo ek fixed aur limited set of constant values ko represent karta hai.
// PHP 8.1+ me enums introduce hue jo type-safe aur readable tareeqe se predefined values handle karte hain.

// Example:

enum DiscountType: int {
    case Percentage = 1;
    case Fixed = 2;
}

// 🔹 Enum kab banana chahiye (Scenarios)
// Enum banani chahiye jab:
// Fixed aur predefined values ho (jaise DiscountType: Percentage, Fixed).
// Business rules enforce karne ho (sirf valid options allow karne ke liye).
// Readability aur maintainability improve karni ho (numbers/strings ke bajaye meaningful names use karne ke liye).
// Type safety chahiye ho (invalid value pass karne par error aata hai).
// Code reuse multiple jagah hona ho (Enums ek central place pe defined rehte hain).
// Enum (Enumeration) kya hai?
// Enum ek special data type hai jo ek fixed set of related values ko represent karta hai.
// Laravel 9+ / PHP 8.1 me native enum introduce hua.


enum DiscountType: int
{
    case Percentage = 1;
    case Fixed = 2;
}

// Ab discount type ke liye koi bhi random value (jaise 3, abc) database me nahi jayegi. Sirf 1 ya 2 hi valid hain

// Enum kyu zaroori hai?

// Enums ke faide ye hain:
// Data integrity (valid values enforce karna)
// → Aapko har jagah constant ya string likhne ki zaroorat nahi. Sirf predefined values use hongi.
// Example: DiscountType::Percentage ya DiscountType::Fixed

// Readability aur maintainability
// → Code clear hota hai:

if ($promo->discount_type === DiscountType::Percentage) {
    // clear hai ki Percentage discount hai
}
// vs

if ($promo->discount_type === 1) {
    // 1 ka matlab yaad rakhna padega
}
// Type safety          
// Agar galti se aap koi invalid value pass karo to PHP error throw karega.
// Example:

function setDiscountType(DiscountType $type) { ... }
setDiscountType(3); // ❌ error


// Extra functionality
// → Aap apne enums me methods bana sakte ho, jaise label() jo human readable name deta hai.

// 🔹 Kis scenario me Enum banani chahiye?

// Enums banani chahiye jab:
// Fixed options ho jinke values kabhi bar bar use honge
// Example: DiscountType (Percentage, Fixed)
// Example: OrderStatus (Pending, Paid, Shipped, Cancelled)
// Example: UserRole (Admin, Customer, Vendor)
// Jab DB me numeric ya string values store hoti hain, lekin aapko code me readable aur safe tarike se access karna hai

// DB: 1
// Code: DiscountType::Percentage
// Business rules strict ho
// Jaise payment ke methods → Stripe, Paypal, BankTransfer
// Aap chahte ho ke system me koi aur galti se invalid method na aaye.

// 🔹 Jab Enum na banaye

// Agar values dynamic / frequently changing hain (jaise categories, tags jo admin panel se add hoti hain).

// Agar sirf ek hi jagah chhota sa use case hai aur baar baar use nahi hoga.

// 🔹 Real-world Example

// Without Enum (unsafe)
$promo->discount_type = 1; // yaad rakhna padega ki 1 ka matlab Percentage hai

// With Enum (safe & clear)
$promo->discount_type = DiscountType::Percentage;

// API Response:

{
    "discount_type": {
        "value": 1,
        "name": "Percentage",
        "label": "Percentage"
    }
}
  
// Conclusion:
// Enum un scenarios me zaroori hai jaha:
// Aapke paas limited fixed values hain.
// Aapko code readability aur safety chahiye.
// Aap human readable API response chahte ho.




// Step 1: Enum Banana
// PHP 8.1+ me enum aise banta hai:

<?php

namespace App\Enums\PromoCode;

enum DiscountType: int
{
    case Percentage = 1;
    case Fixed = 2;

    // Extra method to get label
    public function label(): string
    {
        return match ($this) {
            self::Percentage => 'Percentage',
            self::Fixed => 'Fixed',
        };
    }
}


// Isme 2 values fix hain:
// 1 = Percentage
// 2 = Fixed

// Step 2: Model me Cast use karna

// Apne PromoCode model me cast lagao:

class PromoCode extends Model
{
    protected $fillable = [
        'title',
        'promo_code',
        'discount_type',
        'discount_amount',
        'start_date',
        'expiry_date',
    ];

    protected $casts = [
        'discount_type' => \App\Enums\PromoCode\DiscountType::class,
        'start_date'    => 'date',
        'expiry_date'   => 'date',
    ];
}
// Ab jab DB me 1 save hoga to model pe aapko DiscountType::Percentage milega.

// Step 3: Controller me Use

// Aap controller me enum ka use aise karoge:

public function store(Request $request)
{
    $promo = new PromoCode();
    $promo->title = $request->title;
    $promo->promo_code = $request->promo_code;
    $promo->discount_type = DiscountType::from($request->discount_type); // convert int to enum
    $promo->discount_amount = $request->discount_amount;
    $promo->start_date = $request->start_date;
    $promo->expiry_date = $request->expiry_date;
    $promo->save();

    return response()->json([
        'id' => $promo->id,
        'title' => $promo->title,
        'discount_type' => [
            'value' => $promo->discount_type->value, // 1
            'name'  => $promo->discount_type->name,  // "Percentage"
            'label' => $promo->discount_type->label(), // "Percentage"
        ]
    ]);
}


// Step 4: Blade File me Use
// Aap directly blade me enum ke saath kaam kar sakte ho:


{{-- Example 1: Label show karna --}}
<p>Discount Type: {{ $promo->discount_type->label() }}</p>

{{-- Example 2: Check karna --}}
@if($promo->discount_type === \App\Enums\PromoCode\DiscountType::Percentage)
    <p>This is a Percentage discount.</p>
@endif

{{-- Example 3: Dropdown banane ke liye --}}
<select name="discount_type" class="form-select">
    @foreach(\App\Enums\PromoCode\DiscountType::cases() as $type)
        <option value="{{ $type->value }}" {{ $promo->discount_type === $type ? 'selected' : '' }}>
            {{ $type->label() }}
        </option>
    @endforeach
</select>

// Step 5: API Response Example
// Agar aap cast use karte ho aur API return karte ho:

{
    "id": 1,
    "title": "New Year Promo",
    "discount_type": {
        "value": 1,
        "name": "Percentage",
        "label": "Percentage"
    },
    "discount_amount": 20,
    "start_date": "2025-09-25",
    "expiry_date": "2025-09-30"
}

// Summary
// Enum: Fixed values ko define karne ka tareeqa.
// Model Cast: Enum ko DB ke int/string se bind karna.
// Controller: Store & fetch karte waqt enum ka safe use.
// Blade: Enum ka label show karna, comparison karna, dropdown banane ke liye use.
// API Response: Human readable + machine readable data dono milta hai.


// PHP Enum aur Database Enum alag cheezen hain
// PHP Enum
// Ye PHP 8.1 ka language feature hai.
// Ye sirf code ke andar values ko restrict aur readable banata hai.
// Database ko koi farq nahi padta, DB me int ya string hi save hota hai.

// Example:

$promo->discount_type = DiscountType::Percentage; 
// DB me 1 save hoga

// Database Enum (MySQL ENUM type)
// Ye DB ka column type hai.
// Aap directly DB column me allowed values define karte ho.

// Example:
// discount_type ENUM('Percentage','Fixed')

// DB ke level pe hi validation hoti hai.

// 🔹 Laravel me best practice
// Laravel / PHP world me DB me int ya string store karna aur PHP Enum cast use karna zyada flexible aur recommended hai, because:
// Database portability (MySQL/Postgres/SQLite sab support karega)
// Code readability (DiscountType::Percentage)
// Easy changes (Agar kal ko aur cases add karni ho to DB schema change nahi karna padega).

//  Answer
// Agar aap Laravel + PHP enums use kar rahe ho → DB column int/string rakho (jaise tinyint ya varchar).
// DB ENUM type avoid karo, kyunki ye rigid hota hai aur migrate karna mushkil ho jata hai.
// Matlab: aapke case me discount_type ko DB me tinyint rakho, aur PHP me enum DiscountType banake cast use karo.





// 🔹 Scenario
// Aapke paas ek order_types table hai (jaise: Pending, Processing, Completed, Cancelled, etc).
// Aur ek orders table hai jo order_type_id ke zarye us table se related hai.

// 🔹 Option 1: Separate Table (order_types) + Relationship
// order_types
// -------------
// id | name
// 1  | Pending
// 2  | Processing
// 3  | Completed
// 4  | Cancelled

// orders
// -------------
// id | user_id | order_type_id
// 101|   1     | 1
// 102|   2     | 3

//  Pros
// Easily extendable: Admin panel se naye types add ho sakte hain.
// Translation / multi-language support (jaise "Pending" ko urdu/english me show karna).
// Relationship ka benefit (joins, eager loading, etc).

//  Cons
// Har order ke liye join karna padta hai type ke naam ke liye.
// Agar types kabhi nahi badalti, to table thoda overkill ho jata hai.

// 🔹 Option 2: Enum (PHP Enum + int column)
enum OrderStatus: int {
    case Pending = 1;
    case Processing = 2;
    case Completed = 3;
    case Cancelled = 4;
}


// orders table me:

// orders
// -------------
// id | user_id | status (tinyint)
// 101|   1     | 1  -- Pending
// 102|   2     | 3  -- Completed

//  Pros

// Fast & simple (sirf ek int field).
// Fixed values ka clear control.
// No need for extra joins.

// Cons

// Values hard-coded hain, aap dynamically add/change nahi kar sakte.
// Agar kal ko business rule change hua (jaise “On Hold” add karna) → code update + deploy karna padega.

// 🔹 Which one to choose?
//  Decision depends on your business rule:
// Agar order types FIXED hain (Pending, Processing, Completed, Cancelled, aur rarely change honge)
// → PHP Enum + int column best hai.
// Agar order types DYNAMIC hain (Admin nayi type bana sakta hai, multi-language support chahiye, ya frequently change hoti hain)
// → Separate order_types table best hai.

// 🔹 Rule of Thumb

// Fixed values → Enum

// Dynamic values → Separate table (relationship)

//  Aapke scenario me agar order ka status hai (Pending, Processing, Completed, Cancelled) → ye values business logic me fixed hoti hain → isliye Enum + int column best practice hai.


// 1. Enum aur Table ek sath q confusion hoti hai?

// Enum: simple aur lightweight option hai → constant values ko fix aur type-safe rakhta hai.
// Table: zyada flexible option hai → jab tumhe data changeable aur dynamic chahiye ho.
// Agar tum enum bhi banao aur table bhi, to aisa lage ga jaise duplicate cheez maintain karni par rahi hai.
// 2. Kab sirf Enum use karo?
// Jab values kabhi change nahi hoti (e.g. Pending, Processing, Completed, Cancelled).
// Jab tum chahte ho code ke andar hi strongly typed constants rahen.
// Jab reporting/translation ki alag requirement nahi hai.

// Example:

enum OrderStatus: int {
    case Pending = 1;
    case Processing = 2;
    case Completed = 3;
    case Cancelled = 4;
}


// Database me sirf integer store hoga (1, 2, 3, 4).
// Code me readable enums use hoga.
// 3. Kab sirf Table use karo?
// Jab tum chahte ho ye values admin panel se manage ho saken.
// Jab multi-language/translation ka case ho.
// Jab values future me add/remove/update ho sakti hain.
// Jab reports / filters ke liye foreign key relationship banana hai.

// Example:
// order_types table:

// id | name
// ---|----------
// 1  | Pending
// 2  | Processing
// 3  | Completed
// 4  | Cancelled


// Aur orders table me order_type_id foreign key hogi.
// 4. Kab Enum + Table dono banane ka sense banta hai?
// Ye tab hota hai jab tum:
// Code me enum readability chahte ho.
// Database me foreign key relationship aur dynamic flexibility bhi chahte ho.
// Example flow:
// Table order_types me values hain (admin manage kar sakta hai).
// Enum OrderStatus code ke andar readability aur type-safety ke liye.
// Enum ka value aur table ka record ek dusre ke sath sync hona chahiye (warna duplication ka risk hai).
// Iska use rare hota hai, mostly bade systems me.

//  Best Practice (tumhare scenario "order types" me):

// Agar tumhe sirf fixed statuses chahiye (Pending, Processing, Completed...) → Enum best hai.

// Agar tumhe future me naye statuses add karne hain aur admin manage karega → Table best hai.

// Dono banane ki zarurat sirf tab hai jab tum type-safety aur dynamic behavior ko ek sath chahte ho (lekin ye complex aur maintenance heavy hota hai).

//  To tumhare case me, agar order types fixed hain aur rarely change hoti hain → Enum hi best aur simple hai.
// Lekin agar order types ka table already bana hua hai aur wo admin panel se editable hai → to fir table use karna sahi hai, enum ki zarurat nahi.