# ============================================================================
#  GRAPHS — BFS, DFS, Shortest Path, Union-Find
#  (Sabse zyada REAL-WORLD applicable structures me se ek)
# ============================================================================

# ----------------------------------------------------------------------------
# 1) Graph kya hai?
# ----------------------------------------------------------------------------
# NODES (vertices) + EDGES (connections between nodes). Tree se farq: Graph
# me CYCLES ho sakte hain, koi fixed "root" nahi, aur node ke MULTIPLE
# paths se reach ho sakta hai.
#
# KYUN ZARURI: REAL-WORLD relationships GRAPH hote hain — Social networks
# (friends), Maps/Navigation (roads connecting cities), Internet (web pages
# linking), Dependency resolution (npm packages, Makefile), Recommendation
# systems, Network topology.

# ----------------------------------------------------------------------------
# 2) Graph Representation
# ----------------------------------------------------------------------------

# a) Adjacency List (MOST COMMON — space efficient for SPARSE graphs)
graph_list = {
    'A': ['B', 'C'],
    'B': ['A', 'D'],
    'C': ['A', 'D'],
    'D': ['B', 'C', 'E'],
    'E': ['D'],
}
# Space: O(V + E) | "Konse neighbors hain X ke" check: O(degree of X)

# b) Adjacency Matrix (DENSE graphs ke liye, ya jab "edge exist karta hai?"
#    bohat FREQUENT check karna ho)
#      A  B  C  D  E
# A  [ 0, 1, 1, 0, 0 ]
# B  [ 1, 0, 0, 1, 0 ]
# C  [ 1, 0, 0, 1, 0 ]
# D  [ 0, 1, 1, 0, 1 ]
# E  [ 0, 0, 0, 1, 0 ]
# Space: O(V²) | "edge X-Y exist karta hai?" check: O(1) — DIRECT lookup

# DIRECTED vs UNDIRECTED: A->B ka matlab B->A bhi hai (undirected) ya nahi (directed)?
# WEIGHTED vs UNWEIGHTED: edges pe COST/distance hai ya sirf connection?

# ----------------------------------------------------------------------------
# 3) BFS (Breadth-First Search) — QUEUE use karta hai, LEVEL-by-LEVEL explore
# ----------------------------------------------------------------------------
from collections import deque

def bfs(graph, start):
    visited = {start}
    queue = deque([start])
    order = []
    while queue:
        node = queue.popleft()
        order.append(node)
        for neighbor in graph[node]:
            if neighbor not in visited:
                visited.add(neighbor)        # ⚠️ visit karte hi mark karo (queue me daalte waqt)
                queue.append(neighbor)
    return order

print(bfs(graph_list, 'A'))    # ['A', 'B', 'C', 'D', 'E']

# KAB USE KARO: SHORTEST PATH (UNWEIGHTED graph me) — kyunki BFS GUARANTEE
# karta hai ke jab target node MILE, wo SHORTEST path se mila hai (level
# by level explore karne ki wajah se). Social network "degrees of
# separation", Web crawling.

# ----------------------------------------------------------------------------
# 4) DFS (Depth-First Search) — STACK (ya RECURSION) use karta hai, ek path
#    me JITNA JAA SAKTE HO jao, phir backtrack
# ----------------------------------------------------------------------------
def dfs_recursive(graph, node, visited=None, order=None):
    if visited is None: visited = set()
    if order is None: order = []
    visited.add(node)
    order.append(node)
    for neighbor in graph[node]:
        if neighbor not in visited:
            dfs_recursive(graph, neighbor, visited, order)
    return order

print(dfs_recursive(graph_list, 'A'))   # ['A', 'B', 'D', 'C', 'E']

def dfs_iterative(graph, start):
    visited = set()
    stack = [start]
    order = []
    while stack:
        node = stack.pop()
        if node not in visited:
            visited.add(node)
            order.append(node)
            for neighbor in reversed(graph[node]):    # order match karne ke liye reverse
                if neighbor not in visited:
                    stack.append(neighbor)
    return order

# KAB USE KARO: PATH EXISTENCE check, Cycle detection, Topological Sort,
# Connected Components, Maze solving, Backtracking problems (Sudoku, N-Queens)

# ----------------------------------------------------------------------------
# 5) BFS vs DFS — kab kya use karo (CLASSIC INTERVIEW Q)
# ----------------------------------------------------------------------------
# | Aspect              | BFS                              | DFS                            |
# |------------------------|--------------------------------------|------------------------------------|
# | Data Structure            | Queue                                | Stack (ya Recursion)               |
# | Shortest Path (unweighted)  | YES (guaranteed)                      | NO                                  |
# | Memory                         | Zyada (poori CURRENT level store)     | Kam (sirf CURRENT path store)       |
# | Use Case                          | Shortest path, level-order, "nearest" | Path existence, cycle detect, topological sort, all-paths |

# ----------------------------------------------------------------------------
# 6) SHORTEST PATH Algorithms
# ----------------------------------------------------------------------------

# a) BFS — UNWEIGHTED graph shortest path
def shortest_path_bfs(graph, start, end):
    if start == end:
        return [start]
    visited = {start}
    queue = deque([[start]])               # poora PATH store karte hain queue me
    while queue:
        path = queue.popleft()
        node = path[-1]
        for neighbor in graph[node]:
            if neighbor not in visited:
                new_path = path + [neighbor]
                if neighbor == end:
                    return new_path
                visited.add(neighbor)
                queue.append(new_path)
    return None

print(shortest_path_bfs(graph_list, 'A', 'E'))   # ['A', 'B', 'D', 'E'] or similar shortest

# b) Dijkstra's Algorithm — WEIGHTED graph (non-negative weights), shortest
#    path from ONE source to ALL nodes. Heap-based ([[../10_heaps_priority_queue]])
import heapq

def dijkstra(graph, start):
    # graph format: {'A': [('B', weight), ('C', weight)], ...}
    distances = {node: float('inf') for node in graph}
    distances[start] = 0
    pq = [(0, start)]                       # (distance, node) — MIN-HEAP

    while pq:
        current_dist, node = heapq.heappop(pq)
        if current_dist > distances[node]:
            continue                          # PURANI/stale entry hai, skip karo
        for neighbor, weight in graph[node]:
            distance = current_dist + weight
            if distance < distances[neighbor]:
                distances[neighbor] = distance
                heapq.heappush(pq, (distance, neighbor))
    return distances

weighted_graph = {
    'A': [('B', 4), ('C', 1)],
    'B': [('A', 4), ('D', 1)],
    'C': [('A', 1), ('D', 5)],
    'D': [('B', 1), ('C', 5)],
}
print(dijkstra(weighted_graph, 'A'))   # {'A': 0, 'B': 2, 'C': 1, 'D': 3}
# LOGIC: HAMESHA "currently shortest-known-distance wala UNVISITED node"
# process karo pehle (Greedy choice — isi wajah se Heap use hota hai).
# REAL-WORLD: GPS Navigation (Google Maps), Network routing protocols (OSPF)

# ----------------------------------------------------------------------------
# 7) Cycle Detection
# ----------------------------------------------------------------------------
def has_cycle_undirected(graph):
    visited = set()
    def dfs(node, parent):
        visited.add(node)
        for neighbor in graph[node]:
            if neighbor not in visited:
                if dfs(neighbor, node):
                    return True
            elif neighbor != parent:           # visited hai AUR parent nahi hai -> CYCLE!
                return True
        return False
    for node in graph:
        if node not in visited:
            if dfs(node, None):
                return True
    return False

# ----------------------------------------------------------------------------
# 8) TOPOLOGICAL SORT — DIRECTED ACYCLIC GRAPH (DAG) ke liye, ORDERING jahan
#    A->B edge ka matlab "A pehle aana chahiye B se"
# ----------------------------------------------------------------------------
def topological_sort(graph):
    visited = set()
    stack = []
    def dfs(node):
        visited.add(node)
        for neighbor in graph[node]:
            if neighbor not in visited:
                dfs(neighbor)
        stack.append(node)               # node ko POST-ORDER me push karo
    for node in graph:
        if node not in visited:
            dfs(node)
    return stack[::-1]                     # REVERSE = topological order

dag = {'A': ['B', 'C'], 'B': ['D'], 'C': ['D'], 'D': []}
print(topological_sort(dag))   # ['A', 'C', 'B', 'D'] (valid ordering)

# REAL-WORLD: Build systems (compile order — npm/Maven dependency
# resolution), Course prerequisites (kis course se pehle kya padhna hai),
# Task scheduling with dependencies (CI/CD pipelines)

# ----------------------------------------------------------------------------
# 9) UNION-FIND (Disjoint Set Union / DSU) — "Konse nodes SAME GROUP me hain"
# ----------------------------------------------------------------------------
class UnionFind:
    def __init__(self, n):
        self.parent = list(range(n))
        self.rank = [0] * n

    def find(self, x):                       # O(α(n)) ≈ O(1) practically, with path compression
        if self.parent[x] != x:
            self.parent[x] = self.find(self.parent[x])   # PATH COMPRESSION
        return self.parent[x]

    def union(self, x, y):                    # O(α(n)) ≈ O(1) practically
        root_x, root_y = self.find(x), self.find(y)
        if root_x == root_y:
            return False                        # already SAME group me hain
        if self.rank[root_x] < self.rank[root_y]:   # UNION BY RANK
            root_x, root_y = root_y, root_x
        self.parent[root_y] = root_x
        if self.rank[root_x] == self.rank[root_y]:
            self.rank[root_x] += 1
        return True

uf = UnionFind(6)
uf.union(0, 1); uf.union(1, 2); uf.union(3, 4)
print(uf.find(0) == uf.find(2))   # True (same group)
print(uf.find(0) == uf.find(3))   # False (different group)

# REAL-WORLD: Network connectivity check, Detecting cycles in undirected
# graph (efficient alternative to DFS), Kruskal's MST algorithm, "Friend
# circles" / social network clusters, Image processing (connected components)

# ============================================================================
# INTERVIEW Q&A
# ============================================================================
# Q: BFS shortest path GUARANTEE kyun karta hai (DFS nahi)?
# A: BFS LEVEL-BY-LEVEL explore karta hai — Level 1 (direct neighbors)
#    PEHLE poora explore hota hai, phir Level 2, etc. Isliye jab target
#    node MILE, wo GUARANTEED minimum number of edges (hops) se mila hai.
#    DFS ek hi path me bohat DEEP chala jata hai — pehla mila hua path
#    SHORTEST guarantee nahi karta.
#
# Q: Dijkstra NEGATIVE weights ke sath kyun fail hota hai?
# A: Dijkstra GREEDY assumption pe based hai — "ek baar node FINALIZED
#    (shortest distance confirm) ho gaya, future me uski distance IMPROVE
#    nahi hogi" kyunki future edges sirf distance ADD karenge (positive).
#    Agar NEGATIVE weight ho, ye assumption TOOT jata hai (baad me ek
#    negative edge se SHORTER path mil sakta hai). Negative weights ke
#    liye Bellman-Ford algorithm use karte hain (O(V*E), slower but correct).
#
# Q: Union-Find aur DFS dono CONNECTED COMPONENTS dhoondh sakte hain —
#    kab kya use karoge?
# A: STATIC graph (edges fix hain, sirf query karni hai) -> dono fine.
#    DYNAMIC graph (edges INCREMENTALLY add ho rahe hain, beech beech me
#    "connected hain?" query bhi aa rahi hai) -> Union-Find BEHTAR hai
#    (almost O(1) per operation with path compression + union by rank),
#    DFS har query pe poora graph RE-TRAVERSE karega.
#
# Q: Topological Sort sirf DAG (Directed ACYCLIC Graph) pe kyun possible hai?
# A: Agar CYCLE ho (A->B->C->A), to "A pehle aaye ya C pehle" — ye CONTRADICTION
#    create karta hai (A, B se pehle hona chahiye, lekin C se baad — aur C,
#    B se pehle). Cyclic dependency ka koi VALID linear order NAHI ho sakta
#    (real life: circular dependency error build tools me isi wajah se aati hai).
