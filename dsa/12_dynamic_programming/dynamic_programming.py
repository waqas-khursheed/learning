# ============================================================================
#  DYNAMIC PROGRAMMING (DP) — Interview ka sabse "darr wala" topic, lekin
#  pattern samajhne ke baad SABSE PREDICTABLE topic ban jata hai
# ============================================================================

# ----------------------------------------------------------------------------
# 1) DP kya hai?
# ----------------------------------------------------------------------------
# DP ek TECHNIQUE hai (data structure NAHI hai) jo problems solve karta hai
# jinme 2 properties hon:
#
# 1. OVERLAPPING SUBPROBLEMS: Same subproblem BAAR BAAR solve ho raha hai
#    (Fibonacci: fib(5) ke liye fib(3) DO BAAR calculate hota hai naive recursion me)
# 2. OPTIMAL SUBSTRUCTURE: Bade problem ka OPTIMAL solution, CHHOTE
#    subproblems ke optimal solutions se BAN sakta hai
#
# AGAR ye 2 properties hain -> DP use karke EXPONENTIAL solution ko
# POLYNOMIAL bana sakte ho (RESULTS CACHE karke — wahi HASHING wala
# Space-Time Tradeoff concept, [[../07_hashing]])

# ----------------------------------------------------------------------------
# 2) DP ke DO APPROACHES
# ----------------------------------------------------------------------------

# A) TOP-DOWN (Memoization) — Recursion + CACHE
def fib_memo(n, cache=None):
    if cache is None: cache = {}
    if n in cache:
        return cache[n]              # already calculate ho chuka -> REUSE
    if n <= 1:
        return n
    cache[n] = fib_memo(n - 1, cache) + fib_memo(n - 2, cache)
    return cache[n]

# B) BOTTOM-UP (Tabulation) — Iterative, CHHOTE subproblems se shuru karke UPAR jao
def fib_tabulation(n):
    if n <= 1:
        return n
    dp = [0] * (n + 1)
    dp[1] = 1
    for i in range(2, n + 1):
        dp[i] = dp[i - 1] + dp[i - 2]
    return dp[n]

print(fib_memo(30))         # Naive recursion: O(2^n) -> Memo: O(n)
print(fib_tabulation(30))    # O(n) time, O(n) space (O(1) space possible — neeche)

# Space-optimized (sirf last 2 values yaad rakhne ki zarurat hai):
def fib_optimized(n):
    if n <= 1:
        return n
    prev2, prev1 = 0, 1
    for _ in range(2, n + 1):
        prev2, prev1 = prev1, prev1 + prev2
    return prev1
# O(n) time, O(1) space — BEST possible

# ----------------------------------------------------------------------------
# 3) Top-Down vs Bottom-Up — kab kya choose karo
# ----------------------------------------------------------------------------
# | Aspect             | Top-Down (Memoization)              | Bottom-Up (Tabulation)         |
# |------------------------|-------------------------------------------|--------------------------------------|
# | Code style                | Recursive, natural se likhna easy hai      | Iterative, thora SOCH ke order likhna padta hai |
# | Subproblems compute karta  | SIRF jo zarurat hai (lazy)                  | SAARE chhote-se-bade tak (eager)     |
# | Stack overflow risk           | YES (deep recursion)                         | NO                                   |
# | Performance                     | Thora overhead (function calls)                | Generally FASTER                     |
#
# STRATEGY: Pehle TOP-DOWN se solution SOCHO (natural hai), phir agar
# performance/stack-depth issue ho to BOTTOM-UP me CONVERT karo.

# ----------------------------------------------------------------------------
# 4) CLASSIC DP PROBLEM 1: 0/1 Knapsack (interview SUPER common)
# ----------------------------------------------------------------------------
# "N items hain, har ek ka weight aur value hai. Bag ki capacity W hai.
#  MAXIMUM value kya hai jo bag me fit ho sakta hai?" (har item ya to PURA
#  lo, ya bilkul mat lo — 0/1, "fraction" allowed nahi)
def knapsack(weights, values, capacity):
    n = len(weights)
    # dp[i][w] = first i items consider karke, capacity w me MAX value
    dp = [[0] * (capacity + 1) for _ in range(n + 1)]

    for i in range(1, n + 1):
        for w in range(capacity + 1):
            if weights[i - 1] <= w:
                # CHOICE: is item ko INCLUDE karo (value + remaining capacity ka best)
                #         ya EXCLUDE karo (pichla best as-is)
                dp[i][w] = max(
                    values[i - 1] + dp[i - 1][w - weights[i - 1]],   # include
                    dp[i - 1][w]                                       # exclude
                )
            else:
                dp[i][w] = dp[i - 1][w]    # fit hi nahi hoga, EXCLUDE karna padega
    return dp[n][capacity]

print(knapsack([1, 3, 4, 5], [1, 4, 5, 7], 7))   # 9 (items with weight 3+4=7, value 4+5=9)

# ----------------------------------------------------------------------------
# 5) CLASSIC DP PROBLEM 2: Longest Common Subsequence (LCS)
# ----------------------------------------------------------------------------
# Use case: Git DIFF algorithm, DNA sequence matching, Plagiarism detection
def lcs(text1, text2):
    m, n = len(text1), len(text2)
    dp = [[0] * (n + 1) for _ in range(m + 1)]
    for i in range(1, m + 1):
        for j in range(1, n + 1):
            if text1[i - 1] == text2[j - 1]:
                dp[i][j] = 1 + dp[i - 1][j - 1]        # match -> diagonal + 1
            else:
                dp[i][j] = max(dp[i - 1][j], dp[i][j - 1])   # no match -> best of skipping either
    return dp[m][n]

print(lcs("abcde", "ace"))   # 3 ("ace")

# ----------------------------------------------------------------------------
# 6) CLASSIC DP PROBLEM 3: Coin Change (Minimum coins for amount)
# ----------------------------------------------------------------------------
def coin_change(coins, amount):
    dp = [float('inf')] * (amount + 1)
    dp[0] = 0                                  # 0 amount ke liye 0 coins chahiye
    for i in range(1, amount + 1):
        for coin in coins:
            if coin <= i:
                dp[i] = min(dp[i], dp[i - coin] + 1)
    return dp[amount] if dp[amount] != float('inf') else -1

print(coin_change([1, 2, 5], 11))   # 3 (5+5+1)
# REAL APPLICATION: ATM cash dispensing logic, currency exchange optimization

# ----------------------------------------------------------------------------
# 7) CLASSIC DP PROBLEM 4: Longest Increasing Subsequence (LIS)
# ----------------------------------------------------------------------------
def length_of_lis(nums):
    if not nums:
        return 0
    dp = [1] * len(nums)               # dp[i] = LIS ending AT index i
    for i in range(1, len(nums)):
        for j in range(i):
            if nums[j] < nums[i]:
                dp[i] = max(dp[i], dp[j] + 1)
    return max(dp)

print(length_of_lis([10, 9, 2, 5, 3, 7, 101, 18]))   # 4 ([2,3,7,101] or [2,3,7,18])
# Naive: O(n²). Binary Search optimization se O(n log n) bhi possible hai
# (advanced — patience sorting technique)

# ----------------------------------------------------------------------------
# 8) CLASSIC DP PROBLEM 5: House Robber (adjacent constraint pattern)
# ----------------------------------------------------------------------------
# "Houses ek line me hain, har ek me paisa hai. ADJACENT houses rob nahi
#  kar sakte (alarm baj jayega). MAX paisa kitna loot sakte ho?"
def house_robber(nums):
    rob, skip = 0, 0          # rob = is house tak max agar CURRENT include kiya, skip = agar exclude kiya
    for num in nums:
        new_rob = skip + num             # current rob karo -> pichla "skip" + current value
        skip = max(rob, skip)             # current skip karo -> pichla best (rob ya skip)
        rob = new_rob
    return max(rob, skip)

print(house_robber([2, 7, 9, 3, 1]))   # 12 (2+9+1)

# ----------------------------------------------------------------------------
# 9) DP PATTERN RECOGNITION — Problem dekh kar PEHCHANNA kaise hai
# ----------------------------------------------------------------------------
# | Signal in problem statement                              | Likely DP pattern         |
# |----------------------------------------------------------------|---------------------------------|
# | "Maximum/Minimum ways/cost/value"                            | Optimization DP (Knapsack-style)|
# | "Count number of ways to..."                                    | Counting DP (Fibonacci-style)   |
# | "2 strings/sequences compare karo"                                | LCS / Edit Distance pattern     |
# | "Subarray/Subsequence with property X"                              | LIS-style ya Kadane's pattern   |
# | "Choices with CONSTRAINT (adjacent mat lo, capacity limit)"           | Knapsack-family                |
# | Problem "choices ka TREE" jaisa lag raha hai, SAME state baar baar aata hai | Memoize karo!                |

# ----------------------------------------------------------------------------
# 10) HOW TO APPROACH a DP problem (STEP-BY-STEP, interview me ye process bolo)
# ----------------------------------------------------------------------------
# 1. Pehle BRUTE FORCE recursive solution likho (chahe slow ho)
# 2. IDENTIFY karo: "STATE" kya hai? (jo variables result ko UNIQUELY define karte hain)
# 3. Check karo: kya SAME state baar baar aa raha hai? (overlapping subproblems)
# 4. RECURRENCE RELATION likho: dp[state] = f(dp[smaller states])
# 5. MEMOIZE karo (top-down) YA TABULATE karo (bottom-up)
# 6. Agar possible ho, SPACE optimize karo (sirf last K states yaad rakho — jaisa fib_optimized)

# ============================================================================
# INTERVIEW Q&A
# ============================================================================
# Q: DP aur Greedy me farq?
# A: Greedy [[../13_greedy_algorithms]] har step pe LOCALLY best choice
#    leta hai aur KABHI WAPIS nahi jata (no backtrack/reconsider) — sirf
#    KUCH specific problems me CORRECT result deta hai (Greedy Choice
#    Property honi chahiye). DP SAARE possible choices consider karta hai
#    (systematically, with caching) — GUARANTEED optimal result deta hai
#    agar Optimal Substructure ho, chahe Greedy fail ho jaye.
#
# Q: DP aur Divide & Conquer me farq (dono recursion use karte hain)?
# A: Divide & Conquer (Merge Sort) ke SUBPROBLEMS INDEPENDENT hote hain
#    (overlap NAHI karte) — isliye CACHING ka koi fayda nahi. DP ke
#    subproblems OVERLAP karte hain (same subproblem multiple paths se
#    aata hai) — CACHING (memoization) yahi fayda deta hai.
#
# Q: Memoization se TIME complexity kaise IMPROVE hoti hai (Fibonacci example se explain karo)?
# A: Naive Fibonacci: fib(n) = fib(n-1) + fib(n-2) — recursion TREE banta
#    hai jisme HAR subproblem MULTIPLE BAAR calculate hota hai (fib(2)
#    fib(5) ke calculation me 3 baar aata hai). Total calls = O(2^n).
#    Memoization se: HAR UNIQUE subproblem (0 se n tak) SIRF EK BAAR
#    calculate hota hai (cache me store ho jata hai), baaki saare calls
#    O(1) me cache se return hote hain. Total: O(n).
#
# Q: 2D DP table ko 1D me OPTIMIZE kab kar sakte ho?
# A: Jab CURRENT row/state sirf PICHLI row/state pe depend karta ho (jaise
#    Knapsack me dp[i] sirf dp[i-1] pe depend karta hai), to POORI 2D table
#    rakhne ki zarurat nahi — sirf "previous row" (ya kabhi "previous 2
#    values" jaisa Fibonacci me) yaad rakho. Space O(n*m) se O(m) ya O(1)
#    tak optimize ho sakti hai.
