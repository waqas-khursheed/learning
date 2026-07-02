# ============================================================================
#  SEARCHING ALGORITHMS — Linear, Binary Search & Variants
# ============================================================================

# ----------------------------------------------------------------------------
# 1) Linear Search — Simplest, koi precondition nahi chahiye
# ----------------------------------------------------------------------------
# Definition: Har element ko EK EK karke check karo jab tak target na mil jaye.
# Time Complexity: O(n) | Space: O(1)
# Kab use karo: UNSORTED data, ya chhoti list (overhead of sorting nahi chahiye)

def linear_search(arr, target):
    for i, val in enumerate(arr):
        if val == target:
            return i
    return -1

print(linear_search([5, 2, 9, 1, 7], 9))   # 2

# ----------------------------------------------------------------------------
# 2) Binary Search — SORTED data pe, divide-and-conquer
# ----------------------------------------------------------------------------
# Definition: Sorted array ko baar baar AADHA karte jao — middle element
# check karo, target chhota hai to LEFT half me jao, bada hai to RIGHT me.
# Time Complexity: O(log n) | Space: O(1) iterative, O(log n) recursive
# Kab use karo: SORTED data, frequent lookups (sorting ka one-time cost
# amortize ho jata hai agar baar baar search karni hai)
#
# REAL-WORLD: Database INDEXES (B+Tree, [[../DB/07_indexes]]), dictionary
# lookup, "guess the number" game, git bisect (commit history me bug dhoondna)

def binary_search(arr, target):
    low, high = 0, len(arr) - 1
    while low <= high:
        mid = (low + high) // 2        # ⚠️ bade languages me overflow se bachne ke liye: low + (high-low)//2
        if arr[mid] == target:
            return mid
        elif arr[mid] < target:
            low = mid + 1               # right half me jao
        else:
            high = mid - 1              # left half me jao
    return -1

sorted_arr = [1, 3, 5, 7, 9, 11, 13, 15]
print(binary_search(sorted_arr, 11))   # 5

# Recursive version:
def binary_search_recursive(arr, target, low, high):
    if low > high:
        return -1
    mid = (low + high) // 2
    if arr[mid] == target:
        return mid
    elif arr[mid] < target:
        return binary_search_recursive(arr, target, mid + 1, high)
    else:
        return binary_search_recursive(arr, target, low, mid - 1)

# ----------------------------------------------------------------------------
# 3) Binary Search VARIANTS — Interview me iski variations zyada pucha jata hai
# ----------------------------------------------------------------------------

# a) Find FIRST occurrence (duplicates wale sorted array me)
def find_first_occurrence(arr, target):
    low, high, result = 0, len(arr) - 1, -1
    while low <= high:
        mid = (low + high) // 2
        if arr[mid] == target:
            result = mid
            high = mid - 1        # LEFT me continue karo (pehla occurrence dhoondne ke liye)
        elif arr[mid] < target:
            low = mid + 1
        else:
            high = mid - 1
    return result

print(find_first_occurrence([1, 2, 2, 2, 3, 4], 2))   # index 1 (pehla 2)

# b) Find LAST occurrence
def find_last_occurrence(arr, target):
    low, high, result = 0, len(arr) - 1, -1
    while low <= high:
        mid = (low + high) // 2
        if arr[mid] == target:
            result = mid
            low = mid + 1          # RIGHT me continue karo
        elif arr[mid] < target:
            low = mid + 1
        else:
            high = mid - 1
    return result

print(find_last_occurrence([1, 2, 2, 2, 3, 4], 2))    # index 3 (aakhri 2)

# c) Search in ROTATED Sorted Array (CLASSIC HARD interview question)
def search_rotated(arr, target):
    low, high = 0, len(arr) - 1
    while low <= high:
        mid = (low + high) // 2
        if arr[mid] == target:
            return mid
        # decide karo: LEFT half sorted hai ya RIGHT half
        if arr[low] <= arr[mid]:                 # left half sorted hai
            if arr[low] <= target < arr[mid]:
                high = mid - 1
            else:
                low = mid + 1
        else:                                      # right half sorted hai
            if arr[mid] < target <= arr[high]:
                low = mid + 1
            else:
                high = mid - 1
    return -1

print(search_rotated([4, 5, 6, 7, 0, 1, 2], 0))   # index 4

# d) Find MINIMUM in rotated sorted array
def find_min_rotated(arr):
    low, high = 0, len(arr) - 1
    while low < high:
        mid = (low + high) // 2
        if arr[mid] > arr[high]:    # minimum RIGHT side me hai
            low = mid + 1
        else:                        # minimum mid samet LEFT side me hai
            high = mid
    return arr[low]

print(find_min_rotated([4, 5, 6, 7, 0, 1, 2]))   # 0

# e) Find "Peak Element" (jo apne dono neighbours se bada ho)
def find_peak(arr):
    low, high = 0, len(arr) - 1
    while low < high:
        mid = (low + high) // 2
        if arr[mid] > arr[mid + 1]:
            high = mid             # peak LEFT side (mid samet) me hai
        else:
            low = mid + 1           # peak RIGHT side me hai
    return low

print(find_peak([1, 2, 3, 1]))     # index 2 (value 3)

# f) Binary Search on ANSWER (advanced pattern — value range pe search karna,
#    array index pe nahi). Use case: "minimum capacity dhoondo jisse condition X satisfy ho"
def min_eating_speed(piles, h):
    # Koko Eats Bananas (LeetCode classic): minimum speed dhoondo jisse
    # saare piles 'h' hours me khatam ho sakein
    def hours_needed(speed):
        return sum((pile + speed - 1) // speed for pile in piles)

    low, high = 1, max(piles)
    while low < high:
        mid = (low + high) // 2
        if hours_needed(mid) <= h:
            high = mid              # ye speed kaam karti hai, aur kam try karo
        else:
            low = mid + 1            # speed badhani padegi
    return low

print(min_eating_speed([3, 6, 7, 11], 8))   # 4

# ----------------------------------------------------------------------------
# 4) Python built-in: bisect module (production me MANUAL binary search
#    likhne ki bajaye ye use karo)
# ----------------------------------------------------------------------------
import bisect
sorted_list = [1, 3, 5, 7, 9]
print(bisect.bisect_left(sorted_list, 5))    # 2 (insertion point, leftmost)
print(bisect.bisect_right(sorted_list, 5))   # 3 (insertion point, rightmost)
bisect.insort(sorted_list, 6)                 # sorted order maintain karte hue insert
print(sorted_list)                              # [1, 3, 5, 6, 7, 9]

# ============================================================================
# INTERVIEW Q&A
# ============================================================================
# Q: Binary Search ke liye PRECONDITION kya hai?
# A: Array SORTED hona chahiye. Agar unsorted hai aur baar baar search karni
#    hai, to pehle SORT karo (O(n log n) one-time) phir Binary Search (O(log n)
#    per search) — agar ek hi baar search karni hai to Linear Search (O(n))
#    behtar hai (sorting ka overhead bachega).
#
# Q: "mid = (low + high) // 2" me koi issue ho sakta hai?
# A: Languages jahan INTEGER OVERFLOW hota hai (Java, C++) wahan low+high
#    bohat bada number ban kar overflow kar sakta hai. Safe formula:
#    mid = low + (high - low) // 2. Python me integers arbitrary precision
#    hain isliye ye issue nahi hota, lekin concept interview me pucha jata hai.
#
# Q: Real life me Binary Search kahan use hota hai (sirf array search nahi)?
# A: 1) Database B+Tree indexes (sorted data me fast lookup)
#    2) Git bisect (binary search se buggy commit dhoondna)
#    3) "Binary Search on Answer" pattern — jab problem me MONOTONIC
#       condition ho (jaise upar min_eating_speed example), value RANGE
#       pe binary search karte hain, array index pe nahi.
