# ============================================================================
#  TREES — Binary Tree, BST, Traversals, Balanced Trees, Trie
# ============================================================================

# ----------------------------------------------------------------------------
# 1) Tree kya hai?
# ----------------------------------------------------------------------------
# HIERARCHICAL data structure — ek ROOT node, jiske CHILDREN hote hain, jinke
# aage apne CHILDREN hote hain (Linked List ki tarah, lekin ek node ke
# MULTIPLE "next" ho sakte hain). NO CYCLES (warna Graph ban jata hai).
#
# KYUN ZARURI: REAL-WORLD hierarchical data NATURALLY tree hota hai — File
# systems (folders/files), DOM (HTML structure), Org charts, Database
# Indexes (B+Tree — [[../../DB/07_indexes]]), Decision Trees (ML),
# Autocomplete (Trie), JSON/XML nested structures.

# ----------------------------------------------------------------------------
# 2) Binary Tree — har node ke MAXIMUM 2 children (left, right)
# ----------------------------------------------------------------------------
class TreeNode:
    def __init__(self, val):
        self.val = val
        self.left = None
        self.right = None

#         1
#       /   \
#      2     3
#     / \
#    4   5
root = TreeNode(1)
root.left = TreeNode(2)
root.right = TreeNode(3)
root.left.left = TreeNode(4)
root.left.right = TreeNode(5)

# ----------------------------------------------------------------------------
# 3) TRAVERSALS — Tree ke saare nodes visit karne ke 4 standard tareeqe
# ----------------------------------------------------------------------------

# a) PRE-ORDER (Root -> Left -> Right) — tree ko COPY/SERIALIZE karne ke liye useful
def preorder(node, result=None):
    if result is None: result = []
    if node:
        result.append(node.val)
        preorder(node.left, result)
        preorder(node.right, result)
    return result
print("Preorder:", preorder(root))    # [1, 2, 4, 5, 3]

# b) IN-ORDER (Left -> Root -> Right) — BST me ye SORTED ORDER deta hai!
def inorder(node, result=None):
    if result is None: result = []
    if node:
        inorder(node.left, result)
        result.append(node.val)
        inorder(node.right, result)
    return result
print("Inorder:", inorder(root))      # [4, 2, 5, 1, 3]

# c) POST-ORDER (Left -> Right -> Root) — tree DELETE karne ke liye useful
#    (pehle children delete karo, phir parent — warna reference lost ho jayega)
def postorder(node, result=None):
    if result is None: result = []
    if node:
        postorder(node.left, result)
        postorder(node.right, result)
        result.append(node.val)
    return result
print("Postorder:", postorder(root))   # [4, 5, 2, 3, 1]

# d) LEVEL-ORDER (BFS — level by level, QUEUE use hoti hai)
from collections import deque
def level_order(root):
    if not root:
        return []
    result, queue = [], deque([root])
    while queue:
        node = queue.popleft()
        result.append(node.val)
        if node.left: queue.append(node.left)
        if node.right: queue.append(node.right)
    return result
print("Level-order:", level_order(root))   # [1, 2, 3, 4, 5]

# ----------------------------------------------------------------------------
# 4) BINARY SEARCH TREE (BST) — Ordering property ke sath Binary Tree
# ----------------------------------------------------------------------------
# RULE: Har node ke liye -> LEFT subtree ke saare values < node.val
#                         -> RIGHT subtree ke saare values > node.val
#
# YEHI property hai jo SEARCH ko O(log n) (balanced tree me) banati hai —
# bilkul Binary Search jaisa, lekin LINKED structure pe.

class BST:
    def __init__(self):
        self.root = None

    def insert(self, val):                       # O(log n) avg, O(n) worst (skewed tree)
        self.root = self._insert(self.root, val)

    def _insert(self, node, val):
        if not node:
            return TreeNode(val)
        if val < node.val:
            node.left = self._insert(node.left, val)
        else:
            node.right = self._insert(node.right, val)
        return node

    def search(self, val):                         # O(log n) avg, O(n) worst
        return self._search(self.root, val)

    def _search(self, node, val):
        if not node or node.val == val:
            return node is not None
        if val < node.val:
            return self._search(node.left, val)
        return self._search(node.right, val)

bst = BST()
for v in [50, 30, 70, 20, 40, 60, 80]:
    bst.insert(v)
print(bst.search(40))   # True
print(inorder(bst.root))   # [20, 30, 40, 50, 60, 70, 80] -> SORTED! (in-order property)

# ⚠️ WORST CASE PROBLEM: Agar SORTED data insert karo (1,2,3,4,5...), BST
# ek SKEWED LINKED LIST ban jata hai -> O(n) operations (BALANCE khatam)!
# Isi problem ko solve karne ke liye SELF-BALANCING trees bane (neeche).

# ----------------------------------------------------------------------------
# 5) SELF-BALANCING Trees — AVL Tree, Red-Black Tree (CONCEPT level — interview theory)
# ----------------------------------------------------------------------------
# Problem: Plain BST WORST CASE me O(n) ho sakta hai (skewed). Self-balancing
# trees HAR insert/delete ke baad khud ko REBALANCE karte hain (rotations
# se) taake height HAMESHA O(log n) guaranteed rahe.
#
# AVL Tree: STRICTLY balanced (har node ke left/right subtree height ka
#   farq max 1 hota hai). Lookups FASTER, lekin insert/delete me zyada
#   rotations (STRICTER balancing) — read-heavy use case ke liye behtar.
#
# Red-Black Tree: LOOSELY balanced (color rules se balance maintain karta
#   hai). Insert/delete FASTER (kam rotations), lookups thori slower AVL se.
#   Java TreeMap, C++ std::map, Linux kernel scheduler isay use karte hain.
#
# B-Tree / B+Tree: Disk-based databases ke liye (MySQL InnoDB) — node me
#   MULTIPLE keys/children hote hain (binary nahi), taake DISK I/O kam ho.
#   [[../../DB/07_indexes/indexes_internals.sql]] me FULL DETAIL hai.
#
# REAL-WORLD: Python ka dict/set RED-BLACK ya AVL nahi use karta (HASH
# TABLE hai), lekin Java TreeMap, C++ map/set INTERNALLY Red-Black Tree
# hote hain (SORTED order maintain karne ke liye, O(log n) guaranteed).

# ----------------------------------------------------------------------------
# 6) Common Tree Problems (Interview Classics)
# ----------------------------------------------------------------------------

# a) Height/Depth of Tree
def tree_height(node):
    if not node:
        return 0
    return 1 + max(tree_height(node.left), tree_height(node.right))
print("Height:", tree_height(root))   # 3

# b) Check if tree is BALANCED (har node pe left/right height farq <= 1)
def is_balanced(node):
    def check(n):
        if not n:
            return 0
        left = check(n.left)
        if left == -1: return -1
        right = check(n.right)
        if right == -1: return -1
        if abs(left - right) > 1:
            return -1               # unbalanced signal
        return 1 + max(left, right)
    return check(node) != -1

# c) Lowest Common Ancestor (LCA) in BST
def lca_bst(node, p, q):
    if p < node.val and q < node.val:
        return lca_bst(node.left, p, q)
    elif p > node.val and q > node.val:
        return lca_bst(node.right, p, q)
    else:
        return node.val             # split point = LCA
print("LCA:", lca_bst(bst.root, 20, 40))   # 30

# d) Validate BST (CLASSIC GOTCHA — sirf parent se compare karna KAAFI NAHI)
def is_valid_bst(node, low=float('-inf'), high=float('inf')):
    if not node:
        return True
    if not (low < node.val < high):
        return False
    return (is_valid_bst(node.left, low, node.val) and
            is_valid_bst(node.right, node.val, high))
# ⚠️ GALTI: sirf "node.left.val < node.val < node.right.val" check karna
# GALAT hai — RIGHT subtree ka HAR node parent se bada hona chahiye, sirf
# IMMEDIATE child nahi. Isliye range (low, high) PASS karna padta hai.

# e) Diameter of Tree (longest path between any 2 nodes)
def diameter(node):
    result = [0]
    def depth(n):
        if not n:
            return 0
        left = depth(n.left)
        right = depth(n.right)
        result[0] = max(result[0], left + right)    # is node se guzarne wala longest path
        return 1 + max(left, right)
    depth(node)
    return result[0]

# ----------------------------------------------------------------------------
# 7) TRIE (Prefix Tree) — STRING-specific tree, Autocomplete ka core
# ----------------------------------------------------------------------------
# Har node ek CHARACTER represent karta hai, ROOT se LEAF tak ka path ek
# WORD banata hai. Common PREFIXES SHARE hote hain (memory efficient for
# many similar strings).
#
# REAL-WORLD: Autocomplete/Search suggestions (Google search box), Spell
# checkers, IP routing (longest prefix match), Word games (Boggle)

class TrieNode:
    def __init__(self):
        self.children = {}            # char -> TrieNode
        self.is_end_of_word = False

class Trie:
    def __init__(self):
        self.root = TrieNode()

    def insert(self, word):              # O(L), L = word length
        node = self.root
        for char in word:
            if char not in node.children:
                node.children[char] = TrieNode()
            node = node.children[char]
        node.is_end_of_word = True

    def search(self, word):               # O(L) — EXACT word exist karta hai?
        node = self._find_node(word)
        return node is not None and node.is_end_of_word

    def starts_with(self, prefix):          # O(L) — AUTOCOMPLETE ke liye
        return self._find_node(prefix) is not None

    def _find_node(self, prefix):
        node = self.root
        for char in prefix:
            if char not in node.children:
                return None
            node = node.children[char]
        return node

trie = Trie()
for word in ["car", "card", "care", "dog"]:
    trie.insert(word)
print(trie.search("car"))         # True
print(trie.search("ca"))           # False (exact word nahi hai)
print(trie.starts_with("ca"))       # True (prefix match — autocomplete ke liye)

# ----------------------------------------------------------------------------
# 8) Time Complexity Summary
# ----------------------------------------------------------------------------
# | Operation        | Balanced BST | Unbalanced BST (worst) | Trie (word length L) |
# |---------------------|------------------|----------------------------|----------------------------|
# | Search                | O(log n)         | O(n)                          | O(L)                        |
# | Insert                  | O(log n)         | O(n)                          | O(L)                        |
# | Delete                    | O(log n)         | O(n)                          | O(L)                        |

# ============================================================================
# INTERVIEW Q&A
# ============================================================================
# Q: BST aur Binary Tree me farq?
# A: Binary Tree: sirf "max 2 children" rule hota hai, koi ORDERING nahi.
#    BST: Binary Tree + ORDERING property (left < node < right) — ISI
#    PROPERTY ki wajah se EFFICIENT search possible hota hai.
#
# Q: In-order traversal BST pe SORTED result kyun deta hai?
# A: In-order = Left -> Root -> Right. BST property hai LEFT < Root < RIGHT.
#    Isliye recursively LEFT (chhote values) PEHLE visit honge, phir Root,
#    phir RIGHT (bade values) — natural ASCENDING order ban jata hai.
#
# Q: BST WORST CASE me O(n) kyun ho jata hai?
# A: Agar SORTED ya REVERSE-SORTED data insert karo, har naya node sirf
#    EK SIDE (right ya left) me jata hai — tree ek LINKED LIST jaisi
#    SKEWED shape ban jati hai, height = n (log n nahi rehti). Isiliye
#    PRODUCTION systems Self-Balancing trees (AVL/Red-Black/B-Tree) use
#    karte hain, plain BST nahi.
#
# Q: Trie kab use karoge, Hash Set kab?
# A: Sirf "word EXIST karta hai?" check karna ho -> Hash Set O(1) average,
#    SIMPLER. Lekin "PREFIX se shuru hone wale saare words" (autocomplete)
#    chahiye ho -> Trie BEHTAR hai (Hash Set me ye efficiently possible
#    nahi, har prefix ke liye saari keys scan karni padengi). Trie SHARED
#    prefixes ki wajah se memory bhi save karta hai jab bohat saare
#    similar words hon.
