// ============================================================================
// 05 — ARRAYS & ARRAY METHODS (SABSE ZYADA INTERVIEW MEIN PUCHA JANE WALA TOPIC)
// ============================================================================


// ============================================================================
// BASIC ARRAY OPERATIONS
// ============================================================================

const fruits = ["Apple", "Banana", "Mango"];

fruits.push("Orange");          // END mein add — ["Apple","Banana","Mango","Orange"]
fruits.pop();                   // END se remove — wapas ["Apple","Banana","Mango"]
fruits.unshift("Grapes");       // START mein add — ["Grapes","Apple","Banana","Mango"]
fruits.shift();                 // START se remove — wapas ["Apple","Banana","Mango"]

console.log(fruits.length);     // 3
console.log(fruits.indexOf("Banana"));   // 1
console.log(fruits.includes("Mango"));   // true


// ============================================================================
// forEach() — Har element par EK kaam karna (KUCH RETURN NAHI karta)
// ============================================================================

const numbers = [1, 2, 3, 4, 5];

numbers.forEach((num) => {
    console.log(num * 2);   // 2, 4, 6, 8, 10 (sirf print, NAYA array nahi banta)
});


// ============================================================================
// map() — Har element ko TRANSFORM karke NAYA array banana (SABSE COMMON)
// ============================================================================

const doubled = numbers.map((num) => num * 2);
console.log(doubled);   // [2, 4, 6, 8, 10] — NAYA array, original NAHI badla

// REAL EXAMPLE: Products ki list se sirf names nikalna
const products = [
    { id: 1, name: "Laptop", price: 150000 },
    { id: 2, name: "Mouse", price: 1500 },
];

const productNames = products.map((p) => p.name);
console.log(productNames);   // ["Laptop", "Mouse"]


// ============================================================================
// filter() — Condition ke mutabiq elements CHUN'na (NAYA array)
// ============================================================================

const evenNumbers = numbers.filter((num) => num % 2 === 0);
console.log(evenNumbers);   // [2, 4]

// REAL EXAMPLE: Sirf wo products jo budget mein hain
const affordableProducts = products.filter((p) => p.price < 10000);
console.log(affordableProducts);   // [{ id: 2, name: "Mouse", price: 1500 }]


// ============================================================================
// reduce() — Poore array ko EK VALUE mein "reduce/sametna" (SABSE POWERFUL)
// ============================================================================

const total = numbers.reduce((accumulator, current) => accumulator + current, 0);
console.log(total);   // 15 (1+2+3+4+5)

// REAL EXAMPLE: Cart ka total price calculate karna
const cart = [
    { name: "Laptop", price: 150000, qty: 1 },
    { name: "Mouse", price: 1500, qty: 2 },
];

const cartTotal = cart.reduce((sum, item) => sum + item.price * item.qty, 0);
console.log(cartTotal);   // 153000

// reduce() se OBJECT bhi bana sakte ho (grouping ke liye bohot common):
const groupedById = products.reduce((acc, product) => {
    acc[product.id] = product;
    return acc;
}, {});
console.log(groupedById);   // { 1: {...Laptop}, 2: {...Mouse} }


// ============================================================================
// find() vs filter() — IMPORTANT FARAK
// ============================================================================

const foundProduct = products.find((p) => p.id === 2);
console.log(foundProduct);   // { id: 2, name: "Mouse", price: 1500 } — SIRF EK object

const filteredProducts = products.filter((p) => p.id === 2);
console.log(filteredProducts);   // [{ id: 2, ... }] — HAMESHA ek ARRAY (chahe 1 hi match ho)

// find() → Pehla MATCHING element (ya undefined agar na mile)
// filter() → SAARE matching elements ka ARRAY (ya empty array agar koi na mile)


// ============================================================================
// some() vs every()
// ============================================================================

console.log(numbers.some((n) => n > 4));    // true  — KAM SE KAM EK condition pass karta hai
console.log(numbers.every((n) => n > 0));   // true  — SAARE elements condition pass karte hain
console.log(numbers.every((n) => n > 4));   // false — sab > 4 nahi hain


// ============================================================================
// sort() — ⚠️ ORIGINAL ARRAY ko MODIFY karta hai (mutate)
// ============================================================================

const unsorted = [5, 2, 8, 1, 9];

unsorted.sort((a, b) => a - b);   // Ascending order — comparator function ZAROORI hai!
console.log(unsorted);   // [1, 2, 5, 8, 9]

unsorted.sort((a, b) => b - a);   // Descending order
console.log(unsorted);   // [9, 8, 5, 2, 1]

// ⚠️ COMMON MISTAKE: sort() BINA comparator ke STRING jaisa sort karta hai:
console.log([10, 2, 33, 4].sort());   // [10, 2, 33, 4] → [10, 2, 33, 4] GALAT order!
// (kyunke default string comparison hai: "10" < "2" alphabetically)


// ============================================================================
// CHAINING — Multiple methods EK sath jorr kar (Real production pattern)
// ============================================================================

const expensiveProductNames = products
    .filter((p) => p.price > 10000)   // Pehle filter karo
    .map((p) => p.name)               // Phir sirf names nikalo
    .sort();                          // Phir sort karo

console.log(expensiveProductNames);   // ["Laptop"]


// ============================================================================
// SPREAD OPERATOR ke sath ARRAY OPERATIONS (Immutable style — modern best practice)
// ============================================================================

const original = [1, 2, 3];

// ❌ MUTATING (original ko badal deta hai) — avoid karo jahan mumkin ho:
// original.push(4);

// ✅ NON-MUTATING (naya array banata hai, original SAFE rehta hai):
const withNewItem = [...original, 4];
console.log(original);       // [1, 2, 3] — safe
console.log(withNewItem);    // [1, 2, 3, 4]


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. map/filter/reduce IMMUTABLE hain (naya array return karte hain) —
//    React/Vue jaisi modern frameworks mein ye STANDARD practice hai
//    (state ko directly mutate karna bugs create karta hai).

// 2. forEach() se return value NAHI milti — agar transform/filter karna
//    hai to map/filter use karo, forEach sirf "side-effects" (logging,
//    DOM update) ke liye.

// 3. reduce() SABSE POWERFUL hai — map() aur filter() dono ka kaam reduce()
//    se ho sakta hai, lekin readability ke liye specific method use karo.

// 4. Performance: BARE arrays (lakhon items) par chaining (.filter().map())
//    multiple passes karta hai — agar performance critical ho, single
//    reduce() ya for loop zyada efficient ho sakta hai.
