# ============================================================================
#  HEAPS — Min-Heap, Max-Heap, Priority Queue ka FOUNDATION
# ============================================================================

# ----------------------------------------------------------------------------
# 1) Heap kya hai?
# ----------------------------------------------------------------------------
# Heap ek SPECIAL Binary Tree hai jo:
# 1. COMPLETE (har level filled hai, last level LEFT se filled hota hai) —
#    isi wajah se ARRAY me efficiently represent ho sakta hai (no pointers needed!)
# 2. HEAP PROPERTY follow karta hai:
#    - MIN-HEAP: har parent <= apne children (ROOT = SMALLEST element)
#    - MAX-HEAP: har parent >= apne children (ROOT = LARGEST element)
#
# ⚠️ GOTCHA: Heap, BST NAHI hai — sirf PARENT-CHILD relationship guaranteed
# hai, LEFT-RIGHT subtree ke beech koi ordering NAHI hai (BST se confuse mat karo).
#
# KYUN ZARURI: "MINIMUM/MAXIMUM element BAAR BAAR nikalna hai, aur naye
# elements bhi add hote rehte hain" — is problem ko O(log n) me solve karta
# hai (sorted array O(n) insert, unsorted array O(n) extract-min lagta — heap DONO O(log n)).

# ----------------------------------------------------------------------------
# 2) Array Representation (Heap ko LINKED structure ki zarurat nahi)
# ----------------------------------------------------------------------------
# Index i ke liye:
#   Parent index   = (i - 1) // 2
#   Left child      = 2*i + 1
#   Right child      = 2*i + 2
#
# Example MIN-HEAP array: [1, 3, 2, 7, 5, 4]
#            1            (index 0)
#          /   \
#         3     2          (index 1, 2)
#        / \   /
#       7   5 4             (index 3, 4, 5)

# ----------------------------------------------------------------------------
# 3) Python's heapq — MIN-HEAP only (production me ye use karo, manual mat banao)
# ----------------------------------------------------------------------------
import heapq

min_heap = [5, 2, 8, 1, 9]
heapq.heapify(min_heap)                   # O(n) — array ko heap me convert
print(min_heap)                            # [1, 2, 8, 5, 9] (heap property maintained)

heapq.heappush(min_heap, 0)                 # O(log n)
print(heapq.heappop(min_heap))               # O(log n) -> 0 (sabse chhota)
print(min_heap[0])                            # O(1) — PEEK (sirf dekho, remove mat karo)

# MAX-HEAP TRICK: Python me direct nahi hai — NEGATE karo values ko
max_heap = [5, 2, 8, 1, 9]
max_heap = [-x for x in max_heap]
heapq.heapify(max_heap)
print(-heapq.heappop(max_heap))             # 9 (sabse bada, negate karke wapis)

# ----------------------------------------------------------------------------
# 4) MANUAL Min-Heap Implementation (CONCEPT samajhne ke liye)
# ----------------------------------------------------------------------------
class MinHeap:
    def __init__(self):
        self.heap = []

    def _parent(self, i): return (i - 1) // 2
    def _left(self, i): return 2 * i + 1
    def _right(self, i): return 2 * i + 2

    def push(self, val):                       # O(log n)
        self.heap.append(val)
        self._sift_up(len(self.heap) - 1)

    def _sift_up(self, i):
        # Naya element (END me add hua) ko UPAR move karo jab tak heap property satisfy na ho
        while i > 0 and self.heap[i] < self.heap[self._parent(i)]:
            self.heap[i], self.heap[self._parent(i)] = self.heap[self._parent(i)], self.heap[i]
            i = self._parent(i)

    def pop(self):                               # O(log n)
        if not self.heap:
            raise IndexError("pop from empty heap")
        min_val = self.heap[0]
        last = self.heap.pop()                    # last element nikalo
        if self.heap:
            self.heap[0] = last                     # ROOT pe rakh do
            self._sift_down(0)
        return min_val

    def _sift_down(self, i):
        # ROOT (jo abhi last element hai) ko NEECHE move karo jab tak heap property satisfy na ho
        n = len(self.heap)
        while True:
            smallest = i
            left, right = self._left(i), self._right(i)
            if left < n and self.heap[left] < self.heap[smallest]:
                smallest = left
            if right < n and self.heap[right] < self.heap[smallest]:
                smallest = right
            if smallest == i:
                break
            self.heap[i], self.heap[smallest] = self.heap[smallest], self.heap[i]
            i = smallest

mh = MinHeap()
for v in [5, 2, 8, 1, 9]:
    mh.push(v)
print(mh.pop())   # 1
print(mh.pop())   # 2

# ----------------------------------------------------------------------------
# 5) Time Complexity Summary
# ----------------------------------------------------------------------------
# | Operation        | Complexity | Kyun                                            |
# |---------------------|----------------|------------------------------------------------------|
# | Build Heap (heapify) | O(n)            | Bottom-up build, naively O(n log n) lagta lekin proof se O(n) |
# | Insert (push)          | O(log n)        | Tree height tak sift-up                                |
# | Extract Min/Max (pop)    | O(log n)        | Tree height tak sift-down                              |
# | Peek (top dekho)           | O(1)            | Root array ke index 0 pe hai                            |

# ----------------------------------------------------------------------------
# 6) REAL-WORLD USE CASE 1: Top-K Problems (interview me BOHAT common pattern)
# ----------------------------------------------------------------------------
def top_k_largest(nums, k):
    # MIN-HEAP of size k -> agar naya element heap ke MIN se BADA hai, swap karo
    heap = nums[:k]
    heapq.heapify(heap)
    for num in nums[k:]:
        if num > heap[0]:
            heapq.heapreplace(heap, num)     # pop smallest + push new, 1 operation me
    return heap

print(top_k_largest([3, 1, 5, 12, 2, 11], 3))   # [5, 12, 11] (top 3 largest)
# WHY MIN-HEAP for "top K LARGEST"? Kyunki hum SMALLEST ko jaldi NIKAALNA
# chahte hain jab koi bada element aaye — heap ka ROOT (min) hamesha
# "weakest link" hota hai jo replace hoga. Complexity: O(n log k) — better
# than sorting WHOLE array O(n log n) jab k << n ho.

# ----------------------------------------------------------------------------
# 7) REAL-WORLD USE CASE 2: Merge K Sorted Lists
# ----------------------------------------------------------------------------
def merge_k_sorted_lists(lists):
    heap = []
    for i, lst in enumerate(lists):
        if lst:
            heapq.heappush(heap, (lst[0], i, 0))   # (value, list_index, element_index)
    result = []
    while heap:
        val, list_idx, elem_idx = heapq.heappop(heap)
        result.append(val)
        if elem_idx + 1 < len(lists[list_idx]):
            next_val = lists[list_idx][elem_idx + 1]
            heapq.heappush(heap, (next_val, list_idx, elem_idx + 1))
    return result

print(merge_k_sorted_lists([[1, 4, 5], [1, 3, 4], [2, 6]]))
# [1, 1, 2, 3, 4, 4, 5, 6]
# REAL APPLICATION: Merging sorted log files from MULTIPLE servers (distributed
# systems), External merge sort (data disk pe sorted chunks me hai)

# ----------------------------------------------------------------------------
# 8) REAL-WORLD USE CASE 3: Task Scheduling by Priority
# ----------------------------------------------------------------------------
tasks = []
heapq.heappush(tasks, (1, "Critical bug fix"))      # (priority, task) — chhota = urgent
heapq.heappush(tasks, (3, "Update documentation"))
heapq.heappush(tasks, (2, "Code review"))

while tasks:
    priority, task = heapq.heappop(tasks)
    print(f"Processing (priority {priority}): {task}")
# Output order: Critical bug fix -> Code review -> Update documentation

# ----------------------------------------------------------------------------
# 9) REAL-WORLD USE CASE 4: Dijkstra's Shortest Path (detail: 11_graphs/)
# ----------------------------------------------------------------------------
# Dijkstra HAMESHA "currently SHORTEST known distance wala node" process
# karta hai pehle — Priority Queue (Min-Heap) EXACTLY yehi efficiently deta hai.

# ----------------------------------------------------------------------------
# 10) Heap Sort (Sorting algorithm jo Heap use karta hai — O(n log n) guaranteed)
# ----------------------------------------------------------------------------
def heap_sort(arr):
    heapq.heapify(arr)                       # O(n)
    return [heapq.heappop(arr) for _ in range(len(arr))]   # n * O(log n) = O(n log n)

print(heap_sort([5, 2, 8, 1, 9]))   # [1, 2, 5, 8, 9]
# Quick Sort se SLOWER hota hai practically (constant factors), lekin
# Quick Sort ki tarah WORST CASE O(n²) NAHI hota — guaranteed O(n log n) hai

# ============================================================================
# INTERVIEW Q&A
# ============================================================================
# Q: Heap aur BST me farq?
# A: Heap: SIRF parent-child ordering guarantee (parent <= children for
#    min-heap), LEFT-RIGHT ke beech koi relation nahi, array-based
#    (COMPLETE tree). BST: FULL ordering (left < node < right), search ke
#    liye optimized, ARRAY-based represent nahi ho sakta efficiently
#    (gaps ho sakte hain). Heap "min/max nikalne" ke liye BEHTAR hai, BST
#    "kisi BHI value search karne" ke liye behtar hai.
#
# Q: "Top K elements" problem me Heap kyun, Sorting kyun nahi?
# A: Sorting POORA array sort karta hai O(n log n), jabke hume sirf TOP K
#    chahiye. Heap approach O(n log k) deta hai — agar k bohat CHHOTA hai
#    n ke comparison me (e.g. top 10 from 1 million), ye SIGNIFICANTLY
#    fast hai.
#
# Q: heapify() O(n) kaise hai, jabke n elements push karne se O(n log n) lagta?
# A: heapify() BOTTOM-UP approach use karta hai (LEAF nodes se shuru karke
#    upar jata hai) — MATHEMATICAL PROOF se pata chalta hai ke saari
#    sift-down operations ka SUM O(n) hai (kyunki lower levels me zyada
#    nodes hote hain lekin unki sift-down depth kam hoti hai — ye balance
#    out hota hai). Individual push() O(log n) hai, lekin n bार push karna
#    aur ek baar heapify() karna — DONO me FARQ hai.
