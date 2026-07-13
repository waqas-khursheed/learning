// ============================================
// POPULAR INTERVIEW QUESTIONS — Must Know
// Run: node 08_popular_interview_questions.js
// ============================================

// ---------- 1. Two Sum ----------
// Diye gaye array mein do numbers dhoondo jinka sum target ke barabar ho
// Brute force: O(n^2)
function twoSumBrute(arr, target) {
  for (let i = 0; i < arr.length; i++) {
    for (let j = i + 1; j < arr.length; j++) {
      if (arr[i] + arr[j] === target) return [i, j];
    }
  }
  return [];
}
// Optimized: O(n) — hashmap (object) use karke
function twoSumOptimized(arr, target) {
  const seen = {}; // { value: index }
  for (let i = 0; i < arr.length; i++) {
    const complement = target - arr[i];
    if (seen[complement] !== undefined) {
      return [seen[complement], i];
    }
    seen[arr[i]] = i;
  }
  return [];
}
console.log("Two Sum:", twoSumOptimized([2, 7, 11, 15], 9)); // [0, 1]

// ---------- 2. Valid Parentheses ----------
// Check karo brackets sahi tarah open/close hue hain: "()[]{}", "([)]" invalid
function isValidParentheses(str) {
  const stack = []; // array ko manually stack ki tarah use kar rahe (push/pop)
  const pairs = { ")": "(", "]": "[", "}": "{" };

  for (let i = 0; i < str.length; i++) {
    const ch = str[i];
    if (ch === "(" || ch === "[" || ch === "{") {
      stack.push(ch);
    } else if (ch === ")" || ch === "]" || ch === "}") {
      if (stack.length === 0 || stack[stack.length - 1] !== pairs[ch]) {
        return false;
      }
      stack.pop();
    }
  }
  return stack.length === 0;
}
console.log("Valid '()[]{}': ", isValidParentheses("()[]{}"));  // true
console.log("Valid '([)]':   ", isValidParentheses("([)]"));    // false

// ---------- 3. Majority Element (Jo Array Mein Sabse Zyada Baar Aaye) ----------
// Boyer-Moore Voting Algorithm — O(n) time, O(1) space
function majorityElement(arr) {
  let candidate = null;
  let count = 0;

  for (let i = 0; i < arr.length; i++) {
    if (count === 0) candidate = arr[i];
    count += arr[i] === candidate ? 1 : -1;
  }
  return candidate;
}
console.log("Majority:", majorityElement([2, 2, 1, 1, 1, 2, 2])); // 2

// ---------- 4. Move Zeroes To End (Order Baaki Elements Ka Change Na Ho) ----------
function moveZeroesToEnd(arr) {
  const a = [...arr];
  let insertPos = 0;
  for (let i = 0; i < a.length; i++) {
    if (a[i] !== 0) {
      a[insertPos] = a[i];
      insertPos++;
    }
  }
  while (insertPos < a.length) {
    a[insertPos] = 0;
    insertPos++;
  }
  return a;
}
console.log("Zeroes moved:", moveZeroesToEnd([0, 1, 0, 3, 12])); // [1,3,12,0,0]

// ---------- 5. Maximum Subarray Sum — Kadane's Algorithm ----------
// Contiguous subarray jiska sum maximum ho, O(n) time
function maxSubarraySum(arr) {
  let maxSoFar = arr[0];
  let currentMax = arr[0];

  for (let i = 1; i < arr.length; i++) {
    currentMax = Math.max(arr[i], currentMax + arr[i]);
    maxSoFar = Math.max(maxSoFar, currentMax);
  }
  return maxSoFar;
}
console.log("Max Subarray Sum:", maxSubarraySum([-2, 1, -3, 4, -1, 2, 1, -5, 4])); // 6 (subarray [4,-1,2,1])

// ---------- 6. Array Mein Duplicate Hai Ya Nahi (Fast Check) ----------
function hasDuplicate(arr) {
  const seen = {};
  for (let i = 0; i < arr.length; i++) {
    if (seen[arr[i]]) return true;
    seen[arr[i]] = true;
  }
  return false;
}
console.log("Has duplicate [1,2,3,1]:", hasDuplicate([1, 2, 3, 1])); // true

// ---------- 7. Apna Own .map() Banao (Polyfill) ----------
function myMap(arr, callback) {
  const result = [];
  for (let i = 0; i < arr.length; i++) {
    result.push(callback(arr[i], i, arr));
  }
  return result;
}
console.log("My Map:", myMap([1, 2, 3], (x) => x * 2)); // [2, 4, 6]

// ---------- 8. Apna Own .filter() Banao (Polyfill) ----------
function myFilter(arr, callback) {
  const result = [];
  for (let i = 0; i < arr.length; i++) {
    if (callback(arr[i], i, arr)) result.push(arr[i]);
  }
  return result;
}
console.log("My Filter:", myFilter([1, 2, 3, 4, 5], (x) => x % 2 === 0)); // [2, 4]

// ---------- 9. Apna Own .reduce() Banao (Polyfill) ----------
function myReduce(arr, callback, initialValue) {
  let accumulator = initialValue;
  let startIndex = 0;

  if (accumulator === undefined) {
    accumulator = arr[0];
    startIndex = 1;
  }

  for (let i = startIndex; i < arr.length; i++) {
    accumulator = callback(accumulator, arr[i], i, arr);
  }
  return accumulator;
}
console.log("My Reduce (sum):", myReduce([1, 2, 3, 4], (acc, x) => acc + x, 0)); // 10

// ---------- 10. Object Ko "a.b.c" Dot Notation Se Flatten Karo ----------
function flattenObject(obj, prefix = "") {
  let result = {};
  for (const key in obj) {
    const newKey = prefix ? `${prefix}.${key}` : key;
    if (typeof obj[key] === "object" && obj[key] !== null && !Array.isArray(obj[key])) {
      result = { ...result, ...flattenObject(obj[key], newKey) };
    } else {
      result[newKey] = obj[key];
    }
  }
  return result;
}
console.log("Flattened obj:", flattenObject({ a: { b: { c: 1 } }, d: 2 })); // { "a.b.c": 1, d: 2 }

// ============================================
// PRACTICE — Khud Karo (solution nahi diya)
// ============================================
// 1. Apna own .forEach() polyfill banao
// 2. Array mein "Three Sum" solve karo (teen numbers jinka sum 0 ho)
// 3. Longest substring without repeating characters dhoondo (sliding window technique)
// 4. Binary tree ka level-order traversal likho (agar tree data structure practice karni ho)
// 5. LRU Cache implement karo (basic version — get/put with fixed capacity)
// 6. Apna own Promise.all() polyfill banao (advanced — async samajhne ke baad try karo)

// ============================================
// AGLA STEP
// ============================================
// In sab problems ko solve karne ke baad LeetCode (Easy → Medium) pe practice
// continue karo — yahan jo patterns seekhe (two-pointer, sliding window, hashmap,
// recursion, binary search) wahi patterns 80% interview questions mein repeat hote hain.
