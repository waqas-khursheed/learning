// ============================================================================
// 08 — 'this' KEYWORD, call(), apply(), bind() (SABSE CONFUSING JS TOPIC)
// ============================================================================


// ============================================================================
// 'this' KA BASIC RULE: "JIS OBJECT SE FUNCTION CALL HUA, 'this' WAHI OBJECT HAI"
// ============================================================================

const user = {
    name: "Ali",
    greet() {
        console.log(`Hello, ${this.name}`);   // 'this' = user (kyunke user.greet() call hua)
    },
};

user.greet();   // "Hello, Ali"


// ============================================================================
// PROBLEM 1 — Function ko VARIABLE mein assign karke call karna ('this' khoo jata hai)
// ============================================================================

const greetFunction = user.greet;
greetFunction();   // "Hello, undefined" ❌ — 'this' ab user nahi raha!

// WAJAH: 'this' ka decision CALL-TIME par hota hai, function KAHAN define
// hua tha usse farak nahi parta. Yahan greetFunction() ko BINA kisi
// object ke call kiya — isliye 'this' undefined (strict mode) ya
// global object (non-strict mode) ban gaya.


// ============================================================================
// PROBLEM 2 — Regular function CALLBACK mein 'this' kho jata hai
// ============================================================================

const timer = {
    seconds: 0,
    start() {
        setInterval(function () {
            this.seconds++;   // ❌ 'this' yahan 'timer' NAHI hai (setInterval ne call kiya)
            console.log(this.seconds);   // NaN (this.seconds undefined hai)
        }, 1000);
    },
};
// timer.start();   // Bug — agar chalao to NaN print hoga


// ============================================================================
// SOLUTION — ARROW FUNCTION use karo (Arrow function APNA 'this' NAHI rakhti)
// ============================================================================

const timerFixed = {
    seconds: 0,
    start() {
        setInterval(() => {
            this.seconds++;   // ✅ Arrow function OUTER scope ka 'this' use karti hai (timerFixed)
            console.log(this.seconds);
        }, 1000);
    },
};
// timerFixed.start();   // 1, 2, 3, 4... sahi chalega

// RULE: Arrow function 'this' ko khud DEFINE NAHI karti — jahan LIKHI
// gayi hai, wahan ke surrounding scope ka 'this' "inherit" karti hai
// (LEXICAL this — dekho 07_scope_closures.js mein Lexical Scope)


// ============================================================================
// call() — Function ko CALL karte waqt 'this' MANUALLY specify karna
// ============================================================================

function introduce() {
    console.log(`Main ${this.name} hoon, ${this.city} se`);
}

const person1 = { name: "Ali", city: "Karachi" };
const person2 = { name: "Sara", city: "Lahore" };

introduce.call(person1);   // "Main Ali hoon, Karachi se"
introduce.call(person2);   // "Main Sara hoon, Lahore se"

// call() arguments INDIVIDUALLY pass karta hai:
function introduceWithAge(age, profession) {
    console.log(`${this.name}, ${age} saal, ${profession}`);
}
introduceWithAge.call(person1, 25, "Developer");   // "Ali, 25 saal, Developer"


// ============================================================================
// apply() — call() jaisa hi hai, LEKIN arguments ARRAY mein pass karte hain
// ============================================================================

introduceWithAge.apply(person1, [25, "Developer"]);   // SAME result jaisa call()

// REAL USE CASE: Jab arguments ARRAY mein already maujood hon
const numbers = [5, 2, 8, 1, 9];
console.log(Math.max.apply(null, numbers));   // 9 — array ko individual args ki tarah pass kiya
// (Modern alternative: Math.max(...numbers) — spread operator, 02 file dekho)


// ============================================================================
// bind() — call() jaisa, LEKIN FORAN CALL nahi karta — NAYA function RETURN karta hai
// ============================================================================

const aliIntroduce = introduce.bind(person1);   // 'this' PERMANENTLY person1 ho gaya
aliIntroduce();   // "Main Ali hoon, Karachi se" — baad mein call kiya, tab bhi sahi 'this'

// REAL-WORLD USE CASE — Event handlers mein 'this' fix karna:
class Button {
    constructor(label) {
        this.label = label;
        // bind() na karo to handleClick ke andar 'this' undefined ho jayega
        this.handleClick = this.handleClick.bind(this);
    }

    handleClick() {
        console.log(`${this.label} button clicked`);
    }
}

const submitButton = new Button("Submit");
const clickHandler = submitButton.handleClick;
clickHandler();   // "Submit button clicked" — bind() ki wajah se 'this' safe hai


// ============================================================================
// COMPARISON TABLE — call() vs apply() vs bind()
// ============================================================================

/*
 * ┌──────────┬──────────────────────┬─────────────────────────────────┐
 * │ Method    │ Arguments kaise dete │ Foran CALL karta hai?            │
 * ├──────────┼──────────────────────┼─────────────────────────────────┤
 * │ call()     │ Individually (a, b, c)│ ✅ Haan, foran chalta hai        │
 * │ apply()    │ Array mein [a, b, c]  │ ✅ Haan, foran chalta hai        │
 * │ bind()     │ Individually (a, b, c)│ ❌ Nahi, NAYA function return    │
 * │            │                        │    karta hai, baad mein call ho │
 * └──────────┴──────────────────────┴─────────────────────────────────┘
 */


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. Modern React/Class components mein bind() KAAFI common hai constructor
//    mein (jaisa Button example mein dikhaya) — lekin arrow function class
//    fields (class properties) is problem ko AUTOMATICALLY solve kar dete hain:
//
//    handleClick = () => {
//        console.log(`${this.label} clicked`);   // bind() ki zaroorat NAHI
//    }

// 2. Object METHOD ke liye REGULAR function use karo ('this' chahiye),
//    CALLBACK/EVENT HANDLER ke andar ARROW function use karo (outer
//    'this' chahiye) — ye Section 04 ka rule yahan explain ho gaya.

// 3. Interview mein 'this' ke sawal ALMOST HAMESHA puche jate hain —
//    is file ke saare examples khud chala kar dekho, output predict
//    karne ki practice karo.
