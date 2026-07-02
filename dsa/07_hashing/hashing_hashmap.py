# ============================================================================
#  HASHING / HASH TABLE / HASH MAP — Sabse zyada REAL-WORLD use hone wala DSA topic
# ============================================================================

# ----------------------------------------------------------------------------
# 1) Hashing kya hai?
# ----------------------------------------------------------------------------
# Hash Table ek data structure hai jo KEY ko ek "HASH FUNCTION" se ek INDEX
# (array position) me convert karta hai, taake VALUE ko us index pe O(1)
# average time me STORE/RETRIEVE kiya ja sake.
#
# Python me: dict aur set, dono HASH TABLE hain (internally).
#
# KYUN SABSE ZYADA IMPORTANT: Array/List me "search" O(n) hai. Hash Table
# isay O(1) AVERAGE bana deta hai — EXTRA MEMORY (space) ke trade-off pe
# TIME bachata hai (Space-Time Tradeoff, [[../01_complexity_analysis]]).
#
# REAL-WORLD: Database INDEXES (kuch types hash-based hote hain), CACHING
# (Redis literally ek distributed hash map hai), Duplicate detection,
# Counting frequencies, Two-Sum jaisi problems, Session storage, Compiler
# symbol tables, DNS lookup, Password storage (hashing — alag concept,
# security context me).

# ----------------------------------------------------------------------------
# 2) HASH FUNCTION kaise kaam karta hai (internals)
# ----------------------------------------------------------------------------
# Hash Function: KEY (kisi bhi type) -> FIXED-SIZE INTEGER (hash value)
# Phir: index = hash(key) % array_size
#
# GOOD hash function ki properties:
# - DETERMINISTIC: same key hamesha SAME hash deta hai
# - UNIFORM DISTRIBUTION: keys ko array me EVENLY spread kare (clustering avoid)
# - FAST to compute
print(hash("Ali"))          # Python ka built-in hash function (string ke liye)
print(hash(42))              # integers apna value hi hash hote hain (mostly)

# Simple custom hash function example (samajhne ke liye):
def simple_hash(key, table_size):
    return sum(ord(char) for char in key) % table_size

print(simple_hash("Ali", 10))    # konsa index milega 10-size table me

# ----------------------------------------------------------------------------
# 3) COLLISION — Jab 2 different keys ka SAME hash/index aa jaye
# ----------------------------------------------------------------------------
# Pigeon-hole principle: agar keys > array size, COLLISION ZARUR hoga
# (kabhi na kabhi). Hash table ko collision HANDLE karna padta hai.

# Method A: CHAINING (sabse common — Python dict bhi originally isi se
# inspired tha pehle, ab open addressing use karta hai internally)
class HashTableChaining:
    def __init__(self, size=10):
        self.size = size
        self.table = [[] for _ in range(size)]    # har slot ek LIST (chain)

    def _hash(self, key):
        return hash(key) % self.size

    def put(self, key, value):
        index = self._hash(key)
        bucket = self.table[index]
        for i, (k, v) in enumerate(bucket):
            if k == key:
                bucket[i] = (key, value)    # existing key update
                return
        bucket.append((key, value))          # naya key-value pair

    def get(self, key):
        index = self._hash(key)
        bucket = self.table[index]
        for k, v in bucket:
            if k == key:
                return v
        raise KeyError(key)

    def delete(self, key):
        index = self._hash(key)
        bucket = self.table[index]
        for i, (k, v) in enumerate(bucket):
            if k == key:
                del bucket[i]
                return
        raise KeyError(key)

ht = HashTableChaining()
ht.put("name", "Ali")
ht.put("age", 30)
print(ht.get("name"))   # Ali
# Agar 2 keys same index pe collide karein, dono SAME bucket (list) me
# store ho jate hain — lookup time worst case O(n) ban sakta hai agar
# saari keys EK HI bucket me chali jayein (BAD hash function ki nishani)

# Method B: OPEN ADDRESSING (Linear Probing) — collision pe AGLA khali slot dhoondo
class HashTableOpenAddressing:
    def __init__(self, size=10):
        self.size = size
        self.keys = [None] * size
        self.values = [None] * size

    def _hash(self, key):
        return hash(key) % self.size

    def put(self, key, value):
        index = self._hash(key)
        while self.keys[index] is not None and self.keys[index] != key:
            index = (index + 1) % self.size      # AGLA slot try karo (linear probing)
        self.keys[index] = key
        self.values[index] = value

    def get(self, key):
        index = self._hash(key)
        start = index
        while self.keys[index] is not None:
            if self.keys[index] == key:
                return self.values[index]
            index = (index + 1) % self.size
            if index == start:                     # poora table ghoom liya
                break
        raise KeyError(key)

# ----------------------------------------------------------------------------
# 4) LOAD FACTOR & RESIZING (interview ke liye important)
# ----------------------------------------------------------------------------
# Load Factor = number_of_elements / table_size
# Jab load factor THRESHOLD (typically 0.7) cross kare, table RESIZE hota
# hai (naya bada array, saare elements REHASH hote hain — O(n) one-time cost)
# Isi wajah se hash table operations "AMORTIZED O(1)" kehlate hain, na ke
# strictly O(1) — Python dict bhi yehi karta hai internally.

# ----------------------------------------------------------------------------
# 5) Time Complexity Summary
# ----------------------------------------------------------------------------
# | Operation | Average Case | Worst Case (saari keys collide — bohat rare)|
# |-------------|-----------------|---------------------------------------------|
# | Insert        | O(1)             | O(n)                                          |
# | Search          | O(1)             | O(n)                                          |
# | Delete            | O(1)             | O(n)                                          |
#
# Worst case TABHI hota hai jab Hash Function bohat KHARAB ho (saari keys
# ek hi bucket me ja rahi hon) — production-grade hash functions (Python's
# built-in) is risk ko bohat MINIMIZE karte hain.

# ----------------------------------------------------------------------------
# 6) Python dict & set — PRODUCTION me ye hi use karo, manual mat banao
# ----------------------------------------------------------------------------
d = {"name": "Ali", "age": 30}
d["city"] = "Lahore"           # O(1) insert
print(d["name"])                # O(1) lookup
print("name" in d)               # O(1) — "in" check bhi O(1) hai (list me O(n) hota)
del d["age"]                      # O(1) delete

s = {1, 2, 3, 3, 2}               # set -> automatically DUPLICATES remove
print(s)                           # {1, 2, 3}

# ----------------------------------------------------------------------------
# 7) REAL-WORLD USE CASE 1: Two Sum (CLASSIC — hashing se O(n²) -> O(n))
# ----------------------------------------------------------------------------
def two_sum(nums, target):
    seen = {}                                  # value -> index
    for i, num in enumerate(nums):
        complement = target - num
        if complement in seen:                  # O(1) lookup!
            return [seen[complement], i]
        seen[num] = i
    return []

print(two_sum([2, 7, 11, 15], 9))   # [0, 1] (2+7=9)
# NAIVE approach: nested loop O(n²). HASHING approach: single pass O(n) —
# kyunki "complement exist karta hai?" check O(n) se O(1) ban jata hai

# ----------------------------------------------------------------------------
# 8) REAL-WORLD USE CASE 2: Duplicate Detection
# ----------------------------------------------------------------------------
def has_duplicates(arr):
    return len(arr) != len(set(arr))    # O(n) time, O(n) space

print(has_duplicates([1, 2, 3, 2]))   # True

# ----------------------------------------------------------------------------
# 9) REAL-WORLD USE CASE 3: Frequency Counting (bohat common pattern)
# ----------------------------------------------------------------------------
from collections import Counter

def most_frequent_word(text):
    words = text.lower().split()
    counts = Counter(words)               # O(n) — har word ki frequency
    return counts.most_common(1)[0]

print(most_frequent_word("the cat sat on the mat the cat ran"))
# ('the', 3)

# ----------------------------------------------------------------------------
# 10) REAL-WORLD USE CASE 4: Anagram Grouping
# ----------------------------------------------------------------------------
def group_anagrams(words):
    groups = {}
    for word in words:
        key = "".join(sorted(word))         # "eat" -> "aet" (sorted letters = key)
        groups.setdefault(key, []).append(word)
    return list(groups.values())

print(group_anagrams(["eat", "tea", "tan", "ate", "nat", "bat"]))
# [['eat', 'tea', 'ate'], ['tan', 'nat'], ['bat']]

# ----------------------------------------------------------------------------
# 11) REAL-WORLD USE CASE 5: Caching (Memoization — DP ka core, detail: 12_dynamic_programming)
# ----------------------------------------------------------------------------
def fibonacci_memoized(n, cache={}):
    if n in cache:
        return cache[n]                      # O(1) — already calculated, REUSE karo
    if n <= 1:
        return n
    cache[n] = fibonacci_memoized(n - 1, cache) + fibonacci_memoized(n - 2, cache)
    return cache[n]

print(fibonacci_memoized(50))   # Naive recursion practically hang ho jata, ye INSTANT hai

# ============================================================================
# INTERVIEW Q&A
# ============================================================================
# Q: Hash Table ki average complexity O(1) hai to worst case O(n) kyun ho
#    sakta hai?
# A: Agar HASH FUNCTION kharab ho aur SAARI keys SAME bucket me collide kar
#    jayein, to wo bucket effectively ek LINKED LIST/ARRAY ban jata hai —
#    search O(n) ho jata hai us bucket ke andar. GOOD hash function +
#    proper LOAD FACTOR maintenance is risk ko practically negligible bana
#    deta hai.
#
# Q: Chaining vs Open Addressing — trade-off?
# A: Chaining: implement karna simple, load factor 1 se zyada bhi ho sakta
#    hai (bucket me list grow karti rehti hai), lekin EXTRA POINTER memory
#    overhead. Open Addressing: better CACHE PERFORMANCE (sab same array
#    me), lekin load factor 1 se zyada nahi ho sakta, aur CLUSTERING
#    (consecutive collisions) problem create kar sakta hai (linear probing me).
#
# Q: Hash Table me KEY kya properties honi chahiye (Python dict ke context me)?
# A: Key HASHABLE honi chahiye — matlab IMMUTABLE (string, int, tuple of
#    immutables). List/Dict KEY nahi ban sakte (mutable hain, agar change
#    ho jayein to hash bhi change ho jayega aur lookup BREAK ho jayega) —
#    isliye Python "unhashable type: 'list'" error deta hai.
#
# Q: Jab tumhe O(n²) brute-force solution mile, kaise pehchanoge ke
#    HASHING se optimize ho sakta hai?
# A: Agar problem me "kya ye element/complement/pair EXIST karta hai"
#    jaisa REPEATED LOOKUP ho rahi hai nested loop ke andar — to almost
#    hamesha ek HASH SET/MAP se ek loop me convert ho sakta hai (Two Sum
#    jaisa pattern). Ye DSA ka sabse common optimization trick hai.
