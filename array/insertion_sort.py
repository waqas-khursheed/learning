# Insertion Sort
# The Insertion Sort algorithm uses one part of the array to hold the sorted values, and the other part of the array to hold values that are not sorted yet.

# The algorithm takes one value at a time from the unsorted part of the array and puts it into the right place in the sorted part of the array, until the array is sorted.

# How it works:

# Take the first value from the unsorted part of the array.
# Move the value into the correct place in the sorted part of the array.
# Go through the unsorted part of the array again as many times as there are values.

# Manual Run Through: What Happened?
# We must understand what happened above to fully understand the algorithm, so that we can implement the algorithm in a programming language.

# The first value is considered to be the initial sorted part of the array.

# Every value after the first value must be compared to the values in the sorted part of the algorithm so that it can be inserted into the correct position.

# The Insertion Sort Algorithm must run through the array 4 times, to sort the array of 5 values because we do not have to sort the first value.

# And each time the algorithm runs through the array, the remaining unsorted part of the array becomes shorter.

# We will now use what we have learned to implement the Insertion Sort algorithm in a programming language.

# Insertion Sort Implementation
# To implement the Insertion Sort algorithm in a programming language, we need:

# An array with values to sort.
# An outer loop that picks a value to be sorted. For an array with 
# n
#  values, this outer loop skips the first value, and must run 
# n
# −
# 1
#  times.
# An inner loop that goes through the sorted part of the array, to find where to insert the value. If the value to be sorted is at index 
# i
# , the sorted part of the array starts at index 
# 0
#  and ends at index 
# i
# −
# 1
# .
# The resulting code looks like this:



my_array = [64, 34, 25, 12, 22, 11, 90, 5]

n = len(my_array)
for i in range(1,n):
    insert_index = i
    current_value = my_array.pop(i)
    for j in range(i-1, -1, -1):
        if my_array[j] > current_value:
            insert_index = j
    my_array.insert(insert_index, current_value)

print("Sorted array:", my_array)


# my_array = [64, 34, 25, 12, 22, 11, 90, 5]

# n = len(my_array)
# for i in range(1,n):
#     insert_index = i
#     current_value = my_array[i]
#     for j in range(i-1, -1, -1):
#         if my_array[j] > current_value:
#             my_array[j+1] = my_array[j]
#             insert_index = j
#         else:
#             break
#     my_array[insert_index] = current_value

# print("Sorted array:", my_array)


# 🔹 Idea

# Ye Insertion Sort Algorithm hai.
# Insertion sort mai hum har element ko ek ek karke uthate hain aur use uski correct jagah pe insert kar dete hain.
# Aise array dheere dheere sort hota hai.

# ✅ 1: Initial Array
# my_array = [64, 34, 25, 12, 22, 11, 90, 5]

# ✅ 2: Loop Start

# Loop chal raha hai for i in range(1, n)
# 👉 i=1 se shuru hoga (kyunke index 0 already sorted maan lete hain).

# ✅ 3: Pop Element

# Line:

# current_value = my_array.pop(i)


# Ye element ko nikal deta hai aur uski value current_value mai store ho jati hai.
# Baaki array uske bina reh jata hai.

# ✅ 4: Reverse Loop

# Line:

# for j in range(i-1, -1, -1):


# Ye ulta loop hai jo i-1 se lekar 0 tak jaata hai.
# Purpose: current_value se compare karna aur dekhna ke usse chhote ya bade element ke pehle insert karna hai.

# ✅ 5: Insert Index Update

# Line:

# if my_array[j] > current_value:
#     insert_index = j


# 👉 Agar array ka element bada hai current_value se,
# to uski jagah update kar di jati hai.

# ✅ 6: Insert Wapas

# Line:

# my_array.insert(insert_index, current_value)


# 👉 Element ko us correct jagah list mai daal dete hain.
# Ab tak jo portion array ka sorted hai, wo ab sahi ho jaata hai.

# 🔹 Dry Run Example

# Array: [64, 34, 25, 12, 22, 11, 90, 5]

# i=1 → current_value = 34
# Compare with 64 → insert_index = 0
# Array → [34, 64, 25, 12, 22, 11, 90, 5]

# i=2 → current_value = 25
# Compare with 64, 34 → insert_index = 0
# Array → [25, 34, 64, 12, 22, 11, 90, 5]

# i=3 → current_value = 12
# Compare with 64, 34, 25 → insert_index = 0
# Array → [12, 25, 34, 64, 22, 11, 90, 5]

# i=4 → current_value = 22
# Compare with 64, 34, 25, 12 → insert_index = 1
# Array → [12, 22, 25, 34, 64, 11, 90, 5]

# i=5 → current_value = 11
# Compare with all previous → insert_index = 0
# Array → [11, 12, 22, 25, 34, 64, 90, 5]

# i=6 → current_value = 90
# Compare with 64 → no change
# Array → [11, 12, 22, 25, 34, 64, 90, 5]

# i=7 → current_value = 5
# Compare with all previous → insert_index = 0
# Array → [5, 11, 12, 22, 25, 34, 64, 90]




# Insertion Sort Time Complexity

# 1. Best Case (Already Sorted Array)

# Agar array pehle se sorted hai, to har element apni sahi jagah pe hi hota hai.

# Inner loop for j in range(i-1, -1, -1): sirf 1 hi comparison karega.

# Matlab inner loop constant time (O(1)) chalega.

# Total time = O(n)

# 👉 Best Case Complexity: O(n)

# 2. Worst Case (Reverse Sorted Array)

# Agar array bilkul ulta sorted ho (jaise [9,8,7,6,5,4,3,2,1]), to har naya element ko shift karna padta hai.

# 1st element ke liye 1 comparison,

# 2nd ke liye 2,

# 3rd ke liye 3 … aur last ke liye n-1.

# Total comparisons ≈ n(n-1)/2 ≈ O(n²)

# 👉 Worst Case Complexity: O(n²)

# 3. Average Case (Random Array)

# Random array mai half elements ko shift karna padta hai (average).

# Is liye performance n²/4 ke around hota hai.

# Big-O mai simplify karke: O(n²)

# 👉 Average Case Complexity: O(n²)

# 4. Space Complexity

# Insertion Sort array ko in-place sort karta hai (extra memory nahi chahiye except ek variable current_value).

# Space Complexity = O(1)

# 📊 Summary
# Case	Time Complexity
# Best Case	O(n)
# Average	O(n²)
# Worst Case	O(n²)
# Space	O(1)

# ✅ Insertion Sort ka fayda:

# Chote arrays ke liye fast hota hai.

# Simple implement hota hai.

# Stable sorting algorithm hai (equal elements ki order preserve hoti hai).

# ❌ Nuksan:

# Bade arrays ke liye slow hai (because O(n²)).




# 🔹 Insertion Sort ka Concept

# Insertion Sort ek aise kaam karta hai jaise hum cards ko sort karte hain.
# Har naya card (element) uthate hain aur usko sahi jagah par dal dete hain.

# 🔹 Code Explanation (PHP/Python dono same logic)
# 1. Start:
# $my_array = [64, 34, 25, 12, 22, 11, 90, 5];
# $n = count($my_array);


# Array bana liya.

# n = 8 kyunki 8 elements hain.

# 2. Outer Loop
# for ($i = 1; $i < $n; $i++) {


# Yeh loop 1 se start hota hai (0 se nahi) kyunki hum maan lete hain ke pehla element already sorted hai.

# Har iteration me hum ek naya element lete hain aur use sahi jagah par insert karte hain.

# 3. Current Element uthana
# $insert_index = $i;
# $current_value = $my_array[$i];
# array_splice($my_array, $i, 1); // temporary remove


# Hum i position ka element nikal lete hain (jaise card haath me uthana).

# insert_index ko default i par rakhte hain.

# Example:

# i = 1 → current_value = 34, aur array ban gaya: [64, 25, 12, 22, 11, 90, 5]

# 4. Inner Loop (compare backward)
# for ($j = $i - 1; $j >= 0; $j--) {
#     if ($my_array[$j] > $current_value) {
#         $insert_index = $j;
#     }
# }


# Yeh loop peeche se check karta hai (i-1 se 0 tak).

# Har element ko current_value se compare karta hai.

# Agar koi element bada hota hai to uske index par insert_index set kar dete hain.

# Example:

# Jab i = 1, current_value = 34,
# Compare → 64 > 34 ✅ → insert_index = 0.

# 5. Insert element
# array_splice($my_array, $insert_index, 0, $current_value);


# Jab sahi jagah mil jati hai, hum current_value ko wahan insert kar dete hain.

# Ab array fir se sorted ban jaata hai upto i.

# Example:

# 34 ko index 0 par insert kiya → [34, 64, 25, 12, 22, 11, 90, 5]

# 🔹 Step by Step Dry Run
# Iteration 1 (i = 1):

# Current = 34, Remove → [64, 25, 12, 22, 11, 90, 5]

# Compare: 64 > 34 ✅ → Insert at 0

# Result = [34, 64, 25, 12, 22, 11, 90, 5]

# Iteration 2 (i = 2):

# Current = 25, Remove → [34, 64, 12, 22, 11, 90, 5]

# Compare: 64 > 25 ✅ → index = 1

# Compare: 34 > 25 ✅ → index = 0

# Insert at 0 → [25, 34, 64, 12, 22, 11, 90, 5]

# Iteration 3 (i = 3):

# Current = 12, Remove → [25, 34, 64, 22, 11, 90, 5]

# Compare: 64 > 12 ✅ index = 2

# Compare: 34 > 12 ✅ index = 1

# Compare: 25 > 12 ✅ index = 0

# Insert at 0 → [12, 25, 34, 64, 22, 11, 90, 5]

# Iteration 4 (i = 4):

# Current = 22, Remove → [12, 25, 34, 64, 11, 90, 5]

# Compare: 64 > 22 ✅ index = 3

# Compare: 34 > 22 ✅ index = 2

# Compare: 25 > 22 ✅ index = 1

# 12 < 22 ❌ stop → Insert at 1

# Result = [12, 22, 25, 34, 64, 11, 90, 5]

# Iteration 5 (i = 5):

# Current = 11, Remove → [12, 22, 25, 34, 64, 90, 5]

# Compare sabse → 64, 34, 25, 22, 12 sab > 11 ✅ index = 0

# Insert at 0 → [11, 12, 22, 25, 34, 64, 90, 5]

# Iteration 6 (i = 6):

# Current = 90, Remove → [11, 12, 22, 25, 34, 64, 5]

# Compare sab → sab chhote hain ❌

# Insert at 6 (last) → [11, 12, 22, 25, 34, 64, 90, 5]

# Iteration 7 (i = 7):

# Current = 5, Remove → [11, 12, 22, 25, 34, 64, 90]

# Compare sab elements > 5 ✅ index = 0

# Insert at 0 → [5, 11, 12, 22, 25, 34, 64, 90]

# ✅ Final Sorted Array:

# [5, 11, 12, 22, 25, 34, 64, 90]


# 👉 Ab aapko pura samajh aa gaya hoga ke har loop step kya karta hai:

# Outer loop → ek element uthata hai.

# Inner loop → sahi jagah dhoondta hai.

# Insert → element ko wahan wapas dalta hai.