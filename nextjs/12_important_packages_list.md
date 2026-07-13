# Important Packages — Kya Kaam Karta Hai

Ye poore ecosystem ka reference table hai. Sab packages ek project mein nahi lagte — apni zaroorat ke hisaab se choose karo.

## Core (Already `create-next-app` Se Milte Hain)

| Package | Kaam |
|---|---|
| `next` | Framework khud |
| `react`, `react-dom` | UI library |
| `typescript` | Type safety |
| `eslint`, `eslint-config-next` | Code linting |
| `tailwindcss` | Utility CSS |

## Styling / UI

| Package | Kaam |
|---|---|
| `clsx` | Conditional classNames combine karna |
| `tailwind-merge` | Conflicting Tailwind classes intelligently merge karna |
| `shadcn/ui` (CLI, npm package nahi) | Pre-built accessible components (source copy hota hai) |
| `@radix-ui/*` | Unstyled accessible primitives (shadcn inke upar based hai) |
| `lucide-react` | Icon library (chota, tree-shakeable) |
| `framer-motion` | Animations aur transitions |
| `class-variance-authority` (cva) | Component variants manage karna (size, color props) |

## Forms Aur Validation

| Package | Kaam |
|---|---|
| `react-hook-form` | Performant form state management |
| `zod` | Schema validation (TypeScript-first) |
| `@hookform/resolvers` | react-hook-form ko Zod se connect karta hai |

## Data Fetching / State

| Package | Kaam |
|---|---|
| `axios` | HTTP client (interceptors, better error handling) |
| `@tanstack/react-query` | Client-side data fetching, caching, refetch |
| `swr` | react-query ka lighter alternative (Vercel ka) |
| `zustand` | Simple global state management |
| `@reduxjs/toolkit` + `react-redux` | Enterprise-level state management |
| `jotai` | Atomic state management |

## Database / Backend (Full-Stack Next.js)

| Package | Kaam |
|---|---|
| `prisma` + `@prisma/client` | ORM (schema, migrations, queries) |
| `drizzle-orm` | Lightweight SQL-close ORM |
| `mongoose` | MongoDB ODM |
| `next-auth` (Auth.js) | Authentication (OAuth, credentials, sessions) |
| `bcryptjs` | Password hashing |
| `jsonwebtoken` | JWT create/verify |

## Utilities

| Package | Kaam |
|---|---|
| `date-fns` | Date formatting/manipulation (lightweight, Moment.js ka replacement) |
| `lodash` / `lodash-es` | Utility functions (debounce, groupBy, etc) |
| `zod` | (upar bhi) — general-purpose runtime validation |
| `uuid` | Unique IDs generate karna |
| `sharp` | Image processing (Next.js internally bhi use karta hai) |

## Testing

| Package | Kaam |
|---|---|
| `jest` / `vitest` | Unit testing framework |
| `@testing-library/react` | Component testing (user-behavior focused) |
| `playwright` | End-to-end browser testing |
| `cypress` | End-to-end testing (alternative to Playwright) |

## Dev Tools / Code Quality

| Package | Kaam |
|---|---|
| `prettier` | Code formatting |
| `husky` | Git hooks (commit se pehle lint/test chalana) |
| `lint-staged` | Sirf changed files pe lint chalana |
| `commitlint` | Commit message convention enforce karna |

## Monitoring / Analytics (Production)

| Package | Kaam |
|---|---|
| `@vercel/analytics` | Page views, web vitals tracking (Vercel pe deploy ho to) |
| `@sentry/nextjs` | Error tracking aur monitoring |
| `posthog-js` | Product analytics |

## Ek Typical Real Project Ka `package.json` (Reference)

```json
{
  "dependencies": {
    "next": "^15.0.0",
    "react": "^19.0.0",
    "react-dom": "^19.0.0",
    "axios": "^1.7.0",
    "zustand": "^5.0.0",
    "@tanstack/react-query": "^5.0.0",
    "react-hook-form": "^7.53.0",
    "zod": "^3.23.0",
    "@hookform/resolvers": "^3.9.0",
    "clsx": "^2.1.0",
    "tailwind-merge": "^2.5.0",
    "lucide-react": "^0.460.0",
    "date-fns": "^4.1.0"
  },
  "devDependencies": {
    "typescript": "^5.6.0",
    "tailwindcss": "^3.4.0",
    "prettier": "^3.3.0",
    "eslint": "^8.57.0",
    "eslint-config-next": "^15.0.0"
  }
}
```

## Practice

1. Ek fresh project mein `axios`, `zustand`, `@tanstack/react-query`, `zod`, `react-hook-form`, `@hookform/resolvers`, `clsx`, `lucide-react` install karo — inhe zaroorat parne pe individually use karna practice karo.
2. `npx shadcn@latest init` chala kar dekho ye kya kya install karta hai (`class-variance-authority`, `tailwind-merge` waghera automatically add hote hain).

Agli file: [13_seo_performance.md](13_seo_performance.md)
</content>
