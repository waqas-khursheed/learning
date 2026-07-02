# 🔹 Sorting Algorithms and Their Use Cases

# -------------------------------------------
# 🔹 1. Bubble Sort
# -------------------------------------------

# Definition:
# Bubble Sort ek simple sorting algorithm hai jo har pass mai adjacent (qareebi) elements ko compare karke swap karta hai agar wo galat order mai ho. Yeh process tab tak repeat hota hai jab tak pura array sorted na ho jaye.

# Time Complexity:
# - Best Case: O(n) (jab array already sorted ho)
# - Worst Case: O(n²) (jab array reverse sorted ho)

# Use Case:
# - Choti choti lists (educational purposes).
# - Kabhi kabhi jab memory kaafi limited ho aur simple code chahiye.
# - Practical zyada use nahi hota kyunke slow hota hai.

# -------------------------------------------
# 🔹 2. Selection Sort
# -------------------------------------------

# Definition:
# Selection Sort array ko sort karne ke liye har pass mai minimum (ya maximum) element ko select karta hai aur usko correct position per rakh deta hai.

# Time Complexity:
# - Best Case: O(n²)
# - Worst Case: O(n²)

# Use Case:
# - Jab swaps ki cost zyada ho (kyunki yeh Bubble Sort se kam swaps karta hai).
# - Embedded systems jahan memory aur operations limited hote hain.
# - Lekin overall yeh bhi slow hota hai.

# -------------------------------------------
# 🔹 3. Insertion Sort
# -------------------------------------------

# Definition:
# Insertion Sort array ko left side se sorted banata hai. Har naye element ko uthakar uski correct jagah par insert karta hai (jaise hum cards ko arrange karte hain).

# Time Complexity:
# - Best Case: O(n) (jab array already sorted ho)
# - Worst Case: O(n²)

# Use Case:
# - Choti arrays/lists (10–20 elements).
# - Jab input almost sorted ho (iska performance bohot acha ho jata hai).
# - Computer ke andar kuch algorithms mai ye as a subroutine use hota hai.

# -------------------------------------------
# 🔹 4. Quick Sort
# -------------------------------------------

# Definition:
# Quick Sort ek divide and conquer algorithm hai jo ek "pivot" element choose karta hai aur array ko do parts mai divide karta hai:
# - Pivot se chhote elements
# - Pivot se bade elements
# Phir recursively dono parts ko sort karta hai.

# Time Complexity:
# - Best Case: O(n log n)
# - Average Case: O(n log n)
# - Worst Case: O(n²) (agar pivot kharab choose ho)

# Use Case:
# - Large datasets mai bohot fast hota hai.
# - Practical life mai zyada use hota hai (C, Java, Python ke sort functions iske variants use karte hain).

# -------------------------------------------
# 🔹 5. Counting Sort
# -------------------------------------------

# Definition:
# Counting Sort ek non-comparison based sorting hai. Ye har element ki frequency count karta hai aur uski help se array ko sorted form mai banata hai.

# Time Complexity:
# - Best Case: O(n + k)
# - Worst Case: O(n + k)

# Use Case:
# - Jab range of numbers (0 se k tak) limited aur choti ho.
# - Integers sorting mai fast hota hai.

# -------------------------------------------
# 🔹 6. Radix Sort
# -------------------------------------------

# Definition:
# Radix Sort bhi non-comparison based sorting hai. Ye numbers ko digit-by-digit sort karta hai (pehle 1s place, phir 10s place, phir 100s place...). Usually Counting Sort ke sath use hota hai.

# Time Complexity:
# - Best Case: O(n × k)
# - Worst Case: O(n × k)

# Use Case:
# - Large integers ko efficiently sort karna.
# - Strings sorting (lexicographical order).

# -------------------------------------------
# 🔹 7. Merge Sort
# -------------------------------------------

# Definition:
# Merge Sort bhi divide and conquer algorithm hai. Array ko repeatedly do parts mai divide karta hai, unhe recursively sort karta hai, aur phir merge karke final sorted array banata hai.

# Time Complexity:
# - Best Case: O(n log n)
# - Worst Case: O(n log n)

# Use Case:
# - Large datasets mai reliable aur stable sorting.
# - External sorting (jab data RAM se bada ho, jaise disk pe sorting).

# -------------------------------------------
# 🔹 Summary Table
# -------------------------------------------

# Algorithm        | Complexity Avg. | Stable? | Best Use Case
# ------------------------------------------------------------
# Bubble Sort      | O(n²)           | Yes     | Simple, educational
# Selection Sort   | O(n²)           | No      | Low swaps, small data
# Insertion Sort   | O(n²) / O(n)    | Yes     | Small or nearly sorted data
# Quick Sort       | O(n log n)      | No      | Large datasets, practical
# Counting Sort    | O(n + k)        | Yes     | Integers with small range
# Radix Sort       | O(n × k)        | Yes     | Large numbers/strings
# Merge Sort       | O(n log n)      | Yes     | Large stable sorting, external sorting