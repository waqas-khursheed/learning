# Styling — Tailwind CSS, CSS Modules, shadcn/ui

> **Poora Tailwind CSS deep-dive** (zero se advanced tak — spacing, colors, flexbox/grid, responsive, dark mode, animations, custom theme, reusable components) alag folder mein hai: [tailwind/00_ROADMAP.md](tailwind/00_ROADMAP.md). Ye file sirf ek chota overview hai.

## Option 1: Tailwind CSS (Sabse Zyada Recommended)

Tailwind ek **utility-first CSS framework** hai — har class ek chota CSS rule hoti hai, aap classes combine karke design banate ho, alag `.css` file likhne ki zaroorat nahi.

```tsx
<div className="flex items-center justify-between p-4 bg-white shadow rounded-lg">
  <h2 className="text-xl font-bold text-gray-800">Title</h2>
  <button className="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
    Click
  </button>
</div>
```

`create-next-app` mein "Tailwind CSS" ka option "Yes" chunte hi ye already setup ho jata hai. Common classes:

| Category | Examples |
|---|---|
| Spacing | `p-4` (padding), `m-2` (margin), `gap-4` |
| Layout | `flex`, `grid`, `block`, `hidden` |
| Sizing | `w-full`, `h-screen`, `max-w-md` |
| Colors | `bg-blue-600`, `text-gray-800`, `border-red-500` |
| Typography | `text-xl`, `font-bold`, `text-center` |
| Responsive | `sm:`, `md:`, `lg:` prefix (`md:flex-row`) |
| States | `hover:`, `focus:`, `disabled:` prefix |
| Dark mode | `dark:bg-black` (agar dark mode config ho) |

Responsive design example: `<div className="flex flex-col md:flex-row">` — mobile pe column, medium screen se upar row.

## Option 2: CSS Modules

Agar plain CSS pasand hai, scoped (component-specific) style ke liye:

```css
/* Button.module.css */
.button {
  padding: 8px 16px;
  background: blue;
  color: white;
}
```

```tsx
import styles from "./Button.module.css";

<button className={styles.button}>Click</button>
```

CSS Module ki class names automatically unique hoti hain (koi global clash nahi hota) — bade projects mein Tailwind ke sath bhi kabhi kabhi component-specific complex animations ke liye use hoti hain.

## Option 3: shadcn/ui — Component Library

Ye ek library **nahi** hai jo `npm install` se aati ho — ye ek CLI hai jo ready-made components (Button, Dialog, Dropdown, Form waghera) ka **source code seedha aapke project mein copy kar deti hai**. Aap fully customize kar sakte ho kyun ke code aapke paas hota hai.

```bash
npx shadcn@latest init
npx shadcn@latest add button
npx shadcn@latest add dialog card input
```

Ye components `components/ui/` mein aa jate hain, Tailwind + Radix UI (accessible primitives) pe based hote hain. Senior/professional projects mein bohat use hoti hai kyun ke design consistent aur accessible hota hai bina zyada kaam ke.

```tsx
import { Button } from "@/components/ui/button";

<Button variant="outline" size="lg">Click Me</Button>
```

## `clsx` / `tailwind-merge` — Conditional Classes

Jab classes conditionally apply karni hon:

```tsx
import clsx from "clsx";

<button className={clsx("px-4 py-2 rounded", isActive && "bg-blue-600", !isActive && "bg-gray-300")}>
  Toggle
</button>
```

`tailwind-merge` conflicting Tailwind classes ko intelligently merge karta hai (jaise agar do jagah se `p-2` aur `p-4` aa rahi hon to sirf last wali rahe). shadcn projects mein `cn()` helper (jo dono ko combine karta hai) already `lib/utils.ts` mein milta hai.

## Global Styles

`app/globals.css` mein Tailwind import hota hai aur koi bhi truly global CSS (font-face, CSS variables, resets):

```css
@import "tailwindcss";

:root {
  --primary-color: #2563eb;
}
```

Ye sirf `app/layout.tsx` mein ek dafa import hoti hai — poori app mein apply hoti hai.

## Practice

1. Tailwind se ek responsive navbar banao: mobile pe hamburger-style stacked links, `md:` se upar horizontal row.
2. `npx shadcn@latest add button card` chala kar dekho `components/ui/` mein kya banta hai — code parho, samjho ye normal React component hi hai.
3. `clsx` use karke ek `Badge` component banao jiska color prop (`success`/`error`/`warning`) ke hisaab se background change ho.

Agli file: [07_state_hooks.md](07_state_hooks.md)
</content>
