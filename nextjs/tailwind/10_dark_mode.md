# Dark Mode

## Basic Setup — `dark:` Prefix

Tailwind mein by default dark mode **OS ki system setting** follow karta hai:

```html
<div class="bg-white text-black dark:bg-gray-900 dark:text-white">
  Light mode mein white background, dark mode mein dark background
</div>
```

Jab user ka OS/browser dark mode pe ho, `dark:` wali classes automatically apply ho jati hain — koi extra JS code nahi chahiye is default mode mein.

## Manual Toggle — User Khud Switch Kare (Zyada Common Real Projects Mein)

Agar app mein ek toggle button chahiye (system settings pe depend na ho), to Tailwind v4 mein CSS variant define karo:

```css
/* app/globals.css */
@import "tailwindcss";

@custom-variant dark (&:where(.dark, .dark *));
```

Ab `dark:` classes tab apply hongi jab kisi parent element (usually `<html>`) pe `class="dark"` ho — system setting se independent.

## `<html>` Tag Pe Class Toggle Karna (Next.js Example)

```tsx
// components/ThemeToggle.tsx
"use client";
import { useState, useEffect } from "react";

export default function ThemeToggle() {
  const [isDark, setIsDark] = useState(false);

  useEffect(() => {
    const stored = localStorage.getItem("theme");
    const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
    const shouldBeDark = stored === "dark" || (!stored && prefersDark);
    setIsDark(shouldBeDark);
    document.documentElement.classList.toggle("dark", shouldBeDark);
  }, []);

  function toggle() {
    const newValue = !isDark;
    setIsDark(newValue);
    document.documentElement.classList.toggle("dark", newValue);
    localStorage.setItem("theme", newValue ? "dark" : "light");
  }

  return (
    <button onClick={toggle} className="p-2 rounded-lg bg-gray-200 dark:bg-gray-700">
      {isDark ? "☀️ Light" : "🌙 Dark"}
    </button>
  );
}
```

**Flash prevent karne ke liye** (page load pe halka sa light-mode flash dikhna) — root layout mein inline script daalo jo React load hone se pehle hi class set kar de:

```tsx
// app/layout.tsx
<html lang="en">
  <head>
    <script
      dangerouslySetInnerHTML={{
        __html: `
          if (localStorage.getItem('theme') === 'dark' ||
              (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
          }
        `,
      }}
    />
  </head>
  <body>{children}</body>
</html>
```

Behtar aur cleaner package option: **`next-themes`** library — ye poora upar wala logic (localStorage, flash prevention, system detection) khud handle karti hai:

```bash
npm install next-themes
```

```tsx
// app/providers.tsx
"use client";
import { ThemeProvider } from "next-themes";

export function Providers({ children }: { children: React.ReactNode }) {
  return <ThemeProvider attribute="class">{children}</ThemeProvider>;
}
```

```tsx
"use client";
import { useTheme } from "next-themes";

const { theme, setTheme } = useTheme();
<button onClick={() => setTheme(theme === "dark" ? "light" : "dark")}>Toggle</button>
```

## Dark Mode Design Pattern — Poora Card Example

```html
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
  <h3 class="text-gray-900 dark:text-white font-semibold">Title</h3>
  <p class="text-gray-600 dark:text-gray-300">Description text</p>
  <button class="bg-blue-600 dark:bg-blue-500 text-white">Action</button>
</div>
```

**Pattern samjho:** Light mode mein halke backgrounds (`white`, `gray-100`) + dark text (`gray-900`). Dark mode mein gehre backgrounds (`gray-800`, `gray-900`) + halka text (`white`, `gray-300`). Colors ko poori tarah invert mat karo (`bg-black dark:bg-white` jaisa) — usually thoda muted rehna better lagta hai.

## Common Mistake

```html
<!-- GALAT: dark: bina light mode wali class ke likhna -->
<div class="dark:bg-gray-900">...</div>
<!-- Light mode mein koi background nahi lagega (transparent rahega) -->

<!-- THEEK: dono states explicitly likho -->
<div class="bg-white dark:bg-gray-900">...</div>
```

## Practice

1. `next-themes` install karke ek toggle button banao apne Next.js project mein.
2. Poora ek page (navbar + card + button) dark mode support ke sath banao — har element mein `dark:` variant zaroor ho.
3. System dark mode ON karke check karo bina toggle click kiye bhi (`class="dark"` set na ho to) site kaisi dikhti hai default `prefers-color-scheme` behavior se.

Agli file: [11_animations_transitions_transforms.md](11_animations_transitions_transforms.md)
</content>
