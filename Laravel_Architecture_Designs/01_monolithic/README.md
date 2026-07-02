# 1. Monolithic Architecture

## Concept

Poori application — UI, business logic, database access, background jobs —
**ek hi codebase, ek hi deployment unit** mein hoti hai. Fresh Laravel
project (`laravel new`) by default monolith hi hota hai.

```
Browser/Client
      |
      v
+-------------------------+
|   Laravel Application   |
|  Controllers + Models   |
|  + Views + Business     |
|  Logic — SAB EK JAGAH   |
+-------------------------+
      |
      v
   Single Database
```

## Kab Use Karna Hai

- Startup / MVP jahan speed of development sabse important ho
- Small-medium team (1-8 developers)
- Traffic predictable ho, alag-alag scaling ki zaroorat na ho
- Domain complex na ho

## Pros

- Development fast — sab kuch ek jagah, setup simple
- Debugging aasan — ek hi codebase mein trace karo
- Deployment simple — ek hi artifact deploy hota hai
- Transactions aasan — sab ek hi database mein

## Cons

- App badhne pe codebase "spaghetti" ban sakta hai agar discipline na ho
- Poori app ko ek saath deploy/scale karna padta hai (chhoti si change ke
  liye bhi full deploy)
- Ek module ka bug pooree app ko crash kar sakta hai
- Large team ke liye merge conflicts aur coordination overhead

## Real World

Laravel ka default structure (`app/Http/Controllers`, `app/Models`,
`routes/`) monolith hi hai. Shopify, Basecamp, GitHub (shuru mein) — sab
successful monoliths rahe hain.

## File Structure

Dekho `example-structure/` — ye ek plain Laravel monolith hai jahan
Controller seedha Model/DB access kar raha hai, koi extra layering nahi.
