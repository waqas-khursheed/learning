# ============================================================================
#  RECURSION & BACKTRACKING — Trees/Graphs/DP isi pe based hain
# ============================================================================

# ----------------------------------------------------------------------------
# 1) Recursion kya hai?
# ----------------------------------------------------------------------------
# Function jo APNE AAP ko call kare, ek SMALLER version of the SAME problem
# solve karne ke liye, jab tak ek "BASE CASE" (jahan recursion RUKTI hai) na aa jaye.
#
# HAR recursive function ke 2 PARTS hone ZARURI hain:
# 1. BASE CASE: jahan recursion STOP hoti hai (warna INFINITE recursion -> crash)
# 2. RECURSIVE CASE: problem ko SMALLER subproblem me todna + khud ko call karna
#
# KYUN ZARURI: Tree/Graph traversal, Backtracking, Divide & Conquer (Merge/
# Quick Sort), Dynamic Programming — sab RECURSION ki understanding pe
# based hain. Agar recursion clear nahi hai, in sabko samajhna mushkil hoga.

def factorial(n):
    if n <= 1:                # BASE CASE
        return 1
    return n * factorial(n - 1)   # RECURSIVE CASE (smaller subproblem)

print(factorial(5))   # 120

# ----------------------------------------------------------------------------
# 2) Recursion INTERNALLY kaise kaam karta hai (Call Stack)
# ----------------------------------------------------------------------------
# factorial(3) call hone par:
#   factorial(3) -> waits for factorial(2)
#       factorial(2) -> waits for factorial(1)
#           factorial(1) -> returns 1 (BASE CASE)
#       factorial(2) -> returns 2 * 1 = 2
#   factorial(3) -> returns 3 * 2 = 6
#
# Har call STACK FRAME banata hai (memory me) jab tak base case na aaye,
# phir STACK UNWIND hota hai (returns wapis hote hain). [[../05_stack]]

# ----------------------------------------------------------------------------
# 3) Recursion vs Iteration — kab kya use karo
# ----------------------------------------------------------------------------
# | Aspect          | Recursion                          | Iteration             |
# |--------------------|----------------------------------------|---------------------------|
# | Code readability     | Cleaner for tree-like/divide-conquer problems | Often verbose for same problems |
# | Space                  | O(depth) — call stack overhead          | O(1) typically (no extra stack) |
# | Performance               | Thora slower (function call overhead)     | Generally faster              |
# | Risk                        | Stack Overflow (deep recursion)             | No such risk                 |
#
# RULE: Agar problem NATURALLY tree/recursive structure ki hai (file system,
# tree traversal, nested data) -> Recursion zyada CLEAR hoga. Simple
# loops (1 se n tak sum) ke liye Iteration BEHTAR/faster hai.

# ----------------------------------------------------------------------------
# 4) TAIL RECURSION (concept — Python me optimize NAHI hota, but samajhna zaruri)
# ----------------------------------------------------------------------------
# Tail Recursive: recursive call function ka AAKHRI statement hai (uske
# baad koi aur computation nahi)
def factorial_tail(n, accumulator=1):
    if n <= 1:
        return accumulator
    return factorial_tail(n - 1, n * accumulator)   # TAIL call — last operation hai
# ⚠️ Python COMPILER/interpreter level pe Tail Call Optimization NAHI karta
# (jaise C/Scheme karte hain) — isliye Python me deep recursion abhi bhi
# Stack Overflow (RecursionError) de sakti hai chahe tail-recursive ho.
# Production Python me bohat deep recursion ke liye ITERATIVE approach
# ya sys.setrecursionlimit() (risky) use karte hain.

import sys
print(sys.getrecursionlimit())   # default 1000 — isse zyada deep recursion crash karega

# ----------------------------------------------------------------------------
# 5) Classic Recursion Examples
# ----------------------------------------------------------------------------

# Fibonacci (naive — O(2^n), DEMO ke liye ke recursion KHARAB bhi ho sakta hai)
def fib(n):
    if n <= 1:
        return n
    return fib(n - 1) + fib(n - 2)

# Sum of array recursively
def sum_array(arr):
    if not arr:
        return 0
    return arr[0] + sum_array(arr[1:])    # ⚠️ arr[1:] har baar NAYI list banata hai O(n) — better: index pass karo

def sum_array_efficient(arr, index=0):
    if index == len(arr):
        return 0
    return arr[index] + sum_array_efficient(arr, index + 1)

# Power function (Fast Exponentiation — Divide & Conquer se O(log n))
def power(base, exp):
    if exp == 0:
        return 1
    half = power(base, exp // 2)
    if exp % 2 == 0:
        return half * half
    else:
        return half * half * base

print(power(2, 10))   # 1024, O(log n) instead of naive O(n)

# ----------------------------------------------------------------------------
# 6) BACKTRACKING — Recursion + "UNDO" (TRY -> agar fail ho -> WAPIS jao)
# ----------------------------------------------------------------------------
# Pattern: CHOOSE -> EXPLORE (recursive call) -> UNCHOOSE (backtrack)
# Use case: Saari POSSIBLE combinations/permutations explore karni hain,
# aur jaldi INVALID paths ko CHHOD dena hai (prune karna).

# a) Subsets (Power Set) — har element "include karo ya na karo"
def subsets(nums):
    result = []
    current = []

    def backtrack(index):
        if index == len(nums):
            result.append(current[:])     # COPY save karo (reference nahi)
            return
        # CHOICE 1: current element INCLUDE mat karo
        backtrack(index + 1)
        # CHOICE 2: current element INCLUDE karo
        current.append(nums[index])
        backtrack(index + 1)
        current.pop()                       # BACKTRACK (undo)

    backtrack(0)
    return result

print(subsets([1, 2, 3]))
# [[], [3], [2], [2,3], [1], [1,3], [1,2], [1,2,3]]

# b) Permutations
def permutations(nums):
    result = []

    def backtrack(current, remaining):
        if not remaining:
            result.append(current[:])
            return
        for i in range(len(remaining)):
            current.append(remaining[i])                       # CHOOSE
            backtrack(current, remaining[:i] + remaining[i+1:])  # EXPLORE
            current.pop()                                          # UNCHOOSE (backtrack)

    backtrack([], nums)
    return result

print(permutations([1, 2, 3]))
# [[1,2,3],[1,3,2],[2,1,3],[2,3,1],[3,1,2],[3,2,1]]

# c) N-Queens (CLASSIC backtracking interview question)
def solve_n_queens(n):
    solutions = []
    board = [-1] * n          # board[row] = column jahan queen rakhi hai

    def is_safe(row, col):
        for r in range(row):
            c = board[r]
            if c == col or abs(c - col) == abs(r - row):    # same column ya diagonal
                return False
        return True

    def backtrack(row):
        if row == n:
            solutions.append(board[:])
            return
        for col in range(n):
            if is_safe(row, col):
                board[row] = col            # CHOOSE
                backtrack(row + 1)           # EXPLORE
                board[row] = -1               # UNCHOOSE (backtrack)

    backtrack(0)
    return solutions

print(len(solve_n_queens(4)))   # 2 solutions for 4-Queens

# d) Combination Sum (target ke liye numbers combine karna, repetition allowed)
def combination_sum(candidates, target):
    result = []
    current = []

    def backtrack(start, remaining):
        if remaining == 0:
            result.append(current[:])
            return
        if remaining < 0:
            return                              # PRUNE — ye path invalid hai, aage mat jao
        for i in range(start, len(candidates)):
            current.append(candidates[i])
            backtrack(i, remaining - candidates[i])   # 'i' (not i+1) -> repetition allowed
            current.pop()

    backtrack(0, target)
    return result

print(combination_sum([2, 3, 6, 7], 7))   # [[2,2,3], [7]]

# ----------------------------------------------------------------------------
# 7) Backtracking ka "PRUNING" concept — performance KEY hai
# ----------------------------------------------------------------------------
# Backtracking BRUTE FORCE jaisa lagta hai, lekin PRUNING (invalid paths ko
# JALDI chhod dena, jaisa combination_sum me "if remaining < 0: return") ise
# practically EFFICIENT bana deta hai — bina pruning ke exponential time
# bohat zyada wasteful hota.

# ============================================================================
# INTERVIEW Q&A
# ============================================================================
# Q: Recursion aur Backtracking me farq?
# A: Recursion ek GENERAL technique hai (function khud ko call kare).
#    Backtracking ek SPECIFIC recursion PATTERN hai jahan hum DECISIONS
#    explore karte hain, aur agar koi decision INVALID/unwanted result de
#    to use UNDO karke (backtrack) doosra decision try karte hain. Saari
#    Backtracking Recursion hai, lekin saari Recursion Backtracking nahi hai.
#
# Q: Recursion ko Iteration me kaise convert karte ho (jab recursion limit
#    issue ho)?
# A: EXPLICIT STACK use karke (manually call stack simulate karo) —
#    jo kaam recursion AUTOMATICALLY karta hai (function calls memory me
#    push/pop), wahi tum khud ek list/stack data structure se karte ho.
#    Example: Tree traversal recursive se iterative+stack me convert karna.
#
# Q: subsets() function me current[:] (copy) kyun use kiya, current kyun nahi?
# A: current EK HI list OBJECT hai jo poori recursion me MODIFY (mutate)
#    ho rahi hai (append/pop se). Agar hum current ko DIRECTLY result me
#    daal dein (bina copy ke), to result me sab REFERENCES honge SAME
#    list ke — jab current baad me change hoga, result ke saare entries
#    bhi change ho jayenge (CLASSIC Python mutable reference bug).
