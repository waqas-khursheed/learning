// ============================================================================
// 10 — ASYNCHRONOUS JS: CALLBACKS, PROMISES, ASYNC/AWAIT
// ============================================================================

// (NOTE: javascript/Synchronous_vs_Asynchronous.js mein bhi basic intro hai,
// ye file DETAIL aur REAL EXAMPLES ke sath cover karti hai)


// ============================================================================
// SYNCHRONOUS vs ASYNCHRONOUS — Bunyadi Farak
// ============================================================================

// SYNCHRONOUS: Code LINE BY LINE chalta hai, har line PEHLI line khatam
// hone ka WAIT karti hai.

console.log("1");
console.log("2");
console.log("3");
// Output: 1, 2, 3 (HAMESHA isi order mein)


// ASYNCHRONOUS: Kuch operations (jaise API call, file read, timer) TIME
// LETE hain — JS unhe BACKGROUND mein chalne deta hai, aur AAGE wala
// code RUKTA NAHI, chalta rehta hai.

console.log("Order 1");
setTimeout(() => {
    console.log("Order 2 — 2 second baad aaya");
}, 2000);
console.log("Order 3");
// Output: "Order 1", "Order 3", phir 2 second baad "Order 2"
// (Order 3 ne Order 2 ka WAIT NAHI kiya!)


// ============================================================================
// CALLBACKS — Async JS ka SABSE PURANA tareeqa (Problem: "CALLBACK HELL")
// ============================================================================

function fetchUser(id, callback) {
    setTimeout(() => {
        callback({ id: id, name: "Ali" });
    }, 1000);
}

function fetchOrders(userId, callback) {
    setTimeout(() => {
        callback([{ id: 1, item: "Laptop" }]);
    }, 1000);
}

// ❌ CALLBACK HELL — Nested callbacks, padhna mushkil ("Pyramid of Doom"):
fetchUser(1, (user) => {
    fetchOrders(user.id, (orders) => {
        console.log(user, orders);
        // Agar aur steps hon, aur nested ho jata — UNREADABLE ban jata hai
    });
});


// ============================================================================
// PROMISES — Callback Hell ka SOLUTION (Modern, cleaner)
// ============================================================================

// Promise = "Vaada" — ke koi async kaam ya to SUCCESS hoga (resolve)
// ya FAIL hoga (reject), aur tumhe baad mein result milega.

function fetchUserPromise(id) {
    return new Promise((resolve, reject) => {
        setTimeout(() => {
            if (id) {
                resolve({ id: id, name: "Ali" });   // Success
            } else {
                reject(new Error("User ID zaroori hai"));   // Failure
            }
        }, 1000);
    });
}

// .then() — SUCCESS case handle karta hai
// .catch() — FAILURE case handle karta hai
fetchUserPromise(1)
    .then((user) => {
        console.log("User mila:", user);
        return user.id;
    })
    .then((userId) => {
        console.log("User ID:", userId);
    })
    .catch((error) => {
        console.log("Error aaya:", error.message);
    });


// ============================================================================
// PROMISE CHAINING — Callback Hell ki jagah CLEAN chain
// ============================================================================

function fetchOrdersPromise(userId) {
    return new Promise((resolve) => {
        setTimeout(() => resolve([{ id: 1, item: "Laptop" }]), 1000);
    });
}

// ✅ CLEAN — chahe kitne bhi steps hon, FLAT rehta hai (nested nahi hota):
fetchUserPromise(1)
    .then((user) => fetchOrdersPromise(user.id))
    .then((orders) => console.log("Orders:", orders))
    .catch((error) => console.log("Error:", error.message));


// ============================================================================
// ASYNC/AWAIT — Promises ka SABSE MODERN, SABSE READABLE syntax
// ============================================================================

// async/await ANDAR SE Promises hi use karta hai — bas SYNCHRONOUS
// code jaisa DIKHTA hai (asaan parhna/samajhna)

async function getUserData() {
    try {
        const user = await fetchUserPromise(1);       // Promise resolve hone ka WAIT
        console.log("Async User:", user);

        const orders = await fetchOrdersPromise(user.id);
        console.log("Async Orders:", orders);

        return { user, orders };
    } catch (error) {
        // try/catch se ERRORS handle hote hain (.catch() ki jagah)
        console.log("Async Error:", error.message);
    }
}

getUserData();

// RULES:
// - "async" keyword function ke pehle lagana ZAROORI hai await use karne ke liye
// - "await" sirf async function ke ANDAR use ho sakta hai
// - await Promise ke RESOLVE hone tak WAIT karta hai, result seedha return karta hai


// ============================================================================
// PROMISE.all() — MULTIPLE async operations EK SAATH (PARALLEL) chalana
// ============================================================================

async function loadDashboard() {
    // ❌ SLOW — Sequential (ek ke baad ek, total time = sum of all):
    // const user = await fetchUserPromise(1);      // 1 second
    // const orders = await fetchOrdersPromise(1);   // +1 second = 2 second total

    // ✅ FAST — Parallel (sab EK SAATH chalte hain, total time = SABSE LAMBA wala):
    const [user, orders] = await Promise.all([
        fetchUserPromise(1),
        fetchOrdersPromise(1),
    ]);
    // Total time = 1 second (dono PARALLEL chale, sequential nahi)

    console.log("Dashboard:", user, orders);
}

loadDashboard();

// REAL EXAMPLE: Agar EK page par 3 ALAG APIs se data chahiye
// (user info, notifications, settings) aur EK doosre par depend NAHI
// karte, to Promise.all() use karo — MUCH FASTER hota hai.


// ============================================================================
// Promise.allSettled() — Jab kuch promises FAIL bhi ho sakte hain (safe version)
// ============================================================================

async function loadWithFailures() {
    const results = await Promise.allSettled([
        fetchUserPromise(1),
        fetchUserPromise(null),   // Ye REJECT hoga
    ]);

    results.forEach((result) => {
        if (result.status === "fulfilled") {
            console.log("Success:", result.value);
        } else {
            console.log("Failed:", result.reason.message);
        }
    });
}
// Promise.all() agar EK bhi promise fail ho to POORA reject ho jata hai
// Promise.allSettled() HAR promise ka result deta hai, chahe fail ho ya pass ho


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. async/await ko DEFAULT choice banao modern code mein — .then() chains
//    se ZYADA readable hai, especially jab MULTIPLE steps ho.

// 2. Promise.all() use karo jab MULTIPLE INDEPENDENT async calls ho —
//    sequential await sequence SLOW hota hai bewajah.

// 3. try/catch HAMESHA lagao async functions mein — warna UNHANDLED
//    PROMISE REJECTION error aata hai (silent failure, debug karna mushkil)

// 4. Laravel ke Event/Queue system se concept SIMILAR hai (dekho
//    event-listener/ folder) — async JS bhi "kaam ko background mein
//    bhejo, jab ready ho tab result lo" — same philosophy hai.
