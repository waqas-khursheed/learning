// Synchronous vs Asynchronous JavaScript 

// 🔹 1. Synchronous JavaScript
// Code line by line (one after another) execute hota hai.
// Jab tak ek line complete na ho, next line start nahi hoti.
// Isko hum blocking code bhi kehte hain.

console.log("1. Start");

function task() {
  for (let i = 0; i < 1000000000; i++) {} // heavy loop
  console.log("2. Task complete");
}

task();

console.log("3. End");

// Output hoga:

// 1. Start
// 2. Task complete
// 3. End
// Yahan loop kaam khatam hone tak sab block ho gaya, page freeze bhi ho sakta hai.


// 🔹 2. Asynchronous JavaScript
// Code non-blocking hota hai.
// Matlab program ko rukne nahi deta, dusra kaam parallel chalta rahta hai.
// Mostly setTimeout, API calls, promises, async/await se hota hai.
// Jab async task complete ho jata hai, tab result milta hai (callback / promise resolve hota hai).

console.log("1. Start");

setTimeout(() => {
  console.log("2. Task complete after 2s");
}, 2000);

console.log("3. End");

// Output hoga:

// 1. Start
// 3. End
// 2. Task complete after 2s

// Yahan pe setTimeout background me chal gaya, aur baaki code turant run ho gaya.


// | Feature     | Synchronous 🕐         | Asynchronous ⚡          |
// | ----------- | ---------------------- | ----------------------- |
// | Execution   | Line by line           | Parallel / non-blocking |
// | Performance | Slow if task heavy     | Fast, smooth UI         |
// | Example     | Loops, normal code     | setTimeout, API calls   |
// | Blocking?   | Yes (blocks next code) | No (doesn’t block)      |

// Real life analogy:

// Synchronous: Tum pizza order karo aur wahi khade raho jab tak pizza na aaye.
// Asynchronous: Tum pizza order karo, aur dusre kaam karte raho. Jab pizza aa jaye to waiter bula lega.


// 🔹 Default Behavior

// JavaScript single-threaded language hai → ek hi thread pe code run hota hai.
// Is liye by default sab kuch line-by-line synchronous execute hota hai.
// Agar koi heavy task ho (jaise bada loop ya calculation) to wo agla code block kar dega

// 🔹 Asynchronous kaise hota hai?
// Asynchronous sirf tab hota hai jab aap browser APIs ya Node.js APIs use karte ho, jaise:
// setTimeout, setInterval
// fetch() (API calls)
// Promise, async/await
// File read/write (Node.js)
// Ye kaam background me chalte hain aur result baad me JavaScript ko de dete hain (event loop ke zarye).

// 🔹 Example (default synchronous)

// console.log("1");
// console.log("2");
// console.log("3");

// Output hamesha sequence me hoga:
// 1
// 2
// 3


// Example (async APIs se asynchronous)

console.log("1");

setTimeout(() => {
  console.log("2 (after 2 sec)");
}, 2000);

console.log("3");

// 1
// 3
// 2 (after 2 sec)

// Matlab:
// JavaScript khud default me synchronous hoti hai
// lekin jab aap asynchronous functions use karte ho (setTimeout, fetch, promises, async/await), tab wo async behavior show karta hai.


// 1. Asynchronous JavaScript me kya kya hota hai?

// Async code mostly browser ya Node.js APIs ke through run hota hai. Ye background me chal jaata hai aur jab result milta hai tab JS ko wapas deta hai.

// Common async features:

// setTimeout → ek dafa ka delay.
// setInterval → bar-bar repeat karna specific time interval ke baad.
// fetch() / AJAX → server se data mangna.
// Promises → future me result dena.
// async/await → promises ko easy likhne ka tareeqa.

// 🔹 2. Promise kya hai?
// Promise ek object hai jo future me result dega (success ya fail).
// 3 states hoti hain:
// Pending → result abhi aana baqi hai.
// Resolved (fulfilled) → kaam success ho gaya.
// Rejected → error aa gaya.


let promise = new Promise((resolve, reject) => {
  let success = true;
  if (success) {
    resolve("Kaam ho gaya!");
  } else {
    reject("Error aa gaya!");
  }
});

promise
  .then(result => console.log(result)) // success case
  .catch(error => console.log(error)); // error case

//   3. async / await kyu lagate hain?

// Promises ko handle karna kabhi complex lagta hai (nested .then() chain).
// Isko simple banane ke liye async/await aaya.

// async function → ye hamesha ek promise return karta hai.

// await → promise ka result aane tak rukta hai (sirf async function ke andar hi use hota hai).

// Example with Promise:

function getData() {
  return new Promise(resolve => {
    setTimeout(() => {
      resolve("Data loaded");
    }, 2000);
  });
}

getData().then(result => console.log(result));

// Same Example with async/await:

async function fetchData() {
  let result = await getData();
  console.log(result);
}

fetchData();

// Magar async/await likhne me zyada clean aur readable hai.

// setInterval

// Bar-bar har interval me repeat hota hai jab tak roka na jaye (clearInterval se stop karte hain).

let count = 0;

let interval = setInterval(() => {
  count++;
  console.log("Running...", count);

  if (count === 5) {
    clearInterval(interval);
  }
}, 1000);


// Running... 1
// Running... 2
// Running... 3
// Running... 4
// Running... 5


// Summary Table

// | Feature         | Use Case                                   |
// | --------------- | ------------------------------------------ |
// | **Promise**     | Future result handle karna (success/fail)  |
// | **async/await** | Promises ko simple aur readable banata hai |
// | **setTimeout**  | Delay ke baad ek dafa code run karna       |
// | **setInterval** | Har interval ke baad repeat karna          |


// Matlab:

// Promise = "Main wada karta hoon result future me milega."
// async/await = Wada ko easy tarike se likhne ka tareeqa.
// setTimeout = Ek dafa ke liye delay.
// setInterval = Bar-bar repeat hone wala kaam.



// async/await kya karta hai?

// await ka matlab hota hai: "is promise ka result aane tak ruk jao"
// Lekin sirf us async function ke andar jahan await likha hai rukta hai.
// JavaScript poore program ko block nahi karti, bas wahi function "pause" hota hai, aur baaki code (dusre functions, event listeners, UI) chal sakta hai.

// API call example

async function getUserData() {
  console.log("1. API call start");

  let response = await fetch("https://jsonplaceholder.typicode.com/users/1");
  let data = await response.json();

  console.log("2. API response mila:", data);
  return data;
}

console.log("3. Function call karne se pehle");

getUserData();

console.log("4. Function call kar diya");

// Output hoga:
// 3. Function call karne se pehle
// 1. API call start
// 4. Function call kar diya
// 2. API response mila: {id: 1, name: "Leanne Graham", ...}



// Explanation

// Jab await fetch(...) aaya → JS ne us line ko pause kar diya.

// Lekin baaki program chal raha tha (console.log("4. ...") turant print hua).

// Jaise hi API ka result aya → JS ne wapas us point se continue kiya.

// 🔹 Important Point

// Backend apna kaam karta rehta hai (API process hoti rehti hai server par).

// Lekin frontend JS thread block nahi hota, wo dusre kaam (UI render, animations, button clicks) handle karta rehta hai.

// Sirf await wali function line "ruk jati hai" jab tak promise resolve/reject na ho.

// 🔹 Agar response hi na mile (error case)?

// Agar backend fail ho jaye (server down, timeout, network error), to aapko await se error milega.
// Isko handle karna zaroori hai:


async function getData() {
  try {
    let res = await fetch("https://invalid-api.com/data");
    let data = await res.json();
    console.log(data);
  } catch (error) {
    console.log("Error aaya:", error.message);
  }
}
getData();

// Yahan code crash nahi hoga, balki error catch me handle ho jayega


// Matlab:

// await sirf us function ke andar execution rokta hai jahan likha hai.
// Baaki code, UI, aur JS event loop chalti rehti hai → isiliye app freeze nahi hoti.
// Agar result na aaye to await promise ke reject hone ka wait karega aur error throw karega.

// 🔹 API call ke tareeqe
// 1. Callback (Purana style)

function getData(callback) {
  fetch("https://jsonplaceholder.typicode.com/users/1")
    .then(res => res.json())
    .then(data => callback(null, data))
    .catch(err => callback(err));
}

getData((err, data) => {
  if (err) {
    console.log("Error:", err);
  } else {
    console.log("User:", data);
  }
});
// Ye tarika kaam karta hai, lekin callback hell create kar deta hai (nested functions).

// 2. Promises (.then / .catch)

fetch("https://jsonplaceholder.typicode.com/users/1")
  .then(res => res.json())
  .then(data => console.log("User:", data))
  .catch(err => console.log("Error:", err));


  // Ye modern tareeqa hai aur callback se zyada clean hai.

  // 3. async/await (syntactic sugar)

  async function getUser() {
    try {
      let res = await fetch("https://jsonplaceholder.typicode.com/users/1");
      let data = await res.json();
      console.log("User:", data);
    } catch (err) {
      console.log("Error:", err);
    }
  }
  
  getUser();
// Ye promises ka hi shortcut hai. Under the hood async/await = Promises.
// Ye sirf code ko synchronous jaisa readable banata hai.

// Zaroori point

// API call bina async/await bhi ho sakti hai (Promises ya callbacks se).
// async/await compulsory nahi hai, bas code readable banata hai.
// Real projects me readability + error handling ke liye mostly async/await prefer hota hai.

// Matlab:
// Callback → Old style
// Promise → Modern, clean
// async/await → Promise ka easy aur readable version