# Quick Sort – Definition

# Quick Sort ek divide and conquer sorting algorithm hai.
# Isme ek pivot element select kiya jata hai, aur array ko do parts mai tod diya jata hai:

# Pivot se chhote elements left side

# Pivot se bade elements right side

# Phir recursively left aur right dono parts ko sort karte hain. Aakhir mai pura array sorted ho jata hai.

# Example (Ascending Order):

# Array: [5, 2, 4, 1, 3]

# Step 1: Pivot = 5

# Left: [2, 4, 1, 3]

# Right: []

# Result: [2, 4, 1, 3] + [5] + []

# Step 2: Left sub-array [2, 4, 1, 3], Pivot = 2

# Left: [1]

# Right: [4, 3]

# Result: [1] + [2] + [4, 3]

# Step 3: Sub-array [4, 3], Pivot = 4

# Left: [3]

# Right: []

# Result: [3] + [4] + []

# Now combine:
# [1, 2, 3, 4, 5] sorted

# 🔹 Time Complexity

# Best Case: O(n log n) (jab pivot array ko barabar todta hai)

# Average Case: O(n log n)

# Worst Case: O(n²) (jab pivot bohot bura choose ho jaise smallest/largest element har bar)

# 🔹 Space Complexity

# O(log n) (recursive stack ke liye)

# 🔹 Characteristics

# Not Stable (duplicates ka order change ho sakta hai)

# In-place sorting (zyada memory nahi chahiye)

# Large datasets ke liye bohot fast hota hai.

# 🔹 Use Cases

# Large datasets ke liye efficient.

# Real-world mai bohot use hota hai (C, C++, Java, Python ke built-in sorting algorithms Quick Sort ke variations use karte hain).

# Databases aur system libraries mai kaafi popular hai