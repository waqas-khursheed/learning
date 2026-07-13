# App Router Aur File Conventions

App Router mein Next.js kuch **special file names** ko recognize karta hai — har ek ka apna kaam hai. In file names ko hubahu yaad rakhna hai (case-sensitive, exact name).

## Special Files

| File | Kaam |
|---|---|
| `page.tsx` | Is folder ka route accessible banata hai. Iske bina folder route nahi banta. |
| `layout.tsx` | Is folder aur sab child routes ko wrap karta hai (header/sidebar/footer jaisa shared UI) |
| `loading.tsx` | Jab tak page ka data load ho raha ho, ye dikhta hai (automatic Suspense boundary) |
| `error.tsx` | Agar route mein error aaye to ye dikhta hai (automatic Error Boundary) |
| `not-found.tsx` | 404 case ke liye custom UI |
| `route.ts` | API endpoint (backend logic) — page.tsx ke sath ek folder mein nahi ho sakta |
| `middleware.ts` | Root mein — har matching request se pehle chalta hai |

## Basic Routing Example

```
app/
├── layout.tsx              → root layout, saare pages ko wrap karta hai
├── page.tsx                 → "/"
├── about/
│   └── page.tsx              → "/about"
└── dashboard/
    ├── layout.tsx             → sirf dashboard routes ko wrap karta hai (sidebar waghera)
    ├── page.tsx                → "/dashboard"
    └── settings/
        └── page.tsx             → "/dashboard/settings"
```

## Dynamic Routes

```
app/blog/[slug]/page.tsx
```

`[slug]` ek dynamic segment hai — `/blog/anything-here` match hoga. Component mein value mile gi:

```tsx
export default async function BlogPost({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  return <h1>Post: {slug}</h1>;
}
```

**Catch-all route:** `app/docs/[...slug]/page.tsx` → `/docs/a`, `/docs/a/b`, `/docs/a/b/c` sab match honge, `slug` array milega (`['a','b','c']`).

**Optional catch-all:** `app/docs/[[...slug]]/page.tsx` → upar wala + `/docs` (bina kisi segment ke) bhi match hoga.

## Route Groups — `(folderName)`

Parenthesis mein folder naam rakho to wo URL mein show nahi hota, sirf organization ke liye hota hai:

```
app/
├── (marketing)/
│   ├── about/page.tsx        → "/about" (marketing/ URL mein nahi aata)
│   └── contact/page.tsx      → "/contact"
└── (app)/
    └── dashboard/page.tsx    → "/dashboard"
```

Use case: `(marketing)` aur `(app)` ke alag-alag `layout.tsx` ho sakte hain, bina URL structure change kiye.

## Layouts — Nested Wrapping

```tsx
// app/layout.tsx (root — MUST have html/body tags)
export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="ur">
      <body>
        <Header />
        {children}
        <Footer />
      </body>
    </html>
  );
}
```

Child layouts automatically parent ke andar nest hote hain — `dashboard/layout.tsx` root layout ke andar render hoga, aur `dashboard/settings/page.tsx` dono ke andar.

## Navigation

```tsx
import Link from "next/link";

<Link href="/about">About</Link>   // client-side navigation, poora page reload nahi hota
```

Programmatic navigation (button click pe, ya form submit ke baad) ke liye:

```tsx
"use client";
import { useRouter } from "next/navigation";

const router = useRouter();
router.push("/dashboard");
router.back();
router.refresh();
```

`<a href>` mat use karo internal links ke liye — wo full page reload karta hai, `Link` seedha JS se navigate karta hai (SPA jaisa fast).

## Metadata (SEO Basics)

```tsx
// app/about/page.tsx
export const metadata = {
  title: "About Us",
  description: "Hamare baare mein jaano",
};
```

Detail [13_seo_performance.md](13_seo_performance.md) mein.

## Practice

1. `app/products/[id]/page.tsx` banao, `params` se `id` nikal kar page pe dikhao.
2. Ek `app/(shop)/` route group banao jisme `cart/page.tsx` aur `checkout/page.tsx` ho, dekho URL mein `(shop)` show nahi hota.
3. `dashboard/loading.tsx` banao jisme sirf "Loading..." text ho — `dashboard/page.tsx` mein 2 second ka artificial delay (`await new Promise(r => setTimeout(r, 2000))`) daal kar dekho loading state kaise dikhti hai.

Agli file: [05_components_server_client.md](05_components_server_client.md)
</content>
