// 🔹 JavaScript – Overview

// JavaScript ek single-threaded, high-level, interpreted programming language hai.
// Originally sirf browsers (front-end) ke liye banayi gayi thi → pages ko interactive banane ke liye.
// Aaj kal back-end (server) pe bhi chalti hai → Node.js ke zarye.

// 🔹 Front-End JavaScript
// Kya hai?
// Jo code directly browser me chal raha hota hai (Chrome, Firefox, Edge).
// Mostly UI ko interactive banata hai.
// Use Cases:
// Buttons click par action
// Forms validation
// Animations, sliders
// API calls (fetch data from server)

// Frameworks: React, Angular, Vue

// Example:

<button onclick="alert('Hello!')">Click Me</button>
// Ye sirf browser me chalega.

// Back-End JavaScript

// Kya hai?
// Server side pe chalne wala JS code.
// Ye directly browser me nahi, balki Node.js runtime me chalta hai.

// Use Cases:
// API banana (REST / GraphQL)
// Database se data read/write
// Authentication (login, signup)
// Real-time apps (chat apps, sockets)
// Frameworks: Node.js, Express.js, Nest.js
// Example (Node.js server):

const http = require("http");

http.createServer((req, res) => {
  res.write("Hello from backend!");
  res.end();
}).listen(3000);

// Ye server port 3000 pe chalega.


// JavaScript Versions

// JavaScript ki official specification ko ECMAScript (ES) kehte hain.
// Versions har saal update hote hain (ES6, ES7, ES8 …).
// ES5 (2009) → widely supported
// ES6 / ES2015 → modern JS revolution (let/const, arrow functions, classes, promises, modules)
// Latest (2025) → ES15 (har saal naye features aate hain)
//  Browser aur Node.js dono ECMAScript standards follow karte hain.

// 🔹 JavaScript Kahan Run Hota Hai?
// Front-end (Browser) → Browser ka JavaScript Engine run karta hai.
// Chrome → V8
// Firefox → SpiderMonkey
// Safari → JavaScriptCore
// Edge → Chakra
// Back-end (Server) → Mostly Node.js (jo Chrome ka V8 engine use karta hai).
// Node.js ke zarye JS server apps, APIs, CLI tools banata hai.

// 🔹 Fayday (Advantages)
//  Front-End (Client-Side)
// Fast UI interaction (browser me hi chal jata hai, server ki zarurat nahi).
// Rich interfaces (animations, real-time updates).
// Wide community + frameworks.

//  Back-End (Server-Side)
// Same language (JS) dono taraf use hoti hai → ek hi developer front + back dono bana sakta hai (Full-Stack Dev).
// Node.js bahut fast hai (non-blocking I/O, event-driven).
// Huge ecosystem (npm = world’s biggest package manager).

// Summary Table

// | Feature       | Front-End JS (Browser)          | Back-End JS (Server)   |
// | ------------- | ------------------------------- | ---------------------- |
// | Run Hoti Hai? | Browser engine (V8, etc.)       | Node.js runtime        |
// | Purpose       | UI interaction, DOM, animations | APIs, DB, server logic |
// | Example       | Form validation, slider         | Login system, chat app |
// | Frameworks    | React, Vue, Angular             | Node.js, Express, Nest |

// Matlab:
// JavaScript ek full-stack language hai → ek hi language se aap UI (frontend) aur Server (backend) dono bana sakte ho.
// Iska sabse bada power hai: ek language har jagah.



// JavaScript Engine kya hai?

// JavaScript Engine woh program hai jo JavaScript code ko read, interpret aur execute karta hai.
// Har browser aur runtime (Node.js) ka apna engine hota hai.
// Common Engines:
// V8 (Google Chrome, Node.js, Edge)
// Sabse mashhoor engine.
// C++ mein likha gaya.
// Isko Node.js bhi use karta hai (is wajah se JS browser ke bahar bhi chalti hai).
// SpiderMonkey (Mozilla Firefox)
// Yeh pehla JS engine tha, Mozilla ne banaya.
// JavaScriptCore (Safari)
// Apple Safari browser ka engine.

//  Node.js kya hai?
// Node.js ek runtime environment hai jo JavaScript ko browser ke bahar run karne deta hai.
// Yeh bhi V8 Engine use karta hai, lekin uske sath aur bhi cheezen deta hai:
// File System access (read/write files)
// Network access (API server banane ka system)
// Process control
// ➡️ Matlab: Browser ka JavaScript sirf UI aur DOM manipulate karta hai,
// lekin Node.js ki wajah se JavaScript backend server aur APIs bana sakti hai.

// React kya hai?
// React ek frontend library hai (Facebook/Meta ne banayi).
// Yeh JavaScript par hi chalti hai, aur mainly UI banane ke liye use hoti hai.
// React apna khud ka engine nahi rakhta, yeh sirf browser ke JS engine ko use karta hai.

// JavaScript kahan run hota hai?

// Frontend (Browser)
// JS engine (V8, SpiderMonkey, etc.) ke through run hota hai.
// Example: Form validation, animations, API calls, DOM changes.

// Backend (Node.js)
// V8 engine ke sath Node.js APIs ke through run hota hai.
// Example: Database connection, authentication, API server banana.

//  JavaScript ke fayde:

// ✔ Single language for frontend + backend
// ✔ Bohat zyada community support
// ✔ Async programming (non-blocking)
// ✔ Powerful frameworks (React, Angular, Vue for frontend; Express.js for backend)
// ✔ Speed (V8 engine bohat fast hai)

//  Matlab simple shabdon mein:
// JavaScript Engine = Jo JS chalata hai (V8, SpiderMonkey, etc.)
// Node.js = JS ko browser ke bahar chalane ka runtime (backend ke liye)
// React = Frontend library jo UI ko easy banati hai (engine nahi hai, JS par chalti hai).



// Node.js ka Engine

// Node.js sirf ek engine use karta hai → Google ka V8 Engine.
// Yeh Chrome browser ka hi engine hai, jo C++ mein likha gaya hai.
// Iska kaam hai JavaScript code ko machine code mein convert karna taa ke woh bohat fast chale.

// Node.js FAST APIs kyu banata hai?
// Node.js API development ke liye bohat fast mana jata hai. Yeh kuch main reasons hain:
// Non-blocking I/O
// Node.js ek event loop model use karta hai.
// Jab ek request aati hai (jaise DB se data lena), toh woh thread block nahi hota, balki event loop doosre kaam kar leta hai.
// Matlab ek hi server 1000+ requests handle kar sakta hai bina ruke.
// Single-threaded with Event Loop
// Normal servers (jaise PHP, Java, Python) har request ke liye naya thread banate hain → slow aur heavy.
// Node.js sirf ek hi thread use karta hai aur asynchronously kaam karta hai → fast aur lightweight.
// V8 Engine ki speed
// V8 engine JS ko direct machine code mein compile karta hai (JIT compiler).
// Is wajah se Node.js API ka response time bohat fast hota hai.
// NPM Ecosystem (Node Package Manager)
// Ready-made libraries (Express.js, Fastify, NestJS) available hain jo APIs banane ko aur fast aur simple banati hain.

//  Example Flow:
// Client ne API call ki /users par.
// Node.js ne database se data manga.
// Jab tak DB se response aata hai, Node.js free ho kar dusri requests handle karta hai.
// DB ka response aata hi Node.js usse client ko send kar deta hai.

//  Isko hi kehte hain Asynchronous Non-blocking I/O
// ⚡ Short me:
// Engine → V8 (super fast, C++ based)
// Fast APIs → Non-blocking I/O + Single-threaded Event Loop + JIT compilation


// 1. Event Loop

//  Kya hai?
// Event Loop Node.js ka dil hai.
// Yeh ek mechanism hai jo asynchronous tasks (API call, file read, DB query, timer) ko handle karta hai bina server ko block kiye.
// Node.js single-threaded hai, lekin Event Loop ki wajah se multi-tasking jaisa lagta hai.

//  Kaam kaise karta hai?
// Jab JS code run hota hai → synchronous (normal) code pehle execute hota hai.
// Jab async task aata hai (fetch, DB, setTimeout) → usko background mein bhej diya jata hai (Node APIs, libuv handle karte hain).
// Jab woh task complete hota hai → callback Event Queue mein add hota hai.
// Event Loop continuously queue check karta hai aur jab main thread free hota hai toh callback execute kar deta hai.

//  Matlab: Event Loop ke through Node.js ek thread mein hi hazaron requests handle kar leta hai.

//  2. JIT Compilation (Just-In-Time Compilation)
//  Kya hai?
// Normal interpreter line by line code chalata hai → slow hota hai.
// JIT Compiler (V8 engine ka part) JavaScript ko direct machine code mein convert kar deta hai run-time par.
// Yeh machine code CPU ko direct samajh aata hai → isliye bohat fast execution hoti hai.

//  Steps:
// JavaScript code aata hai.
// V8 Engine ka Parser + Interpreter usse bytecode mein convert karta hai.
// JIT Compiler frequently used code ko detect karke usse optimized machine code bana deta hai.
// Machine code CPU pe direct execute hota hai.

//  Matlab: JIT compilation ki wajah se JavaScript ki speed C/C++ level ke close hoti hai.

//  Dono ka Role
// Event Loop = Multiple tasks manage karta hai bina rukke (non-blocking I/O).
// JIT Compilation = Code execution ko turbo speed banata hai (JS → Machine code).
// ➡️ Dono milke Node.js ko fast aur efficient banate hain API banane ke liye.

//  Example socho:

// Tum API call karte ho → DB query lagti hai 2 sec.
// Event Loop kehta hai: "Theek hai, tu wait kar, main dusri requests handle karta hoon."
// Isi waqt dusra banda aya → uski request turant process ho gayi.
// Jab DB ka response aya, Event Loop uska callback execute karke client ko bhej deta hai.
// Aur yeh sab ultra-fast hota hai kyunki JIT Compiler code ko machine code mein run kar raha hota hai.


// Node.js Event Loop + JIT Compilation Flow
// ┌─────────────────────────────┐
// │        JavaScript Code      │
// └──────────────┬──────────────┘
//                │
//                ▼
//       (1) V8 Engine Parse + Interpreter
//                │
//                ▼
//       (2) JIT Compilation (Hot Code)
//        JS → Machine Code (Fast)
//                │
//                ▼
//        Main Thread Execution
//                │
// ┌──────────────┴───────────────┐
// │      Synchronous Tasks        │
// │  (loops, variables, math etc) │
// └──────────────┬───────────────┘
//                │
//                ▼
// ┌─────────────────────────────┐
// │      Async Tasks (API, DB,  │
// │      setTimeout, File I/O)  │
// └──────────────┬──────────────┘
//                │
//                ▼
//       Background (libuv, OS APIs)
//                │
//                ▼
//       Task Completed → Event Queue
//                │
//                ▼
// ┌─────────────────────────────┐
// │       Event Loop Checks      │
// └──────────────┬──────────────┘
//                │
//                ▼
// Callback pushed to Main Thread → Executed 

// Step by Step:
// Code likha → V8 parse karta hai.
// JIT Compiler frequently used code ko machine code bana deta hai (super fast).
// Synchronous code turant chal jata hai.
// Async tasks (API calls, DB, timers) → background mein chalay jate hain.
// Jab woh complete hote hain → Event Queue mein callback aata hai.
// Event Loop continuously check karta hai aur jab thread free hota hai → callback execute kar deta hai.

// ⚡ Is tarah Event Loop + JIT Node.js ko fast banata hai:
// Event Loop → Non-blocking multitasking
// JIT Compilation → Turbo-speed execution