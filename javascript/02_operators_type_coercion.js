// ============================================================================
// 02 — OPERATORS & TYPE COERCION
// ============================================================================


// ============================================================================
// ARITHMETIC OPERATORS
// ============================================================================

console.log(10 + 5);   // 15
console.log(10 - 5);   // 5
console.log(10 * 5);   // 50
console.log(10 / 5);   // 2
console.log(10 % 3);   // 1   — Modulus (remainder) — even/odd check ke liye common
console.log(2 ** 3);   // 8   — Exponent (Power) — 2 ki power 3


// ============================================================================
// COMPARISON OPERATORS — == vs === (SABSE IMPORTANT INTERVIEW TOPIC)
// ============================================================================

console.log(5 == "5");    // true  — LOOSE equality, type CONVERT karke compare karta hai
console.log(5 === "5");   // false — STRICT equality, type bhi match hona chahiye

console.log(0 == false);       // true  — loose equality mein 0 aur false barabar
console.log(0 === false);      // false — strict mein NAHI (number vs boolean)

console.log(null == undefined);   // true  — special case, dono "khaali" maane jate hain
console.log(null === undefined);  // false — different types hain

// RULE: HAMESHA === aur !== use karo. == kabhi nahi (predictability ke liye)


// ============================================================================
// TYPE COERCION — JavaScript KHUD types CONVERT kar deta hai (Bohot bugs ki wajah)
// ============================================================================

console.log("5" + 3);     // "53"   — Number ko String mein convert kar diya (+ string priority)
console.log("5" - 3);     // 2      — String ko Number mein convert kar diya (- sirf numbers samajhta hai)
console.log("5" * "2");   // 10     — Dono String ko Number bana diya
console.log(true + 1);    // 2      — true ko 1 bana diya
console.log(false + 1);   // 1      — false ko 0 bana diya
console.log("" + 1);      // "1"    — Empty string + number = string
console.log([] + []);     // ""     — Dono arrays empty string ban gaye
console.log([] + {});     // "[object Object]"  — Bohot ajeeb result! (Interview trick question)

// REAL ADVICE: Coercion par BHAROSA mat karo — explicit conversion karo:
console.log(Number("5") + 3);     // 8  — explicit, clear, koi confusion nahi
console.log(String(5) + "3");     // "53"


// ============================================================================
// TRUTHY vs FALSY VALUES
// ============================================================================

// Ye SAARI values FALSY hain (if condition mein false ki tarah behave karti hain):
//   false, 0, -0, "", null, undefined, NaN

// BAAKI SAB TRUTHY hain — including:
//   "0" (string mein "0"), "false" (string), [], {}, " " (space)

if ("0") {
    console.log("Ye chalega — string '0' TRUTHY hai!");  // Ye print hoga
}

if ([]) {
    console.log("Empty array bhi TRUTHY hai!");  // Ye bhi print hoga
}

if (0) {
    console.log("Ye NAHI chalega");  // Skip ho jayega — 0 falsy hai
}


// ============================================================================
// LOGICAL OPERATORS — &&, ||, !
// ============================================================================

console.log(true && false);   // false
console.log(true || false);   // true
console.log(!true);           // false

// SHORT-CIRCUIT EVALUATION — Real-world use case (bohot common pattern):
let username = null;
let displayName = username || "Guest";   // Agar username falsy hai, "Guest" use karo
console.log(displayName);                // "Guest"

let userObj = { name: "Ali" };
userObj && console.log(userObj.name);    // Agar userObj truthy hai, tabhi log karo


// ============================================================================
// NULLISH COALESCING (??) — Modern JS (ES2020), || se BEHTAR hai kuch cases mein
// ============================================================================

let score = 0;
console.log(score || 100);    // 100  ❌ GALAT — kyunke 0 falsy hai, isay "missing" samjha
console.log(score ?? 100);    // 0    ✅ SAHI — ?? sirf null/undefined ko "missing" samajhta hai

// REAL EXAMPLE: User ka "discount" 0 ho sakta hai (valid value), lekin
// "set hi nahi hua" (undefined) bhi ho sakta hai — ?? dono mein farak karta hai


// ============================================================================
// TERNARY OPERATOR — if/else ka shortcut
// ============================================================================

let age = 20;
let category = age >= 18 ? "Adult" : "Minor";
console.log(category);   // "Adult"


// ============================================================================
// SPREAD (...) aur REST (...) OPERATORS — Same syntax, ALAG context
// ============================================================================

// SPREAD — values ko "phaila" (expand) deta hai
const arr1 = [1, 2, 3];
const arr2 = [...arr1, 4, 5];   // [1, 2, 3, 4, 5]

const obj1 = { name: "Ali" };
const obj2 = { ...obj1, age: 25 };   // { name: "Ali", age: 25 }

// REST — multiple values ko EK array mein "samet" (collect) leta hai
function sum(...numbers) {   // numbers = [1, 2, 3, 4] (jitne bhi args ho)
    return numbers.reduce((total, n) => total + n, 0);
}
console.log(sum(1, 2, 3, 4));   // 10


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. === aur !== STANDARD practice hai professional code mein — == sirf
//    legacy code mein milega, naya code mein mat likho.

// 2. ?? aur || ka farak samajhna ZAROORI hai — 0, "", false jaisi "valid
//    falsy" values ke sath ?? use karo, warna bugs aayenge.

// 3. Type coercion ko SAMAJHNA zaroori hai (interview ke liye), lekin
//    PRODUCTION code mein ISKO AVOID karo — explicit Number()/String()
//    conversions use karo, taake code PREDICTABLE rahe.
