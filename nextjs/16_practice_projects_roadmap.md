# Practice Projects — Beginner Se Senior Tak

Har project pichle se harder hai. Isi order mein banao — sirf tutorials mat dekho, khud likho, atko to docs padho.

## Project 1: Personal Portfolio (Hafta 1)
**Concepts:** App Router basics, layouts, Tailwind, `next/image`, `next/font`, metadata, static deployment.

- Home, About, Projects, Contact pages
- Fully static (SSG) — koi backend nahi chahiye
- Vercel pe deploy karo
- **Goal:** Routing aur styling comfortable ho jaye

## Project 2: Blog With Markdown (Hafta 2)
**Concepts:** Dynamic routes, `generateStaticParams`, `generateMetadata`, sitemap.

- `content/*.md` files se posts padho (ya headless CMS jaisa Contentful/Sanity try karo)
- `/blog` list page + `/blog/[slug]` detail page
- SEO metadata dynamic ho har post ke liye
- **Goal:** Dynamic SSG pages, content-driven sites samajh aayein

## Project 3: CRUD App With Your Laravel Backend (Hafta 3-4)
**Concepts:** Real API integration, Server Components fetching, Server Actions, forms, Zod validation.

- Apni koi existing Laravel API (ya nayi simple banao — products/tasks/notes) ko backend banao
- Next.js frontend: list, create, edit, delete
- `lib/api.ts` layer, Zod validation, loading/error states
- **Goal:** Frontend-backend integration end-to-end pakka ho jaye — ye aapka core skill gap band karega

## Project 4: Auth + Dashboard (Hafta 5-6)
**Concepts:** Authentication, middleware, protected routes, global state, client-side interactivity.

- Login/Signup (Laravel Sanctum ya NextAuth se)
- httpOnly cookie token storage
- Protected `/dashboard` (middleware se guard)
- Zustand se user state, TanStack Query se data fetching/caching
- Charts (recharts ya chart.js) se ek analytics widget
- **Goal:** Real-world app jaisa auth flow, client state management

## Project 5: E-Commerce Ya Booking App (Hafta 7-9)
**Concepts:** Complex forms, cart state, payment integration, optimistic UI, image galleries.

- Product listing (ISR — `revalidate`), filters/search (debounced)
- Cart (Zustand, persist to localStorage)
- Checkout form (react-hook-form + Zod)
- Payment gateway integration (Stripe ya local gateway jaisa aapne Laravel mein kiya hoga)
- **Goal:** Full production-shape app, real payment flow

## Project 6: Full SaaS (Multi-Tenant Ya Team-Based) (Hafta 10+)
**Concepts:** Advanced architecture, feature-based folders, monitoring, CI/CD, role-based access.

- Multi-user, roles/permissions (admin/member)
- Feature-based folder structure ([15_senior_level_structure_best_practices.md](15_senior_level_structure_best_practices.md) follow karo)
- Sentry error monitoring, GitHub Actions CI
- Docker deployment ya Vercel + separate Laravel API on VPS
- **Goal:** Ye aapka portfolio-defining project banega — is standard pe pohanchte hi aap senior frontend/full-stack developer ke barabar ho

## Senior Frontend Developer Banne Ke Liye Final Checklist

- [ ] Server vs Client component decision automatically, bina soche sahi lagta hai
- [ ] Kabhi bhi `useEffect` se data fetch nahi karte jab tak zaroori na ho
- [ ] Har form Zod (ya equivalent) se validate hota hai
- [ ] API calls hamesha ek dedicated layer se hoti hain, kabhi inline nahi
- [ ] `NEXT_PUBLIC_` aur private env vars ka farak muh-zabani pata hai
- [ ] Naya project dekh kar 2 minute mein uska folder structure samajh aata hai
- [ ] Deployment khud handle kar sakte ho — Vercel ya Docker/VPS dono
- [ ] Performance ke liye `next/image`, `dynamic()`, caching strategy jaante ho use karna
- [ ] Apni backend (Laravel/Node) skills ko Next.js ke sath seamlessly jod sakte ho

Ye poori roadmap complete karne ke baad aap ek **full-stack developer** ho jaoge jo frontend aur backend dono confidently handle kar sakta hai — jo bilkul aapka goal tha.
</content>
