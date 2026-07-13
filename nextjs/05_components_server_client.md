# Components Banana — Server vs Client

## Component Kya Hai

React component ek function hai jo JSX (HTML jaisa syntax) return karta hai. Ye aapke UI ka reusable building block hai — jaise Laravel Blade mein `@include` component, bas yahan pura JavaScript function hai.

```tsx
// components/ui/Button.tsx
type ButtonProps = {
  label: string;
  onClick?: () => void;
};

export default function Button({ label, onClick }: ButtonProps) {
  return (
    <button onClick={onClick} className="px-4 py-2 bg-blue-600 text-white rounded">
      {label}
    </button>
  );
}
```

Use karna:

```tsx
import Button from "@/components/ui/Button";

<Button label="Submit" onClick={() => alert("clicked")} />
```

## Server Components (Default)

Next.js App Router mein **har component by default Server Component hota hai** — matlab wo server pe render hota hai, browser ko sirf final HTML milta hai, iska JS bundle mein weight nahi jata (chota bundle = fast site).

Server Component seedha `await` kar sakta hai (database call, API call) — bina `useEffect` ke:

```tsx
// app/users/page.tsx (Server Component — koi 'use client' nahi)
async function getUsers() {
  const res = await fetch("https://api.example.com/users");
  return res.json();
}

export default async function UsersPage() {
  const users = await getUsers();
  return (
    <ul>
      {users.map((u: any) => <li key={u.id}>{u.name}</li>)}
    </ul>
  );
}
```

**Server Component mein ye allowed NAHI hai:** `useState`, `useEffect`, `onClick`, browser-only APIs (`window`, `localStorage`). Kyun ke ye sab browser mein chalta hai, server pe nahi.

## Client Components — `'use client'`

Jab bhi interactivity chahiye (click, form input, state, effect), file ke **sabse upar** likho:

```tsx
"use client";

import { useState } from "react";

export default function Counter() {
  const [count, setCount] = useState(0);
  return <button onClick={() => setCount(count + 1)}>Count: {count}</button>;
}
```

`'use client'` likhne ka matlab: ye component (aur iske children) browser mein bhi JS ke sath render/hydrate honge.

## Kab Konsa Use Karo — Decision Table

| Chahiye | Component Type |
|---|---|
| Database/API se data fetch karna | Server |
| SEO-important static content | Server |
| onClick, onChange, form input | Client |
| useState, useEffect, useContext | Client |
| Browser API (localStorage, window) | Client |
| Third-party library jo hooks use kare (chart library, etc) | Client |

**Rule of thumb: Server Component se shuru karo, jab interactivity chahiye tab hi `'use client'` add karo — aur usse bhi chota (leaf-level) component banao jisme sirf wo interactive part ho.**

## Composition Pattern — Server Component Client Ko Wrap Kare

Best practice: Client Component ko chota rakho, usko Server Component ke andar use karo:

```tsx
// components/LikeButton.tsx (Client — sirf interactive part)
"use client";
import { useState } from "react";

export default function LikeButton() {
  const [liked, setLiked] = useState(false);
  return <button onClick={() => setLiked(!liked)}>{liked ? "❤️" : "🤍"}</button>;
}
```

```tsx
// app/posts/[id]/page.tsx (Server — data fetching)
import LikeButton from "@/components/LikeButton";

export default async function PostPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  const post = await getPost(id);   // server pe fetch hota hai

  return (
    <article>
      <h1>{post.title}</h1>
      <p>{post.body}</p>
      <LikeButton />   {/* sirf ye chota hissa client-side interactive hai */}
    </article>
  );
}
```

Isse zyada tar page server pe render hota hai (fast, SEO-friendly), sirf jitna zaroori hai utna hi JS browser bhejta hai.

## Props Aur Children

```tsx
type CardProps = {
  title: string;
  children: React.ReactNode;   // Blade ke @slot jaisa
};

function Card({ title, children }: CardProps) {
  return (
    <div className="border rounded p-4">
      <h2>{title}</h2>
      {children}
    </div>
  );
}

// Use:
<Card title="Profile">
  <p>Ye content children ke through pass hua</p>
</Card>
```

## Component Folder Organization

```
components/
├── ui/              → generic, project-agnostic (Button, Input, Modal, Card)
└── features/         → business-specific (UserProfileCard, OrderSummary)
```

Naming convention: PascalCase file aur component name dono (`UserCard.tsx` → `function UserCard()`).

## Practice

1. `components/ui/Card.tsx` banao jo `title` prop aur `children` le.
2. `components/features/Counter.tsx` (Client Component) banao jisme `+`/`-` buttons se number badhta/ghatta ho.
3. Ek Server Component page banao jo `Card` ke andar `Counter` ko use kare — dekho dono kaise combine hote hain.

Agli file: [06_styling_tailwind.md](06_styling_tailwind.md)
</content>
