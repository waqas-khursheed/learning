# Custom Theme Aur Configuration

## Tailwind v4 — CSS-First Config (`@theme`)

Purane Tailwind (v3) mein sab kuch `tailwind.config.js` mein JavaScript object ki tarah define hota tha. **v4 mein config seedha CSS file mein `@theme` block ke andar hota hai:**

```css
/* app/globals.css */
@import "tailwindcss";

@theme {
  --color-brand: #6d28d9;
  --color-brand-light: #a78bfa;
  --font-display: "Poppins", sans-serif;
  --spacing-18: 4.5rem;
  --breakpoint-3xl: 1920px;
}
```

Ab ye naye tokens directly classes ki tarah use ho sakte hain:

```html
<div class="bg-brand text-white font-display p-18">
  Custom brand color, custom font, custom spacing — sab automatic class ban gayi
</div>
```

## Apna Color Palette Add Karna

```css
@theme {
  --color-primary-50: #eff6ff;
  --color-primary-100: #dbeafe;
  --color-primary-500: #3b82f6;
  --color-primary-600: #2563eb;
  --color-primary-900: #1e3a8a;
}
```

```html
<button class="bg-primary-600 hover:bg-primary-700 text-white">
  Apna brand color, poori scale ke sath
</button>
```

Real projects mein ye common hota hai — company/client ka brand color palette poore project mein consistent naming (`primary-*`, `secondary-*`, `accent-*`) se use hota hai, random hex values baar baar likhne ki zaroorat nahi.

## Extend Vs Override — Zaroori Farak

`@theme` block mein jo bhi likhoge wo **default Tailwind values ke sath add** hota hai (extend jaisa behavior). Agar koi default value **poori tarah replace** karni ho (jaise pura spacing scale hi apna define karna), to us specific namespace ko override karna hota hai — zyada tar cases mein sirf extend hi kaafi hota hai, poora scale replace karne ki zaroorat kam parti hai.

## Custom Font Family (Next.js Ke Sath)

```tsx
// app/layout.tsx
import { Poppins } from "next/font/google";

const poppins = Poppins({ subsets: ["latin"], weight: ["400", "600", "700"], variable: "--font-poppins" });

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html className={poppins.variable}>
      <body>{children}</body>
    </html>
  );
}
```

```css
@theme {
  --font-poppins: var(--font-poppins);
}
```

```html
<h1 class="font-poppins">Custom font heading</h1>
```

## Custom Spacing Scale

```css
@theme {
  --spacing-1.5: 0.375rem;
  --spacing-128: 32rem;
}
```

```html
<div class="w-128">Custom width value</div>
```

## tailwind.config.js — Ab Bhi Kab Chahiye Hota Hai

Kuch advanced cheezein abhi bhi JS config file mein hoti hain (plugins register karna, content paths agar auto-detect na ho rahi ho, safelist):

```js
// tailwind.config.ts
import type { Config } from "tailwindcss";

export default {
  theme: {
    extend: {
      colors: {
        brand: "#6d28d9",
      },
    },
  },
  plugins: [require("@tailwindcss/forms"), require("@tailwindcss/typography")],
} satisfies Config;
```

**Rule of thumb:** Simple design tokens (colors, spacing, fonts) → `@theme` (CSS) mein karo. Plugins aur complex logic → `tailwind.config.ts` mein karo.

## Design System Approach — Senior Level Tip

Chote project mein direct `bg-blue-600` use karna theek hai. Bade/team projects mein **semantic naming** better hoti hai:

```css
@theme {
  --color-primary: var(--color-blue-600);
  --color-primary-hover: var(--color-blue-700);
  --color-danger: var(--color-red-600);
  --color-success: var(--color-emerald-600);
}
```

```html
<button class="bg-primary hover:bg-primary-hover">Submit</button>
```

Faida: agar brand color change karna ho (blue se purple), sirf ek jagah (`@theme`) change karo — poore codebase mein `bg-blue-600` dhoondh kar replace nahi karna parta.

## Practice

1. Apne `globals.css` mein `@theme` block add karo, ek custom `--color-brand` define karo, use karke ek button banao.
2. `next/font` se ek Google Font import karo, `@theme` mein `--font-*` variable bana kar use karo.
3. Semantic color naming try karo (`--color-primary`, `--color-danger`) — ek chota form banao jisme in naming se buttons/errors styled hon.

Agli file: [13_reusable_components_apply_cva.md](13_reusable_components_apply_cva.md)
</content>
