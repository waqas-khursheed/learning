# State Management Aur Hooks

Hooks sirf **Client Components** (`'use client'`) mein chal sakte hain.

## useState — Local State

```tsx
"use client";
import { useState } from "react";

export default function Counter() {
  const [count, setCount] = useState(0);

  return (
    <div>
      <p>Count: {count}</p>
      <button onClick={() => setCount(count + 1)}>+</button>
      <button onClick={() => setCount((prev) => prev - 1)}>-</button>
    </div>
  );
}
```

Rule: `setCount` call karne se **poora function re-run** hota hai, `count` ki nayi value ke sath. Object/array state update karte waqt hamesha naya reference do (mutate mat karo):

```tsx
setUser((prev) => ({ ...prev, name: "New Name" }));   // ✅ correct
setItems((prev) => [...prev, newItem]);                // ✅ correct
```

## useEffect — Side Effects

Jab component render hone ke *baad* kuch chalana ho (subscription, timer, manual data fetch on client):

```tsx
"use client";
import { useEffect, useState } from "react";

export default function LiveClock() {
  const [time, setTime] = useState(new Date());

  useEffect(() => {
    const interval = setInterval(() => setTime(new Date()), 1000);
    return () => clearInterval(interval);   // cleanup — component unmount pe chalta hai
  }, []);   // empty array = sirf ek dafa mount pe chale

  return <p>{time.toLocaleTimeString()}</p>;
}
```

**Zaroori:** dependency array `[]` khali ho to effect sirf mount pe chalta hai. Agar `[userId]` ho to jab bhi `userId` change ho, effect dobara chalega. **App Router mein data fetching ke liye `useEffect` ka use avoid karo** — Server Component mein seedha `await fetch()` karna better hai ([08_data_fetching_api_integration.md](08_data_fetching_api_integration.md)).

## useContext — Prop Drilling Se Bachna

Jab data ko bohat neeche tak pass karna ho bina har level pe props de kar:

```tsx
"use client";
import { createContext, useContext, useState } from "react";

const ThemeContext = createContext<{ theme: string; toggle: () => void } | null>(null);

export function ThemeProvider({ children }: { children: React.ReactNode }) {
  const [theme, setTheme] = useState("light");
  const toggle = () => setTheme((t) => (t === "light" ? "dark" : "light"));
  return <ThemeContext.Provider value={{ theme, toggle }}>{children}</ThemeContext.Provider>;
}

export function useTheme() {
  const ctx = useContext(ThemeContext);
  if (!ctx) throw new Error("useTheme must be used inside ThemeProvider");
  return ctx;
}
```

`ThemeProvider` ko `app/layout.tsx` mein wrap karo, phir kahin bhi `useTheme()` call karo.

## Custom Hooks — Reusable Logic

Naming convention: `use` se shuru hona chahiye.

```tsx
"use client";
import { useState, useEffect } from "react";

export function useDebounce<T>(value: T, delay = 500): T {
  const [debounced, setDebounced] = useState(value);
  useEffect(() => {
    const timer = setTimeout(() => setDebounced(value), delay);
    return () => clearTimeout(timer);
  }, [value, delay]);
  return debounced;
}
```

```tsx
const debouncedSearch = useDebounce(searchTerm, 300);
```

## useRef — DOM Access Ya Value Persist Karna (Bina Re-render)

```tsx
"use client";
import { useRef } from "react";

const inputRef = useRef<HTMLInputElement>(null);
<input ref={inputRef} />
<button onClick={() => inputRef.current?.focus()}>Focus Input</button>
```

## Global State — Context Se Aage

`useContext` chote apps ke liye theek hai, lekin bade state (cart, user session, global filters) ke liye dedicated library better hoti hai:

| Library | Kab Use Karo |
|---|---|
| **Context API** | Chota, kam-frequent-update state (theme, locale) |
| **Zustand** | Zyada tar cases ke liye recommended — simple, boilerplate kam, TypeScript-friendly |
| **Redux Toolkit** | Bade enterprise apps, complex state logic, team already Redux se familiar ho |
| **Jotai** | Atomic state, fine-grained reactivity chahiye ho |

Zustand example (bohat kam code):

```tsx
import { create } from "zustand";

type CartStore = {
  items: string[];
  addItem: (item: string) => void;
};

export const useCartStore = create<CartStore>((set) => ({
  items: [],
  addItem: (item) => set((state) => ({ items: [...state.items, item] })),
}));

// Kahin bhi Client Component mein:
const { items, addItem } = useCartStore();
```

Zustand ko Context Provider wrap karne ki zaroorat nahi — direct import karke use hota hai.

## Practice

1. `useState` se ek to-do list banao: add, delete, toggle-complete.
2. `useDebounce` custom hook use karke ek search input banao jo 300ms ruk kar console.log kare.
3. Zustand se ek simple cart store banao (`addItem`, `removeItem`, `total`), do alag components mein use karke dekho state share ho rahi hai.

Agli file: [08_data_fetching_api_integration.md](08_data_fetching_api_integration.md)
</content>
