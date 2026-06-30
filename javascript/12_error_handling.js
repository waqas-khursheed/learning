// ============================================================================
// 12 — ERROR HANDLING (try/catch/finally, Custom Errors)
// ============================================================================


// ============================================================================
// BASIC try/catch/finally
// ============================================================================

try {
    const result = JSON.parse("{ invalid json }");   // Ye ERROR throw karega
    console.log(result);
} catch (error) {
    console.log("Error pakra gaya:", error.message);
} finally {
    console.log("Ye HAMESHA chalta hai — chahe error aaye ya na aaye");
}


// ============================================================================
// throw — KHUD ERROR banana
// ============================================================================

function divide(a, b) {
    if (b === 0) {
        throw new Error("Zero se divide nahi kar sakte");
    }
    return a / b;
}

try {
    console.log(divide(10, 0));
} catch (error) {
    console.log("Caught:", error.message);   // "Caught: Zero se divide nahi kar sakte"
}


// ============================================================================
// ERROR OBJECT KI PROPERTIES
// ============================================================================

try {
    null.someProperty;   // TypeError throw hoga
} catch (error) {
    console.log(error.name);       // "TypeError"
    console.log(error.message);    // "Cannot read properties of null..."
    console.log(error.stack);      // Poora stack trace (debugging ke liye)
}


// ============================================================================
// BUILT-IN ERROR TYPES
// ============================================================================

// TypeError    → Galat type par operation (jaise null.property)
// ReferenceError → Variable jo define hi nahi hua use karna
// SyntaxError    → Code likhne mein syntax galti (JSON.parse invalid string)
// RangeError     → Number range se bahar (jaise Array(-1))

try {
    undefinedVariable;   // ReferenceError
} catch (error) {
    console.log(error instanceof ReferenceError);   // true
}


// ============================================================================
// CUSTOM ERROR CLASSES — Production code mein bohot common (Senior pattern)
// ============================================================================

class ValidationError extends Error {
    constructor(message, field) {
        super(message);   // Parent Error class ka constructor call karna ZAROORI hai
        this.name = "ValidationError";
        this.field = field;
    }
}

class NotFoundError extends Error {
    constructor(resource) {
        super(`${resource} nahi mila`);
        this.name = "NotFoundError";
    }
}

function validateUser(user) {
    if (!user.email) {
        throw new ValidationError("Email zaroori hai", "email");
    }
    if (!user.email.includes("@")) {
        throw new ValidationError("Email format galat hai", "email");
    }
}

function findUser(id) {
    const users = { 1: { name: "Ali" } };
    if (!users[id]) {
        throw new NotFoundError("User");
    }
    return users[id];
}

// AB DIFFERENT error types ko ALAG ALAG handle kar sakte hain:
try {
    validateUser({ email: "invalid-email" });
} catch (error) {
    if (error instanceof ValidationError) {
        console.log(`Validation Error in field "${error.field}": ${error.message}`);
    } else {
        console.log("Kuch aur error hua:", error.message);
    }
}

try {
    findUser(999);
} catch (error) {
    if (error instanceof NotFoundError) {
        console.log("404:", error.message);
    }
}


// ============================================================================
// ASYNC ERROR HANDLING — Promises aur async/await ke sath
// ============================================================================

async function fetchUserData(id) {
    try {
        if (!id) {
            throw new ValidationError("User ID zaroori hai", "id");
        }

        // Simulate API call
        const response = await new Promise((resolve, reject) => {
            setTimeout(() => {
                if (id === 999) {
                    reject(new NotFoundError("User"));
                } else {
                    resolve({ id, name: "Ali" });
                }
            }, 500);
        });

        return response;
    } catch (error) {
        // Error ko LOG karo, lekin caller ko bhi BATAO (re-throw)
        console.log(`fetchUserData mein error: ${error.message}`);
        throw error;   // Caller ko bhi handle karne do
    }
}


// ============================================================================
// MULTIPLE CATCH-JAISA PATTERN (JS mein multiple catch BLOCKS nahi hote,
// PHP/Java ki tarah — instanceof se check karna parta hai)
// ============================================================================

async function handleUserRequest(id) {
    try {
        const user = await fetchUserData(id);
        console.log("Success:", user);
    } catch (error) {
        if (error instanceof ValidationError) {
            console.log("400 Bad Request:", error.message);
        } else if (error instanceof NotFoundError) {
            console.log("404 Not Found:", error.message);
        } else {
            console.log("500 Server Error:", error.message);
        }
    }
}

handleUserRequest(999);   // "404 Not Found: User nahi mila"
handleUserRequest(null);  // "400 Bad Request: User ID zaroori hai"


// ============================================================================
// GLOBAL ERROR HANDLING — Unhandled errors pakadna (Production monitoring)
// ============================================================================

// Browser mein:
// window.addEventListener('error', (event) => {
//     console.log('Unhandled error:', event.error);
//     // Sentry/Bugsnag jaisi service ko bhej sakte ho
// });

// window.addEventListener('unhandledrejection', (event) => {
//     console.log('Unhandled promise rejection:', event.reason);
// });

// Node.js mein:
// process.on('uncaughtException', (error) => { ... });
// process.on('unhandledRejection', (reason) => { ... });


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. Custom Error classes banana PRODUCTION code mein STANDARD practice
//    hai — generic "Error" ke bajaye, specific error types se caller
//    KO PATA chalta hai EXACTLY kya ghalat hua (jaise Laravel exceptions:
//    ValidationException, ModelNotFoundException)

// 2. try/catch ko sirf WAHAN lagao jahan tum WAQAI error ko HANDLE kar
//    sakte ho (retry, fallback, user ko message dikhana). Bewajah HAR
//    function mein try/catch lagana NOISE create karta hai.

// 3. Async function mein error throw karna automatically Promise ko
//    REJECT karta hai — caller ko .catch() ya try/catch se handle karna hota hai.

// 4. finally block CLEANUP ke liye perfect hai (loading spinner band
//    karna, connection close karna) — chahe success ho ya fail, HAMESHA chalta hai.
