# Radix Sort – Definition

# Radix Sort ek non-comparison based sorting algorithm hai jo numbers ko digit by digit sort karta hai.

# Sabse pehle 1s place (least significant digit) se sort karta hai.

# Phir 10s place, phir 100s place, aur aise hi sab digits tak.

# Usually Counting Sort ko sub-routine ke tor par use karta hai har digit ko sort karne ke liye.

# Example:

# Array: [170, 45, 75, 90, 802, 24, 2, 66]

# Step 1: Sort by 1s place (LSD)
# → [170, 90, 802, 2, 24, 45, 75, 66]

# Step 2: Sort by 10s place
# → [802, 2, 24, 45, 66, 170, 75, 90]

# Step 3: Sort by 100s place
# → [2, 24, 45, 66, 75, 90, 170, 802]  sorted

# 🔹 Time Complexity

# O(n × k)

# n = number of elements

# k = number of digits (in the largest number)

# 🔹 Space Complexity

# O(n + k) (kyunki Counting Sort lagta hai har digit ke liye).

# 🔹 Characteristics

# Stable Sort (agar Counting Sort stable use karein).

# Comparison-based nahi hai.

# Integers aur strings dono ke liye kaam karta hai.

# 🔹 Use Cases

# Large integers sort karna.

# Strings ko lexicographical order me sort karna.

# Jab numbers ke digits limited ho (jaise 32-bit integers).

# Jab data kaafi bara ho lekin range of digits manageable ho.

#  matlab, Radix Sort kaam karta hai jab range badi ho aur Counting Sort directly feasible na ho, lekin digits ka count manageable ho.