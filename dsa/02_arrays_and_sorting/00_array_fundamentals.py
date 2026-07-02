# ============================================================================
#  ARRAY FUNDAMENTALS — Overview file (baqi sorting algos isi folder me
#  pehle se maujood hain — array/ folder se copy kiye gaye: bubble_sort.py,
#  selection_sort.py, insertion_sort.py, quicksort.py, merge_sort.py,
#  counting_sort.py, radix_sort.py, list_array.py + definations/ subfolder)
# ============================================================================

# ----------------------------------------------------------------------------
# 1) Array kya hai aur kyun fundamental hai?
# ----------------------------------------------------------------------------
# Array = CONTIGUOUS (lagataar) memory locations me stored elements ka
# collection, FIXED type (low-level languages me) aur INDEX se access hote hain.
#
# KYUN FUNDAMENTAL: Almost har doosri data structure (Stack, Queue, Hash
# Table, Heap) INTERNALLY array use karti hai. Array samajhna = baqi sab
# samajhne ki base hai.
#
# Python me "list" technically DYNAMIC ARRAY hai (resizable), C/Java me
# array FIXED-SIZE hota hai — concept same hai, implementation thori alag.

# ----------------------------------------------------------------------------
# 2) Array Operations & Time Complexity (RATTA LAGANA ZARURI HAI)
# ----------------------------------------------------------------------------
# | Operation              | Time Complexity | KYUN                                          |
# |---------------------------|--------------------|---------------------------------------------------|
# | Access by index (arr[i])    | O(1)               | Memory address = base_address + (i * element_size) — direct calculate ho jata hai |
# | Search (unsorted)            | O(n)               | Worst case har element check karna padega         |
# | Search (sorted — binary search)| O(log n)         | [[03_searching]] me detail                        |
# | Insert at END                  | O(1) amortized     | Agar space hai to direct, full ho to resize O(n)  |
# | Insert at BEGINNING/MIDDLE      | O(n)               | Baqi saare elements SHIFT karne padte hain        |
# | Delete from END                  | O(1)               | Direct remove                                      |
# | Delete from BEGINNING/MIDDLE      | O(n)               | Baqi elements shift karke gap fill karna padta hai |

arr = [10, 20, 30, 40, 50]

# O(1) access:
print(arr[2])                      # 30 — direct memory calculation

# O(n) insert at beginning (sab kuch ek position shift hota hai):
arr.insert(0, 5)                   # [5, 10, 20, 30, 40, 50]

# O(1) amortized insert at end:
arr.append(60)                     # [5, 10, 20, 30, 40, 50, 60]

# O(n) delete from middle:
arr.remove(30)                     # baqi sab shift hote hain gap fill karne ke liye

# ----------------------------------------------------------------------------
# 3) Dynamic Array kaise kaam karta hai INTERNALLY (Python list, Java ArrayList)
# ----------------------------------------------------------------------------
# - Shuru me ek CHHOTI fixed-size array allocate hoti hai
# - Jab full ho jaye aur naya element add karna ho:
#     1. NAYI bigger array allocate hoti hai (typically DOUBLE size — growth factor)
#     2. SAARE purane elements NAYI array me COPY hote hain — O(n) operation
#     3. Naya element add hota hai
# - Ye resize RARELY hota hai (doubling ki wajah se), isliye AMORTIZED O(1)
#   insert at end milta hai (detail: 01_complexity_analysis/big_o_notation.py)
#
# Isi wajah se: agar pehle se pata ho kitne elements aayenge, to CAPACITY
# PRE-ALLOCATE karna performance behtar karta hai (resize overhead avoid hota hai).

# ----------------------------------------------------------------------------
# 4) Array vs Linked List (CLASSIC INTERVIEW COMPARISON — detail: 04_linked_list/)
# ----------------------------------------------------------------------------
# | Aspect             | Array                          | Linked List                    |
# |-----------------------|------------------------------------|-------------------------------------|
# | Memory                 | Contiguous                          | Scattered (nodes + pointers)        |
# | Access by index          | O(1)                                | O(n)                                |
# | Insert/Delete at start     | O(n)                                | O(1)                                |
# | Cache Performance            | BEHTAR (contiguous = CPU cache friendly) | KHARAB (scattered memory)     |
# | Memory overhead                | Kam (sirf data)                     | Zyada (data + pointer per node)     |

# ----------------------------------------------------------------------------
# 5) Multi-dimensional Arrays (Matrix) — common interview problems ka base
# ----------------------------------------------------------------------------
matrix = [
    [1, 2, 3],
    [4, 5, 6],
    [7, 8, 9],
]
# Access: O(1) -> matrix[row][col]
print(matrix[1][2])      # 6

# Common pattern: matrix traversal, rotation, transpose
def transpose(m):
    rows, cols = len(m), len(m[0])
    result = [[0] * rows for _ in range(cols)]
    for r in range(rows):
        for c in range(cols):
            result[c][r] = m[r][c]
    return result
print(transpose(matrix))

# ----------------------------------------------------------------------------
# 6) Sorting Algorithms ka SUMMARY (DETAIL is folder ki baqi files me hai)
# ----------------------------------------------------------------------------
# | File                  | Algorithm     | Avg Complexity | Kab use karo                        |
# |--------------------------|------------------|--------------------|------------------------------------------|
# | bubble_sort.py            | Bubble Sort       | O(n²)              | Sirf educational, real use nahi          |
# | selection_sort.py          | Selection Sort     | O(n²)              | Kam swaps chahiye (memory-write costly)  |
# | insertion_sort.py           | Insertion Sort      | O(n²) / O(n) best   | Chhoti ya near-sorted arrays             |
# | quicksort.py                  | Quick Sort            | O(n log n) avg       | General purpose, most languages ka default |
# | merge_sort.py                   | Merge Sort              | O(n log n)             | STABLE sort chahiye, large data, linked lists |
# | counting_sort.py                  | Counting Sort             | O(n + k)                | Integers, limited range                |
# | radix_sort.py                       | Radix Sort                  | O(n × k)                  | Bade integers/strings, digit-wise sort |
#
# Python ka built-in sort() / sorted() = TIMSORT (Merge Sort + Insertion
# Sort ka hybrid) — O(n log n) worst case, STABLE, real-world data (jisme
# already-sorted runs hote hain) pe bohat fast. Production me HAMESHA
# built-in sort use karo — manual implementation sirf LEARNING ke liye hai.

# ----------------------------------------------------------------------------
# 7) STABLE vs UNSTABLE Sort (interview gotcha)
# ----------------------------------------------------------------------------
# STABLE sort: equal elements ka RELATIVE ORDER maintain rehta hai
# Use case: pehle name se sort kiya, ab age se sort karna hai — agar STABLE
# hai to same-age logon ka NAME order pehle jaisa hi rahega.

people = [("Ali", 25), ("Sara", 22), ("Bilal", 25), ("Zara", 22)]
sorted_people = sorted(people, key=lambda p: p[1])   # age se sort (stable)
print(sorted_people)
# [('Sara', 22), ('Zara', 22), ('Ali', 25), ('Bilal', 25)]
# Sara/Zara ka order PRESERVE hua (jaisa input me tha), Ali/Bilal ka bhi

# ============================================================================
# INTERVIEW Q&A
# ============================================================================
# Q: Array me element insert/delete O(n) kyun hai, jabke access O(1) hai?
# A: Access: index se direct memory address CALCULATE ho jata hai (math
#    formula). Insert/Delete (beech me): contiguous memory maintain karne
#    ke liye baqi elements ko SHIFT karna padta hai — worst case (start me
#    insert) saare n elements shift karne padte hain.
#
# Q: Python list aur C array me practical farq?
# A: C array: fixed size, single data type, manual memory management.
#    Python list: DYNAMIC (auto-resize), MIXED data types allow karta hai
#    (har element ek pointer/reference hai, actual object kahin aur store
#    hota hai) — isliye Python list thori SLOWER aur zyada MEMORY leti hai
#    C array se, lekin flexibility zyada deti hai.
#
# Q: Kab Quick Sort use karoge, kab Merge Sort?
# A: Quick Sort: in-place hai (O(log n) extra space), average case fast,
#    general purpose. Merge Sort: STABLE hai, GUARANTEED O(n log n) (worst
#    case bhi), EXTERNAL sorting (data RAM se bada ho, disk pe) ke liye
#    better, linked lists pe bhi efficient (array shifting ki zarurat nahi).
