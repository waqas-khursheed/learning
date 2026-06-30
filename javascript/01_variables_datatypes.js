// ============================================================================
// 01 — VARIABLES & DATA TYPES
// ============================================================================

// JavaScript mein variable banane ke 3 tareeqe hain: var, let, const


// ============================================================================
// var vs let vs const
// ============================================================================

var oldWay = "Purana tareeqa";     // FUNCTION-scoped, re-declare ho sakta hai (AVOID karo)
let modernWay = "Naya tareeqa";    // BLOCK-scoped, value change ho sakti hai
const fixedValue = "Fixed";        // BLOCK-scoped, REASSIGN nahi ho sakta

modernWay = "Value badal di";      // ✅ OK — let reassign ho sakta hai
// fixedValue = "Try"; // ❌ ERROR — const ko reassign nahi kar sakte

// Real Life Example:
// let  → User ka cart total (badalta rehta hai)
// const → Tax rate (fixed rehta hai, kabhi nahi badalta)

const taxRate = 0.05;
let cartTotal = 1000;
cartTotal += cartTotal * taxRate;   // cartTotal badal sakta hai, taxRate nahi


// ============================================================================
// const KA EK IMPORTANT NUKTA — Object/Array ke andar ki values badal sakti hain
// ============================================================================

const user = { name: 'Ali', age: 25 };
user.age = 26;          // ✅ OK — object ke ANDAR ki property badal rahe hain
// user = {};            // ❌ ERROR — poora object REPLACE nahi kar sakte

const numbers = [1, 2, 3];
numbers.push(4);        // ✅ OK — array mein add karna allowed hai
// numbers = [5, 6];     // ❌ ERROR — poora array REPLACE nahi kar sakte

// YAAD RAKHO: const ka matlab "QEEMAT FIX hai" nahi — matlab hai
// "VARIABLE ka REFERENCE fix hai" (Object/Array ke andar change ho sakta hai)


// ============================================================================
// PRIMITIVE DATA TYPES (7 types)
// ============================================================================

let str       = "Hello";              // String
let num       = 42;                   // Number (int/float dono same type)
let isActive  = true;                 // Boolean
let nothing   = null;                 // Null — JAAN-BOOJH kar "khaali" set kiya
let notSet;                           // Undefined — value abhi set HI nahi hui
let id        = Symbol('id');         // Symbol — unique identifier (rarely used)
let bigNum    = 9007199254740993n;    // BigInt — bohot bara number (n suffix)

console.log(typeof str);       // "string"
console.log(typeof num);       // "number"
console.log(typeof isActive);  // "boolean"
console.log(typeof nothing);   // "object"  ⚠️ YE JAVASCRIPT KA FAMOUS BUG HAI!
console.log(typeof notSet);    // "undefined"


// ============================================================================
// null vs undefined — INTERVIEW KA COMMON SAWAL
// ============================================================================

let a;              // undefined — JS ne khud "value nahi di" bola
let b = null;        // null — DEVELOPER ne JAAN-BOOJH kar "khaali" set kiya

console.log(a == null);   // true  (loose comparison — type ignore karta hai)
console.log(a === null);  // false (strict comparison — type bhi check karta hai)

// Real Life Example:
// let user = null;       → "User abhi load NAHI hua, but field exist karta hai"
// let user;              → "User field define hi nahi hua"


// ============================================================================
// REFERENCE TYPES — Object, Array, Function (Primitive NAHI hain)
// ============================================================================

let obj1 = { name: 'Ali' };
let obj2 = obj1;             // obj2 SAME object ko POINT karta hai (reference copy)
obj2.name = 'Sara';
console.log(obj1.name);      // "Sara" — original bhi badal gaya! (Object reference hai)

let num1 = 10;
let num2 = num1;             // num2 ki APNI ALAG copy bani (primitive copy by VALUE)
num2 = 20;
console.log(num1);           // 10 — original NAHI badla


// ============================================================================
// TYPE CHECKING — typeof, Array.isArray()
// ============================================================================

console.log(typeof []);              // "object"   ⚠️ Array bhi "object" dikhata hai
console.log(Array.isArray([]));      // true        — sahi tareeqa array check karne ka
console.log(typeof null);            // "object"   ⚠️ ye historical JS bug hai (fix nahi hua backward-compatibility ki wajah se)


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. var KABHI use mat karo modern code mein — sirf legacy code maintain
//    karte waqt samajhne ke liye zaroori hai. let/const hamesha use karo.

// 2. const ko DEFAULT choice banao. Sirf TAB let use karo jab variable ki
//    value WAQAI reassign honi ho (loop counter, accumulator, etc.)

// 3. "==" (loose equality) avoid karo, "===" (strict equality) use karo —
//    == type coercion karta hai jo unpredictable bugs create karta hai
//    (dekho 02_operators_type_coercion.js)
