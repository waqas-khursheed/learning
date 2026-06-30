// ============================================================================
// 07 — SCOPE & CLOSURES (SABSE CONFUSING LEKIN IMPORTANT TOPIC)
// ============================================================================


// ============================================================================
// SCOPE KYA HOTA HAI?
// ============================================================================

// Scope batata hai "variable KAHAN se accessible hai"

// 1. GLOBAL SCOPE — File ke top-level par, HAR JAGAH accessible
let globalVar = "Main global hoon";

function showGlobal() {
    console.log(globalVar);   // ✅ Accessible — global scope se
}

// 2. FUNCTION SCOPE — Sirf usi function ke ANDAR accessible
function myFunction() {
    let functionVar = "Main function ke andar hoon";
    console.log(functionVar);   // ✅ Accessible
}
// console.log(functionVar);   // ❌ ERROR — bahar accessible NAHI

// 3. BLOCK SCOPE — Sirf {} ke ANDAR accessible (let/const ke liye)
if (true) {
    let blockVar = "Main block ke andar hoon";
    console.log(blockVar);   // ✅ Accessible
}
// console.log(blockVar);   // ❌ ERROR — block ke bahar accessible NAHI


// ============================================================================
// var vs let — SCOPE KA BARA FARAK
// ============================================================================

function scopeDemo() {
    if (true) {
        var varVariable = "var hoon";     // FUNCTION-scoped
        let letVariable = "let hoon";     // BLOCK-scoped
    }

    console.log(varVariable);   // ✅ "var hoon" — block ke BAHAR bhi accessible!
    // console.log(letVariable);  // ❌ ERROR — block ke bahar accessible NAHI
}
scopeDemo();

// YE EXACT WAJAH HAI ke var AVOID kiya jata hai — unexpected jagah
// accessible ho jata hai, bugs create karta hai.


// ============================================================================
// LEXICAL SCOPE — Inner function, OUTER function ke variables ACCESS kar sakta hai
// ============================================================================

function outerFunction() {
    const outerVar = "Outer se aaya";

    function innerFunction() {
        console.log(outerVar);   // ✅ Inner function, outer ka variable dekh sakta hai
    }

    innerFunction();
}
outerFunction();   // "Outer se aaya"

// LEXICAL SCOPE ka matlab: Function jahan LIKHA gaya hai (define hua),
// wahan ke surrounding scope ko HAMESHA access kar sakta hai — chahe
// function kahin bhi se CALL kiya jaye.


// ============================================================================
// CLOSURE — Lexical Scope ka POWERFUL result (SABSE IMPORTANT INTERVIEW TOPIC)
// ============================================================================

// Closure = Function jo apne OUTER scope ke variables ko "yaad" rakhta hai,
// CHAHE outer function khatam ho chuka ho!

function createCounter() {
    let count = 0;   // Ye PRIVATE variable hai — bahar se access NAHI ho sakta

    return function () {
        count++;
        return count;
    };
}

const counter = createCounter();   // createCounter() KHATAM ho chuka hai
console.log(counter());   // 1
console.log(counter());   // 2
console.log(counter());   // 3

// HAIRAT KI BAAT: createCounter() function khatam ho chuka hai, lekin
// returned function ABHI BHI "count" variable ko YAAD rakhta hai —
// YE HAI CLOSURE!


// ============================================================================
// REAL-WORLD EXAMPLE 1 — PRIVATE VARIABLES (Encapsulation jaisa, dekho oop/02)
// ============================================================================

function createBankAccount(initialBalance) {
    let balance = initialBalance;   // PRIVATE — bahar se direct access NAHI

    return {
        deposit(amount) {
            balance += amount;
            return balance;
        },
        withdraw(amount) {
            if (amount > balance) {
                console.log("Insufficient balance");
                return balance;
            }
            balance -= amount;
            return balance;
        },
        getBalance() {
            return balance;
        },
    };
}

const account = createBankAccount(1000);
console.log(account.deposit(500));    // 1500
console.log(account.withdraw(200));   // 1300
console.log(account.balance);         // undefined — DIRECT access NAHI ho sakta!


// ============================================================================
// REAL-WORLD EXAMPLE 2 — EVENT HANDLERS mein closure (bohot common)
// ============================================================================

function setupButtonCounter() {
    let clickCount = 0;

    return function handleClick() {
        clickCount++;
        console.log(`Button ${clickCount} baar click hua`);
    };
}

const handleClick = setupButtonCounter();
// button.addEventListener('click', handleClick);
// Har click par clickCount yaad rehta hai (closure ki wajah se)


// ============================================================================
// CLASSIC CLOSURE BUG — for LOOP mein var ka masla
// ============================================================================

// ❌ GALAT (var ke sath):
for (var i = 1; i <= 3; i++) {
    setTimeout(function () {
        console.log("var loop: " + i);   // 4, 4, 4 (teeno baar 4 print hota hai!)
    }, 100);
}
// WAJAH: var FUNCTION-scoped hai, loop khatam hone tak i=4 ho chuka hota hai,
// teeno setTimeout callbacks SAME "i" ko refer karte hain.

// ✅ SAHI (let ke sath):
for (let j = 1; j <= 3; j++) {
    setTimeout(function () {
        console.log("let loop: " + j);   // 1, 2, 3 (sahi order mein)
    }, 200);
}
// WAJAH: let BLOCK-scoped hai — har iteration ka APNA ALAG "j" banta hai


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. Closures Encapsulation/Module Pattern banane ke liye use hote hain —
//    PURANI JS mein (Classes se pehle) yahi tareeqa tha private data banane ka.

// 2. React Hooks (useState, useEffect) ANDAR SE closures use karte hain —
//    isay samajhna React seekhne ke liye bohot zaroori hai.

// 3. Memory leak ka khayal rakho — closures variable ko MEMORY mein
//    "zinda" rakhte hain jab tak closure khud exist karta hai. Bohot
//    saare closures (jaise event listeners jo remove na hon) memory
//    leak create kar sakte hain.
