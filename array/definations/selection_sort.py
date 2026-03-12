# 🔹 Selection Sort – Definition

# Selection Sort ek simple sorting algorithm hai jo array ko is tarah sort karta hai:

# Har pass mai, array ke unsorted part se minimum element dhoondta hai.

# Us minimum element ko unsorted part ke pehle element ke sath swap kar deta hai.

# Ye process tab tak repeat hota hai jab tak pura array sorted na ho jaye.

# Example (Ascending Order):

# Array: [5, 2, 4, 1, 3]

# Pass 1:

# Minimum = 1 (array me [5,2,4,1,3])

# Swap with first element → [1, 2, 4, 5, 3]

# Pass 2:

# Minimum = 2 (remaining [2,4,5,3])

# Already in place → [1, 2, 4, 5, 3]

# Pass 3:

# Minimum = 3 (remaining [4,5,3])

# Swap 3 with 4 → [1, 2, 3, 5, 4]

# Pass 4:

# Minimum = 4 (remaining [5,4])

# Swap 4 with 5 → [1, 2, 3, 4, 5]

#  Array sorted ho gaya.

# 🔹 Time Complexity

# Best Case: O(n²) (kyunki comparison hamesha karne padte hain, swap chahe na ho)

# Average Case: O(n²)

# Worst Case: O(n²)

# 🔹 Space Complexity

# O(1) (in-place sorting, extra memory nahi chahiye).

# 🔹 Characteristics

# Not Stable: Agar duplicate elements hain, to unki relative order change ho sakti hai.

# Simple aur kam swaps karta hai (Bubble sort se kam).

# 🔹 Use Cases

# Jab swaps costly ho (jaise flash memory me, jahan write operations limited hote hain).

# Small datasets jahan algorithm ki simplicity important hai.

# Educational purpose ke liye.