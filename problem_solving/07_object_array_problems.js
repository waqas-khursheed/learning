// ============================================
// OBJECT / ARRAY-OF-OBJECTS PROBLEMS — JS Interview Classics
// Run: node 07_object_array_problems.js
// ============================================

// ---------- 1. Array Of Objects Ko Ek Key Se Group Karo (lodash groupBy nahi) ----------
function groupBy(arr, key) {
  const result = {};
  for (let i = 0; i < arr.length; i++) {
    const groupKey = arr[i][key];
    if (!result[groupKey]) result[groupKey] = [];
    result[groupKey].push(arr[i]);
  }
  return result;
}
const employees = [
  { name: "Ali", dept: "IT" },
  { name: "Sara", dept: "HR" },
  { name: "Ahmed", dept: "IT" },
];
console.log("Grouped:", JSON.stringify(groupBy(employees, "dept")));

// ---------- 2. Nested Array Ko Flatten Karo (.flat() nahi) ----------
function flattenArray(arr) {
  let result = [];
  for (let i = 0; i < arr.length; i++) {
    if (Array.isArray(arr[i])) {
      result = result.concat(flattenArray(arr[i])); // recursion
    } else {
      result.push(arr[i]);
    }
  }
  return result;
}
console.log("Flattened:", flattenArray([1, [2, 3], [4, [5, 6, [7]]]])); // [1,2,3,4,5,6,7]

// ---------- 3. Object Ko Deep Clone Karo (structuredClone/JSON trick nahi, manual) ----------
function deepClone(obj) {
  if (obj === null || typeof obj !== "object") return obj;

  if (Array.isArray(obj)) {
    const clonedArr = [];
    for (let i = 0; i < obj.length; i++) {
      clonedArr[i] = deepClone(obj[i]);
    }
    return clonedArr;
  }

  const clonedObj = {};
  for (const key in obj) {
    if (Object.prototype.hasOwnProperty.call(obj, key)) {
      clonedObj[key] = deepClone(obj[key]);
    }
  }
  return clonedObj;
}
const original = { name: "Ali", address: { city: "Lahore" }, tags: [1, 2, 3] };
const cloned = deepClone(original);
cloned.address.city = "Karachi";
console.log("Original city:", original.address.city); // "Lahore" (untouched)
console.log("Cloned city:", cloned.address.city);       // "Karachi"

// ---------- 4. Sentence Mein Har Word Ki Frequency (Object Bhi Ek Hashmap Hai) ----------
function wordFrequency(sentence) {
  const words = sentence.toLowerCase().split(" ");
  const freq = {};
  for (let i = 0; i < words.length; i++) {
    freq[words[i]] = (freq[words[i]] || 0) + 1;
  }
  return freq;
}
console.log("Word freq:", wordFrequency("the quick fox the lazy fox"));
// { the: 2, quick: 1, fox: 2, lazy: 1 }

// ---------- 5. Do Objects Ko Manually Merge Karo (Object.assign/{...a,...b} nahi) ----------
function mergeObjects(obj1, obj2) {
  const result = {};
  for (const key in obj1) result[key] = obj1[key];
  for (const key in obj2) result[key] = obj2[key]; // obj2 ki values overwrite karengi
  return result;
}
console.log("Merged:", mergeObjects({ a: 1, b: 2 }, { b: 3, c: 4 })); // { a:1, b:3, c:4 }

// ---------- 6. Array Ko Object Mein Convert Karo (keyBy) ----------
function keyBy(arr, key) {
  const result = {};
  for (let i = 0; i < arr.length; i++) {
    result[arr[i][key]] = arr[i];
  }
  return result;
}
const users = [{ id: 1, name: "Ali" }, { id: 2, name: "Sara" }];
console.log("KeyBy id:", JSON.stringify(keyBy(users, "id")));
// { "1": {id:1,name:"Ali"}, "2": {id:2,name:"Sara"} }

// ---------- 7. Category Ke Hisaab Se Values Sum Karo ----------
function sumByCategory(items) {
  const result = {};
  for (let i = 0; i < items.length; i++) {
    const { category, amount } = items[i];
    result[category] = (result[category] || 0) + amount;
  }
  return result;
}
const orders = [
  { category: "food", amount: 500 },
  { category: "travel", amount: 1200 },
  { category: "food", amount: 300 },
];
console.log("Sum by category:", sumByCategory(orders)); // { food: 800, travel: 1200 }

// ---------- 8. Debounce Function Implement Karo (Frontend Interview Classic) ----------
// Use case: search input mein har keystroke pe API call na ho, typing rukne ke
// baad hi ek dafa call ho.
function debounce(fn, delay) {
  let timer;
  return function (...args) {
    clearTimeout(timer);
    timer = setTimeout(() => fn.apply(this, args), delay);
  };
}
const debouncedLog = debounce((msg) => console.log("Debounced:", msg), 300);
debouncedLog("a"); debouncedLog("ab"); debouncedLog("abc"); // sirf "abc" print hoga, 300ms baad

// ---------- 9. Throttle Function Implement Karo ----------
// Use case: scroll/resize event mein function ko har X ms mein sirf ek dafa chalne do
function throttle(fn, limit) {
  let inThrottle = false;
  return function (...args) {
    if (!inThrottle) {
      fn.apply(this, args);
      inThrottle = true;
      setTimeout(() => (inThrottle = false), limit);
    }
  };
}
const throttledLog = throttle(() => console.log("Throttled call"), 1000);
throttledLog(); throttledLog(); throttledLog(); // sirf pehli call turant chalegi

// ============================================
// PRACTICE — Khud Karo (solution nahi diya)
// ============================================
// 1. Array of objects mein duplicate objects hatao (by ek specific key, jaise "id")
// 2. Nested object ko "dot notation" mein flatten karo: {a:{b:{c:1}}} → {"a.b.c": 1}
// 3. Ek simple curry function banao: curry(add)(1)(2)(3) === 6
// 4. Object ke keys aur values ko swap karo: {a:1, b:2} → {1:"a", 2:"b"}
// 5. Array of objects ko multiple keys se sort karo (jaise pehle dept, phir name)
