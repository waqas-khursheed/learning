# Next.js Rendering Strategies — Complete Guide (SSG, SSR, ISR, CSR)

Ye document Next.js App Router (13+) ke context mein hai. Har concept ke sath: kya hai, kaise likhte hain, file structure kaisi hoti hai, real code example, aur kab use karna hai.

---

## 1. Basic Concept — Pehle Ye Samjho

Next.js mein **koi alag "SSR mode" ya "SSG mode" on/off nahi karte**. Har page by default **Server Component** hota hai, aur uska rendering behavior decide hota hai:

1. Tum `fetch()` mein caching kaise likhte ho
2. Kya tum `revalidate`, `dynamic`, ya `generateStaticParams` export karte ho
3. Kya component `"use client"` hai ya nahi

Yani rendering strategy = **kuch chand lines of config**, alag folder structure nahi. Lekin har concept ka apna use-case aur pattern hota hai jo neeche detail mein hai.

---

## 2. SSG (Static Site Generation)

### Kya Hai
HTML **build time** pe (`npm run build` ke waqt) ek hi baar generate hoti hai. Uske baad wo static file CDN pe serve hoti hai — koi server processing nahi hoti future requests pe.

### File Structure
```
src/app/(shop)/about/page.tsx
src/app/(shop)/terms/page.tsx
src/app/(shop)/blog/[slug]/page.tsx
```

### Code Example
```typescript
// src/app/(shop)/about/page.tsx
// Koi extra config nahi likhi — Next.js automatically SSG treat karega
// agar andar koi dynamic function (cookies, headers) use nahi ho rahi

async function getAboutContent() {
  const res = await fetch("https://api.example.com/pages/about", {
    cache: "force-cache", // Explicitly bata rahe hain: build time pe cache karo, kabhi refetch mat karo
  });
  return res.json();
}

export default async function AboutPage() {
  const content = await getAboutContent();

  return (
    <div className="max-w-3xl mx-auto py-10 px-4">
      <h1 className="text-3xl font-bold">{content.title}</h1>
      <p className="mt-4 text-muted-foreground">{content.description}</p>
    </div>
  );
}
```

**Kya ho raha hai:**
- `cache: "force-cache"` (ye Next.js ka default hai fetch ke liye) → matlab ye data sirf ek baar build ke waqt fetch hoga
- Is page ki HTML file build ke baad `.next/` folder mein static generate ho jati hai
- Har user ko wahi pehle se bani hui HTML milti hai

### Kab Use Karo
- About Us, Contact, Terms & Conditions, Privacy Policy
- Blog posts jo publish hone ke baad edit nahi hote
- Landing pages jo rarely change hote hain

### Fayde / Nuksan
| Fayda | Nuksan |
|---|---|
| Sabse fast (CDN se seedha serve) | Data update ke liye dobara build chahiye |
| Zero server load | Real-time data ke liye unfit |
| SEO perfect | — |

---

## 3. SSR (Server-Side Rendering)

### Kya Hai
Har **individual request** pe server real-time HTML generate karta hai aur user ko bhejta hai. Data hamesha fresh hota hai.

### File Structure
```
src/app/admin/dashboard/page.tsx
src/app/(shop)/orders/[id]/page.tsx
```

### Code Example
```typescript
// src/app/admin/dashboard/page.tsx
export const dynamic = "force-dynamic"; // Explicitly SSR force karo, har request pe fresh render

async function getDashboardStats() {
  const res = await fetch("https://api.example.com/admin/stats", {
    cache: "no-store", // Kabhi cache mat karo, hamesha fresh fetch karo
    headers: {
      Authorization: `Bearer ${getServerToken()}`,
    },
  });
  return res.json();
}

export default async function DashboardPage() {
  const stats = await getDashboardStats();

  return (
    <div className="grid grid-cols-2 md:grid-cols-4 gap-4 p-6">
      <StatCard title="Total Orders" value={stats.totalOrders} />
      <StatCard title="Revenue" value={`Rs ${stats.revenue}`} />
      <StatCard title="Pending Orders" value={stats.pending} />
      <StatCard title="Active Users" value={stats.activeUsers} />
    </div>
  );
}
```

**Kya ho raha hai:**
- `cache: "no-store"` → Next.js ko bolta hai ke is fetch ko **kabhi cache mat karo**
- `export const dynamic = "force-dynamic"` → poore page ko dynamic (SSR) mark karta hai
- Har baar jab koi admin dashboard kholega, fresh data server se aayega

### Kab Use Karo
- Admin dashboard (real-time stats)
- Order confirmation / order detail page (payment status fresh chahiye)
- Koi bhi page jo `cookies()` ya `headers()` use kar raha ho (auth-dependent content)

### Fayde / Nuksan
| Fayda | Nuksan |
|---|---|
| Hamesha latest data | Har request pe server processing → high load |
| SEO achi hai | SSG/ISR se slower |
| User-specific content possible | Bahut traffic mein server slow ho sakta hai |

---

## 4. ISR (Incremental Static Regeneration) ⭐ Ecommerce Ke Liye Best

### Kya Hai
SSG jaisa static hi hota hai, lekin ek **time interval** set karte ho jiske baad Next.js **background mein** page ko dobara generate kar deta hai — bina kisi user ko wait karwaye.

### File Structure
```
src/app/(shop)/products/page.tsx
src/app/(shop)/products/[slug]/page.tsx
src/app/(shop)/page.tsx                  # Homepage
```

### Code Example — Product Listing
```typescript
// src/app/(shop)/products/page.tsx
export const revalidate = 60; // Har 60 second baad stale ho jayega aur background regenerate hoga

async function getProducts() {
  const res = await fetch("https://api.example.com/products", {
    next: { revalidate: 60 }, // fetch-level revalidate bhi likh sakte ho
  });
  return res.json();
}

export default async function ProductsPage() {
  const products = await getProducts();

  return (
    <div className="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 p-4">
      {products.map((product: Product) => (
        <ProductCard key={product.id} product={product} />
      ))}
    </div>
  );
}
```

### Code Example — Product Detail (Dynamic Route with Pre-built Pages)
```typescript
// src/app/(shop)/products/[slug]/page.tsx
interface Props {
  params: { slug: string };
}

// Ye function batata hai Next.js ko: "ye products build time pe pehle se bana lo"
export async function generateStaticParams() {
  const res = await fetch("https://api.example.com/products/top?limit=100");
  const topProducts = await res.json();

  return topProducts.map((product: Product) => ({
    slug: product.slug,
  }));
}

export const revalidate = 60;

async function getProductBySlug(slug: string) {
  const res = await fetch(`https://api.example.com/products/${slug}`, {
    next: { revalidate: 60 },
  });
  if (!res.ok) return null;
  return res.json();
}

export default async function ProductDetailPage({ params }: Props) {
  const product = await getProductBySlug(params.slug);

  if (!product) {
    return <div className="p-10 text-center">Product not found</div>;
  }

  return (
    <div className="max-w-6xl mx-auto p-4 grid md:grid-cols-2 gap-8">
      <ProductGallery images={product.images} />
      <div>
        <h1 className="text-2xl font-bold">{product.name}</h1>
        <p className="text-xl text-primary mt-2">Rs {product.price}</p>
        <p className="mt-4 text-muted-foreground">{product.description}</p>
        <AddToCartButton product={product} />
      </div>
    </div>
  );
}

// Optional: SEO metadata bhi dynamically generate karo
export async function generateMetadata({ params }: Props) {
  const product = await getProductBySlug(params.slug);
  return {
    title: product?.name ?? "Product Not Found",
    description: product?.description,
  };
}
```

**Kya ho raha hai (step by step):**
1. Build ke waqt top 100 products ki HTML pehle se generate ho jati hai (`generateStaticParams`)
2. Koi bhi user in products ko visit kare → instant static HTML milti hai
3. 60 second baad wo page "stale" ho jata hai
4. Agla user aaye to usay purana (stale) version milta hai **turant**, lekin background mein Next.js naya version generate kar leta hai
5. Uske baad wale users ko naya version milta hai
6. Agar koi product visit ho jo top 100 mein nahi tha → pehli baar SSR jaisa generate hota hai, phir wo bhi cache ho jata hai (**On-Demand ISR**)

### Kab Use Karo
- Product listing pages
- Product detail pages
- Category pages
- Homepage (featured products, banners)
- Blog listing (agar frequently publish hota ho)

### Fayde / Nuksan
| Fayda | Nuksan |
|---|---|
| SSG jaisa fast | Data thoda stale ho sakta hai (revalidate window tak) |
| Server load minimum (SSR se bohot kam) | Real-time data (jaise stock count) ke liye unfit |
| Bahut users ek sath handle kar leta hai | — |
| SEO perfect | — |

---

## 5. CSR (Client-Side Rendering)

### Kya Hai
Server sirf khaali HTML shell bhejta hai. Browser mein JavaScript chal kar API call karta hai aur data ke sath UI banata hai.

### File Structure
```
src/app/(shop)/cart/page.tsx
src/app/(shop)/checkout/page.tsx
src/app/admin/products/page.tsx
```

### Code Example
```typescript
// src/app/(shop)/cart/page.tsx
"use client"; // Ye line CSR trigger karti hai

import { useCart } from "@/hooks/useCart";
import { CartItem } from "@/components/cart/CartItem";
import { CartSummary } from "@/components/cart/CartSummary";
import { Loader } from "@/components/shared/Loader";

export default function CartPage() {
  const { data: cart, isLoading, error } = useCart();

  if (isLoading) return <Loader />;
  if (error) return <div className="p-10 text-center">Cart load nahi ho saka</div>;
  if (!cart || cart.items.length === 0) {
    return <div className="p-10 text-center">Aapka cart khaali hai</div>;
  }

  return (
    <div className="max-w-4xl mx-auto p-4 grid md:grid-cols-3 gap-6">
      <div className="md:col-span-2 space-y-3">
        {cart.items.map((item) => (
          <CartItem key={item.id} item={item} />
        ))}
      </div>
      <CartSummary cart={cart} />
    </div>
  );
}
```

```typescript
// src/hooks/useCart.ts
import { useQuery } from "@tanstack/react-query";
import { cartService } from "@/services";

export function useCart() {
  return useQuery({
    queryKey: ["cart"],
    queryFn: cartService.getCart,
    staleTime: 0, // Cart hamesha fresh chahiye
  });
}
```

**Kya ho raha hai:**
- `"use client"` likhte hi ye component browser mein render hota hai
- Server sirf ek basic HTML shell bhejta hai + JavaScript bundle
- Browser mein `useCart()` hook chalta hai jo TanStack Query se API call karta hai
- Jab tak data nahi aata, `Loader` dikhta hai

### Kab Use Karo
- Cart, Checkout (personal, real-time, SEO ki zarurat nahi)
- Admin panel ke saare pages
- Wishlist, user settings
- Koi bhi highly interactive UI (filters jo instantly update hon, live search)

### Fayde / Nuksan
| Fayda | Nuksan |
|---|---|
| Server pe zero load | SEO kharab (Google ko khaali HTML milti hai) |
| Highly interactive UI ke liye best | First load thoda slow (JS download + execute) |
| Personal data ke liye safe pattern | — |

---

## 6. Sab Ka Comparison — Ek Nazar Mein

| Strategy | HTML Kab Banti Hai | Speed | SEO | Data Freshness | Server Load | Config |
|---|---|---|---|---|---|---|
| **SSG** | Build time (once) | ⚡⚡⚡ | ✅ Best | ❌ Manual rebuild chahiye | ✅ None | `cache: "force-cache"` (default) |
| **ISR** | Build + periodic | ⚡⚡⚡ | ✅ Best | ✅ Auto-refresh | ✅ Very Low | `revalidate: N` |
| **SSR** | Har request pe | ⚡⚡ | ✅ Best | ✅✅ Always fresh | ❌ High | `cache: "no-store"` / `dynamic: "force-dynamic"` |
| **CSR** | Browser mein | ⚡ (first load slow) | ❌ Poor | ✅✅ Always fresh | ✅ None (client pe) | `"use client"` |

---

## 7. Tumhare Ecommerce Project Ki Complete File-Wise Mapping

```
src/app/
├── (shop)/
│   ├── page.tsx                    → ISR (revalidate: 60)     [Homepage]
│   ├── products/
│   │   ├── page.tsx                → ISR (revalidate: 60)     [Product listing]
│   │   └── [slug]/page.tsx         → ISR + generateStaticParams [Product detail]
│   ├── categories/[slug]/page.tsx  → ISR (revalidate: 120)    [Category page]
│   ├── cart/page.tsx               → CSR ("use client")        [Cart]
│   ├── checkout/page.tsx           → CSR ("use client")        [Checkout]
│   ├── orders/[id]/page.tsx        → SSR (cache: "no-store")   [Order status - fresh chahiye]
│   ├── blog/[slug]/page.tsx        → SSG (cache: "force-cache") [Blog post]
│   ├── about/page.tsx              → SSG                       [About]
│   └── terms/page.tsx              → SSG                       [Terms]
│
└── admin/
    ├── dashboard/page.tsx          → SSR or CSR                [Real-time stats]
    ├── products/page.tsx           → CSR ("use client")        [Admin CRUD]
    └── orders/page.tsx             → CSR ("use client")        [Admin CRUD]
```

---

## 8. Decision Flowchart (Kab Kya Use Karo)

```
Page banate waqt ye sawal khud se pucho:

1. Kya ye page SEO ke liye important hai (Google pe rank hona chahiye)?
   NAHI → CSR use karo ("use client")
   HAAN → Neeche jao

2. Kya data user-specific / highly sensitive / real-time critical hai?
   HAAN → SSR use karo (cache: "no-store")
   NAHI → Neeche jao

3. Kya data kabhi kabhi change hota hai (minutes/hours mein)?
   HAAN → ISR use karo (revalidate: N seconds)
   NAHI (bilkul static content) → SSG use karo (default cache)
```

---

## 9. Ek Zaroori Baat — Server vs Client Component Mix

Ek page **SSR/SSG/ISR (Server Component)** ho sakta hai, aur uske andar **CSR (Client Component)** bhi nested ho sakta hai. Ye bohot common pattern hai:

```typescript
// src/app/(shop)/products/[slug]/page.tsx
// Ye Server Component hai (ISR)
export const revalidate = 60;

export default async function ProductDetailPage({ params }: Props) {
  const product = await getProductBySlug(params.slug); // Server pe fetch

  return (
    <div>
      <h1>{product.name}</h1>                {/* Static, ISR se aya */}
      <p>Rs {product.price}</p>               {/* Static, ISR se aya */}
      <AddToCartButton product={product} />   {/* Ye andar "use client" hai - interactive */}
    </div>
  );
}
```

```typescript
// src/components/product/AddToCartButton.tsx
"use client"; // Sirf ye component client-side hai

import { useCartStore } from "@/store/cart.store";

export function AddToCartButton({ product }: { product: Product }) {
  const addItem = useCartStore((state) => state.addItem);

  return (
    <button onClick={() => addItem(product)} className="...">
      Add to Cart
    </button>
  );
}
```

**Ye pattern best practice hai:** Static content (product info) ISR se aata hai (fast + SEO), sirf jo cheez interactive honi chahiye (button click) wo Client Component hai. Poora page CSR nahi karna parta.

---

## 10. Quick Reference — Config Cheatsheet

```typescript
// SSG - default behavior, kuch likhne ki zarurat nahi
fetch(url) // default cache: "force-cache"

// SSG - explicit
fetch(url, { cache: "force-cache" })

// ISR
fetch(url, { next: { revalidate: 60 } })
// ya poore page ke liye:
export const revalidate = 60;

// SSR
fetch(url, { cache: "no-store" })
// ya poore page ke liye:
export const dynamic = "force-dynamic";

// CSR
"use client";
// phir useEffect/TanStack Query se client mein fetch karo
```

---

## Summary — Ek Line Mein Yaad Rakho

- **SSG** = "Ye kabhi change nahi hoga" → build time pe ban jao
- **ISR** = "Ye thoda thoda change hota hai" → static bano, periodically refresh ho jao
- **SSR** = "Ye har baar different/fresh hona chahiye" → har request pe server pe banao
- **CSR** = "Ye personal hai, SEO ki zarurat nahi" → browser mein bana lo
