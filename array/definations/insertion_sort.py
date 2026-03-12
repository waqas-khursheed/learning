# Insertion Sort – Definition

# Insertion Sort ek simple aur efficient algorithm hai small ya nearly-sorted arrays ke liye.

# Algorithm array ko sorted aur unsorted part mai divide karta hai.

# Left side ka part hamesha sorted hota hai.

# Har naye element ko uthakar uski sahi jagah par insert karta hai (jaise hum cards ko arrange karte hain).

# Example (Ascending Order):

# Array: [5, 2, 4, 1, 3]

# Pass 1:

# Left = [5] (already sorted)

# Pass 2:

# Next element = 2

# Compare with 5 → 2 < 5 → Insert before 5

# Array: [2, 5, 4, 1, 3]

# Pass 3:

# Next element = 4

# Compare with 5 → 4 < 5 → move 5 → [2, 4, 5, 1, 3]

# Pass 4:

# Next element = 1

# Compare with 5, 4, 2 → Insert at start

# Array: [1, 2, 4, 5, 3]

# Pass 5:

# Next element = 3

# Compare with 5 → move 5 → [1, 2, 4, 3, 5]

# Compare with 4 → move 4 → [1, 2, 3, 4, 5]

#  Array sorted.

# 🔹 Time Complexity

# Best Case (already sorted): O(n)

# Average Case: O(n²)

# Worst Case (reverse sorted): O(n²)

# 🔹 Space Complexity

# O(1) (in-place sort, extra memory nahi chahiye).

# 🔹 Characteristics

# Stable Sort: Duplicates ka relative order preserve hota hai.

# Choti arrays ya nearly sorted arrays ke liye bohot fast hota hai.

# Simple implementation.

# 🔹 Use Cases

# Small datasets (jaise 20–30 elements tak).

# Jab data almost sorted ho (kyunki performance O(n) ho jata hai).

# Computer science mai advanced algorithms ke liye subroutine ke tor par use hota hai (jaise TimSort, hybrid sorting).