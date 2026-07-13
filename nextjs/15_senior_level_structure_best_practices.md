# Senior Level Structure Aur Best Practices

Ye file batati hai senior developers Next.js projects **kaise** structure aur maintain karte hain — sirf "code chal jaye" se aage.

## Feature-Based Folder Structure (Type-Based Se Better)

**Chota project (type-based) — theek hai shuru mein:**
```
components/    hooks/    lib/    types/
```

**Bada project (feature-based) — senior teams ye use karte hain:**
```
src/
├── app/                       → routes only, thin — logic yahan nahi likhte
├── features/
│   ├── auth/
│   │   ├── components/          → LoginForm, SignupForm
│   │   ├── hooks/                 → useAuth
│   │   ├── api.ts                  → auth API calls
│   │   └── types.ts
│   ├── products/
│   │   ├── components/
│   │   ├── hooks/
│   │   ├── api.ts
│   │   └── types.ts
│   └── cart/
│       └── ...
├── components/ui/              → sirf truly generic (Button, Modal, Input)
├── lib/                         → axios instance, db, generic utils
└── types/                       → shared/global types only
```

Faida: ek feature pe kaam karte waqt sab related files ek jagah milte hain, dhoondna aasan hota hai, teams parallel kaam kar sakti hain bina ek dusre se takraye.

## `app/` Ko Thin Rakho

Route file (`page.tsx`) mein sirf data fetch + layout compose karo, business logic `features/` mein rakho:

```tsx
// app/products/page.tsx — thin
import { ProductList } from "@/features/products/components/ProductList";
import { getProducts } from "@/features/products/api";

export default async function ProductsPage() {
  const products = await getProducts();
  return <ProductList products={products} />;
}
```

## Absolute Imports — `@/` Alias

`tsconfig.json` mein already configured hota hai:

```json
{
  "compilerOptions": {
    "paths": { "@/*": ["./src/*"] }
  }
}
```

Isse `../../../components/Button` ki jagah `@/components/Button` likhte ho — clean aur file move karne pe imports break nahi hote.

## Error Boundaries Aur Loading States Har Route Pe

Har major route segment mein `loading.tsx` aur `error.tsx` rakho — chota kaam hai lekin UX bohat improve hota hai (skeleton screens, graceful error handling instead of white screen crash).

## API Layer Abstraction

Kabhi bhi components ke andar seedha `fetch`/`axios` mat likho — ek dedicated layer rakho:

```tsx
// features/products/api.ts
import { api } from "@/lib/axios";
import type { Product } from "./types";

export const productsApi = {
  getAll: () => api.get<Product[]>("/products").then((r) => r.data),
  getById: (id: string) => api.get<Product>(`/products/${id}`).then((r) => r.data),
  create: (data: Partial<Product>) => api.post<Product>("/products", data).then((r) => r.data),
};
```

Faida: backend URL/response shape change ho to ek jagah fix hota hai, testing aasan hoti hai (mock karna simple), Laravel ke Repository pattern jaisa hi concept hai.

## Type Safety End-to-End

Backend response ke liye types define karo, kahin bhi `any` mat use karo:

```tsx
// features/products/types.ts
export type Product = {
  id: string;
  name: string;
  price: number;
  createdAt: string;
};
```

Agar backend Laravel hai aur OpenAPI/Swagger spec generate karta hai, tools jaise `openapi-typescript` se types auto-generate kar sakte ho — hand-written types se drift avoid hota hai.

## Barrel Exports — Sambhal Kar Use Karo

```tsx
// features/products/index.ts
export * from "./components/ProductList";
export * from "./api";
```

Chote modules ke liye theek hai, lekin bade barrel files build performance kharab kar sakti hain (unnecessary imports pull hote hain) — sirf jahan zaroori ho wahan use karo, har folder mein blindly mat banao.

## Git Workflow (Standard, Aapko Laravel Se Pehle Se Pata Hoga)

- `main`/`master` — production
- `develop` (optional) — staging
- `feature/xyz`, `fix/xyz` — feature branches, PR ke through merge

## Code Review Checklist (Frontend-Specific)

- Server Component ho sakta tha, `'use client'` unnecessarily to nahi laga?
- Loading/error states handle hue hain?
- TypeScript `any` to nahi use hua bina wajah ke?
- Images `next/image` se hain, plain `<img>` to nahi?
- Environment secrets `NEXT_PUBLIC_` prefix ke sath to nahi (accidental leak)?
- API calls dedicated layer (`lib/api.ts` / `features/*/api.ts`) se ho rahi hain, component ke andar inline to nahi?
- Form validation client + server dono jagah hai (client UX ke liye, server security ke liye)?

## Monorepo — Bade Projects Ke Liye (Turborepo)

Jab frontend + multiple apps (admin panel, marketing site) + shared packages ek repo mein rakhne hon:

```
apps/
├── web/          → main Next.js app
├── admin/        → admin Next.js app
packages/
├── ui/            → shared components
└── config/         → shared eslint/tsconfig
```

`turborepo` ya `nx` isse manage karte hain — abhi ke liye sirf jaan lo ye exist karta hai, jab team/project bada ho tab explore karna.

## Environment Config Management

Production secrets kabhi git mein commit na karo. `.env.example` file rakho (bina real values ke) taake team ko pata chale kaun se vars chahiye:

```
# .env.example
DATABASE_URL=
NEXT_PUBLIC_API_URL=
NEXTAUTH_SECRET=
```

## Logging/Monitoring

Production mein `console.log` kaafi nahi — `@sentry/nextjs` install karke errors track karo, taake user ke crash hone se pehle aapko pata chale.

## Practice

1. Apna 06-07 files wala practice project uthao, `features/` folder structure mein migrate karo.
2. `lib/axios.ts` + ek `features/<name>/api.ts` layer banao, sab direct fetch calls hata kar isse route karo.
3. Khud ka code review checklist ke against apna kaam check karo.

Agli file: [16_practice_projects_roadmap.md](16_practice_projects_roadmap.md)
</content>
