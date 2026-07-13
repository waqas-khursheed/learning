// ============================================
// NUMBER / MATH PROBLEMS
// Run: node 06_number_math_problems.js
// ============================================

// ---------- 1. Prime Number Check Karo ----------
function isPrime(n) {
  if (n < 2) return false;
  for (let i = 2; i * i <= n; i++) { // sirf sqrt(n) tak check karna kaafi hai
    if (n % i === 0) return false;
  }
  return true;
}
console.log("Is 17 prime?", isPrime(17)); // true
console.log("Is 15 prime?", isPrime(15)); // false

// ---------- 2. Armstrong Number Check Karo ----------
// (jaise 153 = 1^3 + 5^3 + 3^3)
function isArmstrong(num) {
  const original = num;
  const numDigits = String(num).length;
  let sum = 0;
  let n = num;
  while (n > 0) {
    const digit = n % 10;
    sum += Math.pow(digit, numDigits);
    n = Math.floor(n / 10);
  }
  return sum === original;
}
console.log("Is 153 Armstrong?", isArmstrong(153)); // true

// ---------- 3. Number Reverse Karo ----------
function reverseNumber(num) {
  let reversed = 0;
  let n = Math.abs(num);
  while (n > 0) {
    const digit = n % 10;
    reversed = reversed * 10 + digit;
    n = Math.floor(n / 10);
  }
  return num < 0 ? -reversed : reversed;
}
console.log("Reverse 12345:", reverseNumber(12345)); // 54321

// ---------- 4. Number Palindrome Check Karo ----------
function isNumberPalindrome(num) {
  return num === reverseNumber(num);
}
console.log("Is 121 palindrome?", isNumberPalindrome(121)); // true
console.log("Is 123 palindrome?", isNumberPalindrome(123)); // false

// ---------- 5. FizzBuzz (Classic Interview Warm-up) ----------
function fizzBuzz(n) {
  const result = [];
  for (let i = 1; i <= n; i++) {
    if (i % 15 === 0) result.push("FizzBuzz");
    else if (i % 3 === 0) result.push("Fizz");
    else if (i % 5 === 0) result.push("Buzz");
    else result.push(String(i));
  }
  return result;
}
console.log("FizzBuzz(15):", fizzBuzz(15));
// ["1","2","Fizz","4","Buzz","Fizz","7","8","Fizz","Buzz","11","Fizz","13","14","FizzBuzz"]

// ---------- 6. GCD Aur LCM ----------
function gcd(a, b) {
  while (b !== 0) {
    [a, b] = [b, a % b];
  }
  return a;
}
function lcm(a, b) {
  return (a * b) / gcd(a, b);
}
console.log("GCD(12, 18):", gcd(12, 18)); // 6
console.log("LCM(12, 18):", lcm(12, 18)); // 36

// ---------- 7. Perfect Number Check Karo ----------
// (jiske divisors ka sum khud number ke barabar ho, jaise 6 = 1+2+3)
function isPerfectNumber(num) {
  let sum = 0;
  for (let i = 1; i < num; i++) {
    if (num % i === 0) sum += i;
  }
  return sum === num;
}
console.log("Is 28 perfect?", isPerfectNumber(28)); // true (1+2+4+7+14=28)

// ---------- 8. Digits Count Karo ----------
function countDigits(num) {
  if (num === 0) return 1;
  let count = 0;
  let n = Math.abs(num);
  while (n > 0) {
    count++;
    n = Math.floor(n / 10);
  }
  return count;
}
console.log("Digits in 45678:", countDigits(45678)); // 5

// ---------- 9. Do Numbers Ko Swap Karo Bina Third Variable Ke ----------
function swapWithoutTemp(a, b) {
  a = a + b;
  b = a - b;
  a = a - b;
  return [a, b];
}
console.log("Swapped:", swapWithoutTemp(5, 10)); // [10, 5]

// ---------- 10. Factorial Iterative (Loop Se, Recursion Nahi) ----------
function factorialIterative(n) {
  let result = 1;
  for (let i = 2; i <= n; i++) {
    result *= i;
  }
  return result;
}
console.log("Factorial iterative(6):", factorialIterative(6)); // 720

// ============================================
// PRACTICE — Khud Karo (solution nahi diya)
// ============================================
// 1. Sieve of Eratosthenes implement karo — 1 se N tak sab prime numbers ek saath nikalo (O(n log log n))
// 2. Fibonacci series print karo N terms tak (loop se)
// 3. Check karo number "Strong Number" hai (digits ke factorial ka sum number ke barabar ho, jaise 145 = 1!+4!+5!)
// 4. Decimal number ko binary mein convert karo — bina .toString(2) ke
// 5. Binary number ko decimal mein convert karo
// 6. Check karo ek saal leap year hai ya nahi
