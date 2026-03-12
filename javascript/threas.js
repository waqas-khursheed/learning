// 1. Pehle “Thread” kya hota hai?

// Thread basically ek lightweight process hoti hai —
// yaani ek chhoti unit jo program ke andar independently code execute karti hai.
// Ek program ke andar multiple threads ho sakti hain jo alag-alag kaam ek saath karti hain.

// ⚙️ 2. Example se samjho

//  Socho aapke paas ek kitchen hai (program),
// aur usmein ek cook (thread) hai.
// Agar sirf ek cook hai → to wo ek hi time ek kaam karega.
// Agar 4 cooks hain → sab alag kaam ek saath karenge.
// Single-threaded: ek cook → ek kaam ek waqt pe
// Multi-threaded: multiple cooks → multiple kaam parallel

// 3. Single-Threaded vs Multi-Threaded

// | Concept   | Single-threaded                        | Multi-threaded                           |
// | --------- | -------------------------------------- | ---------------------------------------- |
// | Threads   | 1 main thread                          | Multiple threads                         |
// | Execution | One task at a time                     | Multiple tasks simultaneously            |
// | Example   | Node.js (main thread + async handling) | Java, C#, Python (multi-thread)          |
// | Advantage | Simpler, less memory                   | Parallelism (fast for CPU-heavy)         |
// | Problem   | CPU heavy tasks block main thread      | Needs synchronization, complex debugging |


// 4. Node.js Single-threaded kyun hai?
// Node.js ko single-threaded bola jata hai kyunki:
// JavaScript code sirf ek main thread (Event Loop) pe chalta hai.
// Ye non-blocking asynchronous model use karta hai.
// Matlab:
// Agar ek task heavy hai (like DB query, API call),
// to Node.js us task ko background threads (libuv thread pool) me bhej deta hai
// aur main thread free rehta hai dusre requests handle karne ke liye.

// 5. “libuv” aur “Worker Threads” kya karte hain?

// Node.js ke andar libuv naam ki C++ library hoti hai.
// Iske andar ek thread pool (default: 4 threads) hoti hai —
// jo background tasks handle karti hai jaise:
// File system I/O
// DNS lookup
// Crypto operations
// Network operations

// ┌──────────────────────┐
// │   Your JS Code       │
// │  (Single Thread)     │
// └──────────┬───────────┘
//            │
// ┌──────────▼───────────┐
// │     Event Loop       │
// └──────────┬───────────┘
//            │
// ┌───────────▼────────────┐
// │   libuv Thread Pool    │
// │ (4 background threads) │
// └────────────────────────┘

// 6. Worker Threads in Node.js (Manual Multi-threading)

// Agar aapko CPU-intensive kaam karna hai
// (e.g. image processing, encryption, heavy loops),
// to aap manually Worker Threads module use kar sakte ho.
// main.js
const { Worker } = require('worker_threads');

console.log("Main thread start");

const worker = new Worker('./worker.js');
worker.on('message', (msg) => console.log("From worker:", msg));

console.log("Main thread continues...");
// worker.js
const { parentPort } = require('worker_threads');

let sum = 0;
for (let i = 0; i < 1e9; i++) sum += i;

parentPort.postMessage(`Sum is ${sum}`);

// Yahan:
// main.js main thread hai.
// worker.js ek new thread pe chalta hai.
// Matlab ab Node.js bhi multi-threaded kaam kar raha hai —
// lekin default code hamesha single-threaded hota hai.

// 7. Compare to PHP/Laravel

// | Feature     | Node.js                      | Laravel (PHP)                            |
// | ----------- | ---------------------------- | ---------------------------------------- |
// | Execution   | Single-threaded (event loop) | Multi-process (Apache/Nginx spawns many) |
// | Concurrency | Asynchronous (non-blocking)  | Multiple PHP-FPM workers                 |
// | Scalability | High for I/O tasks           | High for CPU with multiple processes     |
// | Thread Pool | Yes (libuv)                  | Each request = new PHP process           |


// So:
// Laravel → multiple processes handle requests separately
// Node.js → ek process handle karta hai sab async way mein

// Summary
// | Term                | Meaning                                                     |
// | ------------------- | ----------------------------------------------------------- |
// | **Thread**          | Execution unit inside a program                             |
// | **Single-threaded** | One thread executes code (Node.js main thread)              |
// | **Multi-threaded**  | Multiple threads run in parallel (Java, C#)                 |
// | **Node.js**         | Single-threaded for JS, multi-threaded internally via libuv |
// | **Worker Threads**  | Node feature for CPU-heavy parallel tasks                   |
