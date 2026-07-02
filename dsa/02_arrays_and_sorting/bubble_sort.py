# Bubble Sort is an algorithm that sorts an array from the lowest value to the highest value.

# How it works:

# Go through the array, one value at a time.
# For each value, compare the value with the next value.
# If the value is higher than the next one, swap the values so that the highest value comes last.
# Go through the array as many times as there are values in the array.

# 🔹 Bubble Sort kya hai?

# Bubble Sort aik algorithm hai jo array ko chhoti value se bari value tak sort karta hai.

# 🔹 Ye kaise kaam karta hai?

# Array ke andar ek ek value ko check karo.

# Har value ko uske next value ke sath compare karo.

# Agar current value bari ho next value se, to un dono ko swap kar do (taake bari wali value aakhri taraf chali jaye).

# Ye process utni dafa repeat karo jitni values array ke andar hain.

# 👉 Is tarah step by step saari chhoti values start mai aur bari values end mai chali jati hain, aur array sort ho jata hai.


# my_array = [64, 34, 25, 12, 22, 11, 90, 5]  # Ye hamara starting array hai

# n = len(my_array)  # Array ki length nikal rahe hain

# # Outer loop (i) - har round mai last element apni sahi position pe chala jata hai
# for i in range(n-1):
#     print(f"\nPass {i+1}")
#     # Inner loop (j) - adjacent elements compare karte hain
#     for j in range(n-i-1):
#         print(f"  Comparing index {j} and {j+1}")

#         # Agar current element next se bara hai to swap karo
#         if my_array[j] > my_array[j+1]:
#             my_array[j], my_array[j+1] = my_array[j+1], my_array[j]
#             print(f"    Swapped -> {my_array}")

# print("Sorted array:", my_array)  # Final sorted array print karte hain



my_array = [7, 33, 9, 12, 11]

n = len(my_array)
for i in range(n-1):
    swapped = False
    for j in range(n-i-1):
        if my_array[j] > my_array[j+1]:
            my_array[j], my_array[j+1] = my_array[j+1], my_array[j]
            swapped = True
    if not swapped:
        break

print("Sorted array:", my_array)



# Work Process

# Pass 1
#   Comparing index 0 and 1
#     Swapped -> [34, 64, 25, 12, 22, 11, 90, 5]
#   Comparing index 1 and 2
#     Swapped -> [34, 25, 64, 12, 22, 11, 90, 5]
#   Comparing index 2 and 3
#     Swapped -> [34, 25, 12, 64, 22, 11, 90, 5]
#   Comparing index 3 and 4
#     Swapped -> [34, 25, 12, 22, 64, 11, 90, 5]
#   Comparing index 4 and 5
#     Swapped -> [34, 25, 12, 22, 11, 64, 90, 5]
#   Comparing index 5 and 6
#   Comparing index 6 and 7
#     Swapped -> [34, 25, 12, 22, 11, 64, 5, 90]

# Pass 2
#   Comparing index 0 and 1
#     Swapped -> [25, 34, 12, 22, 11, 64, 5, 90]
#   Comparing index 1 and 2
#     Swapped -> [25, 12, 34, 22, 11, 64, 5, 90]
#   Comparing index 2 and 3
#     Swapped -> [25, 12, 22, 34, 11, 64, 5, 90]
#   Comparing index 3 and 4
#     Swapped -> [25, 12, 22, 11, 34, 64, 5, 90]
#   Comparing index 4 and 5
#   Comparing index 5 and 6
#     Swapped -> [25, 12, 22, 11, 34, 5, 64, 90]

# Pass 3
#   Comparing index 0 and 1
#     Swapped -> [12, 25, 22, 11, 34, 5, 64, 90]
#   Comparing index 1 and 2
#     Swapped -> [12, 22, 25, 11, 34, 5, 64, 90]
#   Comparing index 2 and 3
#     Swapped -> [12, 22, 11, 25, 34, 5, 64, 90]
#   Comparing index 3 and 4
#   Comparing index 4 and 5
#     Swapped -> [12, 22, 11, 25, 5, 34, 64, 90]

# Pass 4
#   Comparing index 0 and 1
#   Comparing index 1 and 2
#     Swapped -> [12, 11, 22, 25, 5, 34, 64, 90]
#   Comparing index 2 and 3
#   Comparing index 3 and 4
#     Swapped -> [12, 11, 22, 5, 25, 34, 64, 90]

# Pass 5
#   Comparing index 0 and 1
#     Swapped -> [11, 12, 22, 5, 25, 34, 64, 90]
#   Comparing index 1 and 2
#   Comparing index 2 and 3
#     Swapped -> [11, 12, 5, 22, 25, 34, 64, 90]

# Pass 6
#   Comparing index 0 and 1
#   Comparing index 1 and 2
#     Swapped -> [11, 5, 12, 22, 25, 34, 64, 90]

# Pass 7
#   Comparing index 0 and 1
#     Swapped -> [5, 11, 12, 22, 25, 34, 64, 90]
# Sorted array: [5, 11, 12, 22, 25, 34, 64, 90]


# Bubble Sort Time Complexity

# 🔹 Algorithm Reminder

# Bubble Sort mai:

# Har pass me adjacent elements compare hote hain.

# Agar galat order me hain to swap karte hain.

# Ye process repeat hota hai jab tak array sorted na ho jaye.

# 1. Best Case (Already Sorted Array)

# Agar array pehle se sorted ho aur hum algorithm me flag (no swap check) lagayen, to algorithm ek hi pass me ruk jayega.

# Sirf n-1 comparisons lagenge aur 0 swaps.

# Complexity banegi: O(n)

# 👉 Best Case: O(n) (with optimization)
# 👉 Agar optimization na ho to hamesha O(n²)

# 2. Worst Case (Reverse Sorted Array)

# Agar array bilkul ulta sorted hai to har element ko har pass me swap karna padega.

# Matlab har pass me maximum swaps honge.

# Comparisons = n(n-1)/2 ≈ O(n²)

# Swaps = O(n²)

# 👉 Worst Case: O(n²)

# 3. Average Case (Random Array)

# Random order ke liye average me bhi comparisons hamesha O(n²) hi rahte hain.

# Swaps bhi kaafi hote hain.

# 👉 Average Case: O(n²)

# 4. Space Complexity

# Bubble Sort inplace hai, sirf ek temporary variable swap ke liye lagta hai.

# Space Complexity = O(1)

# 📊 Summary
# Case	Time Complexity
# Best Case	O(n) (optimized) / O(n²) (without check)
# Average	O(n²)
# Worst Case	O(n²)
# Space	O(1)

# ✅ Fayda (Advantages)

# Bahut simple algorithm hai.

# Easy to implement.

# ❌ Nuksan (Disadvantages)

# Bade arrays ke liye bahut slow.

# Hamesha O(n²) ke comparisons karne padte hain.