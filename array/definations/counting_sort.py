# Counting Sort – Definition

# Counting Sort ek non-comparison based sorting algorithm hai.
# Isme elements ko compare nahi kiya jata, balki unki frequency (count) calculate karke array ko sorted banaya jata hai.

# Steps:

# Input array ke sabse maximum element (k) tak ka ek count array banao.

# Har element ki frequency count karo.

# Count array ko cumulative banao (jisse position mil jaye).

# Har element ko uski correct position par place karo → Sorted array mil jata hai.

# Example (Ascending Order):

# Array: [4, 2, 2, 8, 3, 3, 1]

# Max element = 8 → Count array size = 9 (0 to 8)
# Initially: [0,0,0,0,0,0,0,0,0]

# Count frequency:

# 1 → 1

# 2 → 2

# 3 → 2

# 4 → 1

# 8 → 1

# Count array: [0,1,2,2,1,0,0,0,1]

# Cumulative count (positions):
# [0,1,3,5,6,6,6,6,7]

# Place elements in output array:
# [1,2,2,3,3,4,8] sorted

# 🔹 Time Complexity

# O(n + k)

# n = number of elements

# k = maximum element value

# 🔹 Space Complexity

# O(k) extra space (count array ke liye).

# 🔹 Characteristics

# Stable Sort (agar implement sahi tareeke se ho).

# Sirf integers ya discrete values ke liye kaam karta hai.

# Jab values ka range bohot bada ho (e.g. 0 to 1 billion), tab inefficient ho jata hai.

# 🔹 Use Cases

# Marks/grades sorting (0–100)

# Counting items jahan range chhoti ho (like age groups, categories, votes).

# Large dataset me fast ho sakta hai jab values ka range limited ho.