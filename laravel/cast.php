<?php
// Laravel: Casts, Appends, Accessors & Mutators


// 1. Casts
// Definition
// $casts ka kaam hota hai database value ko automatically PHP type me convert karna aur wapas save karte waqt correct format me DB me dalna. 


class PromoCode extends Model
{
    protected $casts = [
        'is_active'       => 'boolean',
        'applies_to'      => 'array',
        'discount_amount' => 'decimal:2',
        'start_date'      => 'date',
        'expiry_date'     => 'datetime',
    ];
}

// Database Row
is_active = 1
applies_to = "[1,2,3]"
discount_amount = 17
start_date = "2025-09-25 00:00:00"
expiry_date = "2025-09-25 14:30:00"

// JSON Response

{
    "is_active": true,
    "applies_to": [1,2,3],
    "discount_amount": "17.00",
    "start_date": "2025-09-25",
    "expiry_date": "2025-09-25T14:30:00.000000Z"
}
//   Casts lagane ke baad hamesha correct datatype milega.



// 2. Accessors
//  Definition

// Accessor ka kaam hota hai attribute ko fetch karte waqt manipulate karna.
// Laravel iske liye method banata hai:
// get{AttributeName}Attribute($value)

class PromoCode extends Model
{
    public function getTitleAttribute($value)
    {
        return strtoupper($value); // hamesha uppercase return karega
    }
}


// Database Row

{
    "title": "NEW YEAR OFFER"
}

// Accessor DB ko change nahi karta, sirf output me transformation hoti hai.


// 3. Mutators
//  Definition

// Mutator ka kaam hota hai attribute ko save karte waqt manipulate karna.
// Laravel me method banta hai:
//  set{AttributeName}Attribute($value)

class PromoCode extends Model
{
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = ucfirst($value); // pehla letter uppercase
    }
}
// Result

$promo = new PromoCode();
$promo->title = "new year offer";
$promo->save();

// Database Row

title = "New year offer"

// JSON Response
{
"title": "New year offer"
}

// Mutator DB me store hone se pehle value ko polish kar deta hai.


// 4. Appends
//  Definition

// $appends ka use hota hai extra virtual fields JSON response me add karne ke liye.
// Yeh fields DB me exist nahi karti.

class PromoCode extends Model
{
    protected $appends = ['full_title'];

    public function getFullTitleAttribute()
    {
        return $this->title . ' - ' . $this->discount_amount . '% OFF';
    }
}

// Database Row

title = "New Year Offer"
discount_amount = 17

// JSON Response 
{
    "title": "New Year Offer",
    "discount_amount": 17,
    "full_title": "New Year Offer - 17% OFF"
}
// Note: Appends ka fayda API me user-friendly info dena hai.

// Summary Table

// | Feature       | Kaam                          | Apply hota hai            | Example Use                                   |
// | ------------- | ----------------------------- | ------------------------- | --------------------------------------------- |
// | **Casts**     | DB values → PHP types         | Save & Fetch              | `is_active => boolean`, `applies_to => array` |
// | **Accessors** | Output polish karna           | Get (read) waqt           | Title ko uppercase karna                      |
// | **Mutators**  | Input polish karna            | Set (save) waqt           | Title ko ucfirst save karna                   |
// | **Appends**   | Extra field JSON me add karna | JSON response banate waqt | `full_title` add karna                        |


// Is tarah aapko poori control milti hai:
// DB me kya save ho
// Code me kya return ho
// API response me user ko kya dikhana hai