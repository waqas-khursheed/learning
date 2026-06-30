// ============================================================================
// 06 — OBJECTS, DESTRUCTURING & SPREAD
// ============================================================================


// ============================================================================
// OBJECT BASICS
// ============================================================================

const user = {
    name: "Ali",
    age: 25,
    email: "ali@example.com",

    // Object ke andar METHOD bhi ho sakti hai
    greet() {
        return `Hello, ${this.name}`;
    },
};

console.log(user.name);          // Dot notation — "Ali"
console.log(user["email"]);      // Bracket notation — "ali@example.com"
console.log(user.greet());       // "Hello, Ali"

// Bracket notation kab zaroori hai: jab key DYNAMIC ho (variable mein ho)
const key = "age";
console.log(user[key]);   // 25 — user.key kaam NAHI karega (literal "key" dhoondega)


// ============================================================================
// OBJECT METHODS — keys(), values(), entries()
// ============================================================================

console.log(Object.keys(user));     // ["name", "age", "email", "greet"]
console.log(Object.values(user));   // ["Ali", 25, "ali@example.com", function]
console.log(Object.entries(user));  // [["name","Ali"], ["age",25], ...]

// REAL EXAMPLE: Object ko loop karna
Object.entries(user).forEach(([key, value]) => {
    if (typeof value !== "function") {
        console.log(`${key}: ${value}`);
    }
});


// ============================================================================
// DESTRUCTURING — Object se VALUES nikal kar variables mein dalna (Modern, common)
// ============================================================================

const { name, age } = user;
console.log(name, age);   // "Ali" 25

// RENAME karte huye destructure karna:
const { name: userName, email: userEmail } = user;
console.log(userName, userEmail);   // "Ali" "ali@example.com"

// DEFAULT VALUE dena (agar property exist na kare):
const { country = "Pakistan" } = user;
console.log(country);   // "Pakistan" — user object mein "country" thi hi nahi

// NESTED destructuring:
const order = {
    id: 101,
    customer: { name: "Sara", city: "Lahore" },
};
const { customer: { name: customerName, city } } = order;
console.log(customerName, city);   // "Sara" "Lahore"


// ============================================================================
// ARRAY DESTRUCTURING
// ============================================================================

const colors = ["Red", "Green", "Blue"];
const [firstColor, secondColor] = colors;
console.log(firstColor, secondColor);   // "Red" "Green"

// SKIP karna kisi element ko comma se:
const [, , thirdColor] = colors;
console.log(thirdColor);   // "Blue"

// SWAP karna 2 variables — Destructuring ka famous trick:
let x = 1, y = 2;
[x, y] = [y, x];
console.log(x, y);   // 2 1


// ============================================================================
// FUNCTION PARAMETERS MEIN DESTRUCTURING (Bohot common production pattern)
// ============================================================================

// ❌ PURANA TAREEQA:
function printUserOld(user) {
    console.log(`${user.name} is ${user.age} years old`);
}

// ✅ DESTRUCTURING SE — clean aur SAAF pata chalta hai function ko KYA chahiye:
function printUser({ name, age }) {
    console.log(`${name} is ${age} years old`);
}
printUser(user);   // "Ali is 25 years old"

// Default values ke sath bhi:
function createOrder({ items = [], discount = 0 } = {}) {
    console.log(items, discount);
}
createOrder();   // [] 0 — koi argument na do tab bhi crash nahi hoga


// ============================================================================
// SPREAD OPERATOR — Object COPY/MERGE karne ke liye (Immutable updates)
// ============================================================================

const baseUser = { name: "Ali", role: "user" };

// IMMUTABLE UPDATE — naya object banao, original ko mat chhero:
const adminUser = { ...baseUser, role: "admin" };   // role OVERRIDE ho gaya
console.log(baseUser);   // { name: "Ali", role: "user" } — original SAFE
console.log(adminUser);  // { name: "Ali", role: "admin" }

// MULTIPLE objects MERGE karna:
const defaults = { theme: "light", language: "en" };
const userPrefs = { theme: "dark" };
const finalSettings = { ...defaults, ...userPrefs };
console.log(finalSettings);   // { theme: "dark", language: "en" } — baad wala JEETA


// ============================================================================
// SHALLOW vs DEEP COPY — IMPORTANT GOTCHA
// ============================================================================

const original = { name: "Ali", address: { city: "Karachi" } };
const shallowCopy = { ...original };

shallowCopy.name = "Sara";              // ✅ Top-level property — original SAFE
shallowCopy.address.city = "Lahore";    // ❌ NESTED object — SAME reference hai!

console.log(original.name);            // "Ali" — safe
console.log(original.address.city);    // "Lahore" — ❌ original bhi badal gaya!

// DEEP COPY ke liye (nested objects bhi safe rahein):
const deepCopy = JSON.parse(JSON.stringify(original));
// (Modern tareeqa: structuredClone(original) — newer browsers/Node mein available)


// ============================================================================
// OPTIONAL CHAINING (?.) — Nested property safely access karna
// ============================================================================

const response = { data: { user: { name: "Ali" } } };

// ❌ PURANA TAREEQA — agar koi step missing ho to ERROR:
// const userName2 = response.data.user.name;

// ✅ OPTIONAL CHAINING — agar koi step null/undefined ho, CRASH nahi hoga:
const userName2 = response?.data?.user?.name;
console.log(userName2);   // "Ali"

const missingName = response?.data?.profile?.name;
console.log(missingName);   // undefined — error NAHI aaya


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. Destructuring function parameters mein use karo — code SELF-DOCUMENTING
//    ban jata hai (pata chal jata hai function ko KYA chahiye, dekhne se).

// 2. Spread (...) se IMMUTABLE updates karna React/Vue/Redux jaisi modern
//    frameworks mein STANDARD practice hai — direct mutation BUGS ka
//    sabse bara source hai.

// 3. Shallow vs Deep copy ka farak SAMAJHNA zaroori hai — nested objects
//    ke sath spread (...) DHOKA de sakta hai agar pata na ho.

// 4. Optional chaining (?.) ko Nullish coalescing (??) ke sath milao
//    real production code mein bohot common pattern hai:
//    const city = response?.data?.address?.city ?? "Unknown";
