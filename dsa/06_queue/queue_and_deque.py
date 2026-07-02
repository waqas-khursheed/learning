# ============================================================================
#  QUEUE — FIFO (First In, First Out) + Deque + Priority Queue
# ============================================================================

# ----------------------------------------------------------------------------
# 1) Queue kya hai?
# ----------------------------------------------------------------------------
# Jaisa LINE me lagna (bank, ticket counter) — jo PEHLE aaya, wo PEHLE jayega
# (FIFO). Do ends: REAR (insert/enqueue) aur FRONT (remove/dequeue).
#
# KYUN ZARURI: Jahan bhi "ORDER preserve karna hai PROCESSING ke liye" wahan
# Queue use hota hai. BFS (Breadth-First Search) ka core hai (detail: 11_graphs).
#
# REAL-WORLD: Task scheduling (OS process queue), Print queue, Message
# queues (RabbitMQ/SQS — production systems me bohat common), Customer
# support ticket system, Rate limiting, Web server request handling.

# ----------------------------------------------------------------------------
# 2) ⚠️ Python list se Queue MAT banao — deque() use karo
# ----------------------------------------------------------------------------
# Python list.pop(0) -> O(n) hai (saare elements LEFT shift hote hain)!
# Production me ye SILENT PERFORMANCE BUG ban jata hai bade data pe.
from collections import deque

queue = deque()
queue.append(1)          # O(1) — enqueue (rear se add)
queue.append(2)
queue.append(3)
print(queue.popleft())   # O(1) — dequeue (front se remove) -> 1
print(list(queue))        # [2, 3]

# deque = "Double-Ended Queue" — DONO ends se O(1) add/remove kar sakte ho
# (isi wajah se Queue AUR Stack DONO ke liye use ho sakta hai)
queue.appendleft(0)       # O(1) — front se add
queue.pop()                # O(1) — rear se remove
print(list(queue))          # [0, 2]

# ----------------------------------------------------------------------------
# 3) Manual Queue implementation (CONCEPT samajhne ke liye — production me deque use karo)
# ----------------------------------------------------------------------------
class Queue:
    def __init__(self):
        self._items = deque()

    def enqueue(self, item):       # O(1)
        self._items.append(item)

    def dequeue(self):              # O(1)
        if self.is_empty():
            raise IndexError("dequeue from empty queue")
        return self._items.popleft()

    def peek(self):
        return self._items[0]

    def is_empty(self):
        return len(self._items) == 0

# ----------------------------------------------------------------------------
# 4) CIRCULAR Queue — Fixed-size buffer, "wrap around" hota hai
# ----------------------------------------------------------------------------
# Use case: Streaming data (audio/video buffer), CPU scheduling (round-robin)
class CircularQueue:
    def __init__(self, capacity):
        self.capacity = capacity
        self.queue = [None] * capacity
        self.front = self.rear = -1

    def enqueue(self, item):
        if (self.rear + 1) % self.capacity == self.front:
            raise OverflowError("Queue is full")
        if self.front == -1:
            self.front = 0
        self.rear = (self.rear + 1) % self.capacity
        self.queue[self.rear] = item

    def dequeue(self):
        if self.front == -1:
            raise IndexError("Queue is empty")
        item = self.queue[self.front]
        if self.front == self.rear:           # last element tha
            self.front = self.rear = -1
        else:
            self.front = (self.front + 1) % self.capacity
        return item

# ----------------------------------------------------------------------------
# 5) PRIORITY QUEUE — FIFO nahi, "highest priority pehle" (heap se implement hota hai)
# ----------------------------------------------------------------------------
# Detail: 10_heaps_priority_queue/ — yahan sirf USE-CASE perspective se:
import heapq

pq = []
heapq.heappush(pq, (2, "medium priority task"))
heapq.heappush(pq, (1, "high priority task"))     # chhota number = high priority (min-heap default)
heapq.heappush(pq, (3, "low priority task"))
print(heapq.heappop(pq))    # (1, 'high priority task') — sabse pehle nikalta hai

# REAL-WORLD: OS task scheduler (priority-based), Dijkstra's algorithm
# (shortest path — hamesha sabse kam-distance node process karo pehle),
# Hospital emergency room (severity ke hisab se), Event-driven simulation

# ----------------------------------------------------------------------------
# 6) REAL-WORLD USE CASE: BFS (Breadth-First Search) — Queue ka SABSE BADA use
# ----------------------------------------------------------------------------
def bfs_level_order(graph, start):
    visited = {start}
    queue = deque([start])
    result = []
    while queue:
        node = queue.popleft()             # FIFO -> level-by-level guarantee
        result.append(node)
        for neighbor in graph.get(node, []):
            if neighbor not in visited:
                visited.add(neighbor)
                queue.append(neighbor)
    return result

graph = {1: [2, 3], 2: [4], 3: [4], 4: []}
print(bfs_level_order(graph, 1))    # [1, 2, 3, 4]
# QUEUE ki wajah se BFS "level by level" guarantee karta hai — Stack use
# karte to DFS ban jata (depth-first, level order nahi)

# ----------------------------------------------------------------------------
# 7) REAL-WORLD USE CASE: Task Scheduling / Message Queue Simulation
# ----------------------------------------------------------------------------
class TaskScheduler:
    def __init__(self):
        self.queue = deque()

    def add_task(self, task):
        self.queue.append(task)
        print(f"Task added: {task}")

    def process_next(self):
        if self.queue:
            task = self.queue.popleft()
            print(f"Processing: {task}")
            return task
        print("No tasks pending")

scheduler = TaskScheduler()
scheduler.add_task("Send email")
scheduler.add_task("Generate report")
scheduler.process_next()      # "Send email" pehle process hoga (FIFO)
# Production me ye CONCEPT RabbitMQ/AWS SQS/Redis Queue jaisi cheezon ka
# foundation hai — distributed systems me workers queue se task UTHATE hain

# ----------------------------------------------------------------------------
# 8) Queue using TWO Stacks (CLASSIC interview design question)
# ----------------------------------------------------------------------------
class QueueUsingStacks:
    def __init__(self):
        self.stack_in = []
        self.stack_out = []

    def enqueue(self, item):                 # O(1)
        self.stack_in.append(item)

    def dequeue(self):                        # Amortized O(1)
        if not self.stack_out:
            while self.stack_in:               # SAARA stack_in REVERSE karke stack_out me daalo
                self.stack_out.append(self.stack_in.pop())
        return self.stack_out.pop()
# TRICK: stack_in me LIFO order hai, ek baar reverse karke stack_out me
# daalne se wo FIFO order me ban jata hai. Har element MAXIMUM 2 baar move
# hota hai (in -> out), isliye AMORTIZED O(1) per operation.

# ============================================================================
# INTERVIEW Q&A
# ============================================================================
# Q: Python me list.pop(0) se Queue kyun nahi banani chahiye?
# A: list.pop(0) O(n) hai — Python list contiguous array hai, front se
#    remove karne par BAAQI saare elements ek position LEFT shift karne
#    padte hain. collections.deque INTERNALLY DOUBLY LINKED LIST jaisi
#    structure hai (blocks ka), jahan dono ends se O(1) operation hoti hai.
#
# Q: Stack se Queue kaise banaoge (aur kyun ye interview question poocha jata hai)?
# A: 2 stacks use karke (upar implementation dekho). Ye poocha isliye jata
#    hai kyunki ye samajhta hai ke tumhe LIFO/FIFO ka CORE DIFFERENCE aur
#    amortized complexity dono pata hain.
#
# Q: BFS me Queue zaruri kyun hai, Stack kyun nahi use karte?
# A: Queue (FIFO) guarantee karta hai ke hum CURRENT LEVEL ke saare nodes
#    process karke hi NEXT LEVEL pe jayein — "level order" traversal. Agar
#    Stack (LIFO) use karein, ek path me bohat DEEP chale jayenge pehle
#    (DFS ban jayega), level-order guarantee TOOT jayega.
#
# Q: Priority Queue ko array se implement karoge to kya problem hogi heap
#    ke comparison me?
# A: Unsorted array: insert O(1) lekin extract-min O(n). Sorted array:
#    insert O(n) lekin extract-min O(1). HEAP dono operations O(log n) me
#    BALANCE kar deta hai — yehi wajah hai Priority Queue ALMOST hamesha
#    heap se implement hoti hai (detail: 10_heaps_priority_queue/).
