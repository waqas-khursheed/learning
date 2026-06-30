// ============================================================================
// 04 — FUNCTIONS (Declarations, Expressions, Arrow Functions)
// ============================================================================


// ============================================================================
// FUNCTION DECLARATION — HOISTED hoti hai (file ke kahin bhi use ho sakti)
// ============================================================================

console.log(add(2, 3));   // 5 — Function call SE PEHLE bhi kaam karta hai!

function add(a, b) {
    return a + b;
}

// HOISTING: JavaScript function declarations ko file ke TOP par "utha"
// (hoist) leta hai memory mein, isliye define hone se pehle bhi call ho sakti hai.


// ============================================================================
// FUNCTION EXPRESSION — HOISTED NAHI hoti, define hone ke BAAD hi use ho sakti hai
// ============================================================================

// console.log(subtract(5, 2));  // ❌ ERROR — abhi tak define nahi hui

const subtract = function (a, b) {
    return a - b;
};

console.log(subtract(5, 2));   // 3 — define hone ke baad chalega


// ============================================================================
// ARROW FUNCTIONS (ES6) — Modern, short syntax
// ============================================================================

const multiply = (a, b) => {
    return a * b;
};

// Agar function mein SIRF EK return statement hai, to short syntax:
const square = (n) => n * n;
console.log(square(5));   // 25

// EK parameter ho to brackets () bhi optional hain:
const double = n => n * 2;

// Object return karna ho to () mein wrap karna ZAROORI hai:
const createUser = (name, age) => ({ name, age });
console.log(createUser("Ali", 25));   // { name: "Ali", age: 25 }


// ============================================================================
// DEFAULT PARAMETERS
// ============================================================================

function greet(name = "Guest") {
    return `Hello, ${name}!`;
}

console.log(greet());           // "Hello, Guest!"
console.log(greet("Ali"));      // "Hello, Ali!"


// ============================================================================
// REST PARAMETERS — Multiple arguments ek array mein samet'na
// ============================================================================

function calculateTotal(...prices) {
    return prices.reduce((sum, price) => sum + price, 0);
}

console.log(calculateTotal(100, 200, 300));   // 600


// ============================================================================
// REGULAR FUNCTION vs ARROW FUNCTION — IMPORTANT FARAK (this keyword)
// ============================================================================

// Detail 08_this_call_apply_bind.js mein hai, lekin yahan quick intro:

const obj = {
    name: "Ali",

    // Regular function — 'this' obj ko refer karta hai
    regularGreet: function () {
        console.log(`Regular: ${this.name}`);
    },

    // Arrow function — 'this' obj ko refer NAHI karta (outer scope se leta hai)
    arrowGreet: () => {
        console.log(`Arrow: ${this.name}`);   // undefined (arrow function 'this' nahi rakhti)
    },
};

obj.regularGreet();   // "Regular: Ali"
obj.arrowGreet();     // "Arrow: undefined"

// RULE: Object METHODS ke liye regular function use karo (this chahiye),
// CALLBACKS ke liye arrow function use karo (this chahiye nahi/outer wala chahiye)


// ============================================================================
// CALLBACK FUNCTIONS — Function ko FUNCTION mein PASS karna
// ============================================================================

function processOrder(orderTotal, callback) {
    const tax = orderTotal * 0.05;
    callback(orderTotal + tax);
}

processOrder(1000, function (finalAmount) {
    console.log(`Final amount: ${finalAmount}`);   // 1050
});

// Arrow function ke sath zyada common hai modern code mein:
processOrder(1000, (finalAmount) => console.log(`Final: ${finalAmount}`));


// ============================================================================
// HIGHER-ORDER FUNCTIONS — Function jo FUNCTION return kare ya ACCEPT kare
// ============================================================================

// Function RETURN karne wala function:
function createMultiplier(factor) {
    return function (number) {
        return number * factor;
    };
}

const double2 = createMultiplier(2);
const triple = createMultiplier(3);

console.log(double2(5));   // 10
console.log(triple(5));    // 15

// Ye CLOSURES ka real example hai (detail: 07_scope_closures.js)


// ============================================================================
// IIFE — Immediately Invoked Function Expression
// ============================================================================

// Function jo BANTE HI khud chal jati hai — ek bar chalti hai, dobara
// call nahi ho sakti. PURANI code mein "private scope" banane ke liye
// use hoti thi (ab modules iska kaam karte hain — 13_es6_modern_features.js)

(function () {
    const secretValue = "Ye bahar accessible nahi";
    console.log("IIFE chal gaya: " + secretValue);
})();


// ============================================================================
// PURE FUNCTIONS — Senior-level concept (Functional Programming)
// ============================================================================

// PURE function: SAME input → HAMESHA SAME output, KOI side-effect nahi
function pureAdd(a, b) {
    return a + b;   // Bahar ki koi variable change nahi kar raha
}

// IMPURE function: External state change kar raha hai (avoid karo jahan ho sake)
let total = 0;
function impureAdd(value) {
    total += value;   // Bahar ki variable modify ki — side-effect!
    return total;
}

// FAYDA Pure functions ka: TESTING aasan hota hai, bugs predict karna
// aasan hota hai, parallel/concurrent code mein safe hote hain.


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. Arrow functions ko METHODS ke liye use NA karo (this ka masla),
//    lekin array callbacks (map/filter/reduce) ke liye PERFECT hain.

// 2. Function naming MEANINGFUL rakho — "doStuff()" nahi, "calculateTax()"
//    jaisa naam jo SAAF bataye function kya karta hai.

// 3. Function chhoti rakho — agar ek function 50+ lines ki ho rahi hai,
//    to usay chhote functions mein TOR'NA (Single Responsibility — dekho oop/12)
