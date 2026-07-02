# Quicksort
# As the name suggests, Quicksort is one of the fastest sorting algorithms.

# The Quicksort algorithm takes an array of values, chooses one of the values as the 'pivot' element, and moves the other values so that lower values are on the left of the pivot element, and higher values are on the right of it.
# In this tutorial the last element of the array is chosen to be the pivot element, but we could also have chosen the first element of the array, or any element in the array really.

# Then, the Quicksort algorithm does the same operation recursively on the sub-arrays to the left and right side of the pivot element. This continues until the array is sorted.

# Recursion is when a function calls itself.

# After the Quicksort algorithm has put the pivot element in between a sub-array with lower values on the left side, and a sub-array with higher values on the right side, the algorithm calls itself twice, so that Quicksort runs again for the sub-array on the left side, and for the sub-array on the right side. The Quicksort algorithm continues to call itself until the sub-arrays are too small to be sorted.

# The algorithm can be described like this:

# How it works:

# Choose a value in the array to be the pivot element.
# Order the rest of the array so that lower values than the pivot element are on the left, and higher values are on the right.
# Swap the pivot element with the first element of the higher values so that the pivot element lands in between the lower and higher values.
# Do the same operations (recursively) for the sub-arrays on the left and right side of the pivot element.

# Manual Run Through: What Happened?
# Before we implement the algorithm in a programming language we need to go through what happened above in more detail.

# We have already seen that last value of the array is chosen as the pivot element, and the rest of the values are arranged so that the values lower than the pivot value are to the left, and the higher values are to the right.

# After that, the pivot element is swapped with the first of the higher values. This splits the original array in two, with the pivot element in between the lower and the higher values.

# Now we need to do the same as above with the sub-arrays on the left and right side of the old pivot element. And if a sub-array has length 0 or 1, we consider it finished sorted.

# To sum up, the Quicksort algorithm makes the sub-arrays become shorter and shorter until array is sorted.

# Quicksort Implementation
# To write a 'quickSort' method that splits the array into shorter and shorter sub-arrays we use recursion. This means that the 'quickSort' method must call itself with the new sub-arrays to the left and right of the pivot element. Read more about recursion here.

# To implement the Quicksort algorithm in a programming language, we need:

# An array with values to sort.
# A quickSort method that calls itself (recursion) if the sub-array has a size larger than 1.
# A partition method that receives a sub-array, moves values around, swaps the pivot element into the sub-array and returns the index where the next split in sub-arrays happens.
# The resulting code looks like this:


# Quicksort

# Jaise ke naam se lagta hai, Quicksort ek bohot tez sorting algorithm hai.

# Algorithm ka Idea

# Quicksort algorithm ek array leta hai, phir us array se ek element ko pivot ke taur pe choose karta hai.

# Pivot ke left side wale elements pivot se chhote hote hain.

# Pivot ke right side wale elements pivot se baray hote hain.

# Is tutorial mai hum last element ko pivot choose karte hain, lekin asal mai koi bhi element pivot banaya jaa sakta hai.

# Phir Quicksort wahi process recursive tareeqe se (yani function khud ko call karta hai) left aur right sub-arrays pe apply karta hai. Ye chalta rehta hai jab tak array sorted na ho jaye.

# Recursion ka Matlab

# Recursion ka matlab hai ek function apne andar khud ko call kare.

# Quicksort mai pivot element ke baad left aur right sub-arrays bante hain.

# Quicksort left sub-array pe dubara chalta hai

# Quicksort right sub-array pe dubara chalta hai

# Ye tab tak repeat hota hai jab tak sub-array ka size 0 ya 1 na ho (aur wo already sorted samjhe jaate hain).

# Algorithm Steps

# Array mai se ek element ko pivot select karo.

# Baaki elements ko arrange karo: pivot se chhote left mai, pivot se baray right mai.

# Pivot ko us position pe swap karo jahan wo apne sahi jagah pe aajaye (beech mai).

# Ab Quicksort ko recursively left aur right sub-arrays pe chalao.

# Manual Example (Samjhne ke liye)

# Array ka last element pivot choose kiya.

# Left mai pivot se chhote, right mai pivot se baray numbers aa gaye.

# Pivot ko apni sahi jagah pe swap kar diya.

# Ab array do hisso mai split ho gaya.

# Yehi kaam left aur right dono sub-arrays pe repeat hota hai.

# Jab arrays chhote hote hote size 0 ya 1 ke ho jate hain → sorting complete ho jaati hai.

# Summary

# Quicksort array ko tod tod ke chhota karta hai, aur har step pe pivot ke hisaab se arrange karta hai.
# Aakhir mai array sorted ho jata hai.



def partition(array, low, high):
    pivot = array[high]
    i = low - 1

    for j in range(low, high):
        if array[j] <= pivot:
            i += 1
            array[i], array[j] = array[j], array[i]

    array[i+1], array[high] = array[high], array[i+1]
    return i+1

def quicksort(array, low=0, high=None):
    if high is None:
        high = len(array) - 1

    if low < high:
        pivot_index = partition(array, low, high)
        quicksort(array, low, pivot_index-1)
        quicksort(array, pivot_index+1, high)

my_array = [64, 34, 25, 12, 22, 11, 90, 15]
quicksort(my_array)
print("Sorted array:", my_array)





# def partition(array, low, high):
#     pivot = array[high]   # last element pivot
#     i = low - 1

#     for j in range(low, high):
#         if array[j] <= pivot:
#             i += 1
#             array[i], array[j] = array[j], array[i]

#     array[i+1], array[high] = array[high], array[i+1]
#     return i+1


# def quicksort(array, low=0, high=None):
#     if high is None:
#         high = len(array) - 1

#     if low < high:
#         pivot_index = partition(array, low, high)
#         quicksort(array, low, pivot_index-1)
#         quicksort(array, pivot_index+1, high)


# def sort_multiple(arrays):
#     for idx, arr in enumerate(arrays):
#         quicksort(arr)   # inplace sort karega
#         print(f"Sorted array {idx+1}: {arr}")
#     return arrays


# # ✅ Example
# arrays = [
#     [64, 34, 25, 12, 22, 11, 90, 15],
#     [5, 9, 1, 3, 7],
#     [100, 50, 200, 150]
# ]

# sorted_arrays = sort_multiple(arrays)

# print("\nAll sorted arrays:", sorted_arrays)






# # Ye function array ko partition karta hai (pivot ke hisaab se divide karna)
# def partition(array, low, high):
#     # Pivot ko humesha last element choose kiya gaya hai
#     pivot = array[high]
    
#     # i pointer pivot se chhote elements ke liye track rakhta hai
#     i = low - 1

#     # Array ko traverse karte hain low se high-1 tak
#     for j in range(low, high):
#         # Agar current element pivot se chhota ya barabar ho
#         if array[j] <= pivot:
#             i += 1  # i ko aage badhao
#             # Swap karo current element aur i position wale element ko
#             array[i], array[j] = array[j], array[i]

#     # Loop ke baad pivot ko uski sahi jagah (i+1) pe le aao
#     array[i+1], array[high] = array[high], array[i+1]

#     # Pivot ki correct index return karo
#     return i+1



# # Quicksort function jo recursive call karta hai
# def quicksort(array, low=0, high=None):
#     # Agar high di hui nahi to array ke last index set karo
#     if high is None:
#         high = len(array) - 1

#     # Base case: jab tak low < high hai tab tak recursion chalega
#     if low < high:
#         # Partition karo aur pivot index hasil karo
#         pivot_index = partition(array, low, high)

#         # Pivot ke left side wale array ko recursively sort karo
#         quicksort(array, low, pivot_index-1)

#         # Pivot ke right side wale array ko recursively sort karo
#         quicksort(array, pivot_index+1, high)


# # Example array
# my_array = [64, 34, 25, 12, 22, 11, 90, 5]

# # Quicksort ko call karo
# quicksort(my_array)

# # Sorted array print karo
# print("Sorted array:", my_array)

# Step by Step Example (Manual Run)

# Maan lo array hai:

# [64, 34, 25, 12, 22, 11, 90, 5]


# Pivot = 5 (last element)

# Sare elements pivot se baray hain, to pivot swap hoke index 0 pe chala jata hai.

# Array ban gaya:

# [5, 34, 25, 12, 22, 11, 90, 64]


# Ab left side empty hai, aur right side [34, 25, 12, 22, 11, 90, 64] pe quicksort chalega.

# Pivot = 64

# Sare chhote elements left mai chale jate hain, aur pivot apni jagah aa jata hai.

# Array:

# [5, 34, 25, 12, 22, 11, 64, 90]


# Ab left side [34, 25, 12, 22, 11] pe quicksort. Pivot = 11

# Array:

# [5, 11, 25, 12, 22, 34, 64, 90]


# Yehi recursive process chalta rehta hai jab tak array pura sort na ho jaye.

# Final result:

# [5, 11, 12, 22, 25, 34, 64, 90]



# Quicksort Time Complexity
# For a general explanation of what time complexity is, visit this page.

# For a more thorough and detailed explanation of Quicksort time complexity, visit this page.

# The worst case scenario for Quicksort is 
# O
# (
# n
# 2
# )
# . This is when the pivot element is either the highest or lowest value in every sub-array, which leads to a lot of recursive calls. With our implementation above, this happens when the array is already sorted.

# But on average, the time complexity for Quicksort is actually just 
# O
# (
# n
# log
# n
# )
# , which is a lot better than for the previous sorting algorithms we have looked at. That is why Quicksort is so popular.

# Below you can see the significant improvement in time complexity for Quicksort in an average scenario 
# O
# (
# n
# log
# n
# )
# , compared to the previous sorting algorithms Bubble, Selection and Insertion Sort with time complexity 
# O
# (
# n
# 2
# )
# :


# Quicksort Time Complexity
# Worst Case

# Quicksort ka worst case tab hota hai jab pivot hamesha ya to sabse chhota element select ho ya sabse bara element.

# Aisa tab hota hai jab array already sorted ho.

# Is case mai bohot zyada recursive calls hoti hain.

# Is liye worst case time complexity: O(n²)

# Average Case

# Lekin average case mai pivot randomly acha select ho jata hai aur array barabar divide hota hai.

# Is case mai recursive calls balanced hoti hain.

# Time complexity: O(n log n)

# Kyun Popular Hai?

# Quicksort average case mai O(n log n) ka performance deta hai, jo Bubble Sort, Selection Sort, aur Insertion Sort se bohot zyada fast hai (kyunki wo sab O(n²) hote hain).

# Comparison

# Bubble Sort → O(n²)

# Selection Sort → O(n²)

# Insertion Sort → O(n²)

# Quicksort (average case) → O(n log n) ✅

# Isi wajah se Quicksort ek bohot popular aur efficient sorting algorithm hai.




# 🔹 Algorithm ka Overview

# Quicksort ek divide and conquer sorting algorithm hai.
# Idea ye hai:

# Ek element choose karo (isko kehte hain pivot).

# Array ke elements ko do groups mai divide karo:

# jo pivot se chhote ya barabar hain (left side)

# jo pivot se bade hain (right side)

# Ab recursively left aur right parts ko sort karo.

# Jab parts ek element ya empty ho jayein → recursion ruk jata hai → array sorted ban jata hai.

# 🔹 Tumhara Code Samajhna

# Tumne 2 functions banaye hain:

# 1. partition(array, low, high)

# Pivot choose karta hai (last element ko).

# Ek loop chalata hai aur pivot ke basis par elements ko left/right adjust karta hai.

# Pivot ko uski sahi jagah par le jata hai aur return karta hai pivot_index.

# 2. quicksort(array, low, high)

# Base case check karta hai: if low < high: (agar ek se zyada element hain to hi sort hoga).

# partition call karta hai pivot ko place karne ke liye.

# Left aur Right parts ko recursively quicksort call karta hai.

# 🔹 Step by Step Execution with Example

# Tumhara array:

# my_array = [64, 34, 25, 12, 22, 11, 90, 15]

# Step 1: First call
# quicksort([64, 34, 25, 12, 22, 11, 90, 15], low=0, high=7)


# 👉 Partition call hota hai (0,7)

# Pivot = last element = 15

# i = -1

# Loop j=0→6:

# 64 > 15 → ignore

# 34 > 15 → ignore

# 25 > 15 → ignore

# 12 <= 15 → i=0 → swap(64,12) → [12, 34, 25, 64, 22, 11, 90, 15]

# 22 > 15 → ignore

# 11 <= 15 → i=1 → swap(34,11) → [12, 11, 25, 64, 22, 34, 90, 15]

# 90 > 15 → ignore

# End mai pivot swap hota hai:
# swap(25,15) → [12, 11, 15, 64, 22, 34, 90, 25]

# Pivot index = 2

# Step 2: Divide

# Ab 2 parts:

# Left: [12, 11] (low=0, high=1)

# Right: [64, 22, 34, 90, 25] (low=3, high=7)

# Step 3: Left side [12,11]
# partition([12,11], low=0, high=1)


# Pivot = 11

# i = -1

# Loop j=0 → 12 > 11 → ignore

# Swap pivot → [11, 12, 15, 64, 22, 34, 90, 25]
# Pivot index = 0

# Ab [11] aur [12] dono already sorted.

# Step 4: Right side [64, 22, 34, 90, 25]
# partition([64,22,34,90,25], low=3, high=7)


# Pivot = 25

# i = 2

# Loop j=3→6

# 64 > 25 → skip

# 22 <= 25 → i=3 → swap(64,22) → [11,12,15,22,64,34,90,25]

# 34 > 25 → skip

# 90 > 25 → skip

# Swap pivot → [11,12,15,22,25,34,90,64]
# Pivot index = 4

# Step 5: Right side split

# Left: [22] (already sorted)

# Right: [34, 90, 64]

# Step 6: Partition [34,90,64]

# Pivot = 64

# i=4

# Loop:

# 34 <= 64 → i=5 → swap(90,34) → [11,12,15,22,25,34,90,64]

# 90 > 64 → skip

# Swap pivot (64,90) → [11,12,15,22,25,34,64,90]
# Pivot index = 6

# Step 7: Finish

# Left: [34]

# Right: [90]
# dono already sorted.

# 🔹 Final Result
# [11, 12, 15, 22, 25, 34, 64, 90]

# 🔹 Simple Samajh lo

# Har step mai last element pivot banta hai.

# Pivot ke chhote numbers uske left chale jate hain, aur bade right.

# Pivot apni final jagah pe fix ho jata hai.

# Yehi process har chhote part ke liye repeat hota hai.

# Jab sub-array mai ek hi element reh jata hai → recursion ruk jata hai.

# 👉 Matlab: Quicksort har step mai array ko todta hai, pivot ko uski sahi jagah pe rakhta hai, aur baaki recursively sort karta hai → result hamesha sorted array.


        #                    [64, 34, 25, 12, 22, 11, 90, 15]
        #                                    |
        #                            Pivot = 15
        #                                    |
        #             ------------------------------------------------
        #             |                                              |
        #         [12, 11]                                  [64, 34, 25, 90, 22]
        #             |                                              |
        #        Pivot = 11                                    Pivot = 22
        #             |                                              |
        #    -------------------                        -------------------------
        #    |                 |                        |                       |
        #   []                [12]                [34, 25]                 [90, 64]
        #                                            |                        |
        #                                       Pivot = 25                Pivot = 64
        #                                            |                        |
        #                                      ----------                 ----------
        #                                      |        |                 |        |
        #                                     []       [34]              []       [90]



# [35, 12, 43, 8, 51, 22, 17]     # original array
#                |
#                17              # pivot = last element (17)
#         /              \
#    [12, 8]           [35, 43, 51, 22]
#       |                   |
#       8                  22
#    /     \            /        \
#  []     [12]      []      [35, 43, 51]
#                                |
#                                51
#                           /          \
#                    [35, 43]         []
#                        |
#                        43
#                    /        \
#                [35]         []



# Explanation 

# Pivot = 17 (root level)

# sab elements ≤ 17 → left [12, 8]

# sab elements > 17 → right [35, 43, 51, 22]

# Left side [12, 8]

# Pivot = 8

# left of 8 → [] (kuch bhi nahi chhota)

# right of 8 → [12] (sirf ek element bacha)
#  Sorted ban gaya [8, 12]

# Right side [35, 43, 51, 22]

# Pivot = 22

# left of 22 → []

# right of 22 → [35, 43, 51]

# Right side [35, 43, 51]

# Pivot = 51

# left of 51 → [35, 43]

# right of 51 → []

# Right side ka left [35, 43]

# Pivot = 43

# left of 43 → [35]

# right of 43 → []

#  Sorted = [35, 43]

# Backtracking & Merging

# [35, 43] + 51 → [35, 43, 51]

# 22 + [35, 43, 51] → [22, 35, 43, 51]

# [8, 12] + 17 + [22, 35, 43, 51] →

#  Final Sorted Array = [8, 12, 17, 22, 35, 43, 51]