# Empty list
x = []

# List with initial values
y = [1, 2, 3, 4, 5]

# List with mixed types
z = [1, "hello", 3.14, True]

print(x)
print(y)
print(z)

# List Methods
# Python lists come with several built-in algorithms (called methods), to perform common operations like appending, sorting, and more.
x = [9, 12, 7, 4, 11]

# Add element:
x.append(8)

# Sort list ascending:
x.sort()

print(x)

# Create an algorithm to find the lowest value in a list:

my_array = [7, 12, 9, 4, 11, 8]
minVal = my_array[0]

for i in my_array:
  if i < minVal:
    minVal = i

print('Lowest value:', minVal)

# | i  | minVal (before) | Check      | minVal (after) |
# | -- | --------------- | ---------- | -------------- |
# | 7  | 7               | 7 < 7 → ❌  | 7              |
# | 12 | 7               | 12 < 7 → ❌ | 7              |
# | 9  | 7               | 9 < 7 → ❌  | 7              |
# | 4  | 7               | 4 < 7 → ✅  | 4              |
# | 11 | 4               | 11 < 4 → ❌ | 4              |
# | 8  | 4               | 8 < 4 → ❌  | 4              |



