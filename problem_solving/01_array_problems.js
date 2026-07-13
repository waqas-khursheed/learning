// ============================================
// ARRAY PROBLEMS — Bina Built-in Helper Ke
// Run: node 01_array_problems.js
// ============================================

// ---------- 1. Max Element Dhoondo (Math.max nahi) ----------
function findMax(arr) {
  let max = arr[0];
  for (let i = 1; i < arr.length; i++) {
    if (arr[i] > max) max = arr[i];
  }
  return max;
}
console.log("Max:", findMax([3, 7, 2, 9, 4])); // 9

// ---------- 2. Min Element Dhoondo ----------
function findMin(arr) {
  let min = arr[0];
  for (let i = 1; i < arr.length; i++) {
    if (arr[i] < min) min = arr[i];
  }
  return min;
}
console.log("Min:", findMin([3, 7, 2, 9, 4])); // 2

// ---------- 3. Array Reverse Karo (.reverse() nahi) ----------
function reverseArray(arr) {
  const result = [];
  for (let i = arr.length - 1; i >= 0; i--) {
    result.push(arr[i]);
  }
  return result;
}
console.log("Reversed:", reverseArray([1, 2, 3, 4, 5])); // [5,4,3,2,1]

// In-place reverse (extra array bhi na banao — two-pointer technique)
function reverseInPlace(arr) {
  let left = 0;
  let right = arr.length - 1;
  while (left < right) {
    const temp = arr[left];
    arr[left] = arr[right];
    arr[right] = temp;
    left++;
    right--;
  }
  return arr;
}
console.log("In-place reversed:", reverseInPlace([1, 2, 3, 4, 5]));

// ---------- 4. Sum Of All Elements ----------
function arraySum(arr) {
  let sum = 0;
  for (let i = 0; i < arr.length; i++) {
    sum += arr[i];
  }
  return sum;
}
console.log("Sum:", arraySum([1, 2, 3, 4, 5])); // 15

// ---------- 5. Duplicates Hatao (Set/includes nahi) ----------
function removeDuplicates(arr) {
  const result = [];
  for (let i = 0; i < arr.length; i++) {
    let found = false;
    for (let j = 0; j < result.length; j++) {
      if (result[j] === arr[i]) {
        found = true;
        break;
      }
    }
    if (!found) result.push(arr[i]);
  }
  return result;
}
console.log("No duplicates:", removeDuplicates([1, 2, 2, 3, 4, 4, 5])); // [1,2,3,4,5]

// ---------- 6. Second Largest Element ----------
function secondLargest(arr) {
  let largest = -Infinity;
  let second = -Infinity;
  for (let i = 0; i < arr.length; i++) {
    if (arr[i] > largest) {
      second = largest;
      largest = arr[i];
    } else if (arr[i] > second && arr[i] !== largest) {
      second = arr[i];
    }
  }
  return second;
}
console.log("2nd Largest:", secondLargest([3, 7, 2, 9, 4])); // 7

// ---------- 7. Array Ko K Position Se Left Rotate Karo ----------
function rotateLeft(arr, k) {
  const n = arr.length;
  k = k % n;
  const result = [];
  for (let i = 0; i < n; i++) {
    result.push(arr[(i + k) % n]);
  }
  return result;
}
console.log("Rotated left by 2:", rotateLeft([1, 2, 3, 4, 5], 2)); // [3,4,5,1,2]

// ---------- 8. Do Sorted Arrays Ko Merge Karo (Sorted Result) ----------
function mergeSortedArrays(a, b) {
  const result = [];
  let i = 0, j = 0;
  while (i < a.length && j < b.length) {
    if (a[i] <= b[j]) {
      result.push(a[i]);
      i++;
    } else {
      result.push(b[j]);
      j++;
    }
  }
  while (i < a.length) { result.push(a[i]); i++; }
  while (j < b.length) { result.push(b[j]); j++; }
  return result;
}
console.log("Merged:", mergeSortedArrays([1, 3, 5], [2, 4, 6])); // [1,2,3,4,5,6]

// ---------- 9. 1 Se N Tak Ke Array Mein Missing Number Dhoondo ----------
function findMissingNumber(arr, n) {
  // 1..n ka sum - actual array ka sum = missing number
  let expectedSum = (n * (n + 1)) / 2;
  let actualSum = arraySum(arr);
  return expectedSum - actualSum;
}
console.log("Missing:", findMissingNumber([1, 2, 4, 5], 5)); // 3

// ---------- 10. Har Element Ki Frequency (Count) Nikalo ----------
function frequencyCount(arr) {
  const freq = {};
  for (let i = 0; i < arr.length; i++) {
    const item = arr[i];
    if (freq[item] === undefined) {
      freq[item] = 1;
    } else {
      freq[item]++;
    }
  }
  return freq;
}
console.log("Frequency:", frequencyCount(["a", "b", "a", "c", "b", "a"]));
// { a: 3, b: 2, c: 1 }

// ============================================
// PRACTICE — Khud Karo (solution nahi diya)
// ============================================
// 1. Do arrays ka intersection nikalo (jo common elements dono mein hon) — bina .includes() ke
// 2. Nested array ko flatten karo, e.g. [1, [2, 3], [4, [5, 6]]] → [1,2,3,4,5,6] — bina .flat() ke
// 3. Check karo array sorted hai ya nahi (ascending) — bina .sort() ke
// 4. Array mein sabse zyada frequency wala element (mode) dhoondo
// 5. Array ko chunks mein baanto, e.g. chunk([1,2,3,4,5], 2) → [[1,2],[3,4],[5]]
// 6. Do arrays mein se pehle wale mein se wo elements hatao jo dusre mein maujood hain (array difference)
