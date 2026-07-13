<?php
// Laravel: CSRF Protection (Cross-Site Request Forgery)


// 1. CSRF kya hota hai?
// Definition

// CSRF ek attack hai jisme koi malicious website, user ke logged-in
// session ka fayda utha kar, uski marzi ke bina, kisi trusted site par
// request bhej deti hai (jaise password change, money transfer, etc).


// 2. Example of Attack (bina CSRF protection ke)

// Maan lo aapki site par ye route hai:
// POST /transfer-money  { amount: 1000, to: 'attacker' }

// User already "bank.com" par logged in hai (session cookie active hai).
// Attacker apni site par ye form chupa deta hai:

// <form action="https://bank.com/transfer-money" method="POST">
//     <input type="hidden" name="amount" value="1000">
//     <input type="hidden" name="to" value="attacker">
// </form>
// <script> document.forms[0].submit(); </script>

// Jaise hi user attacker ki site visit karta hai, browser automatically
// bank.com ka session cookie bhi bhej deta hai -> request valid maan
// li jaati hai -> paisay chale jaate hain, user ko pata bhi nahi chalta.


// 3. Laravel CSRF ko kaise rokta hai?
// Definition

// Laravel har logged-in session ke liye ek unique "CSRF Token" generate
// karta hai. Har POST/PUT/PATCH/DELETE request ke sath ye token bhejna
// zaroori hota hai, warna Laravel request ko reject kar deta hai
// (419 Page Expired error).


// 4. Blade Form me CSRF Token
<form action="/transfer-money" method="POST">
    @csrf
    <input type="text" name="amount">
    <button type="submit">Send</button>
</form>

// @csrf ye hidden input generate karta hai:
// <input type="hidden" name="_token" value="abc123randomtoken...">


// 5. AJAX / Fetch requests me CSRF Token

// Meta tag layout me daalte hain:
<meta name="csrf-token" content="{{ csrf_token() }}">

// JavaScript me header ke through bhejte hain:
fetch('/transfer-money', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({ amount: 1000 })
});


// 6. Token match na ho to kya hota hai?

// Agar _token missing ho ya galat ho:
// Laravel automatically "VerifyCsrfToken" middleware ke through
// 419 | Page Expired  error de deta hai, aur request process nahi hoti.


// 7. Routes jo CSRF check se exclude karni ho

// app/Http/Middleware/VerifyCsrfToken.php
class VerifyCsrfToken extends Middleware
{
    protected $except = [
        'webhook/*', // jaise payment gateway (Stripe/PayPal) webhooks
    ];
}
// Note: Sirf un routes ko exclude karo jinko third-party server call
// karta hai (jahan session/cookie hoti hi nahi), normal forms ko nahi.


// 8. Kyun zaroori hai?

// | Without CSRF                          | With CSRF                          |
// | -------------------------------------- | ----------------------------------- |
// | Koi bhi site user ke session se        | Har request ke sath unique token   |
// | fake request bhej sakti hai            | chahiye, jo attacker ke paas nahi   |
// | User ko pata bhi nahi chalta            | hota, is liye request reject hoti  |
// |                                         | hai                                  |


// 9. Summary
// - CSRF attack me user ke bina marzi ke uske session se request bheji jaati hai
// - Laravel @csrf directive se hidden token form me add karta hai
// - Har request ke sath token verify hota hai (VerifyCsrfToken middleware)
// - Token galat/missing ho to 419 error milta hai
// - Sirf webhooks jaisi external routes ko CSRF se exclude karo
