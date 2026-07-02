# ============================================================================
#  GREEDY ALGORITHMS — Har step pe LOCALLY best choice, kabhi WAPIS nahi jaate
# ============================================================================

# ----------------------------------------------------------------------------
# 1) Greedy kya hai?
# ----------------------------------------------------------------------------
# Greedy approach har STEP pe wahi choice leta hai jo US WAQT sabse BEHTAR
# lagti hai (LOCAL optimum), is UMEED me ke ye choices mil kar OVERALL best
# (GLOBAL optimum) result denge. Decision lene ke baad KABHI WAPIS nahi
# jaata (DP ke ulat — jo SAARI possibilities consider karta hai).
#
# ⚠️ IMPORTANT: Greedy HAMESHA CORRECT result NAHI deta — sirf UN problems
# pe kaam karta hai jinme "GREEDY CHOICE PROPERTY" ho (local optimum se
# global optimum guaranteed milta ho). Isay PROVE karna padta hai (ya
# kam se kam strongly justify), warna GALAT answer mil sakta hai.

# ----------------------------------------------------------------------------
# 2) CLASSIC EXAMPLE: Coin Change (Greedy KAAM karta hai, lekin HAMESHA NAHI!)
# ----------------------------------------------------------------------------
def coin_change_greedy(coins, amount):
    coins = sorted(coins, reverse=True)     # BADE coin se shuru karo
    count = 0
    for coin in coins:
        count += amount // coin
        amount %= coin
    return count if amount == 0 else -1

print(coin_change_greedy([25, 10, 5, 1], 63))   # 6 (25+25+10+1+1+1) -- CORRECT for US coins!

# ⚠️ GOTCHA: Ye CORRECT hai US coin system (1,5,10,25) ke liye, lekin AGAR
# coins = [1, 3, 4] aur amount = 6 ho:
print(coin_change_greedy([4, 3, 1], 6))   # Greedy: 4+1+1 = 3 coins
# OPTIMAL ANSWER hai: 3+3 = 2 coins! GREEDY YAHAN FAIL HO GAYA.
# Isi wajah se "Minimum Coins" problem GENERALLY DP se solve karte hain
# (12_dynamic_programming/coin_change), Greedy sirf SPECIFIC coin systems
# (canonical coin systems) ke liye kaam karta hai.

# ----------------------------------------------------------------------------
# 3) CLASSIC EXAMPLE: Activity Selection (Greedy CORRECTLY kaam karta hai)
# ----------------------------------------------------------------------------
# "N activities hain, har ek ka start/end time hai. MAXIMUM kitni activities
#  attend kar sakte ho (overlap nahi honi chahiye)?"
def activity_selection(activities):
    # Activities ko END TIME ke hisab se sort karo — yehi GREEDY CHOICE hai
    activities = sorted(activities, key=lambda x: x[1])
    selected = [activities[0]]
    last_end = activities[0][1]
    for start, end in activities[1:]:
        if start >= last_end:           # overlap nahi -> SELECT karo
            selected.append((start, end))
            last_end = end
    return selected

print(activity_selection([(1, 4), (3, 5), (0, 6), (5, 7), (3, 9), (5, 9), (6, 10), (8, 11), (8, 12), (2, 14), (12, 16)]))
# WHY END TIME se sort karna SAHI hai: jo activity SABSE PEHLE khatam hoti
# hai, wo FUTURE activities ke liye SABSE ZYADA "room" chhodti hai — isliye
# usay choose karna hamesha OPTIMAL hota hai (provable greedy choice).

# ----------------------------------------------------------------------------
# 4) CLASSIC EXAMPLE: Fractional Knapsack (Greedy WORKS — 0/1 Knapsack se
#    DIFFERENT, DP wala) [[../12_dynamic_programming]]
# ----------------------------------------------------------------------------
# "Items ko FRACTION me bhi le sakte ho (0/1 Knapsack me PURA ya BILKUL nahi)"
def fractional_knapsack(items, capacity):
    # items = [(value, weight), ...] -> VALUE/WEIGHT ratio se sort karo (descending)
    items = sorted(items, key=lambda x: x[0] / x[1], reverse=True)
    total_value = 0.0
    for value, weight in items:
        if capacity >= weight:
            total_value += value
            capacity -= weight
        else:
            total_value += value * (capacity / weight)   # FRACTION le lo
            break
    return total_value

print(fractional_knapsack([(60, 10), (100, 20), (120, 30)], 50))   # 240.0
# WHY GREEDY WORKS YAHAN (lekin 0/1 Knapsack me NAHI): Fraction allow hone
# ki wajah se hum HAMESHA sabse "VALUABLE PER UNIT WEIGHT" item se shuru
# karke OPTIMAL fill kar sakte hain — 0/1 me ye possible nahi (pura item
# lena PADTA hai, jo SUBOPTIMAL combination bana sakta hai).

# ----------------------------------------------------------------------------
# 5) CLASSIC EXAMPLE: Huffman Coding (Data Compression — uses Heap + Greedy)
# ----------------------------------------------------------------------------
import heapq
from collections import Counter

def huffman_encoding(text):
    freq = Counter(text)
    heap = [[weight, [char, ""]] for char, weight in freq.items()]
    heapq.heapify(heap)

    while len(heap) > 1:
        # GREEDY: HAMESHA 2 SABSE KAM frequency wale nodes combine karo
        lo = heapq.heappop(heap)
        hi = heapq.heappop(heap)
        for pair in lo[1:]:
            pair[1] = '0' + pair[1]
        for pair in hi[1:]:
            pair[1] = '1' + pair[1]
        heapq.heappush(heap, [lo[0] + hi[0]] + lo[1:] + hi[1:])

    return sorted(heapq.heappop(heap)[1:], key=lambda p: len(p[-1]))

codes = huffman_encoding("aaabbc")
print(codes)   # frequent chars (jaise 'a') ko SHORTER code milta hai
# REAL-WORLD: ZIP compression, JPEG compression, MP3 encoding — sab
# Huffman-jaisi techniques use karte hain. GREEDY CHOICE: sabse kam
# frequency wale 2 nodes combine karo pehle (taake unki depth zyada ho,
# aur frequent chars ki depth kam — overall ENCODED SIZE minimize hoti hai)

# ----------------------------------------------------------------------------
# 6) CLASSIC EXAMPLE: Jump Game (Greedy reachability check)
# ----------------------------------------------------------------------------
def can_jump(nums):
    max_reach = 0
    for i, num in enumerate(nums):
        if i > max_reach:
            return False               # is index tak pohanch hi nahi sakte
        max_reach = max(max_reach, i + num)
    return True

print(can_jump([2, 3, 1, 1, 4]))   # True
print(can_jump([3, 2, 1, 0, 4]))   # False (index 3 pe FASS jaate ho)

# ----------------------------------------------------------------------------
# 7) GREEDY vs DP — DECISION FRAMEWORK (interview me clearly explain karna)
# ----------------------------------------------------------------------------
# | Question to ask                                              | Greedy ho sakta hai? |
# |-------------------------------------------------------------------|----------------------------|
# | Kya LOCAL best choice se hamesha GLOBAL best milta hai (PROVABLE)?  | YES -> Greedy try karo     |
# | Kya FUTURE choices PAST decisions se affect hoti hain in a complex way? | NO -> DP chahiye       |
# | Kya "undo"/reconsider karne ki zarurat PAD sakti hai?                | YES zarurat -> DP, Greedy nahi |
#
# PRACTICAL TIP: Agar confuse ho Greedy use kare ya DP, PEHLE Greedy try
# karo (SIMPLER, FASTER), aur EK COUNTEREXAMPLE dhoondhne ki koshish karo
# (jaisa coin [1,3,4] amount=6 wala). Agar counterexample mil jaye -> DP
# use karo. Agar PROVE kar sako ke greedy choice hamesha safe hai -> Greedy use karo.

# ============================================================================
# INTERVIEW Q&A
# ============================================================================
# Q: Greedy algorithm kab FAIL hota hai? Example do.
# A: Jab problem me "GREEDY CHOICE PROPERTY" na ho — matlab abhi ka best
#    choice FUTURE options ko WORSE bana sakta hai. Example: Coin Change
#    with coins=[1,3,4], amount=6 — Greedy (4+1+1=3 coins) FAILS,
#    optimal hai (3+3=2 coins). DP saari possibilities explore karta hai
#    isliye correct rehta hai.
#
# Q: Activity Selection me hum END TIME se sort karte hain, START TIME se kyun nahi?
# A: START TIME se sort karne se PEHLE shuru hone wali activity SELECT ho
#    jayegi, chahe wo BOHAT DER tak chale (lambi activity future options
#    ko BLOCK kar degi). END TIME se sort karna ensure karta hai ke hum
#    HAMESHA wo activity choose karein jo SABSE JALDI khatam ho — taake
#    REMAINING TIME me MAXIMUM future activities fit ho sakein.
#
# Q: Dijkstra's Algorithm Greedy hai ya DP?
# A: Dijkstra GREEDY hai — har step pe "currently SHORTEST KNOWN distance
#    wala node" choose karta hai aur isay FINAL maan leta hai (kabhi
#    reconsider nahi karta). Ye CORRECT hai sirf NON-NEGATIVE weights ke
#    liye (Greedy Choice Property hold karti hai). Negative weights me
#    Greedy assumption TOOT jati hai, isliye Bellman-Ford (DP-based) use
#    karte hain — [[../11_graphs/graphs_bfs_dfs.py]] me detail hai.
