# DSA Counting Sort
# The Counting Sort algorithm sorts an array by counting the number of times each value occurs.


# How it works:

# Create a new array for counting how many there are of the different values.
# Go through the array that needs to be sorted.
# For each value, count it by increasing the counting array at the corresponding index.
# After counting the values, go through the counting array to create the sorted array.
# For each count in the counting array, create the correct number of elements, with values that correspond to the counting array


# Conditions for Counting Sort
# These are the reasons why Counting Sort is said to only work for a limited range of non-negative integer values:

# Integer values: Counting Sort relies on counting occurrences of distinct values, so they must be integers. With integers, each value fits with an index (for non negative values), and there is a limited number of different values, so that the number of possible different values 
# k
#  is not too big compared to the number of values 
# n
# .
# Non negative values: Counting Sort is usually implemented by creating an array for counting. When the algorithm goes through the values to be sorted, value x is counted by increasing the counting array value at index x. If we tried sorting negative values, we would get in trouble with sorting value -3, because index -3 would be outside the counting array.
# Limited range of values: If the number of possible different values to be sorted 
# k
#  is larger than the number of values to be sorted 
# n
# , the counting array we need for sorting will be larger than the original array we have that needs sorting, and the algorithm becomes ineffective.


# Manual Run Through: What Happened?
# Before we implement the algorithm in a programming language we need to go through what happened above in more detail.

# We have seen that the Counting Sort algorithm works in two steps:

# Each value gets counted by incrementing at the correct index in the counting array. After a value is counted, it is removed.
# The values are recreated in the right order by using the count, and the index of the count, from the counting array.
# With this in mind, we can start implementing the algorithm using Python.

# Counting Sort Implementation
# To implement the Counting Sort algorithm in a programming language, we need:

# An array with values to sort.
# A 'countingSort' method that receives an array of integers.
# An array inside the method to keep count of the values.
# A loop inside the method that counts and removes values, by incrementing elements in the counting array.
# A loop inside the method that recreates the array by using the counting array, so that the elements appear in the right order.
# One more thing: We need to find out what the highest value in the array is, so that the counting array can be created with the correct size. For example, if the highest value is 5, the counting array must be 6 elements in total, to be able count all possible non negative integers 0, 1, 2, 3, 4 and 5.

# The resulting code looks like this:


# Urdu

# DSA Counting Sort

# Counting Sort algorithm array ko sort karta hai by counting ke har value kitni dafa aayi hai.

# How it works:

# Ek naya array banao counting ke liye jahan par ye save ho ke har value kitni dafa aayi hai.

# Array ke andar se guzro jo sort karna hai.

# Har value ke liye uske index par counting array me +1 kar do.

# Jab sab values count ho jayein, counting array ke through guzro aur sorted array create karo.

# Har count ke liye utne elements sorted array me dal do jo us count ke barabar ho, aur us index ki value dalni hai.

# Conditions for Counting Sort

# Ye wo reasons hain jinki wajah se Counting Sort sirf limited range ke non-negative integer values ke liye kaam karta hai:

# Integer values: Counting Sort values ko count karta hai aur index par rakhta hai, is liye values integer honi chahiyein. Integer ke sath har value ek index par map ho jati hai (agar negative na ho), aur alag values ki ek limited ginti hoti hai. Agar possible alag values ki ginti k bohot zyada ho compared to elements n, to algorithm slow ho jata hai.

# Non negative values: Normally Counting Sort non-negative integers ke liye hota hai. Kyunke agar negative numbers ho jayein to unka index (jaise -3) counting array me possible nahi hota (index out of range).

# Limited range of values: Agar possible alag values k bohot zyada badi ho compared to elements n, to hume ek bohot bara counting array banana padega jo original array se bhi bara hoga. Ye inefficient ho jata hai.

# Manual Run Through: What Happened?

# Algorithm do steps me kaam karta hai:

# Har value ko count karte hain aur uska count sahi index par store kar dete hain. Count hone ke baad original se value hata di jati hai.

# Phir values ko wapas recreate karte hain sahi order me by using count aur index from counting array.

# Counting Sort Implementation

# Algorithm implement karne ke liye hume chahiye:

# Ek array jisme values hongi jo sort karni hain.

# Ek method countingSort jo integer array receive kare.

# Ek array method ke andar jo counting rakhega values ka.

# Ek loop jo original array se guzre aur counting array me increment kare har value ke index par.

# Ek loop jo counting array se guzre aur sorted array recreate kare sahi order me.

# Aur ek cheez: hume pehle highest value find karni hoti hai array me, taake counting array ka size sahi ban sake. Example: agar highest value 5 hai, to counting array me 6 elements honge (0 se 5 tak count karne ke liye).



def countingSort(arr):
    max_val = max(arr)
    count = [0] * (max_val + 1)

    while len(arr) > 0:
        num = arr.pop(0)
        count[num] += 1

    for i in range(len(count)):
        while count[i] > 0:
            arr.append(i)
            count[i] -= 1

    return arr

unsortedArr = [4, 2, 2, 6, 3, 3, 1, 6, 5, 2, 3]
sortedArr = countingSort(unsortedArr)
print("Sorted array:", sortedArr)



# 🔎 Step by Step Explanation
# (1) max_val = max(arr)

# pehle array me jo sabse badi value hai usay nikal lo.

# example: [4,2,2,6,3,3,1,6,5,2,3]

# sabse badi value = 6.

# (2) count = [0] * (max_val + 1)

# ek naya array banaya jisme sab 0 hain aur uska size (max_val+1) hoga.

# kyun? kyunki hume 0 se le kar max value tak sab numbers ka count rakhna hai.

# example me max_val = 6 → count array bana:

# count = [0,0,0,0,0,0,0]   # indexes 0 → 6

# (3) while len(arr) > 0:

# jab tak original arr me elements hain, ek ek karke nikalte jao aur counting karo.

# (3a) num = arr.pop(0)

# arr.pop(0) array ka first element nikalta hai aur remove bhi kar deta hai.

# example:

# pehle step me num = 4 nikla, ab arr bacha [2,2,6,3,3,1,6,5,2,3].

# (3b) count[num] += 1

# jo number nikla uska counter +1 kar do.

# example:

# pehle num=4 nikla → count[4] = 1

# agla num=2 nikla → count[2] = 1

# phir ek aur 2 nikla → count[2] = 2

# … ye process chalti rahegi jab tak arr khali na ho jaye.

# 👉 last me count array banega:

# count = [0, 1, 3, 3, 1, 1, 2]
# index:   0  1  2  3  4  5  6


# iska matlab:

# 1 ek dafa aya

# 2 teen dafa aya

# 3 teen dafa aya

# 4 ek dafa aya

# 5 ek dafa aya

# 6 do dafa aya

# (4) for i in range(len(count))

# ab counting array se guzro aur wapas sorted array banao.

# (4a) while count[i] > 0:

# agar count[i] zyada than 0 hai to iska matlab ye number aaya tha.

# (4b) arr.append(i)

# us number ko sorted array me daal do.

# (4c) count[i] -= 1

# ek dafa dalne ke baad uska counter 1 ghatado.

# jab tak count 0 na ho jaye, repeat karo.

# Example of this phase:

# i=0 → count[0]=0 → kuch nahi

# i=1 → count[1]=1 → ek 1 arr me add hua

# i=2 → count[2]=3 → teen 2 arr me add hue

# i=3 → count[3]=3 → teen 3 arr me add hue

# i=4 → count[4]=1 → ek 4 add hua

# i=5 → count[5]=1 → ek 5 add hua

# i=6 → count[6]=2 → do 6 add hue

# (5) return arr

# ab arr sorted ban gaya hai.

# 👉 Final Result:

# Sorted array: [1,2,2,2,3,3,3,4,5,6,6]





# Counting Sort Time Complexity
# For a general explanation of what time complexity is, visit this page.

# For a more thorough and detailed explanation of Counting Sort time complexity, visit this page.

# How fast the Counting Sort algorithm runs depends on both the range of possible values 
# k
#  and the number of values 
# n
# .

# In general, time complexity for Counting Sort is 
# O
# (
# n
# +
# k
# )
# .

# In a best case scenario, the range of possible different values 
# k
#  is very small compared to the number of values 
# n
#  and Counting Sort has time complexity 
# O
# (
# n
# )
# .

# But in a worst case scenario, the range of possible different values 
# k
#  is very big compared to the number of values 
# n
#  and Counting Sort can have time complexity 
# O
# (
# n
# 2
# )
#  or even worse.

# The plot below shows how much the time complexity for Counting Sort can vary.


# Counting Sort Time Complexity (roman urdu mai)

# Counting Sort ki speed (kitni fast run karta hai) depend karti hai do cheezon par:

# values ki range kitni badi hai (k)

# total values kitni hain (n)

# general case mai Counting Sort ka time complexity hota hai:

# O(n + k)


# 👉 Best Case Scenario
# agar possible values ki range (k) bohot chhoti ho compared to number of values (n),
# toh Counting Sort bohot fast hota hai aur uska time complexity hota hai:

# O(n)


# 👉 Worst Case Scenario
# agar possible values ki range (k) bohot badi ho compared to number of values (n),
# toh Counting Sort slow ho jata hai aur uska time complexity ho sakta hai:

# O(n^2) ya us se bhi bura


# 📊 matlab yeh algorithm ki speed kaafi vary karti hai is baat par ke n aur k ka ratio kya hai.