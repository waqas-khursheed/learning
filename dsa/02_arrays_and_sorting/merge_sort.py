# Merge Sort
# The Merge Sort algorithm is a divide-and-conquer algorithm that sorts an array by first breaking it down into smaller arrays, and then building the array back together the correct way so that it is sorted.

# ivide: The algorithm starts with breaking up the array into smaller and smaller pieces until one such sub-array only consists of one element.

# Conquer: The algorithm merges the small pieces of the array back together by putting the lowest values first, resulting in a sorted array.

# The breaking down and building up of the array to sort the array is done recursively.

# In the animation above, each time the bars are pushed down represents a recursive call, splitting the array into smaller pieces. When the bars are lifted up, it means that two sub-arrays have been merged together.

# The Merge Sort algorithm can be described like this:

# How it works:

# Divide the unsorted array into two sub-arrays, half the size of the original.
# Continue to divide the sub-arrays as long as the current piece of the array has more than one element.
# Merge two sub-arrays together by always putting the lowest value first.
# Keep merging until there are no sub-arrays left.



# Manual Run Through: What Happened?
# We see that the algorithm has two stages: first splitting, then merging.

# Although it is possible to implement the Merge Sort algorithm without recursion, we will use recursion because that is the most common approach.

# We cannot see it in the steps above, but to split an array in two, the length of the array is divided by two, and then rounded down to get a value we call "mid". This "mid" value is used as an index for where to split the array.

# After the array is split, the sorting function calls itself with each half, so that the array can be split again recursively. The splitting stops when a sub-array only consists of one element.

# At the end of the Merge Sort function the sub-arrays are merged so that the sub-arrays are always sorted as the array is built back up. To merge two sub-arrays so that the result is sorted, the values of each sub-array are compared, and the lowest value is put into the merged array. After that the next value in each of the two sub-arrays are compared, putting the lowest one into the merged array.

# Merge Sort Implementation
# To implement the Merge Sort algorithm we need:

# An array with values that needs to be sorted.
# A function that takes an array, splits it in two, and calls itself with each half of that array so that the arrays are split again and again recursively, until a sub-array only consist of one value.
# Another function that merges the sub-arrays back together in a sorted way.
# The resulting code looks like this:



# Merge Sort

# Merge Sort ek divide-and-conquer algorithm hai jo array ko sort karta hai pehle usay chhote chhote arrays mai todh kar, phir unko sahi order mai wapas jodhta hai taa keh sorted array mil jaye.

# Divide: Algorithm shuru mai array ko todhta hai chhoti aur chhoti pieces mai, jab tak ek sub-array sirf ek element ka na reh jaye.

# Conquer: Uske baad algorithm chhoti pieces ko wapas merge karta hai, hamesha chhoti value pehle rakhte hue, taa keh array sorted ban jaye.

# Array ka todhna aur dobara banana recursion se hota hai.

# Animation mai jab bars neeche push hote hain to iska matlab hai recursive call ho rahi hai, array todha ja raha hai. Jab bars upar uthte hain to iska matlab hai do sub-arrays merge ho gaye hain.

# Algorithm ka tareeqa:

# Unsorted array ko do sub-arrays mai divide karo, jo asal array ka aadha size hote hain.

# Sub-arrays ko divide karte raho jab tak current piece mai sirf ek element na reh jaye.

# Do sub-arrays ko merge karo hamesha chhoti value pehle daal ke.

# Merge karte raho jab tak koi sub-array baqi na rahe.

# Manual Run Through: Kya hua?

# Algorithm do stages mai hota hai:

# Pehle splitting (todna)

# Phir merging (dobara jorna)

# Array ko do parts mai todhne ke liye array ki length ko 2 se divide karke floor value nikalte hain, usay "mid" bolte hain. Ye "mid" index hota hai jahan array todhna hai.

# Jab array split ho jata hai, sorting function khud ko call karta hai har half ke liye, taa keh arrays bar bar todhe ja saken recursively. Splitting us waqt rukta hai jab sub-array sirf ek element ka reh jaye.

# Akhri mai Merge Sort function sub-arrays ko dobara merge karta hai taa keh har step pe merged array sorted banta jaye. Merge karte waqt dono sub-arrays ke elements compare kiye jate hain aur chhoti value pehle rakhi jati hai. Phir agla element compare hota hai aur chhoti value merged array mai daali jati hai.

# Merge Sort Implementation mai chahiye hota hai:

# Ek array jisme values sort karni hain.

# Ek function jo array ko split kare aur recursively khud ko call kare jab tak sub-array mai sirf ek value na reh jaye.

# Ek function jo sub-arrays ko dobara merge kare sorted order mai.

def mergeSort(arr):
    if len(arr) <= 1:
        return arr

    mid = len(arr) // 2
    leftHalf = arr[:mid]
    rightHalf = arr[mid:]

    sortedLeft = mergeSort(leftHalf)
    sortedRight = mergeSort(rightHalf)

    return merge(sortedLeft, sortedRight)

def merge(left, right):
    result = []
    i = j = 0

    while i < len(left) and j < len(right):
        if left[i] < right[j]:
            result.append(left[i])
            i += 1
        else:
            result.append(right[j])
            j += 1

    result.extend(left[i:])
    result.extend(right[j:])

    return result

unsortedArr = [3, 7, 6, -10, 15, 23.5, 55, -13]
sortedArr = mergeSort(unsortedArr)
print("Sorted array:", sortedArr)


# Merge Sort without Recursion

# def merge(left, right):
#     result = []
#     i = j = 0
    
#     while i < len(left) and j < len(right):
#         if left[i] < right[j]:
#             result.append(left[i])
#             i += 1
#         else:
#             result.append(right[j])
#             j += 1
            
#     result.extend(left[i:])
#     result.extend(right[j:])
    
#     return result

# def mergeSort(arr):
#     step = 1  # Starting with sub-arrays of length 1
#     length = len(arr)
    
#     while step < length:
#         for i in range(0, length, 2 * step):
#             left = arr[i:i + step]
#             right = arr[i + step:i + 2 * step]
            
#             merged = merge(left, right)
            
#             # Place the merged array back into the original array
#             for j, val in enumerate(merged):
#                 arr[i + j] = val
                
#         step *= 2  # Double the sub-array length for the next iteration
        
#     return arr

# unsortedArr = [3, 7, 6, -10, 15, 23.5, 55, -13]
# sortedArr = mergeSort(unsortedArr)
# print("Sorted array:", sortedArr)


# def mergeSort(arr, level=0):
#     print("  " * level, f"mergeSort called on: {arr}")

#     if len(arr) <= 1:
#         print("  " * level, f"Returning (base case): {arr}")
#         return arr

#     mid = len(arr) // 2
#     leftHalf = arr[:mid]
#     rightHalf = arr[mid:]

#     print("  " * level, f"Splitting into -> Left: {leftHalf}, Right: {rightHalf}")

#     sortedLeft = mergeSort(leftHalf, level + 1)
#     sortedRight = mergeSort(rightHalf, level + 1)

#     merged = merge(sortedLeft, sortedRight, level + 1)
#     print("  " * level, f"Merged {sortedLeft} and {sortedRight} -> {merged}")
#     return merged


# def merge(left, right, level=0):
#     print("  " * level, f"merge() called with Left: {left}, Right: {right}")
#     result = []
#     i = j = 0

#     while i < len(left) and j < len(right):
#         if left[i] < right[j]:
#             result.append(left[i])
#             i += 1
#         else:
#             result.append(right[j])
#             j += 1

#     result.extend(left[i:])
#     result.extend(right[j:])

#     print("  " * level, f"merge() result: {result}")
#     return result


# unsortedArr = [3, 7, 6, -10, 15, 23.5, 55, -13]
# sortedArr = mergeSort(unsortedArr)
# print("\n Final Sorted array:", sortedArr)


# mergeSort called on: [3, 7, 6, -10, 15, 23.5, 55, -13]

# Splitting into -> Left: [3, 7, 6, -10], Right: [15, 23.5, 55, -13]

# mergeSort called on: [3, 7, 6, -10]

# Splitting into -> Left: [3, 7], Right: [6, -10]

# mergeSort called on: [3, 7]

# Splitting into -> Left: [3], Right: [7]

# mergeSort called on: [3]

# Returning (base case): [3]

# mergeSort called on: [7]

# Returning (base case): [7]

# merge() called with Left: [3], Right: [7]

# merge() result: [3, 7]

# Merged [3] and [7] -> [3, 7]

# mergeSort called on: [6, -10]

# Splitting into -> Left: [6], Right: [-10]

# mergeSort called on: [6]

# Returning (base case): [6]

# mergeSort called on: [-10]

# Returning (base case): [-10]

# merge() called with Left: [6], Right: [-10]

# merge() result: [-10, 6]

# Merged [6] and [-10] -> [-10, 6]

# merge() called with Left: [3, 7], Right: [-10, 6]

# merge() result: [-10, 3, 6, 7]

# Merged [3, 7] and [-10, 6] -> [-10, 3, 6, 7]

# mergeSort called on: [15, 23.5, 55, -13]

# Splitting into -> Left: [15, 23.5], Right: [55, -13]

# mergeSort called on: [15, 23.5]

# Splitting into -> Left: [15], Right: [23.5]

# mergeSort called on: [15]

# Returning (base case): [15]

# mergeSort called on: [23.5]

# Returning (base case): [23.5]

# merge() called with Left: [15], Right: [23.5]

# merge() result: [15, 23.5]

# Merged [15] and [23.5] -> [15, 23.5]

# mergeSort called on: [55, -13]

# Splitting into -> Left: [55], Right: [-13]

# mergeSort called on: [55]

# Returning (base case): [55]

# mergeSort called on: [-13]

# Returning (base case): [-13]

# merge() called with Left: [55], Right: [-13]

# merge() result: [-13, 55]

# Merged [55] and [-13] -> [-13, 55]

# merge() called with Left: [15, 23.5], Right: [-13, 55]

# merge() result: [-13, 15, 23.5, 55]

# Merged [15, 23.5] and [-13, 55] -> [-13, 15, 23.5, 55]

# merge() called with Left: [-10, 3, 6, 7], Right: [-13, 15, 23.5, 55]

# merge() result: [-13, -10, 3, 6, 7, 15, 23.5, 55]

# Merged [-10, 3, 6, 7] and [-13, 15, 23.5, 55] -> [-13, -10, 3, 6, 7, 15, 23.5, 55]

# ✅ Final Sorted array: [-13, -10, 3, 6, 7, 15, 23.5, 55]




# mergeSort([3, 7, 6, -10, 15, 23.5, 55, -13])
# │
# ├── mergeSort([3, 7, 6, -10])
# │   │
# │   ├── mergeSort([3, 7])
# │   │   ├── mergeSort([3]) → [3]
# │   │   ├── mergeSort([7]) → [7]
# │   │   └── merge([3], [7]) → [3, 7]
# │   │
# │   ├── mergeSort([6, -10])
# │   │   ├── mergeSort([6]) → [6]
# │   │   ├── mergeSort([-10]) → [-10]
# │   │   └── merge([6], [-10]) → [-10, 6]
# │   │
# │   └── merge([3, 7], [-10, 6]) → [-10, 3, 6, 7]
# │
# └── mergeSort([15, 23.5, 55, -13])
#     │
#     ├── mergeSort([15, 23.5])
#     │   ├── mergeSort([15]) → [15]
#     │   ├── mergeSort([23.5]) → [23.5]
#     │   └── merge([15], [23.5]) → [15, 23.5]
#     │
#     ├── mergeSort([55, -13])
#     │   ├── mergeSort([55]) → [55]
#     │   ├── mergeSort([-13]) → [-13]
#     │   └── merge([55], [-13]) → [-13, 55]
#     │
#     └── merge([15, 23.5], [-13, 55]) → [-13, 15, 23.5, 55]

# Final Merge:
# merge([-10, 3, 6, 7], [-13, 15, 23.5, 55])
# → [-13, -10, 3, 6, 7, 15, 23.5, 55]



