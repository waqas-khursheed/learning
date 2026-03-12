// JavaScript Loops and Iteration Methods — Complete Guide

// Introduction

// JavaScript mai loops aur iteration methods use kiye jate hain taake hum ek hi code ko bar bar chala saken —
// jaise array ke har element ko print karna, ya kisi condition tak repeat karna.

// Is document mai hum ye 5 cheezein detail mai dekhenge 

// for loop

// while loop

// do...while loop

// forEach()

// map()

// Aur samjhenge ke unka difference, syntax, aur kab use karna chahiye.

// 1. for Loop
// 🔸 Definition

// for loop JavaScript ka sabse basic aur flexible loop hai.
// Ye tab use hota hai jab hume pehle se pata ho ke kitni dafa loop chalani hai.

for (initialization; condition; increment) {
    // code yahan chalega
}

Example
for (let i = 1; i <= 5; i++) {
    console.log("Number:", i);
}

// 🔸 Output
// Number: 1
// Number: 2
// Number: 3
// Number: 4
// Number: 5

// Use Case

// Jab aapko exact count pata ho kitni dafa loop chalani hai.
// Index-based array loop karne ke liye best.

// 2. while Loop
// 🔸 Definition

// while loop tab tak chalti rehti hai jab tak condition true hoti hai.
// Agar condition false ho jaye to loop ruk jati hai.

// 🔸 Syntax

while (condition) {
    // code yahan chalega
}

Example
let i = 1;
while (i <= 5) {
    console.log("Count:", i);
    i++;
}
// Jab aapko nahi pata ke loop kitni dafa chalegi.
// Real-time conditions ya user input ke cases mai useful hoti hai.


// 3. do...while Loop
// 🔸 Definition

// do...while loop while jaisi hi hoti hai,
// lekin ye kam az kam ek dafa code execute karti hai —
// chahe condition false hi kyu na ho.

// 🔸 Syntax

do {
    // code yahan chalega
} while (condition);


Example
let i = 1;
do {
    console.log("Executed:", i);
    i++;
} while (i <= 5);


// Use Case

// Jab chahte ho ke code kam az kam ek dafa zaroor chale.
// Input validation ya menu systems mai zyada use hoti hai.


// 4. forEach() Method
// 🔸 Definition

// forEach() ek array method hai jo har array element ke liye ek function run karta hai.
// Ye array ke elements ko change nahi karta, sirf un par kaam karta hai.

// 🔸 Syntax

array.forEach((value, index) => {
    // code yahan chalega
});

// Example
let numbers = [10, 20, 30];

numbers.forEach((num, index) => {
  console.log(`Index ${index}: ${num}`);
});

// 🔸 Output
// Index 0: 10
// Index 1: 20
// Index 2: 30


// 🔸 Use Case

//  Jab aapko har array element par koi action karna ho
// (jaise print karna, sum nikalna, ya object banana).
// Ye koi naya array return nahi karta.

// 5. map() Method
// 🔸 Definition

// map() bhi forEach() ki tarah array ke har element par function run karta hai,
// lekin difference ye hai ke ye ek naya array return karta hai.


// Syntax
let newArray = array.map((value, index) => {
  return // kuch naya value
});

// 🔸 Example
let numbers = [1, 2, 3, 4];
let doubled = numbers.map(num => num * 2);

console.log(doubled);

// 🔸 Output
// [2, 4, 6, 8]

// 🔸 Use Case

// Jab aapko array ke har element se naya array banana ho.
// Ye functional programming mai bohot zyada use hota hai.
// Ye bhi original array ko change nahi karta. 

// Comparison Table

// | Feature             | `for` Loop  | `while` Loop           | `do...while`  | `forEach()`            | `map()`                |
// | ------------------- | ----------- | ---------------------- | ------------- | ---------------------- | ---------------------- |
// | Runs how many times | Fixed count | Jab tak condition true | At least once | For each array element | For each array element |
// | Works on arrays     | ✅           | ✅                      | ✅             | ✅                      | ✅                      |
// | Returns new array   | ❌           | ❌                      | ❌             | ❌                      | ✅                      |
// | Can modify array    | ✅           | ✅                      | ✅             | ✅ (manually)           | ❌ (returns new one)    |
// | Use when            | Count known | Count unknown          | Must run once | Action per element     | Need new array         |


// Summary
// for → jab iterations ka count pata ho
// while → jab condition pe depend karta ho
// do...while → jab kam az kam ek dafa code chalana ho
// forEach() → jab sirf har element pe action karna ho
// map() → jab har element se naya array banana ho