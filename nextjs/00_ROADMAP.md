# Next.js Roadmap — Zero se Senior Frontend Developer Tak

Aap already Laravel, Node.js aur Python jaante ho — backend ka solid base hai. Is folder ka maqsad hai aapko **frontend + Next.js** itna sikhana ke aap full-stack developer ban jao, aur Next.js projects ko **senior level** pe structure, build, aur deploy kar sako.

## Kaise follow karna hai

Files sequence mein parho — har file pichli file ke concepts pe build karti hai. Sirf parhna kaafi nahi — har file ke end mein "Practice" section hai, wahan khud code likh kar try karo. Aapke paas already programming ka strong base hai, is liye hum basic JS/programming concepts explain nahi karenge — seedha Next.js aur React ke specific concepts pe focus karenge.

| # | File | Kya seekhoge |
|---|------|---------------|
| 1 | [01_nextjs_kya_hai_kyun_use_karen.md](01_nextjs_kya_hai_kyun_use_karen.md) | Next.js kya hai, React se farak, rendering strategies (SSR/SSG/CSR/ISR), kab use karna hai |
| 2 | [02_installation_aur_setup.md](02_installation_aur_setup.md) | Node setup, create-next-app, generated files, scripts, config |
| 3 | [03_project_folder_structure.md](03_project_folder_structure.md) | Har folder/file ka kaam, Laravel ke sath comparison |
| 4 | [04_app_router_aur_routing.md](04_app_router_aur_routing.md) | App Router file conventions, dynamic routes, layouts, navigation |
| 5 | [05_components_server_client.md](05_components_server_client.md) | Server vs Client components, component banana, composition |
| 6 | [06_styling_tailwind.md](06_styling_tailwind.md) | Tailwind CSS, CSS Modules, shadcn/ui — poora Tailwind deep-dive [tailwind/](tailwind/00_ROADMAP.md) folder mein |
| 7 | [07_state_hooks.md](07_state_hooks.md) | useState, useEffect, custom hooks, Zustand/Redux |
| 8 | [08_data_fetching_api_integration.md](08_data_fetching_api_integration.md) | Server component fetching, Route Handlers, external API calls |
| 9 | [09_forms_server_actions.md](09_forms_server_actions.md) | Server Actions, forms, validation with Zod |
| 10 | [10_backend_database_integration.md](10_backend_database_integration.md) | Laravel/Node backend se connect karna, Prisma, env vars |
| 11 | [11_authentication.md](11_authentication.md) | NextAuth/Auth.js, JWT, protected routes, middleware |
| 12 | [12_important_packages_list.md](12_important_packages_list.md) | Har category ka har zaroori package, kya kaam karta hai |
| 13 | [13_seo_performance.md](13_seo_performance.md) | Metadata, next/image, next/font, performance optimization |
| 14 | [14_local_dev_aur_deployment.md](14_local_dev_aur_deployment.md) | Local pe run karna, build karna, Vercel/Docker/VPS pe deploy |
| 15 | [15_senior_level_structure_best_practices.md](15_senior_level_structure_best_practices.md) | Senior devs kaise project structure karte hain, best practices |
| 16 | [16_practice_projects_roadmap.md](16_practice_projects_roadmap.md) | 6 projects, beginner se senior tak, is order mein banao |
| 17 | [17_build_process_aur_aws_deployment.md](17_build_process_aur_aws_deployment.md) | `next build` andar se kya karta hai, Node.js live pe chahiye ya nahi, AWS (Amplify/EC2/ECS/S3) pe exact deploy steps |

## Golden Rules

1. **App Router use karo, Pages Router nahi.** App Router (`app/` folder) Next.js ka future hai — sari nayi features (Server Components, Server Actions) isi mein hain.
2. **Server Component default hai.** Sirf tab `'use client'` likho jab interactivity (onClick, useState, useEffect) chahiye ho.
3. **TypeScript use karo.** JavaScript nahi — aap already typed languages (PHP with types, Python) se familiar ho, TypeScript aapko fast lagega aur bugs kam karega.
4. **Har file ka ek kaam hai** — component, hook, ya utility ek hi zimmedari nibhaye (Single Responsibility, jo aapko Laravel se pata hai).
5. **Backend already pata hai — us knowledge ko map karo:** Next.js Route Handler = Laravel Controller, Server Component data fetch = Laravel Blade + Model, middleware.ts = Laravel Middleware.

Shuru karne ke liye [01_nextjs_kya_hai_kyun_use_karen.md](01_nextjs_kya_hai_kyun_use_karen.md) kholo.
</content>
