# ============================================================================
#  LINKED LIST — Singly, Doubly, Circular
# ============================================================================

# ----------------------------------------------------------------------------
# 1) Linked List kya hai?
# ----------------------------------------------------------------------------
# Elements ("nodes") ka collection jo CONTIGUOUS memory me NAHI hote — har
# node apne DATA ke saath NEXT node ka POINTER/REFERENCE rakhta hai.
#
# KYUN ZARURI: Array ke "insert/delete at beginning = O(n)" problem ko solve
# karta hai (O(1) ho jata hai), trade-off ye hai ke INDEX se direct access
# (O(1)) khatam ho jata hai (O(n) ban jata hai, traverse karna padta hai).
#
# REAL-WORLD USE: Browser history (back/forward), Music playlist (next/prev
# song), Undo/Redo functionality, OS me process scheduling (circular list),
# Implementation of Stack/Queue/Hash Table (chaining) ke andar bhi use hota hai.

# ----------------------------------------------------------------------------
# 2) SINGLY Linked List — har node sirf NEXT ko point karta hai
# ----------------------------------------------------------------------------
class Node:
    def __init__(self, data):
        self.data = data
        self.next = None

class SinglyLinkedList:
    def __init__(self):
        self.head = None

    def append(self, data):                    # O(n) — end tak traverse karna padta hai
        new_node = Node(data)
        if not self.head:
            self.head = new_node
            return
        current = self.head
        while current.next:
            current = current.next
        current.next = new_node

    def prepend(self, data):                    # O(1) — sirf head badalna hai
        new_node = Node(data)
        new_node.next = self.head
        self.head = new_node

    def delete(self, key):                       # O(n) — node dhoondhna padta hai
        current = self.head
        if current and current.data == key:
            self.head = current.next
            return
        prev = None
        while current and current.data != key:
            prev = current
            current = current.next
        if current is None:
            return                                 # key nahi mili
        prev.next = current.next                    # node ko "bypass" kar diya

    def search(self, key):                            # O(n)
        current = self.head
        while current:
            if current.data == key:
                return True
            current = current.next
        return False

    def reverse(self):                                  # O(n) time, O(1) space — CLASSIC interview Q
        prev = None
        current = self.head
        while current:
            next_node = current.next     # agla node save kar lo (warna kho jayega)
            current.next = prev          # current ka direction REVERSE kar do
            prev = current                 # prev ko aage badhao
            current = next_node             # current ko aage badhao
        self.head = prev

    def to_list(self):
        result = []
        current = self.head
        while current:
            result.append(current.data)
            current = current.next
        return result

ll = SinglyLinkedList()
ll.append(1); ll.append(2); ll.append(3)
print(ll.to_list())          # [1, 2, 3]
ll.reverse()
print(ll.to_list())          # [3, 2, 1]

# ----------------------------------------------------------------------------
# 3) Array vs Linked List — kab kya use karo
# ----------------------------------------------------------------------------
# | Operation                | Array | Linked List |
# |-----------------------------|---------|----------------|
# | Access by index               | O(1)    | O(n)           |
# | Insert/Delete at START           | O(n)    | O(1)           |
# | Insert/Delete at END (with tail ptr)| O(1)*   | O(1)           |
# | Insert/Delete at MIDDLE             | O(n)    | O(n) (search) + O(1) (link) |
# | Memory                                | Compact, cache-friendly | Extra pointer overhead, scattered |
#
# RULE: Frequent random ACCESS chahiye -> Array. Frequent INSERT/DELETE at
# beginning/middle chahiye (aur sequential access kaafi hai) -> Linked List.

# ----------------------------------------------------------------------------
# 4) DOUBLY Linked List — har node PREV aur NEXT dono rakhta hai
# ----------------------------------------------------------------------------
class DNode:
    def __init__(self, data):
        self.data = data
        self.prev = None
        self.next = None

class DoublyLinkedList:
    def __init__(self):
        self.head = None
        self.tail = None

    def append(self, data):                # O(1) — tail pointer hone ki wajah se
        new_node = DNode(data)
        if not self.head:
            self.head = self.tail = new_node
            return
        new_node.prev = self.tail
        self.tail.next = new_node
        self.tail = new_node

    def delete_node(self, node):            # O(1) — agar node reference pehle se hai
        if node.prev:
            node.prev.next = node.next
        else:
            self.head = node.next
        if node.next:
            node.next.prev = node.prev
        else:
            self.tail = node.prev

# REAL USE-CASE: Browser history (BACK = prev, FORWARD = next), LRU Cache
# implementation (O(1) delete kisi bhi node ko, jab usko recently-used mark karna ho)
# Python ka built-in `collections.deque` internally DOUBLY linked list jaisा hai

# ----------------------------------------------------------------------------
# 5) CIRCULAR Linked List — last node FIRST ko point karta hai (loop ban jata hai)
# ----------------------------------------------------------------------------
class CircularLinkedList:
    def __init__(self):
        self.head = None

    def append(self, data):
        new_node = Node(data)
        if not self.head:
            self.head = new_node
            new_node.next = self.head     # khud ko hi point kare (single node loop)
            return
        current = self.head
        while current.next != self.head:
            current = current.next
        current.next = new_node
        new_node.next = self.head          # loop complete

# REAL USE-CASE: Round-robin CPU scheduling (process queue, last process se
# wapis first pe jana), Multiplayer game turn system, Circular Buffer
# (streaming data, fixed-size queue jo overwrite hoti rehti hai)

# ----------------------------------------------------------------------------
# 6) FLOYD'S CYCLE DETECTION (Tortoise & Hare) — CLASSIC interview question
# ----------------------------------------------------------------------------
# "Pata karo linked list me CYCLE hai ya nahi" — bina extra memory use kiye (O(1) space)
def has_cycle(head):
    slow = fast = head
    while fast and fast.next:
        slow = slow.next               # 1 step
        fast = fast.next.next           # 2 steps
        if slow == fast:                 # mil gaye -> CYCLE hai
            return True
    return False
# LOGIC: agar cycle hai, fast pointer slow se 2x speed se chal kar
# EVENTUALLY usse TAKRAYEGA (jaise circular track pe do runners). Agar
# cycle nahi hai, fast pointer NULL tak pohanch jayega (list khatam).

# Find the START of the cycle (advanced follow-up question):
def find_cycle_start(head):
    slow = fast = head
    while fast and fast.next:
        slow = slow.next
        fast = fast.next.next
        if slow == fast:
            break
    else:
        return None              # cycle nahi hai
    slow = head
    while slow != fast:           # dono ab SAME speed se chalo
        slow = slow.next
        fast = fast.next
    return slow                    # yehi cycle ka START point hai
# PROOF (math): distance head-se-cycle-start = distance meeting-point-se-cycle-start
# (Floyd's algorithm ki mathematical property)

# ----------------------------------------------------------------------------
# 7) Find MIDDLE of Linked List (slow/fast pointer pattern, ek hi pass me)
# ----------------------------------------------------------------------------
def find_middle(head):
    slow = fast = head
    while fast and fast.next:
        slow = slow.next
        fast = fast.next.next
    return slow                     # jab fast end tak pohanche, slow middle pe hoga

# ----------------------------------------------------------------------------
# 8) Merge Two Sorted Linked Lists (Merge Sort ka building block)
# ----------------------------------------------------------------------------
def merge_two_sorted(l1, l2):
    dummy = Node(0)
    tail = dummy
    while l1 and l2:
        if l1.data <= l2.data:
            tail.next = l1
            l1 = l1.next
        else:
            tail.next = l2
            l2 = l2.next
        tail = tail.next
    tail.next = l1 if l1 else l2
    return dummy.next

# ============================================================================
# INTERVIEW Q&A
# ============================================================================
# Q: Linked List reverse karne ka time/space complexity?
# A: Time O(n) (har node ek baar visit), Space O(1) iterative (sirf 3
#    pointers: prev, current, next). Recursive approach bhi possible hai
#    lekin O(n) space leta hai (call stack ki wajah se).
#
# Q: Cycle detect karne ka SABSE EFFICIENT tareeqa (space ke hisab se)?
# A: Floyd's Cycle Detection (Tortoise & Hare) — O(n) time, O(1) space.
#    Alternative (HashSet me visited nodes store karna) bhi kaam karta hai
#    lekin O(n) EXTRA SPACE leta hai — interview me space-optimal solution
#    expect hoti hai.
#
# Q: Singly vs Doubly Linked List — trade-off?
# A: Doubly me BACKWARD traversal aur O(1) delete (agar node reference hai)
#    possible hai, lekin HAR NODE me EXTRA POINTER (prev) ki wajah se zyada
#    memory lagti hai aur insert/delete me thora zyada pointer updates
#    karne padte hain.
#
# Q: LRU Cache kaise implement karoge?
# A: HashMap (O(1) lookup) + Doubly Linked List (O(1) add/remove from
#    front/back) ka combination. HashMap key -> node reference store karta
#    hai. Recently used node ko list ke FRONT pe move karo, capacity exceed
#    hone par list ke END (least recently used) wala node remove karo —
#    dono operations O(1) hote hain is combination ki wajah se.
