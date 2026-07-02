# Radix Sort
# The Radix Sort algorithm sorts an array by individual digits, starting with the least significant digit (the rightmost one).

# he radix (or base) is the number of unique digits in a number system. In the decimal system we normally use, there are 10 different digits from 0 till 9.

# Radix Sort uses the radix so that decimal values are put into 10 different buckets (or containers) corresponding to the digit that is in focus, then put back into the array before moving on to the next digit.

# Radix Sort is a non comparative algorithm that only works with non negative integers.

# The Radix Sort algorithm can be described like this:

# How it works:

# Start with the least significant digit (rightmost digit).
# Sort the values based on the digit in focus by first putting the values in the correct bucket based on the digit in focus, and then put them back into array in the correct order.
# Move to the next digit, and sort again, like in the step above, until there are no digits left.

# Stable Sorting
# Radix Sort must sort the elements in a stable way for the result to be sorted correctly.

# A stable sorting algorithm is an algorithm that keeps the order of elements with the same value before and after the sorting. Let's say we have two elements "K" and "L", where "K" comes before "L", and they both have value "3". A sorting algorithm is considered stable if element "K" still comes before "L" after the array is sorted.

# It makes little sense to talk about stable sorting algorithms for the previous algorithms we have looked at individually, because the result would be same if they are stable or not. But it is important for Radix Sort that the the sorting is done in a stable way because the elements are sorted by just one digit at a time.

# So after sorting the elements on the least significant digit and moving to the next digit, it is important to not destroy the sorting work that has already been done on the previous digit position, and that is why we need to take care that Radix Sort does the sorting on each digit position in a stable way.

# In the simulation below it is revealed how the underlying sorting into buckets is done. And to get a better understanding of how stable sorting works, you can also choose to sort in an unstable way, that will lead to an incorrect result. The sorting is made unstable by simply putting elements into buckets from the end of the array instead of from the start of the array.

# Manual Run Through: What Happened?
# We see that values are moved from the array and placed in the radix array according to the current radix in focus. And then the values are moved back into the array we want to sort.

# This moving of values from the array we want to sort and back again must be done as many times as the maximum number of digits in a value. So for example if 437 is the highest number in the array that needs to be sorted, we know we must sort three times, once for each digit.

# We also see that the radix array needs to be two-dimensional so that more than one value on a specific radix, or index.

# And, as mentioned earlier, we must move values between the two arrays in a way that keeps the order of values with the same radix in focus, so the the sorting is stable.

# Radix Sort Implementation
# To implement the Radix Sort algorithm we need:

# An array with non negative integers that needs to be sorted.
# A two dimensional array with index 0 to 9 to hold values with the current radix in focus.
# A loop that takes values from the unsorted array and places them in the correct position in the two dimensional radix array.
# A loop that puts values back into the initial array from the radix array.
# An outer loop that runs as many times as there are digits in the highest value.
# The resulting code looks like this:



# 🔹 Radix Sort Kya Hai?

# Radix Sort ek sorting algorithm hai jo numbers ko digit by digit sort karta hai.
# Ye sab se pehle least significant digit (rightmost digit) se start karta hai aur phir aglay digits pe move hota hai.

# 🔹 Radix (Base) Kya Hai?

# Radix ka matlab hota hai number system ka base.

# Decimal system me humare paas 10 digits (0–9) hote hain.

# Is liye Radix Sort me hum 10 buckets banate hain (0 se 9 tak).

# 🔹 Kaise Kaam Karta Hai?

# Pehle rightmost digit (least significant digit) ke hisaab se numbers ko buckets me daal dete hain.

# Phir buckets se wapas array me order maintain karte hue numbers nikal lete hain.

# Ab aglay digit (dusra, teesra, etc.) pe move karte hain.

# Ye process utne hi dafa repeat hota hai jitne digits maximum number me hain.

# 🔹 Stable Sorting Kyu Zaroori Hai?

# Stable sorting ka matlab hai: agar do numbers ek jaise hain, to jo pehle tha wo sorted array me bhi pehle hi aaye.

# Radix Sort me ye bohot important hai, kyunki hum digit by digit sort kar rahe hain.

# Agar stable na ho to pehle wale digit ki sorting kharaab ho jaati hai.

# Example:
# Agar humare paas do elements K=23 aur L=23 hain aur K pehle aata hai, to sorting ke baad bhi K pehle hi rehna chahiye.

# 🔹 Manual Run Example

# Maan lo humare array me [437, 146, 329] hain:

# Rightmost digit ke hisaab se sort → [329, 437, 146]

# Middle digit ke hisaab se sort → [329, 437, 146] (order maintain hota hai)

# Leftmost digit ke hisaab se sort → [146, 329, 437]

# 🔹 Implementation Ke Liye Kya Chahiye?

# Ek array jisme non-negative integers hain.

# Ek 2D array (0–9) buckets banane ke liye.

# Loop jo numbers ko correct bucket me daale current digit ke hisaab se.

# Loop jo buckets se numbers wapas array me dale stable order me.

# Outer loop jo itni dafa chale jitne digits maximum number me hain.



myArray = [170, 45, 75, 90, 802, 24, 2, 66]
print("Original array:", myArray)
radixArray = [[], [], [], [], [], [], [], [], [], []]
maxVal = max(myArray)
exp = 1

while maxVal // exp > 0:

    while len(myArray) > 0:
        val = myArray.pop()
        radixIndex = (val // exp) % 10
        radixArray[radixIndex].append(val)

    for bucket in radixArray:
        while len(bucket) > 0:
            val = bucket.pop()
            myArray.append(val)

    exp *= 10

print("Sorted array:", myArray)


# FIFO version of Radix Sort (Python)
# myArray = [170, 45, 75, 90, 802, 24, 2, 66]
# print("Original array:", myArray)

# maxVal = max(myArray)
# exp = 1

# # Jab tak sabse badi value ka digit cover na ho
# while maxVal // exp > 0:
#     # 10 buckets banao (0-9)
#     radixArray = [[], [], [], [], [], [], [], [], [], []]

#     # Step 1: Distribute elements in buckets (FIFO style)
#     for val in myArray:
#         radixIndex = (val // exp) % 10
#         radixArray[radixIndex].append(val)

#     print(f"\nDigit place {exp}: Buckets after distribution → {radixArray}")

#     # Step 2: Collect back elements in FIFO order
#     myArray = []
#     for bucket in radixArray:
#         while len(bucket) > 0:
#             val = bucket.pop(0)   # FIFO: pehla niklega pehle
#             myArray.append(val)

#     print(f"Array after collecting (exp={exp}): {myArray}")

#     # Next digit place par jao (10x)
#     exp *= 10

# print("\n✅ Sorted array:", myArray)



# $myArray = [170, 45, 75, 90, 802, 24, 2, 66];
# echo "Original array: ";
# print_r($myArray);

# $radixArray = array_fill(0, 10, []); // 10 empty buckets
# $maxVal = max($myArray);
# $exp = 1;

# while (intdiv($maxVal, $exp) > 0) {

#     // Step 1: distribute into buckets
#     while (count($myArray) > 0) {
#         $val = array_pop($myArray);
#         $radixIndex = intdiv($val, $exp) % 10;
#         $radixArray[$radixIndex][] = $val;
#     }

#     // Step 2: collect back into array
#     foreach ($radixArray as &$bucket) {
#         while (count($bucket) > 0) {
#             $val = array_pop($bucket);
#             $myArray[] = $val;
#         }
#     }

#     $exp *= 10;
# }

# echo "Sorted array: ";
# print_r($myArray);



# 🔹 Dry Run (Step by Step)
# Initial State
# myArray = [170, 45, 75, 90, 802, 24, 2, 66]
# radixArray = [[], [], [], [], [], [], [], [], [], []]
# maxVal = 802
# exp = 1

# Pass 1: exp = 1 (units digit)

# 👉 (val // 1) % 10 → gives last digit (units place)

# Pop 66 → (66 // 1) % 10 = 6 → bucket[6] = [66]

# Pop 2 → (2 // 1) % 10 = 2 → bucket[2] = [2]

# Pop 24 → (24 // 1) % 10 = 4 → bucket[4] = [24]

# Pop 802 → (802 // 1) % 10 = 2 → bucket[2] = [2, 802]

# Pop 90 → (90 // 1) % 10 = 0 → bucket[0] = [90]

# Pop 75 → (75 // 1) % 10 = 5 → bucket[5] = [75]

# Pop 45 → (45 // 1) % 10 = 5 → bucket[5] = [75, 45]

# Pop 170 → (170 // 1) % 10 = 0 → bucket[0] = [90, 170]

# Buckets after distribution (units place):

# bucket[0] = [90, 170]
# bucket[2] = [2, 802]
# bucket[4] = [24]
# bucket[5] = [75, 45]
# bucket[6] = [66]
# others = []


# Now collect back into myArray:

# From bucket[0] → take [170, 90]

# From bucket[2] → take [802, 2]

# From bucket[4] → take [24]

# From bucket[5] → take [45, 75]

# From bucket[6] → take [66]

# 👉 myArray = [170, 90, 802, 2, 24, 45, 75, 66]

# ✅ End of Pass 1 (sorted by units digit)

# Pass 2: exp = 10 (tens digit)

# 👉 (val // 10) % 10 → gives tens digit

# Pop 66 → (66 // 10) % 10 = 6 → bucket[6] = [66]

# Pop 75 → (75 // 10) % 10 = 7 → bucket[7] = [75]

# Pop 45 → (45 // 10) % 10 = 4 → bucket[4] = [45]

# Pop 24 → (24 // 10) % 10 = 2 → bucket[2] = [24]

# Pop 2 → (2 // 10) % 10 = 0 → bucket[0] = [2]

# Pop 802 → (802 // 10) % 10 = 0 → bucket[0] = [2, 802]

# Pop 90 → (90 // 10) % 10 = 9 → bucket[9] = [90]

# Pop 170 → (170 // 10) % 10 = 7 → bucket[7] = [75, 170]

# Buckets after distribution (tens place):

# bucket[0] = [2, 802]
# bucket[2] = [24]
# bucket[4] = [45]
# bucket[6] = [66]
# bucket[7] = [75, 170]
# bucket[9] = [90]
# others = []


# Now collect back into myArray:

# 👉 myArray = [802, 2, 24, 45, 66, 170, 75, 90]

# ✅ End of Pass 2 (sorted by tens digit)

# Pass 3: exp = 100 (hundreds digit)

# 👉 (val // 100) % 10 → gives hundreds digit

# Pop 90 → (90 // 100) % 10 = 0 → bucket[0] = [90]

# Pop 75 → (75 // 100) % 10 = 0 → bucket[0] = [90, 75]

# Pop 170 → (170 // 100) % 10 = 1 → bucket[1] = [170]

# Pop 66 → (66 // 100) % 10 = 0 → bucket[0] = [90, 75, 66]

# Pop 45 → (45 // 100) % 10 = 0 → bucket[0] = [90, 75, 66, 45]

# Pop 24 → (24 // 100) % 10 = 0 → bucket[0] = [90, 75, 66, 45, 24]

# Pop 2 → (2 // 100) % 10 = 0 → bucket[0] = [90, 75, 66, 45, 24, 2]

# Pop 802 → (802 // 100) % 10 = 8 → bucket[8] = [802]

# Buckets after distribution (hundreds place):

# bucket[0] = [90, 75, 66, 45, 24, 2]
# bucket[1] = [170]
# bucket[8] = [802]
# others = []


# Now collect back into myArray:

# 👉 myArray = [2, 24, 45, 66, 75, 90, 170, 802]

# ✅ End of Pass 3 (sorted by hundreds digit)

# Pass 4: exp = 1000
# maxVal // 1000 = 802 // 1000 = 0


# Loop break ho gaya.

# 🎯 Final Sorted Array
# [2, 24, 45, 66, 75, 90, 170, 802]


# ✅ Summary:

# Pass 1: sort by units → [170, 90, 802, 2, 24, 45, 75, 66]

# Pass 2: sort by tens → [802, 2, 24, 45, 66, 170, 75, 90]

# Pass 3: sort by hundreds → [2, 24, 45, 66, 75, 90, 170, 802]

# Done 🎉




# Radix Sort kahan use hota hai?
# 1. Large integers ya fixed-length numbers

# Jab tumhare paas bohot saari integers ki list ho (jaise IDs, phone numbers, roll numbers), Radix Sort bohot fast hota hai.

# Example: 10^6 integers ko sort karna ho.

# 2. Strings / words sort karne me (dictionary order)

# Radix sort ko characters ke ASCII values ya Unicode digits pe lagao to dictionary order mil jata hai.

# Example:

# ["cat", "bat", "apple", "dog"] → Radix sort karega character by character aur words sort ho jayenge.

# 3. Database indexing / searching

# Databases me jab large numeric keys (student IDs, account numbers, zip codes, phone numbers) ko sort ya index karna hota hai, to Radix sort use hota hai, kyunki yeh O(nk) time me chal jata hai (n = elements, k = digits).

# 4. Computer graphics / image processing

# Pixels ki intensity values ya colors (RGB values = 0–255 integers) ko sort karna hota hai → Radix sort kaam aata hai.

# 5. When numbers have limited digit range

# Agar tumhe pata hai ki numbers fixed range ke andar hain (e.g. 32-bit integers, 9-digit phone numbers) → Radix sort is faster than comparison sorts (like quicksort/mergesort).

# 🔹 Radix Sort kahan nahi use hota?

# Agar numbers ka size unlimited ho (bohot lambi strings, variable-length data).

# Agar comparison-based sorting (jaise sorting on complex objects with custom rules) chahiye.

# ✅ Real life Example:

# Sorting student roll numbers in a school database.

# Sorting phone numbers in telecom records.

# Zip codes sorting for postal systems.

# Log processing where timestamps (fixed-length integers) ko sort karna ho.