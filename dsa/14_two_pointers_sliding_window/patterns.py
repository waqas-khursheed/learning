# ============================================================================
#  TWO POINTERS & SLIDING WINDOW — Interview PATTERNS jo O(n²) ko O(n) banate hain
# ============================================================================

# ----------------------------------------------------------------------------
# 1) TWO POINTERS kya hai?
# ----------------------------------------------------------------------------
# 2 POINTERS (indices) array/string pe ALAG ALAG positions se chalte hain
# (kabhi START+END se, kabhi DONO SAME direction me) — taake NESTED LOOP
# (O(n²)) ki zarurat na pade.
#
# KYUN ZARURI: "Pair/Triplet dhoondo jo condition X satisfy kare" jaisi
# problems NAIVE me O(n²)/O(n³) hoti hain — Two Pointers (agar array SORTED
# ho) se O(n) ho jati hain.

# ----------------------------------------------------------------------------
# 2) PATTERN A: Opposite Ends (start aur end se shuru, beech me milte hain)
# ----------------------------------------------------------------------------

# Use case: Two Sum on SORTED array
def two_sum_sorted(arr, target):
    left, right = 0, len(arr) - 1
    while left < right:
        current = arr[left] + arr[right]
        if current == target:
            return [left, right]
        elif current < target:
            left += 1                  # sum CHHOTA hai -> left ko BADHAO (badi value chahiye)
        else:
            right -= 1                  # sum BADA hai -> right ko GHATAO (chhoti value chahiye)
    return []

print(two_sum_sorted([2, 7, 11, 15], 18))   # [1, 2] (7+11=18)
# O(n) — kyunki HASH MAP wala Two Sum unsorted ke liye behtar tha
# ([[../07_hashing]]), lekin SORTED array me Two Pointers EXTRA SPACE
# (O(1)) ke sath bhi same time complexity de deta hai

# Use case: Valid Palindrome
def is_palindrome(s):
    s = ''.join(c.lower() for c in s if c.isalnum())
    left, right = 0, len(s) - 1
    while left < right:
        if s[left] != s[right]:
            return False
        left += 1
        right -= 1
    return True

print(is_palindrome("A man, a plan, a canal: Panama"))   # True

# Use case: Container With Most Water (CLASSIC)
def max_area(heights):
    left, right = 0, len(heights) - 1
    max_water = 0
    while left < right:
        width = right - left
        height = min(heights[left], heights[right])
        max_water = max(max_water, width * height)
        # CHHOTI height wali line ko MOVE karo (badi wali rakhne se kuch fayda nahi)
        if heights[left] < heights[right]:
            left += 1
        else:
            right -= 1
    return max_water

print(max_area([1, 8, 6, 2, 5, 4, 8, 3, 7]))   # 49

# ----------------------------------------------------------------------------
# 3) PATTERN B: Fast & Slow Pointers (SAME direction, ALAG speed)
# ----------------------------------------------------------------------------
# Use case: Remove Duplicates from Sorted Array (in-place)
def remove_duplicates(arr):
    if not arr:
        return 0
    slow = 0                            # slow = "last unique element ki position"
    for fast in range(1, len(arr)):
        if arr[fast] != arr[slow]:
            slow += 1
            arr[slow] = arr[fast]
    return slow + 1                      # new length

arr = [1, 1, 2, 2, 2, 3, 4, 4]
new_len = remove_duplicates(arr)
print(arr[:new_len])   # [1, 2, 3, 4]

# Use case: Cycle Detection (detail: [[../04_linked_list]]) — yehi pattern hai

# ----------------------------------------------------------------------------
# 4) SLIDING WINDOW kya hai?
# ----------------------------------------------------------------------------
# Ek "WINDOW" (contiguous subarray/substring range) maintain karo jo
# EXPAND (right pointer aage) aur CONTRACT (left pointer aage) hoti hai —
# har baar POORI window RECALCULATE NAHI karte, sirf JO change hua wo update karo.
#
# KYUN ZARURI: "Subarray/Substring with property X" problems NAIVE me
# O(n²) ya O(n³) hoti hain (har possible subarray check karna) — Sliding
# Window se O(n) ho jati hain.

# ----------------------------------------------------------------------------
# 5) FIXED SIZE Window
# ----------------------------------------------------------------------------
def max_sum_subarray_k(arr, k):
    window_sum = sum(arr[:k])
    max_sum = window_sum
    for i in range(k, len(arr)):
        window_sum += arr[i] - arr[i - k]   # NAYA element ADD, PURANA REMOVE — O(1) per step!
        max_sum = max(max_sum, window_sum)
    return max_sum

print(max_sum_subarray_k([2, 1, 5, 1, 3, 2], 3))   # 9 ([5,1,3])
# NAIVE: har window ka sum SE-SHURU calculate karna O(n*k). SLIDING
# WINDOW: O(n) — kyunki HAR element sirf EK BAAR add aur EK BAAR remove hota hai

# ----------------------------------------------------------------------------
# 6) VARIABLE SIZE Window (window GROW/SHRINK hoti hai condition ke hisab se)
# ----------------------------------------------------------------------------

# Use case: Longest Substring WITHOUT Repeating Characters (BOHAT COMMON interview Q)
def longest_unique_substring(s):
    char_index = {}             # char -> last seen index
    left = 0
    max_length = 0
    for right, char in enumerate(s):
        if char in char_index and char_index[char] >= left:
            left = char_index[char] + 1     # window ko SHRINK karo (duplicate ke baad se)
        char_index[char] = right
        max_length = max(max_length, right - left + 1)
    return max_length

print(longest_unique_substring("abcabcbb"))   # 3 ("abc")
# PATTERN: right pointer hamesha AAGE badhta hai (EXPAND), left pointer
# SIRF zarurat parne par AAGE badhta hai (SHRINK) — total movement O(n) hai
# (amortized), isliye POORA algorithm O(n) hai, naive O(n²)/O(n³) ki jagah

# Use case: Minimum Window Substring (HARD level — bohat common in big tech interviews)
def min_window_substring(s, t):
    if not t or not s:
        return ""
    need = Counter(t)
    missing = len(t)                   # kitne characters abhi bhi CHAHIYE
    left = start = end = 0

    for right, char in enumerate(s, 1):
        if need[char] > 0:
            missing -= 1
        need[char] -= 1

        if missing == 0:                # SAARE characters mil gaye, ab SHRINK karke MINIMIZE karo
            while left < right and need[s[left]] < 0:
                need[s[left]] += 1
                left += 1
            if end == 0 or right - left < end - start:
                start, end = left, right
            need[s[left]] += 1            # left ko aage badhane se pehle, us char ko WAPIS "need" me daalo
            missing += 1
            left += 1

    return s[start:end]

from collections import Counter
print(min_window_substring("ADOBECODEBANC", "ABC"))   # "BANC"

# Use case: Subarray Sum Equals K (with HASH MAP — Sliding Window pure form
# kaam nahi karta agar NEGATIVE numbers hon, isliye prefix sum + hashmap)
def subarray_sum_k(arr, k):
    count = 0
    prefix_sum = 0
    sum_freq = {0: 1}              # prefix_sum 0 EK BAAR "exist" karta hai (empty prefix)
    for num in arr:
        prefix_sum += num
        if (prefix_sum - k) in sum_freq:
            count += sum_freq[prefix_sum - k]
        sum_freq[prefix_sum] = sum_freq.get(prefix_sum, 0) + 1
    return count

print(subarray_sum_k([1, 1, 1], 2))   # 2 ([1,1] twice — indices [0,1] and [1,2])
# YE SLIDING WINDOW NAHI hai (negative numbers ki wajah se window
# monotonic nahi rehti) — PREFIX SUM + HASH MAP pattern hai, common confusion!

# ----------------------------------------------------------------------------
# 7) PATTERN RECOGNITION — kab Two Pointers, kab Sliding Window
# ----------------------------------------------------------------------------
# | Signal in problem                                          | Pattern                |
# |------------------------------------------------------------------|------------------------------|
# | "SORTED array me pair/triplet dhoondo"                              | Two Pointers (opposite ends) |
# | "Palindrome check", "reverse in-place"                                 | Two Pointers (opposite ends) |
# | "Linked list cycle/middle"                                                | Fast & Slow Pointers        |
# | "Contiguous SUBARRAY/SUBSTRING with property X (max/min/count)"             | Sliding Window               |
# | "Fixed size K window ka max/min/sum"                                            | Fixed Sliding Window         |
# | "Subarray sum = K, NEGATIVE numbers possible"                                       | Prefix Sum + HashMap (NOT pure sliding window) |

# ============================================================================
# INTERVIEW Q&A
# ============================================================================
# Q: Sliding Window TECHNIQUE kis type ki problems pe APPLY NAHI hoti?
# A: Jab array me NEGATIVE numbers hon aur "sum" related problem ho — window
#    EXPAND karne se sum BADH sakta hai YA GHAT sakta hai (monotonic nahi
#    rehta), isliye "kab shrink karo" ka clear decision nahi le sakte.
#    Aise cases me PREFIX SUM + HASH MAP pattern use karte hain (jaisa
#    subarray_sum_k upar).
#
# Q: Two Pointers technique kaam karne ke liye array ka SORTED hona zaruri hai?
# A: Zyada tar "find pair" wale problems (Two Sum, 3Sum) ke liye HAAN —
#    sorted hone ki wajah se hum DECIDE kar sakte hain ke LEFT ya RIGHT
#    pointer ko move karna hai (kis taraf value badhegi/ghategi pata hota
#    hai). Palindrome check, fast/slow pointer jaisi problems me sorting
#    ki zarurat NAHI hoti — wahan pattern ALAG reason se kaam karta hai.
#
# Q: Sliding Window me "har element O(1) baar add/remove hota hai" se
#    overall complexity O(n) kaise prove hoti hai (jabke nested loop jaisa lagta hai)?
# A: AMORTIZED ANALYSIS — chahe code me NESTED while loop dikhe (right ki
#    outer loop, left ki inner while loop), LEFT pointer POORI execution
#    me MAXIMUM n baar hi move kar sakta hai (0 se n tak, kabhi WAPIS nahi
#    jata). Isliye TOTAL left-moves + TOTAL right-moves <= 2n -> O(n) overall,
#    O(n²) NAHI.
