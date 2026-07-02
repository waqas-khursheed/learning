# 2. Layered Architecture (MVC) — Laravel Default

## Concept

Application ko horizontal **layers** mein baanta jata hai, har layer ka apna
responsibility hota hai aur wo sirf apne neeche wali layer se baat karti hai.

```
Request
   |
   v
+----------------+
|  Presentation   |   Routes, Controllers, Requests (validation), Views
+----------------+
   |
   v
+----------------+
|  Business/App   |   Model methods, simple business rules
+----------------+
   |
   v
+----------------+
|  Data Access    |   Eloquent Models, Query Builder
+----------------+
   |
   v
+----------------+
|    Database     |
+----------------+
```

Ye Laravel ka **out-of-the-box** structure hai — `laravel new` karte hi ye
milta hai.

## Kab Use Karna Hai

- Har naye Laravel project ka starting point
- Jab tak business logic simple hai aur Model ke andar handle ho sakti hai
- CRUD-heavy applications (admin panels, small SaaS)

## Pros

- Laravel convention ke saath 100% match — koi extra setup nahi
- Naye developer ke liye samajhna sabse aasan
- Fast development

## Cons

- Business logic dheere dheere Controllers/Models mein phailne lagti hai
  ("Fat Controller" / "Fat Model" problem)
- Testing mushkil ho jaati hai kyunki logic HTTP layer se tightly coupled hai
- Reusability kam — same logic 2 jagah (web + API controller) mein duplicate
  hone lagti hai

## Real World

Har fresh Laravel app yahin se shuru hoti hai. Jab "Fat Controller" ka
problem mehsoos ho, tab `03_service_repository_pattern/` dekho.

## File Structure

`example-structure/` mein Controller → Model (Eloquent) → DB ka seedha flow
dikhaya gaya hai, request validation `FormRequest` class ke through.
