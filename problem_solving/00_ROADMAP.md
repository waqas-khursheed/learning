# Problem Solving Roadmap — Interview Ke Liye

Ye folder pure **hands-on practice** ke liye hai — coding interviews mein jo type ke sawal aate hain (arrays, strings, sorting, searching, recursion, numbers) unke manual solutions, **bina built-in helper functions use kiye** (`.sort()`, `.reverse()`, `.includes()`, `Math.max()` waghera khud implement karoge).

Ye JavaScript mein hai (aapke Next.js frontend kaam ke sath directly relevant), lekin logic language-agnostic hai — same approach Python/PHP mein bhi kaam karega.

## Kaise Use Karna Hai

1. Har file `node filename.js` se seedha run ho sakti hai — terminal mein result dekh sakte ho.
2. Har file mein pehle **solved examples** hain (approach samajhne ke liye, code parho, samjho, khud dobara likh kar dekho).
3. Har file ke end mein **"Practice — Khud Karo"** section hai — sirf problem statement diya hai, solution nahi. Pehle khud solve karne ki koshish karo, phir upar wale solved examples se apna approach compare karo.
4. **Rule follow karo:** jab tak file mein explicitly na kaha ho, koi bhi "solving" wala built-in method use mat karo (`.sort()`, `.reverse()`, `.includes()`, `.indexOf()`, `Math.max/min()`, `Set`, `.flat()`). Basic cheezein allowed hain: `.length`, indexing (`arr[i]`), `charCodeAt`, loops, `if/else`.

## Files

| # | File | Kya Practice Hoga |
|---|------|--------------------|
| 1 | [01_array_problems.js](01_array_problems.js) | Array manipulation — max/min, reverse, dedupe, rotate, frequency |
| 2 | [02_string_problems.js](02_string_problems.js) | String reverse, palindrome, anagram, char frequency |
| 3 | [03_sorting_algorithms.js](03_sorting_algorithms.js) | Bubble, Selection, Insertion, Quick, Merge sort — manual (asc + desc) |
| 4 | [04_searching_algorithms.js](04_searching_algorithms.js) | Linear search, Binary search (iterative + recursive) |
| 5 | [05_recursion_problems.js](05_recursion_problems.js) | Factorial, Fibonacci, GCD, Tower of Hanoi, permutations |
| 6 | [06_number_math_problems.js](06_number_math_problems.js) | Prime, Armstrong, palindrome number, FizzBuzz, digit problems |
| 7 | [07_object_array_problems.js](07_object_array_problems.js) | Group by, flatten, deep clone, debounce/throttle — JS-specific interview classics |
| 8 | [08_popular_interview_questions.js](08_popular_interview_questions.js) | Two Sum, Valid Parentheses, Kadane's Algorithm, apna map/filter/reduce banana |

## Practice Karne Ka Sahi Tareeqa

1. Problem statement parho, **file band karo ya scroll na karo**
2. Khud paper/editor pe likhne ki koshish karo (5-15 minute apne aap ko do)
3. Atak jao to hints ke liye sirf function ka naam/signature dekho, poora solution nahi
4. Apna solution likhne ke baad hi file ka solution dekho, compare karo — kya approach better ho sakta tha, time complexity kya hai

Shuru karo: [01_array_problems.js](01_array_problems.js)
</content>
