# Merge Sort – Definition

# Merge Sort ek divide and conquer sorting algorithm hai.
# Isme array ko repeatedly do halves mai todte hain, dono halves ko recursively sort karte hain, aur phir unhe merge karke ek sorted array banate hain.

# Example (Ascending Order):

# Array: [5, 2, 4, 1, 3]

# Split: [5, 2, 4] and [1, 3]

# Split [5, 2, 4] → [5, 2] and [4]

# Split [5, 2] → [5] and [2] → merge → [2, 5]

# Merge [2, 5] and [4] → [2, 4, 5]

# Split [1, 3] → [1] and [3] → merge → [1, 3]

# Final merge [2, 4, 5] and [1, 3] → [1, 2, 3, 4, 5] 

# 🔹 Time Complexity

# Best Case: O(n log n)

# Average Case: O(n log n)

# Worst Case: O(n log n)

# ⚡ iska performance hamesha consistent hai (Quick Sort jaisa fluctuation nahi hota).

# 🔹 Space Complexity

# O(n) extra space (kyunki merging ke liye temporary arrays chahiye).

# 🔹 Characteristics

# Stable Sort (duplicate elements ka order preserve karta hai).

# Predictable aur reliable performance.

# Lekin extra memory lagti hai (Quick Sort se zyada).

# 🔹 Use Cases

# Large datasets jahan stability important ho.

# External sorting (jab data RAM se zyada ho aur disk par ho, e.g. big files).

# Databases, file systems, aur system libraries me use hota hai.

#  Summary vs Quick Sort:

# Quick Sort fast hota hai on average aur in-place hota hai (O(log n) space).

# Merge Sort thoda zyada memory leta hai, lekin hamesha O(n log n) performance deta hai aur stable hota hai.