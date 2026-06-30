// ============================================================================
// 13 — ES6+ MODERN FEATURES (Template Literals, Modules, etc.)
// ============================================================================


// ============================================================================
// TEMPLATE LITERALS — String banane ka MODERN tareeqa (backticks `` use hoti hain)
// ============================================================================

const name = "Ali";
const age = 25;

// ❌ PURANA TAREEQA — string concatenation:
const oldWay = "Hello, " + name + "! Tum " + age + " saal ke ho.";

// ✅ TEMPLATE LITERAL — clean, readable:
const newWay = `Hello, ${name}! Tum ${age} saal ke ho.`;

console.log(newWay);

// MULTI-LINE strings — bina \n ke:
const multiLine = `
Line 1
Line 2
Line 3
`;

// EXPRESSIONS bhi chala sakte ho andar:
const cartTotal = 1500;
console.log(`Total: PKR ${cartTotal * 1.05}`);   // Calculation seedha andar


// ============================================================================
// TAGGED TEMPLATES (Advanced — rarely used, lekin frameworks mein milta hai)
// ============================================================================

function highlight(strings, ...values) {
    return strings.reduce((result, str, i) => {
        return `${result}${str}${values[i] ? `[${values[i]}]` : ""}`;
    }, "");
}

console.log(highlight`Naam: ${name}, Umar: ${age}`);
// "Naam: [Ali], Umar: [25]"


// ============================================================================
// MODULES — import / export (Code ko ALAG files mein organize karna)
// ============================================================================

// ---- file: mathUtils.js ----
//
// export function add(a, b) {
//     return a + b;
// }
//
// export function subtract(a, b) {
//     return a - b;
// }
//
// export default function multiply(a, b) {   // DEFAULT export — sirf EK per file
//     return a * b;
// }

// ---- file: app.js ----
//
// import multiply, { add, subtract } from './mathUtils.js';
//
// console.log(add(2, 3));        // 5
// console.log(multiply(2, 3));   // 6
//
// // Sab kuch ek sath import karna:
// import * as MathUtils from './mathUtils.js';
// console.log(MathUtils.add(2, 3));

// FAYDA: Code organize hota hai, sirf JO ZAROORI hai wahi import hota hai
// (Laravel ke namespace/use statements jaisa concept — dekho oop/06_interfaces.php)


// ============================================================================
// SET — UNIQUE values ka collection (duplicates automatically REMOVE)
// ============================================================================

const numbersWithDuplicates = [1, 2, 2, 3, 3, 3, 4];
const uniqueNumbers = [...new Set(numbersWithDuplicates)];
console.log(uniqueNumbers);   // [1, 2, 3, 4]

const tags = new Set();
tags.add("javascript");
tags.add("laravel");
tags.add("javascript");   // Duplicate — IGNORE ho jayega
console.log(tags.size);   // 2


// ============================================================================
// MAP — Object jaisa, LEKIN key KOI BHI type ho sakti hai (sirf string nahi)
// ============================================================================

const userRoles = new Map();
userRoles.set("ali", "admin");
userRoles.set(123, "guest");        // Number bhi key ban sakti hai
userRoles.set(true, "test-user");   // Boolean bhi!

console.log(userRoles.get("ali"));   // "admin"
console.log(userRoles.size);         // 3

// Map vs Object kab use karein:
// Object → Saadi key-value pairs (string keys), JSON ke sath kaam
// Map    → Frequently add/remove hoti keys, NON-string keys, ORDER guarantee chahiye


// ============================================================================
// ARRAY/OBJECT DESTRUCTURING + DEFAULT (Section 06 ka revision, MODERN context mein)
// ============================================================================

function fetchConfig({ timeout = 5000, retries = 3 } = {}) {
    console.log(`Timeout: ${timeout}, Retries: ${retries}`);
}
fetchConfig();                          // Timeout: 5000, Retries: 3
fetchConfig({ timeout: 10000 });        // Timeout: 10000, Retries: 3


// ============================================================================
// OPTIONAL CHAINING + NULLISH COALESCING (Revision — production mein bohot common)
// ============================================================================

const apiResponse = { user: { profile: null } };
const bio = apiResponse?.user?.profile?.bio ?? "Bio nahi likha";
console.log(bio);   // "Bio nahi likha"


// ============================================================================
// ARRAY.flat() / flatMap() — Nested arrays ko "flatten" karna
// ============================================================================

const nested = [1, [2, 3], [4, [5, 6]]];
console.log(nested.flat());        // [1, 2, 3, 4, [5, 6]] — 1 level flatten
console.log(nested.flat(2));       // [1, 2, 3, 4, 5, 6]    — 2 levels flatten
console.log(nested.flat(Infinity)); // Poori tarah flatten, chahe kitne bhi levels hon

const sentences = ["hello world", "foo bar"];
console.log(sentences.flatMap((s) => s.split(" ")));
// ["hello", "world", "foo", "bar"] — map() + flat() EK sath


// ============================================================================
// ARRAY.from() — Array-LIKE cheezon ko ASAL array mein convert karna
// ============================================================================

console.log(Array.from("hello"));          // ["h","e","l","l","o"]
console.log(Array.from({ length: 5 }, (_, i) => i * 2));   // [0,2,4,6,8]

// REAL USE CASE: HTML elements ki NodeList ko array mein convert karna
// const divs = Array.from(document.querySelectorAll('div'));


// ============================================================================
// SHORT-CIRCUIT + LOGICAL ASSIGNMENT (Newer ES2021 feature)
// ============================================================================

let config = { theme: null };

config.theme ??= "light";    // Agar null/undefined hai, TABHI assign karo
console.log(config.theme);   // "light"

let count = 0;
count ||= 10;                 // Agar falsy hai, TABHI assign karo
console.log(count);           // 10 (0 falsy tha)


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. Template literals ko DEFAULT bana lo string concatenation ke liye —
//    cleaner aur multi-line support bhi deta hai.

// 2. Modules (import/export) modern JS development ka FOUNDATION hain —
//    Node.js, React, Vue sab isi system par based hain.

// 3. Set/Map specialized use cases ke liye perfect hain — agar
//    "duplicates avoid karne hain" ya "non-string keys chahiye", inhe
//    use karo, plain Object/Array se zabardasti mat karo.
