// ============================================================================
// 15 — ADVANCED CONCEPTS: EVENT LOOP, MICROTASKS, CURRYING, THROTTLE
// ============================================================================

// (NOTE: javascript/threas.js mein bhi thread-related notes hain,
// ye file EVENT LOOP ko deeply explain karti hai — JS ki SABSE
// IMPORTANT internal working hai, senior interviews mein ZAROOR puchi jati hai)


// ============================================================================
// JAVASCRIPT SINGLE-THREADED HAI — Phir ASYNC kaam kaise karta hai?
// ============================================================================

// JS EK waqt mein SIRF EK kaam kar sakti hai (single thread) — lekin
// BROWSER/Node.js ke pass EXTRA "helpers" hain: Web APIs, Timers,
// Network — jo HEAVY/SLOW kaam BACKGROUND mein karte hain, aur JS ko
// FREE rakhte hain.

// EVENT LOOP ye decide karta hai: "Kab background se result wapas
// MAIN THREAD mein lana hai"


// ============================================================================
// CALL STACK, WEB APIs, CALLBACK QUEUE — TEEN MAIN PARTS
// ============================================================================

console.log("1 — Synchronous");

setTimeout(() => {
    console.log("2 — Timer (Web API se aaya)");
}, 0);   // ⚠️ 0ms hone ke bawajood, ye TURANT nahi chalega!

console.log("3 — Synchronous");

// OUTPUT ORDER: "1", "3", phir "2"
//
// KYUN? Step-by-step:
// 1. console.log("1") → CALL STACK mein chala, turant print hua
// 2. setTimeout() → Web API ko DE diya gaya (chahe 0ms ho), CALL STACK
//    se HAT gaya, JS AAGE badh gayi
// 3. console.log("3") → CALL STACK mein chala, turant print hua
// 4. AB Call Stack KHAALI hai — EVENT LOOP ne setTimeout ka callback
//    QUEUE se utha kar Call Stack mein dala
// 5. console.log("2") print hua


// ============================================================================
// MICROTASKS (Promises) vs MACROTASKS (setTimeout) — PRIORITY KA FARAK
// ============================================================================

console.log("A — Sync");

setTimeout(() => console.log("B — Macrotask (setTimeout)"), 0);

Promise.resolve().then(() => console.log("C — Microtask (Promise)"));

console.log("D — Sync");

// OUTPUT ORDER: "A", "D", "C", "B"
//
// RULE: MICROTASKS (Promises, async/await) HAMESHA MACROTASKS
// (setTimeout, setInterval) se PEHLE chalti hain — chahe setTimeout
// 0ms ka ho!
//
// Event Loop ka order:
// 1. Pehle SAARI Synchronous code chalti hai (Call Stack khaali hone tak)
// 2. Phir SAARI Microtasks chalti hain (Promise callbacks)
// 3. Phir EK Macrotask chalta hai (setTimeout callback)
// 4. Phir dobara microtasks check hoti hain... (cycle chalta rehta hai)


// ============================================================================
// REAL-WORLD IMPACT — Ye samajhna KYUN zaroori hai
// ============================================================================

async function loadData() {
    console.log("Loading shuru...");

    const data = await fetch("/api/data").then((r) => r.json());
    // await ke baad ka code MICROTASK ki tarah QUEUE hota hai

    console.log("Data mil gaya:", data);
}

loadData();
console.log("Ye PEHLE print hoga, loadData() ke 'Data mil gaya' se!");

// WAJAH: await wala code turant ROOK jata hai aur baqi SYNC code
// (jaise console.log neeche wala) PEHLE chal jata hai


// ============================================================================
// THROTTLE — Debounce se ALAG, lekin similar use case
// ============================================================================

// Debounce (dekho 14_dom_events.js): "Ruk jane ke baad" chalao
// Throttle: "HAR X milliseconds mein EK BAAR se zyada mat chalao"

function throttle(func, limit) {
    let inThrottle;
    return function (...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => (inThrottle = false), limit);
        }
    };
}

// REAL USE CASE: Scroll event — page scroll karte waqt HAR pixel par
// function chalana SLOW kar dega page ko. Throttle se MAX har 200ms
// mein EK baar chalega:
//
// window.addEventListener('scroll', throttle(() => {
//     console.log('Scroll position:', window.scrollY);
// }, 200));


// ============================================================================
// CURRYING — Function jo STEP-BY-STEP arguments leta hai (Functional Programming)
// ============================================================================

// Normal function:
function add3(a, b, c) {
    return a + b + c;
}
console.log(add3(1, 2, 3));   // 6

// CURRIED version — EK EK argument lekar, NAYA function return karta hai:
function curriedAdd(a) {
    return function (b) {
        return function (c) {
            return a + b + c;
        };
    };
}
console.log(curriedAdd(1)(2)(3));   // 6

// Arrow function se CLEANER:
const curriedAddArrow = (a) => (b) => (c) => a + b + c;
console.log(curriedAddArrow(1)(2)(3));   // 6

// REAL USE CASE — Reusable, PARTIALLY-applied functions:
const applyDiscount = (discountPercent) => (price) => price - (price * discountPercent) / 100;

const apply10PercentOff = applyDiscount(10);   // Discount FIX kar diya
console.log(apply10PercentOff(1000));          // 900
console.log(apply10PercentOff(500));           // 450


// ============================================================================
// MEMORY MANAGEMENT — Garbage Collection BASICS
// ============================================================================

// JavaScript AUTOMATICALLY memory manage karti hai (Garbage Collector) —
// jo OBJECTS ab kisi VARIABLE se REACHABLE nahi hote, GC unhe HATA deta hai.

function createUser() {
    let user = { name: "Ali" };   // Memory mein allocate hua
    return user;
}

let myUser = createUser();
myUser = null;   // Ab { name: "Ali" } object ko koi REFER nahi karta —
                  // Garbage Collector isay HATA dega

// MEMORY LEAK ka common reason: EVENT LISTENERS jo REMOVE nahi hote,
// ya CLOSURES jo bewajah BARI cheezein "yaad" rakhte hain (dekho 07_scope_closures.js)


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. Event Loop ka order (Sync → Microtasks → Macrotasks) INTERVIEW mein
//    BOHOT common hai — "predict the output" type sawal isi par based hote hain.

// 2. Throttle/Debounce dono PERFORMANCE optimization patterns hain —
//    senior developers se EXPECT kiya jata hai ke ye REAL projects mein
//    apply kar sakein (search box = debounce, scroll/resize = throttle).

// 3. Currying Functional Programming ka core concept hai — REACT/Redux
//    jaisi libraries mein bhi use hota hai (jaise connect()(Component)).

// 4. Memory leaks DEBUG karna senior-level skill hai — Chrome DevTools
//    ke "Memory" tab se HEAP SNAPSHOTS lekar leaks dhoond sakte ho.
