// ============================================
// RECURSION PROBLEMS
// Run: node 05_recursion_problems.js
// ============================================

// ---------- 1. Factorial ----------
function factorial(n) {
  if (n <= 1) return 1; // base case
  return n * factorial(n - 1); // recursive case
}
console.log("Factorial(5):", factorial(5)); // 120

// ---------- 2. Fibonacci (Simple Recursive — Slow, O(2^n)) ----------
function fibonacci(n) {
  if (n <= 1) return n;
  return fibonacci(n - 1) + fibonacci(n - 2);
}
console.log("Fibonacci(10):", fibonacci(10)); // 55

// ---------- 2b. Fibonacci Memoized (Fast, O(n)) ----------
function fibonacciMemo(n, memo = {}) {
  if (n in memo) return memo[n];
  if (n <= 1) return n;
  memo[n] = fibonacciMemo(n - 1, memo) + fibonacciMemo(n - 2, memo);
  return memo[n];
}
console.log("Fibonacci Memo(40):", fibonacciMemo(40)); // fast — simple recursive version yahan atak jata

// ---------- 3. Digits Ka Sum (Recursive) ----------
function sumOfDigits(n) {
  if (n < 10) return n;
  return (n % 10) + sumOfDigits(Math.floor(n / 10));
}
console.log("Sum of digits (1234):", sumOfDigits(1234)); // 10

// ---------- 4. Power Function (x^n) — Bina Math.pow() Ke ----------
function power(base, exp) {
  if (exp === 0) return 1;
  return base * power(base, exp - 1);
}
console.log("Power(2, 10):", power(2, 10)); // 1024

// Optimized version O(log n) — fast exponentiation
function fastPower(base, exp) {
  if (exp === 0) return 1;
  if (exp % 2 === 0) {
    const half = fastPower(base, exp / 2);
    return half * half;
  }
  return base * fastPower(base, exp - 1);
}
console.log("Fast Power(2, 10):", fastPower(2, 10)); // 1024

// ---------- 5. GCD (Greatest Common Divisor) — Euclidean Algorithm ----------
function gcd(a, b) {
  if (b === 0) return a;
  return gcd(b, a % b);
}
console.log("GCD(48, 18):", gcd(48, 18)); // 6

// ---------- 6. String Reverse Recursively ----------
function reverseStringRecursive(str) {
  if (str.length <= 1) return str;
  return reverseStringRecursive(str.slice(1)) + str[0];
}
console.log("Reverse Recursive:", reverseStringRecursive("hello")); // "olleh"

// ---------- 7. Palindrome Check Recursively ----------
function isPalindromeRecursive(str, left = 0, right = str.length - 1) {
  if (left >= right) return true;
  if (str[left] !== str[right]) return false;
  return isPalindromeRecursive(str, left + 1, right - 1);
}
console.log("Palindrome Recursive (racecar):", isPalindromeRecursive("racecar")); // true

// ---------- 8. Tower of Hanoi ----------
function towerOfHanoi(n, from = "A", to = "C", aux = "B", moves = []) {
  if (n === 0) return moves;
  towerOfHanoi(n - 1, from, aux, to, moves);
  moves.push(`Move disk ${n} from ${from} to ${to}`);
  towerOfHanoi(n - 1, aux, to, from, moves);
  return moves;
}
console.log("Tower of Hanoi (3 disks):");
towerOfHanoi(3).forEach((move) => console.log("  " + move));

// ---------- 9. Array Sum Recursively ----------
function arraySumRecursive(arr, index = 0) {
  if (index === arr.length) return 0;
  return arr[index] + arraySumRecursive(arr, index + 1);
}
console.log("Array sum recursive:", arraySumRecursive([1, 2, 3, 4, 5])); // 15

// ---------- 10. Ek Number Array Mein Hai Ya Nahi (Recursive Linear Search) ----------
function containsRecursive(arr, target, index = 0) {
  if (index === arr.length) return false;
  if (arr[index] === target) return true;
  return containsRecursive(arr, target, index + 1);
}
console.log("Contains 9:", containsRecursive([5, 2, 8, 1, 9], 9)); // true

// ---------- Recursion Ka Golden Rule ----------
// Har recursive function mein 2 cheezein zaroor honi chahiye:
// 1. BASE CASE — jahan recursion rukta hai (warna infinite loop/stack overflow)
// 2. RECURSIVE CASE — jahan function khud ko chote input ke sath call kare
// Agar recursion "slow" lage (jaise fibonacci), to MEMOIZATION use karo — pehle
// calculate kiye results cache kar lo, dobara calculate mat karo.

// ============================================
// PRACTICE — Khud Karo (solution nahi diya)
// ============================================
// 1. Ek string ke saare permutations print karo (recursive), e.g. "abc" → abc, acb, bac, bca, cab, cba
// 2. Ek array ke saare subsets (power set) nikalo, e.g. [1,2] → [[],[1],[2],[1,2]]
// 3. Recursive function se array flatten karo (nested arrays), bina .flat() ke
// 4. N-th Fibonacci number iterative (loop se, bina recursion) bhi likho — compare karo dono approach
// 5. Recursively check karo ek number prime hai ya nahi
