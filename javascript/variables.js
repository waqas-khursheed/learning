// The let keyword was introduced in ES6 (2015)
// Variables declared with let have Block Scope
// Variables declared with let must be Declared before use
// Variables declared with let cannot be Redeclared in the same scope

// Example
// Variables declared inside a { } block cannot be accessed from outside the block:

{
    let x = 2;
}
  // x can NOT be used here


//   Global Scope
//   Variables declared with the var always have Global Scope.
//   Variables declared with the var keyword can NOT have block scope:
//   Example
//   Variables declared with varinside a { } block can be accessed from outside the block:
  
  {
    var x = 2;
  }
  // x CAN be used here  


// Cannot be Redeclared
// Variables defined with let can not be redeclared.

// You can not accidentally redeclare a variable declared with let.

// With let you can not do this:

let x = "John Doe";

let x = 0;
// Variables defined with var can be redeclared.


// With var you can do this:

var x = "John Doe";

var x = 0;


// Redeclaring Variables
// Redeclaring a variable using the var keyword can impose problems.
// Redeclaring a variable inside a block will also redeclare the variable outside the block:
// Example
var x = 10;
// Here x is 10

{
var x = 2;
// Here x is 2
}

// Here x is 2


// Redeclaring a variable using the let keyword can solve this problem.
// Redeclaring a variable inside a block will not redeclare the variable outside the block:
// Example
let x = 10;
// Here x is 10

{
let x = 2;
// Here x is 2
}

// Here x is 10


// What is Good?
// let and const have block scope.  
// let and const can be updated.    
// let and const can not be redeclared.
// let and const must be declared before use.
// let and const does not bind to this.
// let and const are not hoisted.

// What is Not Good?
// var does not have to be declared.

// var is hoisted.

// var binds to this.
// var can be redeclared.
// var does not have block scope.
// var can be updated.
// var can be declared without var keyword.
// var is not recommended.
// var is not good for memory management.
// var is not good for security.
// var is not good for debugging.
// var is not good for performance.
// var is not good for readability.
// var is not good for maintainability.
// var is not good for scalability.



// JavaScript Const
// The const keyword was introduced in ES6 (2015)
// Variables defined with const cannot be Redeclared
// Variables defined with const cannot be Reassigned
// Variables defined with const have Block Scope
// Cannot be Reassigned
// A variable defined with the const keyword cannot be reassigned:

// Example
const PI = 3.141592653589793;
PI = 3.14;      // This will give an error
PI = PI + 10;   // This will also give an error

// When to use JavaScript const?
// Always declare a variable with const when you know that the value should not be changed.

// Use const when you declare:

// A new Array
// A new Object
// A new Function
// A new RegExp


// Constant Objects and Arrays
// The keyword const is a little misleading.

// It does not define a constant value. It defines a constant reference to a value.

// Because of this you can NOT:

// Reassign a constant value
// Reassign a constant array
// Reassign a constant object
// But you CAN:

// Change the elements of constant array
// Change the properties of constant object
// Constant Arrays
// You can change the elements of a constant array:

// You can create a constant array:
const cars = ["Saab", "Volvo", "BMW"];

// You can change an element:
cars[0] = "Toyota";

// You can add an element:
cars.push("Audi");

// But you can NOT reassign the array:
const cars = ["Saab", "Volvo", "BMW"];

cars = ["Toyota", "Volvo", "Audi"];    // ERROR


// Constant Objects
// You can change the properties of a constant object:

const car = {type:"Fiat", model:"500", color:"white"};

// You can change a property:
car.color = "red";

// You can add a property:
car.owner = "Johnson";


// But you can NOT reassign the object:

const car = {type:"Fiat", model:"500", color:"white"};

car = {type:"Volvo", model:"EX60", color:"red"}    // ERROR



// JavaScript has 8 Datatypes
// A JavaScript variable can hold 8 types of data:

// Type	Description
// String	A text of characters enclosed in quotes
// Number	A number representing a mathematical value
// Bigint	A number representing a large integer
// Boolean	A data type representing true or false
// Object	A collection of key-value pairs of data
// Undefined	A primitive variable with no assigned value
// Null	A primitive value representing object absence
// Symbol	A unique and primitive identifier


// String
let color = "Yellow";
let lastName = "Johnson";

// Number
let length = 16;
let weight = 7.5;

// BigInt
let x = 1234567890123456789012345n;
let y = BigInt(1234567890123456789012345)

// Boolean
let x = true;
let y = false;

// Object
const person = {firstName:"John", lastName:"Doe"};

// Array object
const cars = ["Saab", "Volvo", "BMW"];

// Date object
const date = new Date("2022-03-25");

// Undefined
let x;
let y;

// Null
let x = null;
let y = null;

// Symbol
const x = Symbol();
const y = Symbol();