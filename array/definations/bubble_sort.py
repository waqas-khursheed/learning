# Bubble Sort – Definition

# Bubble Sort ek simple sorting algorithm hai.

# Isme har pass mai adjacent (qareebi) elements ko compare kiya jata hai.

# Agar wo galat order mai hote hain (e.g. ascending sort chahiye aur left > right hai) to unhe swap kar diya jata hai.

# Yeh process repeat hota hai jab tak pura array sorted na ho jaye.

# Example:
# Suppose array hai: [5, 2, 4, 1, 3]

# Step by step (ascending order ke liye):

# Compare (5,2) → swap → [2, 5, 4, 1, 3]

# Compare (5,4) → swap → [2, 4, 5, 1, 3]

# Compare (5,1) → swap → [2, 4, 1, 5, 3]

# Compare (5,3) → swap → [2, 4, 1, 3, 5]
# ➡ First pass ke baad largest element (5) end par chala gaya.

# Yehi process har pass mai repeat hota hai jab tak list sorted ho jaye.

# 🔹 Bubble Sort – Time Complexity

# Best Case (already sorted):
# Sirf ek pass check karna padta hai → O(n)

# Average Case:
# Har element ko compare aur swap karna padta hai → O(n²)

# Worst Case (reverse sorted):
# Maximum comparisons aur swaps → O(n²)

# 🔹 Space Complexity

# O(1) (in-place sort, extra memory nahi chahiye).

# 🔹 Use Cases

# Mainly educational purpose ke liye (sorting concept sikhane ke liye).

# Small datasets jahan simplicity important ho speed se zyada.

# Real-world mai rarely use hota hai kyunki bohot slow hai.