<?php
// Laravel: Mass Assignment ($fillable / $guarded)


// 1. Mass Assignment kya hota hai?
// Definition

// Mass Assignment ka matlab hai ek hi baar me multiple attributes ko
// array ki shakal me model me assign kar dena, ek-ek karke nahi.

// Normal (ek-ek) way:
$user = new User();
$user->name  = $request->name;
$user->email = $request->email;
$user->save();

// Mass Assignment way (ek hi line me):
$user = User::create($request->all());


// 2. Problem kya hai? (Security Risk)
// Agar model me protection na ho, to attacker request me extra fields
// bhi bhej sakta hai jo aap allow nahi karna chahte, jaise "is_admin".

// Form se aane wali request:
// name=Waqas&email=test@test.com&is_admin=1

// Bina protection ke:
User::create($request->all());
// Ye "is_admin" ko bhi database me save kar dega -> security hole!


// 3. Solution: $fillable
// Definition

// $fillable me hum sirf wo fields likhte hain jo mass assignment
// ke through allow honi chahiye. Baaki sab ignore ho jaati hain.

class User extends Model
{
    protected $fillable = ['name', 'email', 'password'];
    // is_admin yahan nahi hai, is liye request me is_admin=1 bhejne
    // par bhi wo save nahi hoga.
}


// 4. Alternative: $guarded
// Definition

// $guarded ulta kaam karta hai - jo fields yahan likhi hain, sirf
// wahi mass assignment se block hongi, baaki sab allow hongi.

class User extends Model
{
    protected $guarded = ['is_admin', 'id'];
    // is_admin aur id ke ilawa baaki sab fields fillable hongi
}

// Note: $guarded = [] likhne ka matlab hai "sab kuch allow hai"
// (bohot risky, production me avoid karo)


// 5. Fillable vs Guarded - kab use karo?

// | Approach    | Kaam                                   | Use case                              |
// | ----------- | --------------------------------------- | -------------------------------------- |
// | $fillable   | Whitelist - sirf ye allowed hain        | Zyada safe, jab fields kam ho          |
// | $guarded    | Blacklist - ye block, baaki sab allowed | Jab bohot saari fields fillable ho     |


// 6. Example - MassAssignmentException

class Product extends Model
{
    protected $fillable = ['title', 'price'];
}

// Agar "fillable" ya "guarded" set na ho aur kuch strict mode ho:
Product::create([
    'title' => 'Laptop',
    'price' => 50000,
    'secret_field' => 'hacked', // ye field fillable me nahi hai
]);
// 'secret_field' silently ignore ho jayega (ya exception aa sakta hai
// agar Model::preventSilentlyDiscardingAttributes() enabled ho)


// 7. Best Practice
// - Hamesha $fillable use karo (whitelist zyada safe hoti hai)
// - Sensitive fields (is_admin, role, password_confirmed, etc.)
// - kabhi bhi $guarded = [] ke sath open mat chorho
// - Request validate() karke sirf zaroori data hi create/update me bhejo

$validated = $request->validate([
    'name'  => 'required|string',
    'email' => 'required|email',
]);

User::create($validated); // sabse safe tareeka
