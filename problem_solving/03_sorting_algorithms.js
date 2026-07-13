// ============================================
// SORTING ALGORITHMS — Manual (Ascending + Descending)
// Run: node 03_sorting_algorithms.js
// ============================================

// ---------- 1. Bubble Sort ----------
// Time: O(n^2) | Space: O(1) | Simple lekin slow, bade data pe use nahi hota
function bubbleSortAsc(arr) {
  const a = [...arr]; // original ko mutate na karne ke liye copy
  for (let i = 0; i < a.length - 1; i++) {
    for (let j = 0; j < a.length - 1 - i; j++) {
      if (a[j] > a[j + 1]) {
        const temp = a[j];
        a[j] = a[j + 1];
        a[j + 1] = temp;
      }
    }
  }
  return a;
}

function bubbleSortDesc(arr) {
  const a = [...arr];
  for (let i = 0; i < a.length - 1; i++) {
    for (let j = 0; j < a.length - 1 - i; j++) {
      if (a[j] < a[j + 1]) {
        const temp = a[j];
        a[j] = a[j + 1];
        a[j + 1] = temp;
      }
    }
  }
  return a;
}
console.log("Bubble Asc:", bubbleSortAsc([5, 2, 8, 1, 9, 3]));   // [1,2,3,5,8,9]
console.log("Bubble Desc:", bubbleSortDesc([5, 2, 8, 1, 9, 3])); // [9,8,5,3,2,1]

// ---------- 2. Selection Sort ----------
// Time: O(n^2) | Har pass mein minimum dhoondh kar aage rakhte hain
function selectionSortAsc(arr) {
  const a = [...arr];
  for (let i = 0; i < a.length - 1; i++) {
    let minIndex = i;
    for (let j = i + 1; j < a.length; j++) {
      if (a[j] < a[minIndex]) minIndex = j;
    }
    if (minIndex !== i) {
      const temp = a[i];
      a[i] = a[minIndex];
      a[minIndex] = temp;
    }
  }
  return a;
}
console.log("Selection Asc:", selectionSortAsc([5, 2, 8, 1, 9, 3])); // [1,2,3,5,8,9]

// ---------- 3. Insertion Sort ----------
// Time: O(n^2) worst, O(n) best (already sorted) | Cards jaisi technique
function insertionSortAsc(arr) {
  const a = [...arr];
  for (let i = 1; i < a.length; i++) {
    const current = a[i];
    let j = i - 1;
    while (j >= 0 && a[j] > current) {
      a[j + 1] = a[j];
      j--;
    }
    a[j + 1] = current;
  }
  return a;
}
console.log("Insertion Asc:", insertionSortAsc([5, 2, 8, 1, 9, 3])); // [1,2,3,5,8,9]

// ---------- 4. Quick Sort ----------
// Time: O(n log n) average, O(n^2) worst | Divide and conquer, pivot use karta hai
function quickSort(arr) {
  if (arr.length <= 1) return arr;

  const pivot = arr[arr.length - 1];
  const left = [];
  const right = [];

  for (let i = 0; i < arr.length - 1; i++) {
    if (arr[i] < pivot) {
      left.push(arr[i]);
    } else {
      right.push(arr[i]);
    }
  }

  return [...quickSort(left), pivot, ...quickSort(right)];
}
console.log("Quick Sort:", quickSort([5, 2, 8, 1, 9, 3])); // [1,2,3,5,8,9]

// ---------- 5. Merge Sort ----------
// Time: O(n log n) hamesha | Divide and conquer, stable sort
function mergeSort(arr) {
  if (arr.length <= 1) return arr;

  const mid = Math.floor(arr.length / 2);
  const left = mergeSort(arr.slice(0, mid));
  const right = mergeSort(arr.slice(mid));

  return merge(left, right);
}

function merge(left, right) {
  const result = [];
  let i = 0, j = 0;
  while (i < left.length && j < right.length) {
    if (left[i] <= right[j]) {
      result.push(left[i]);
      i++;
    } else {
      result.push(right[j]);
      j++;
    }
  }
  while (i < left.length) { result.push(left[i]); i++; }
  while (j < right.length) { result.push(right[j]); j++; }
  return result;
}
console.log("Merge Sort:", mergeSort([5, 2, 8, 1, 9, 3])); // [1,2,3,5,8,9]

// ---------- Time Complexity Cheat Sheet ----------
// Algorithm       | Best      | Average    | Worst      | Space
// Bubble Sort      | O(n)      | O(n^2)     | O(n^2)     | O(1)
// Selection Sort    | O(n^2)    | O(n^2)     | O(n^2)     | O(1)
// Insertion Sort     | O(n)      | O(n^2)     | O(n^2)     | O(1)
// Quick Sort           | O(n log n)| O(n log n) | O(n^2)     | O(log n)
// Merge Sort            | O(n log n)| O(n log n) | O(n log n) | O(n)
//
// Interview mein: chota data → koi bhi chalega. Real projects mein → language ka
// built-in .sort() use karo (V8 mein Timsort/hybrid hai, already optimized) —
// yahan manual isliye likha taake algorithm samajh aaye, production code mein
// khud sorting algorithm likhna zaroorat nahi hoti.

// ============================================
// PRACTICE — Khud Karo (solution nahi diya)
// ============================================
// 1. Array of objects ko ek key ke hisaab se sort karo (custom comparator), e.g.
//    sortByKey([{name:"B",age:30},{name:"A",age:25}], "age") — bina .sort() ke
// 2. Bubble sort ko optimize karo — agar ek pass mein koi swap na ho to loop rok do (already sorted detect karo)
// 3. Counting Sort implement karo (jab numbers ki range choti/known ho, O(n) sorting)
// 4. Do already-sorted arrays ko ek hi sorted array mein merge karo (merge() function upar hai, khud dobara likho)
