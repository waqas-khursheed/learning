// ============================================================================
// 03 — CONTROL FLOW & LOOPS
// ============================================================================


// ============================================================================
// IF / ELSE / ELSE IF
// ============================================================================

const age = 25;

if (age < 13) {
    console.log("Bacha");
} else if (age < 20) {
    console.log("Teenager");
} else {
    console.log("Adult");
}


// ============================================================================
// SWITCH STATEMENT — Jab MULTIPLE specific values check karne hon
// ============================================================================

const day = "Monday";

switch (day) {
    case "Saturday":
    case "Sunday":
        console.log("Weekend hai");
        break;

    case "Monday":
        console.log("Office wapas jana hai");
        break;

    default:
        console.log("Normal weekday");
}

// ⚠️ "break" likhna ZAROORI hai — warna FALL-THROUGH ho jayega
// (agla case bhi chal jayega, jo chahiye nahi hota — common bug)


// ============================================================================
// FOR LOOP — Jab EXACT count pata ho kitni baar loop chalana hai
// ============================================================================

for (let i = 0; i < 5; i++) {
    console.log(`Iteration: ${i}`);   // 0, 1, 2, 3, 4
}


// ============================================================================
// WHILE LOOP — Jab condition TRUE rehne tak chalana ho (count pata na ho)
// ============================================================================

let stock = 5;
while (stock > 0) {
    console.log(`Stock bacha: ${stock}`);
    stock--;
}


// ============================================================================
// DO-WHILE LOOP — KAM SE KAM EK BAAR chalta hai (condition check baad mein)
// ============================================================================

let attempts = 0;
do {
    console.log("Login try kar rahe hain...");
    attempts++;
} while (attempts < 1);   // Condition false ho phir bhi EK baar chal chuka hoga


// ============================================================================
// FOR...OF — ARRAYS/ITERABLE VALUES par loop chalane ke liye (Modern, common)
// ============================================================================

const fruits = ["Apple", "Banana", "Mango"];

for (const fruit of fruits) {
    console.log(fruit);   // Apple, Banana, Mango — VALUES milti hain
}


// ============================================================================
// FOR...IN — OBJECT KEYS par loop chalane ke liye
// ============================================================================

const user = { name: "Ali", age: 25, city: "Karachi" };

for (const key in user) {
    console.log(`${key}: ${user[key]}`);
    // name: Ali
    // age: 25
    // city: Karachi
}

// ⚠️ COMMON MISTAKE: for...in ARRAYS par use mat karo (INDEX milta hai,
// VALUE nahi, aur order guarantee nahi hota) — Arrays ke liye for...of use karo


// ============================================================================
// BREAK aur CONTINUE
// ============================================================================

// break — Loop ko POORI TARAH rok deta hai
for (let i = 0; i < 10; i++) {
    if (i === 5) break;
    console.log(i);   // 0, 1, 2, 3, 4 (5 par ruk gaya)
}

// continue — SIRF current iteration skip karta hai, loop CHALTA RAHTA hai
for (let i = 0; i < 5; i++) {
    if (i === 2) continue;
    console.log(i);   // 0, 1, 3, 4 (sirf 2 skip hua)
}


// ============================================================================
// REAL-WORLD EXAMPLE — Loop ka practical use
// ============================================================================

const products = [
    { name: "Laptop", price: 150000, inStock: true },
    { name: "Mouse", price: 1500, inStock: false },
    { name: "Keyboard", price: 3500, inStock: true },
];

let totalAvailableValue = 0;

for (const product of products) {
    if (!product.inStock) continue;   // Out of stock items skip karo
    totalAvailableValue += product.price;
}

console.log(`Total available stock value: ${totalAvailableValue}`);  // 153500


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. Modern JS mein for...of aur Array methods (map/filter/reduce — dekho
//    05_arrays_array_methods.js) traditional for loop se ZYADA use hote
//    hain — cleaner aur readable hote hain.

// 2. switch ke bajaye, agar conditions complex hon, to OBJECT LOOKUP
//    pattern zyada clean hota hai:
//
//    const messages = {
//        Saturday: "Weekend hai",
//        Sunday: "Weekend hai",
//        Monday: "Office wapas jana hai",
//    };
//    console.log(messages[day] || "Normal weekday");

// 3. while(true) loops PRODUCTION code mein risky hain agar break
//    condition sahi se na ho — INFINITE LOOP browser/server crash kar sakta hai.
