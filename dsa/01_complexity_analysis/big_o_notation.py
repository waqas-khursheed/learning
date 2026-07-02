# ============================================================================
#  BIG-O NOTATION — Har DSA decision ka FOUNDATION
#  (6 years experience level — seedha concept + WHY, basics belabor nahi karenge)
# ============================================================================

# ----------------------------------------------------------------------------
# 1) Big-O kya hai aur kyun zaruri hai?
# ----------------------------------------------------------------------------
# Big-O batata hai ke INPUT SIZE (n) badhne par, algorithm ka TIME (ya SPACE)
# kitni RATE se badhta hai. Ye EXACT seconds nahi batata — GROWTH RATE batata hai.
#
# Kyun zaruri hai: "Ye code chal raha hai" kaafi nahi — "ye code 10 million
# records pe bhi chalega?" yehi asal sawaal hai. Interview me jab tumse
# poocha jata hai "is solution ki complexity kya hai", wo isliye poochte hain
# kyunki PRODUCTION me data SCALE hota hai.

# ----------------------------------------------------------------------------
# 2) Common Complexities (BEST se WORST, examples ke sath)
# ----------------------------------------------------------------------------

# O(1) — Constant Time: Input size se FARQ NAHI PADTA
def get_first_element(arr):
    return arr[0]                      # array kitna bhi bada ho, ye hamesha 1 step

# O(log n) — Logarithmic: Har step me problem AADHA ho jata hai
def binary_search(arr, target):
    low, high = 0, len(arr) - 1
    while low <= high:
        mid = (low + high) // 2
        if arr[mid] == target:
            return mid
        elif arr[mid] < target:
            low = mid + 1
        else:
            high = mid - 1
    return -1
# 1 million elements me bhi sirf ~20 comparisons lagte hain (log2(1,000,000) ≈ 20)

# O(n) — Linear: Input ke SAATH proportionally badhta hai
def find_max(arr):
    max_val = arr[0]
    for num in arr:                    # har element EK BAAR check hota hai
        if num > max_val:
            max_val = num
    return max_val

# O(n log n) — Linearithmic: Efficient sorting algorithms (merge sort, quick sort avg)
def merge_sort_complexity_demo(arr):
    # n elements ko log(n) levels me divide karte hain, har level pe n work hota hai
    pass

# O(n²) — Quadratic: NESTED loops (har element ko har doosre se compare karna)
def has_duplicate_naive(arr):
    n = len(arr)
    for i in range(n):
        for j in range(i + 1, n):      # nested loop -> n * n operations
            if arr[i] == arr[j]:
                return True
    return False
# ⚠️ 1000 elements = 1,000,000 operations. 10,000 elements = 100,000,000 — SLOW!

# O(2^n) — Exponential: Har step pe choices DOUBLE hoti hain (naive recursion)
def fibonacci_naive(n):
    if n <= 1:
        return n
    return fibonacci_naive(n - 1) + fibonacci_naive(n - 2)
# fibonacci_naive(40) practically HANG ho jata hai — yehi DP ki zarurat dikhata hai

# O(n!) — Factorial: WORST — saari possible PERMUTATIONS try karna
# (e.g. naive Traveling Salesman Problem brute force)

# ----------------------------------------------------------------------------
# 3) Growth Rate Comparison (n = 1000 ke liye approx operations)
# ----------------------------------------------------------------------------
# | Complexity   | n=1000 ke liye operations | Real example                    |
# |----------------|------------------------------|-------------------------------------|
# | O(1)            | 1                            | Hash map lookup                     |
# | O(log n)        | ~10                          | Binary search, BST operations       |
# | O(n)             | 1,000                        | Linear search, single loop          |
# | O(n log n)       | ~10,000                      | Merge sort, Quick sort, Heap sort   |
# | O(n²)            | 1,000,000                    | Nested loops, Bubble/Selection sort |
# | O(2^n)           | astronomically large         | Naive recursive Fibonacci, subsets  |
#
# RULE OF THUMB (interview me bohat useful):
# - n <= 10        -> O(n!) ya O(2^n) bhi chal sakta hai
# - n <= 1,000      -> O(n²) generally fine
# - n <= 100,000    -> O(n log n) chahiye
# - n <= 1,000,000+ -> O(n) ya O(log n) chahiye

# ----------------------------------------------------------------------------
# 4) Big-O RULES (kaise calculate karte hain)
# ----------------------------------------------------------------------------
# Rule 1: DROP CONSTANTS — O(2n) = O(n), O(500) = O(1)
def two_separate_loops(arr):
    for x in arr:       # O(n)
        print(x)
    for x in arr:       # O(n)
        print(x)
    # Total: O(2n) -> simplify to O(n)

# Rule 2: DROP NON-DOMINANT TERMS — O(n² + n) = O(n²)
def mixed_complexity(arr):
    for x in arr:               # O(n)
        print(x)
    for x in arr:                # O(n²) — nested
        for y in arr:
            print(x, y)
    # Total: O(n + n²) -> O(n²) hi dominate karega bade n ke liye

# Rule 3: DIFFERENT INPUTS = DIFFERENT VARIABLES (n aur m alag rakho, combine mat karo)
def two_arrays(arr1, arr2):
    for x in arr1:          # O(n)
        print(x)
    for y in arr2:           # O(m)
        print(y)
    # Total: O(n + m), NOT O(n) — GALTI common hai ye

# ----------------------------------------------------------------------------
# 5) SPACE Complexity (sirf time nahi, MEMORY bhi matter karti hai)
# ----------------------------------------------------------------------------
def in_place_reverse(arr):                 # O(1) space — extra array nahi banaya
    left, right = 0, len(arr) - 1
    while left < right:
        arr[left], arr[right] = arr[right], arr[left]
        left += 1
        right -= 1
    return arr

def new_array_reverse(arr):                # O(n) space — naya array banaya
    return arr[::-1]

# Recursion space: HAR recursive call CALL STACK me jagah leta hai
def factorial_recursive(n):                # O(n) space (call stack depth = n)
    if n <= 1:
        return 1
    return n * factorial_recursive(n - 1)

def factorial_iterative(n):                # O(1) space — koi extra stack nahi
    result = 1
    for i in range(2, n + 1):
        result *= i
    return result

# ----------------------------------------------------------------------------
# 6) Best Case vs Average Case vs Worst Case
# ----------------------------------------------------------------------------
# Interview me HAMESHA "Worst Case" pucha jata hai jab tak specify na karein.
# Example — Linear Search:
#   Best Case:    O(1)   — target pehla element hi nikal aaya
#   Average Case: O(n/2) -> simplify O(n)
#   Worst Case:   O(n)   — target last element hai, ya exist hi nahi karta

# ----------------------------------------------------------------------------
# 7) Amortized Time Complexity (TRICKY interview concept)
# ----------------------------------------------------------------------------
# Python list.append() average O(1) hai, lekin KABHI KABHI O(n) lagta hai
# (jab internal array FULL ho jata hai aur RESIZE karna padta hai — naya
# bada array banta hai, saare elements copy hote hain).
# Lekin ye resize RARELY hota hai (doubling strategy se), isliye AMORTIZED
# (averaged over many operations) complexity O(1) hi rehti hai.
arr = []
for i in range(1000):
    arr.append(i)     # 999 baar O(1), kabhi kabhi O(n) resize — overall amortized O(1)

# ============================================================================
# INTERVIEW Q&A
# ============================================================================
# Q: O(n) aur O(2n) me practically farq hai kya?
# A: Theoretically Big-O constants ko ignore karta hai (dono O(n) hi kehlate
#    hain), lekin REAL production me constant factor bhi matter karta hai
#    (2x slower ho sakta hai). Big-O sirf SCALABILITY ka indicator hai,
#    ABSOLUTE performance ka nahi — bohat bada n ho to hi Big-O ka FARQ
#    dominant hota hai.
#
# Q: O(n log n) se behtar (O(n)) sorting kyun nahi bana sakte (general case me)?
# A: Comparison-based sorting algorithms (jo sirf < > == compare karte hain)
#    PROVABLY O(n log n) se behtar nahi ho sakte (information theory limit
#    — n elements arrange karne ke n! possible ways hain, decision tree ki
#    height kam se kam log(n!) ≈ n log n hoti hai). Counting Sort/Radix Sort
#    O(n) achieve karte hain lekin wo COMPARISON-based nahi hain (specific
#    constraints — limited range integers — pe kaam karte hain).
#
# Q: Time aur Space Complexity me trade-off kab karte ho?
# A: "Space-Time Tradeoff" — e.g. HASHING (07_hashing/) extra MEMORY use
#    karke lookup ko O(n) se O(1) bana deta hai. Memoization (DP) bhi yehi
#    karta hai — pichle results STORE karke (space) future computation
#    (time) bachate hain. Production me decide karna padta hai: memory
#    cheap hai ya time-critical hai (real-time systems me time priority).
