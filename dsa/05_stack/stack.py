# ============================================================================
#  STACK — LIFO (Last In, First Out)
# ============================================================================

# ----------------------------------------------------------------------------
# 1) Stack kya hai aur kyun zaruri hai?
# ----------------------------------------------------------------------------
# Stack ek data structure hai jisme sirf EK END (TOP) se elements add/remove
# hote hain — jaisa plates ka dheer (stack of plates): jo plate AAKHRI me
# rakhi, wahi SABSE PEHLE uthegi (LIFO).
#
# Operations: push (add), pop (remove from top), peek/top (dekho without
# remove), isEmpty. SAARI operations O(1) hoti hain.
#
# KYUN ZARURI: Computer ke andar HAR FUNCTION CALL stack pe based hai (Call
# Stack) — isi wajah se RECURSION kaam karta hai. Undo/Redo, Browser back
# button, Bracket matching, Expression evaluation — sab Stack ka real use hai.

# ----------------------------------------------------------------------------
# 2) Stack Implementation — Python list se (array-based)
# ----------------------------------------------------------------------------
class Stack:
    def __init__(self):
        self._items = []

    def push(self, item):              # O(1) amortized
        self._items.append(item)

    def pop(self):                       # O(1)
        if self.is_empty():
            raise IndexError("pop from empty stack")
        return self._items.pop()

    def peek(self):                       # O(1)
        if self.is_empty():
            raise IndexError("peek from empty stack")
        return self._items[-1]

    def is_empty(self):                    # O(1)
        return len(self._items) == 0

    def size(self):
        return len(self._items)

s = Stack()
s.push(1); s.push(2); s.push(3)
print(s.pop())     # 3 (last in, first out)
print(s.peek())    # 2

# ⚠️ IMPORTANT: Python list ke END pe push/pop karo (append/pop) — START
# pe (insert(0,..)/pop(0)) MAT karo, wo O(n) hai (sab elements shift hote hain).

# ----------------------------------------------------------------------------
# 3) REAL-WORLD USE CASE 1: Balanced Parentheses / Bracket Matching
# ----------------------------------------------------------------------------
# Code editor me "{[()]}" valid hai ya nahi check karna — yehi pattern
# COMPILERS/IDEs syntax checking ke liye use karte hain.
def is_balanced(s):
    stack = []
    pairs = {')': '(', ']': '[', '}': '{'}
    for char in s:
        if char in '([{':
            stack.append(char)
        elif char in ')]}':
            if not stack or stack.pop() != pairs[char]:
                return False
    return len(stack) == 0

print(is_balanced("{[()]}"))    # True
print(is_balanced("{[(])}"))    # False — wrong nesting

# ----------------------------------------------------------------------------
# 4) REAL-WORLD USE CASE 2: Undo/Redo Functionality
# ----------------------------------------------------------------------------
class TextEditor:
    def __init__(self):
        self.text = ""
        self.undo_stack = []
        self.redo_stack = []

    def type(self, chars):
        self.undo_stack.append(self.text)    # current state save karo undo ke liye
        self.text += chars
        self.redo_stack.clear()              # naya action -> purana redo invalid

    def undo(self):
        if self.undo_stack:
            self.redo_stack.append(self.text)
            self.text = self.undo_stack.pop()

    def redo(self):
        if self.redo_stack:
            self.undo_stack.append(self.text)
            self.text = self.redo_stack.pop()

editor = TextEditor()
editor.type("Hello"); editor.type(" World")
editor.undo()
print(editor.text)   # "Hello"
editor.redo()
print(editor.text)   # "Hello World"

# ----------------------------------------------------------------------------
# 5) REAL-WORLD USE CASE 3: Function Call Stack (RECURSION ki wajah samjho)
# ----------------------------------------------------------------------------
# Jab tum function call karte ho, CPU/Runtime us function ka "stack frame"
# (local variables, return address) STACK pe PUSH karta hai. Function return
# hone par frame POP hota hai. Yehi wajah hai:
# - Recursion deep ho to "Stack Overflow" error aata hai (stack ki memory
#   limit hai — har OS/runtime ki apni limit hoti hai)
# - Recursive solution ko ITERATIVE+explicit-stack me convert kar sakte ho
#   (jab recursion limit issue ho)

def factorial_using_explicit_stack(n):
    stack = []
    result = 1
    while n > 1:
        stack.append(n)
        n -= 1
    while stack:
        result *= stack.pop()
    return result

print(factorial_using_explicit_stack(5))   # 120

# ----------------------------------------------------------------------------
# 6) REAL-WORLD USE CASE 4: Expression Evaluation (Infix -> Postfix, Calculator)
# ----------------------------------------------------------------------------
def evaluate_postfix(expression):
    # Postfix (Reverse Polish): "3 4 +" = 7 (operators COMPILERS ke liye easy
    # hote hain evaluate karna kyunki precedence/brackets ki zarurat nahi)
    stack = []
    for token in expression.split():
        if token in '+-*/':
            b = stack.pop()
            a = stack.pop()
            if token == '+': stack.append(a + b)
            elif token == '-': stack.append(a - b)
            elif token == '*': stack.append(a * b)
            elif token == '/': stack.append(a / b)
        else:
            stack.append(int(token))
    return stack.pop()

print(evaluate_postfix("3 4 + 2 *"))   # (3+4)*2 = 14

# ----------------------------------------------------------------------------
# 7) MONOTONIC STACK Pattern — Advanced, bohat interview problems isi se solve hote hain
# ----------------------------------------------------------------------------
# Pattern: Stack ko hamesha INCREASING ya DECREASING order me maintain karo.
# Use case: "Next Greater Element", "Daily Temperatures", "Largest Rectangle in Histogram"

def next_greater_element(arr):
    # Har element ke liye, uske DAAYIN taraf wala pehla BADA element dhoondo
    result = [-1] * len(arr)
    stack = []                                   # indices store karte hain
    for i in range(len(arr)):
        while stack and arr[stack[-1]] < arr[i]:
            result[stack.pop()] = arr[i]         # ye element STACK ke top se BADA hai
        stack.append(i)
    return result

print(next_greater_element([2, 1, 2, 4, 3]))   # [4, 2, 4, -1, -1]
# TRICK: Naive approach O(n²) hota (har element ke liye scan), Monotonic
# Stack se O(n) ho jata hai — har element STACK me ek hi baar push/pop hota hai

# ----------------------------------------------------------------------------
# 8) Min Stack — O(1) me MINIMUM element nikalna (CLASSIC design question)
# ----------------------------------------------------------------------------
class MinStack:
    def __init__(self):
        self.stack = []
        self.min_stack = []          # parallel stack jo HAMESHA current minimum track karta hai

    def push(self, val):
        self.stack.append(val)
        if not self.min_stack or val <= self.min_stack[-1]:
            self.min_stack.append(val)
        else:
            self.min_stack.append(self.min_stack[-1])   # purana min hi repeat karo

    def pop(self):
        self.stack.pop()
        self.min_stack.pop()

    def get_min(self):
        return self.min_stack[-1]    # O(1)!

ms = MinStack()
ms.push(5); ms.push(2); ms.push(8)
print(ms.get_min())   # 2

# ============================================================================
# INTERVIEW Q&A
# ============================================================================
# Q: Stack implement karne ke liye Array better hai ya Linked List?
# A: Dono O(1) push/pop dete hain. Array (dynamic) thora BEHTAR hai cache
#    locality ki wajah se (CPU cache-friendly), lekin resize ka occasional
#    O(n) cost hota hai (amortized O(1)). Linked List me HAMESHA O(1)
#    guaranteed hai (resize nahi hota) lekin pointer overhead aur poor
#    cache performance hai. Practically: ARRAY-BASED zyada use hoti hai.
#
# Q: Stack Overflow kyun hota hai (recursion me)?
# A: Har recursive call ek NAYA stack frame push karta hai call stack pe.
#    Agar recursion bohat DEEP ho (ya base case galat ho, infinite ho jaye),
#    stack memory (jo limited hoti hai, typically few MB) khatam ho jati
#    hai -> StackOverflowError/RecursionError.
#
# Q: Monotonic Stack pattern kab use karoge?
# A: Jab problem me "next greater/smaller element", "span", ya "histogram-
#    based area" jaisi cheezein poochi jayein — jahan naive solution O(n²)
#    (nested loop) ho, Monotonic Stack se O(n) me solve ho jata hai kyunki
#    har element sirf EK BAAR push aur EK BAAR pop hota hai.
