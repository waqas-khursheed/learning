# SEO Aur Performance Optimization

## Metadata API

Static metadata (per page):

```tsx
// app/about/page.tsx
export const metadata = {
  title: "About Us | MySite",
  description: "Hamari company ke baare mein",
  openGraph: {
    title: "About Us",
    images: ["/og-image.png"],
  },
};
```

Dynamic metadata (jab data pe depend kare, jaise blog post title):

```tsx
// app/blog/[slug]/page.tsx
export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const post = await getPost(slug);
  return {
    title: post.title,
    description: post.excerpt,
  };
}
```

Root layout mein ek base template bhi set kar sakte ho:

```tsx
// app/layout.tsx
export const metadata = {
  title: { default: "MySite", template: "%s | MySite" },
  description: "Default description",
};
```

## `next/image` — Automatic Image Optimization

```tsx
import Image from "next/image";

<Image src="/hero.png" alt="Hero" width={800} height={400} priority />
```

Faida: automatic lazy loading (jab tak `priority` na do), responsive sizes generate karta hai, WebP mein convert karta hai, layout shift (CLS) prevent karta hai. Plain `<img>` tag ki jagah **hamesha** `next/image` use karo.

External images ke liye `next.config.ts` mein domain allow karna parta hai:

```ts
// next.config.ts
const nextConfig = {
  images: {
    remotePatterns: [{ hostname: "your-laravel-backend.com" }],
  },
};
```

## `next/font` — Font Optimization

```tsx
import { Inter } from "next/font/google";

const inter = Inter({ subsets: ["latin"] });

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return <html className={inter.className}>...</html>;
}
```

Fonts build time pe download hote hain aur self-host ho jate hain — Google Fonts ko runtime pe call nahi karna parta (privacy + speed dono behtar).

## Dynamic Import — Code Splitting

Bade components (charts, editors, modals) ko sirf tab load karo jab zaroorat ho:

```tsx
import dynamic from "next/dynamic";

const HeavyChart = dynamic(() => import("@/components/HeavyChart"), {
  loading: () => <p>Loading chart...</p>,
  ssr: false,   // agar component browser-only library use karta ho
});
```

## Sitemap Aur Robots.txt

```tsx
// app/sitemap.ts
export default function sitemap() {
  return [
    { url: "https://mysite.com", lastModified: new Date() },
    { url: "https://mysite.com/about", lastModified: new Date() },
  ];
}
```

```tsx
// app/robots.ts
export default function robots() {
  return {
    rules: { userAgent: "*", allow: "/" },
    sitemap: "https://mysite.com/sitemap.xml",
  };
}
```

Ye files khud `/sitemap.xml` aur `/robots.txt` generate kar deti hain, koi extra config nahi chahiye.

## Static Params — SSG Ke Liye

Agar blog posts ka list pehle se pata hai (build time pe), unhe static generate karo:

```tsx
export async function generateStaticParams() {
  const posts = await getAllPosts();
  return posts.map((post) => ({ slug: post.slug }));
}
```

Isse har blog post build time pe hi HTML ban jata hai — request ke waqt instant serve hota hai (SSG behavior).

## Performance Checklist

| Cheez | Kyun Zaroori |
|---|---|
| `next/image` use karo, `<img>` nahi | Layout shift aur bandwidth bachata hai |
| Client Components ko chota rakho | Kam JS browser ko bhejna parega |
| `dynamic()` se heavy components lazy load karo | Initial bundle chota rahega |
| Server Component mein data fetch karo, client mein nahi | Waterfall requests avoid honge |
| `revalidate`/`cache` sahi set karo | Zaroorat se zyada fresh fetch na ho |
| Lighthouse (Chrome DevTools) se regularly check karo | Core Web Vitals (LCP, CLS, INP) measure karta hai |

## Practice

1. Apni site ka ek page `next/image` use karke banao, `priority` attribute ka farak dekho (network tab mein).
2. `app/sitemap.ts` aur `app/robots.ts` banao.
3. Chrome DevTools → Lighthouse chala kar apni dev build ka score dekho, suggestions parho.

Agli file: [14_local_dev_aur_deployment.md](14_local_dev_aur_deployment.md)
</content>
