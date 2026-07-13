# Project Folder Structure

Ek fresh `create-next-app` (TypeScript + Tailwind + src directory + App Router) project ka structure:

```
my-app/
├── src/
│   ├── app/                  → routes yahan bante hain (page.tsx = URL)
│   │   ├── layout.tsx        → root layout (har page ke around wraps hota hai)
│   │   ├── page.tsx          → "/" route (homepage)
│   │   ├── globals.css       → global styles
│   │   └── favicon.ico
│   ├── components/           → reusable UI components (khud banana parta hai)
│   ├── lib/                  → helper functions, API clients, utilities
│   ├── hooks/                → custom React hooks
│   └── types/                → TypeScript types/interfaces
├── public/                   → static files (images, fonts, robots.txt) — seedha URL se accessible
├── .env.local                → secrets/env vars (git ignored)
├── next.config.ts
├── tsconfig.json
├── package.json
└── tailwind.config.ts
```

## Laravel Se Comparison (Aapke Liye Familiar Reference)

| Laravel | Next.js | Kaam |
|---|---|---|
| `routes/web.php` | `app/` folder structure (file-based) | URL → code mapping |
| Controllers | `page.tsx` / `route.ts` | Request handle karna |
| Blade views | Components (`.tsx` files) | UI render karna |
| `app/Models/` | Prisma schema / API calls in `lib/` | Data layer |
| Middleware | `middleware.ts` | Har request se pehle chalne wala logic |
| `.env` | `.env.local` | Environment secrets |
| `public/` | `public/` | Static assets, seedha accessible |
| `config/` | `next.config.ts` | App-wide settings |
| Service Providers | `layout.tsx` (context providers wrap karne ke liye) | App-wide setup |

## `app/` Folder — Sirf Routes Ke Liye

Zaroori baat: `app/` folder **sirf routing structure** ke liye hai. Har folder ek URL segment banata hai, aur andar `page.tsx` ho to wo route accessible ban jata hai. Non-route files (jo khud UI/logic hain) is folder ke bahar `components/`, `lib/`, `hooks/` mein rakhi jati hain.

```
app/
├── page.tsx           → yoursite.com/
├── about/
│   └── page.tsx        → yoursite.com/about
├── blog/
│   ├── page.tsx         → yoursite.com/blog
│   └── [slug]/
│       └── page.tsx     → yoursite.com/blog/kuch-bhi-slug
└── api/
    └── users/
        └── route.ts     → yoursite.com/api/users (backend endpoint)
```

Detail [04_app_router_aur_routing.md](04_app_router_aur_routing.md) mein.

## `src/` Use Karna Chahiye Ya Nahi

`src/` optional hai — agar nahi lete to `app/`, `components/` seedha root mein hote hain. **`src/` use karna better practice hai** kyun ke root mein sirf config files rehte hain, saara app code ek jagah organized rehta hai — bade projects mein clean lagta hai.

## Ek Recommended Extended Structure (Real Projects Mein)

Chota project sirf upar wala structure use karta hai, lekin jaise app badhta hai, senior devs ye pattern follow karte hain:

```
src/
├── app/                    → routes only
├── components/
│   ├── ui/                  → generic reusable (Button, Input, Card)
│   └── features/            → feature-specific (UserCard, OrderTable)
├── lib/
│   ├── api.ts               → axios instance / fetch wrapper
│   └── utils.ts             → helper functions
├── hooks/                   → useAuth, useDebounce waghera
├── types/                   → shared TypeScript types
├── constants/                → app-wide constants
└── store/                    → Zustand/Redux state (agar use ho)
```

Iski detail [15_senior_level_structure_best_practices.md](15_senior_level_structure_best_practices.md) mein aayegi.

## Practice

1. Apne bane hue project mein `src/components/ui/` aur `src/lib/` folders khud banao (Next.js ye khud nahi banata, aapko banana parta hai).
2. `public/` folder mein ek image dalo, `app/page.tsx` mein use karke dekho URL `/image-name.png` se directly accessible hai.

Agli file: [04_app_router_aur_routing.md](04_app_router_aur_routing.md)
</content>
