# Next.js Kya Hai Aur Kyun Use Karen

## Next.js Kya Hai

Next.js ek **React framework** hai — React khud sirf ek UI library hai (sirf components banati hai), Next.js us par ek pura production-ready structure chadhata hai: routing, server-side rendering, API banane ki ability, image/font optimization, aur deployment ka pura setup — sab built-in.

Analogy jo aapko samajh aayegi: **React = Eloquent (sirf ek piece)**, **Next.js = Laravel (pura framework, routing + rendering + everything included)**.

## React Akela Kyun Kaafi Nahi

Plain React (Create React App / Vite) se app banate ho to:
- Sirf **Client-Side Rendering (CSR)** milti hai — browser pe pura JS load hokar phir page banta hai. SEO kharab hota hai (Google ko khali HTML milta hai), first load slow hota hai.
- Routing khud add karni parti hai (react-router).
- API calls ke liye alag backend chahiye hota hai, ya khud fetch/axios setup karna parta hai — koi built-in API layer nahi.
- Image optimization, code splitting, bundling — sab khud configure karna parta hai (Webpack/Vite).

Next.js ye sab **out of the box** deta hai.

## Rendering Strategies (Sabse Important Concept)

Next.js ki sabse badi taqat ye hai ke aap **har page ke liye alag rendering strategy** choose kar sakte ho:

| Strategy | Full Naam | Kab HTML banta hai | Kab use karo |
|---|---|---|---|
| **CSR** | Client-Side Rendering | Browser mein, JS load hone ke baad | Dashboard, admin panel (SEO zaroori nahi) |
| **SSR** | Server-Side Rendering | Har request pe server pe | Frequently changing data, personalized pages (user dashboard) |
| **SSG** | Static Site Generation | Build time pe, ek dafa | Blog, marketing pages, docs (data rarely change hota hai) |
| **ISR** | Incremental Static Regeneration | Build time pe + periodically refresh | Product listing, news — static jaisa fast, par data thora thora update hota rahe |

Next.js App Router mein aap ye per-page control karte ho `fetch` ke options se (`cache`, `revalidate`) — ye [08_data_fetching_api_integration.md](08_data_fetching_api_integration.md) mein detail se aayega.

## App Router vs Pages Router

Next.js ke 2 routing systems hain:

- **Pages Router** (`pages/` folder) — purana system, Next.js 12 aur pehle ka default.
- **App Router** (`app/` folder) — Next.js 13+ ka naya system, Server Components support karta hai, zyada powerful hai.

**Aap App Router hi seekho** — naye projects sab App Router mein bante hain, Pages Router sirf legacy projects maintain karne ke liye milta hai. Is poore course mein hum App Router use karenge.

## Next.js Kab Use Karo (Aur Kab Na Karo)

**Use karo jab:**
- SEO important hai (public website, blog, e-commerce, marketing site)
- Fast initial page load chahiye
- Aapko frontend + kuch lightweight backend logic (API routes) ek hi jagah chahiye
- Full-stack app banani hai bina alag backend project ke

**Skip kar sakte ho jab:**
- Pure internal admin tool hai jahan SEO ka koi matlab nahi (plain React/Vite bhi chalega, thora simpler)
- Aapka backend already Laravel/Node mein bohat bhara hai aur Next.js sirf UI dikhayega — is case mein bhi Next.js chalta hai, bas API routes use nahi karoge (dekho [10_backend_database_integration.md](10_backend_database_integration.md))

## Next.js Ke Core Features (Ek Nazar Mein)

1. **File-based routing** — folder banao, route ban jata hai (routes.php likhne ki zaroorat nahi)
2. **Server Components** — components jo server pe render hote hain, browser ko kam JS bhejte hain
3. **Route Handlers** — `app/api/*/route.ts` mein mini backend endpoints (jaise Laravel controller method)
4. **Server Actions** — form submit seedha server function call kare, API route banaye bina
5. **Built-in Image/Font optimization** — `next/image`, `next/font`
6. **Middleware** — har request se pehle chalne wala code (auth check, redirects)
7. **Zero-config bundling** — Webpack/Turbopack khud handle hota hai

## Practice

1. Ek sentence mein khud ko explain karo: SSR aur SSG mein farak kya hai, aur kis type ki website ke liye kaun sa better hoga (ek blog vs ek live stock price dashboard).
2. Socho: agar aap apni Laravel app ka sirf frontend Next.js mein banate, to APIs kahan se aatin — Laravel se ya Next.js Route Handlers se? (Answer [10_backend_database_integration.md](10_backend_database_integration.md) mein milega.)

Agli file: [02_installation_aur_setup.md](02_installation_aur_setup.md)
</content>
