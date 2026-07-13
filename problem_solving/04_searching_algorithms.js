// ============================================
// SEARCHING ALGORITHMS — Manual
// Run: node 04_searching_algorithms.js
// ============================================

// ---------- 1. Linear Search ----------
// Time: O(n) | Unsorted array pe bhi kaam karta hai
function linearSearch(arr, target) {
  for (let i = 0; i < arr.length; i++) {
    if (arr[i] === target) return i; // index return
  }
  return -1;
}
console.log("Linear Search (find 9):", linearSearch([5, 2, 8, 1, 9, 3], 9)); // 4

// ---------- 2. Binary Search (Iterative) ----------
// Time: O(log n) | Sirf SORTED array pe kaam karta hai
function binarySearchIterative(sortedArr, target) {
  let low = 0;
  let high = sortedArr.length - 1;

  while (low <= high) {
    const mid = Math.floor((low + high) / 2);
    if (sortedArr[mid] === target) return mid;
    if (sortedArr[mid] < target) {
      low = mid + 1;
    } else {
      high = mid - 1;
    }
  }
  return -1;
}
console.log("Binary Search (find 7):", binarySearchIterative([1, 2, 3, 5, 7, 9, 11], 7)); // 4

// ---------- 3. Binary Search (Recursive) ----------
function binarySearchRecursive(sortedArr, target, low = 0, high = sortedArr.length - 1) {
  if (low > high) return -1;

  const mid = Math.floor((low + high) / 2);
  if (sortedArr[mid] === target) return mid;
  if (sortedArr[mid] < target) {
    return binarySearchRecursive(sortedArr, target, mid + 1, high);
  } else {
    return binarySearchRecursive(sortedArr, target, low, mid - 1);
  }
}
console.log("Binary Search Recursive (find 11):", binarySearchRecursive([1, 2, 3, 5, 7, 9, 11], 11)); // 6

// ---------- 4. Sorted Array Mein Element Ki First Aur Last Occurrence ----------
function firstOccurrence(arr, target) {
  let low = 0, high = arr.length - 1, result = -1;
  while (low <= high) {
    const mid = Math.floor((low + high) / 2);
    if (arr[mid] === target) {
      result = mid;
      high = mid - 1; // left mein aur dhoondo (pehli occurrence)
    } else if (arr[mid] < target) {
      low = mid + 1;
    } else {
      high = mid - 1;
    }
  }
  return result;
}

function lastOccurrence(arr, target) {
  let low = 0, high = arr.length - 1, result = -1;
  while (low <= high) {
    const mid = Math.floor((low + high) / 2);
    if (arr[mid] === target) {
      result = mid;
      low = mid + 1; // right mein aur dhoondo (aakhri occurrence)
    } else if (arr[mid] < target) {
      low = mid + 1;
    } else {
      high = mid - 1;
    }
  }
  return result;
}
const dup = [1, 2, 2, 2, 3, 4, 5];
console.log("First occurrence of 2:", firstOccurrence(dup, 2)); // 1
console.log("Last occurrence of 2:", lastOccurrence(dup, 2));   // 3

// ---------- 5. Peak Element Dhoondo (jo apne dono neighbors se bada ho) ----------
function findPeakElement(arr) {
  let low = 0, high = arr.length - 1;
  while (low < high) {
    const mid = Math.floor((low + high) / 2);
    if (arr[mid] > arr[mid + 1]) {
      high = mid;
    } else {
      low = mid + 1;
    }
  }
  return low; // index of a peak
}
console.log("Peak index:", findPeakElement([1, 3, 5, 4, 2])); // 2 (value 5)

// ---------- Linear vs Binary — Kab Konsa ----------
// Linear Search: unsorted data, chota data, ya sirf ek dafa search karna ho
// Binary Search: sorted data, baar baar search karna ho (O(log n) bohat fast hai
//                bade data ke liye — 1 million items mein sirf ~20 comparisons)

// ============================================
// PRACTICE — Khud Karo (solution nahi diya)
// ============================================
// 1. Rotated sorted array mein search karo, e.g. [4,5,6,7,0,1,2] mein target 0 dhoondo — O(log n) mein
// 2. Sorted array mein target ka "closest" number dhoondo (agar exact match na ho)
// 3. 2D sorted matrix mein ek target search karo (rows aur columns dono sorted hain)
// 4. Square root of a number nikalo (binary search se, bina Math.sqrt() ke)
