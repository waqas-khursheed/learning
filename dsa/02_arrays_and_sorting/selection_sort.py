# Selection Sort
# The Selection Sort algorithm finds the lowest value in an array and moves it to the front of the array.
# The algorithm looks through the array again and again, moving the next lowest values to the front, until the array is sorted.

# How it works:

# Go through the array to find the lowest value.
# Move the lowest value to the front of the unsorted part of the array.
# Go through the array again as many times as there are values in the array.

# 🔹 Selection Sort kya hai?

# Selection Sort aik algorithm hai jo array ke andar sabse chhoti value ko find karke array ke front (start) mai le aata hai.
# Phir agle steps mai next chhoti values ko start mai move karta hai, jab tak pura array sort na ho jaye.

# 🔹 Ye kaise kaam karta hai?

# Array ke andar se sabse chhoti value find karo.

# Us chhoti value ko unsorted part ke front (start position) pe le aao (swap karke).

# Phir dobara array mai check karo aur next chhoti value find karke front pe le aao.

# Ye process utni dafa repeat karo jitni values array mai hain.

# 👉 Is tarah step by step saari chhoti values start ki taraf aati hain, aur array sort ho jata hai.


my_array = [64, 34, 25, 5, 22, 11, 90, 12]

n = len(my_array)  # array ka length nikal liya
for i in range(n-1):  # outer loop — har position ke liye ek chhota sa number dhundhega
    min_index = i  # abhi ke liye assume kar liya ke current i hi smallest hai
    for j in range(i+1, n):  # inner loop — baaki elements check karega
        if my_array[j] < my_array[min_index]:  # agar koi chhota number mil jaye
            min_index = j  # to uska index save kar lo
    
    # yahan mil gaya smallest number ka index
    min_value = my_array.pop(min_index)  # usko list se nikal lo
    my_array.insert(i, min_value)  # aur current position pe daal do

print("Sorted array:", my_array)



# Selection Sort Time Complexity

# 🔹 Algorithm Reminder

# Selection Sort mai:

# Har step pe sabse chhota element dhoondha jata hai (minimum search).

# Usko current position pe swap kar dete hain.

# Ye process poore array ke liye repeat hota hai.

# 1. Best Case (Already Sorted Array)

# Chahe array pehle se sorted ho, har step pe minimum dhoondhna padta hai.

# Isliye O(n²) hi rahega.

# 👉 Best Case Complexity: O(n²)

# 2. Worst Case (Reverse Sorted Array)

# Agar array bilkul ulta sorted hai, tab bhi har step pe pura array scan karna hoga minimum element ke liye.

# Again O(n²) comparisons honge.

# 👉 Worst Case Complexity: O(n²)

# 3. Average Case (Random Array)

# Random array ke liye bhi hamesha har iteration me minimum dhoondhna padta hai.

# Matlab performance same rahega.

# 👉 Average Case Complexity: O(n²)

# 4. Space Complexity

# Selection Sort inplace hota hai (sirf ek temporary variable lagta hai swap ke liye).

# Space Complexity = O(1)

# 📊 Summary
# Case	Time Complexity
# Best Case	O(n²)
# Average	O(n²)
# Worst Case	O(n²)
# Space	O(1)

# ✅ Fayda (Advantages)

# Simple implementation.

# Swapping kam hoti hai (Bubble sort ke comparison mai).

# In-place sorting (extra memory nahi lagti).

# ❌ Nuksan (Disadvantages)

# Hamesha O(n²) time lagta hai (even if sorted).

# Bade arrays ke liye slow hai.