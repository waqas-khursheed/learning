# a = 12
# b = 23

# if a > b:
#     print("a grater then b")
# else:
#     print("b grate the a")    


# x = str(3)
# y = int(3)
# z = float(3)

# print(type(x))
# print(y)
# print(z)


# Many Values to Multiple Variables
# x, y, z = "Orange", "Banana", "Cherry"
# print(x)
# print(y)
# print(z)


# Unpack a Collection
# If you have a collection of values in a list, tuple etc. Python allows you to extract the values into variables. This is called unpacking.

# fruits = ["apple", "banana", "cherry"]
# x, y, z = fruits
# print(x)
# print(y)
# print(z)

# In the print() function, you output multiple variables, separated by a comma:

# x = "Python"
# y = "is"
# z = "awesome"
# print(x, y, z)

# You can also use the + operator to output multiple variables:

# x = "Python "
# y = "is "
# z = "awesome"
# print(x + y + z)


# For numbers, the + character works as a mathematical operator:

# x = 5
# y = 10
# print(x + y)


# The best way to output multiple variables in the print() function is to separate them with commas, which even support different data types:

# x = 5
# y = "John"
# print(x, y)

# Global Variables
# Create a variable outside of a function, and use it inside the function

# x = "awesome"

# def myfunc():
#   print("Python is " + x)

# myfunc()


# Create a variable inside a function, with the same name as the global variable

# x = "awesome"

# def myfunc():
#   x = "fantastic"
#   print("Python is " + x)

# myfunc()

# print("Python is " + x)


# The global Keyword
# Normally, when you create a variable inside a function, that variable is local, and can only be used inside that function.

# To create a global variable inside a function, you can use the global keyword

# If you use the global keyword, the variable belongs to the global scope:

# def myfunc():
#   global x
#   x = "fantastic"

# myfunc()

# print("Python is " + x)


# To change the value of a global variable inside a function, refer to the variable by using the global keyword:

# x = "awesome"

# def myfunc():
#   global x
#   x = "fantastic"

# myfunc()

# print("Python is " + x)