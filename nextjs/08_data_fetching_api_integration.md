# Data Fetching Aur API Integration

## Server Component Mein Direct Fetch (Recommended Default)

Server Component async ho sakta hai, seedha `await fetch()`:

```tsx
// app/products/page.tsx
async function getProducts() {
  const res = await fetch("https://api.example.com/products");
  if (!res.ok) throw new Error("Failed to fetch");
  return res.json();
}

export default async function ProductsPage() {
  const products = await getProducts();
  return (
    <ul>
      {products.map((p: any) => <li key={p.id}>{p.name}</li>)}
    </ul>
  );
}
```

Ye database query jaisa lagta hai — koi loading state, koi useEffect nahi chahiye, kyun ke ye server pe render hone se pehle hi resolve ho jata hai.

## Caching Control — Next.js Ka `fetch` Extended Hai

Next.js `fetch` ko extra options deta hai jo rendering strategy control karte hain:

```tsx
fetch(url, { cache: "force-cache" })          // SSG jaisa — result cache hoga, static
fetch(url, { cache: "no-store" })              // SSR jaisa — har request pe fresh data
fetch(url, { next: { revalidate: 60 } })       // ISR jaisa — 60 second mein ek dafa refresh
```

| Option | Behavior |
|---|---|
| default (kuch na likho) | `force-cache` jaisa hi behave karta hai zyada tar cases mein |
| `cache: "no-store"` | Dynamic rendering — har request pe naya data |
| `next: { revalidate: N }` | N seconds baad background mein refresh (ISR) |
| `next: { tags: ["products"] }` | `revalidateTag("products")` se manually invalidate karne ke liye |

## Route Handlers — Apna Mini Backend

`app/api/*/route.ts` mein aap khud API endpoints bana sakte ho — bilkul Laravel controller method jaisa:

```tsx
// app/api/users/route.ts
import { NextResponse } from "next/server";

export async function GET() {
  const users = await db.user.findMany();
  return NextResponse.json(users);
}

export async function POST(request: Request) {
  const body = await request.json();
  const user = await db.user.create({ data: body });
  return NextResponse.json(user, { status: 201 });
}
```

```tsx
// app/api/users/[id]/route.ts
export async function GET(request: Request, { params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  const user = await db.user.findUnique({ where: { id } });
  return NextResponse.json(user);
}
```

Ye tab use hota hai jab Next.js khud backend ka kaam bhi kar raha ho (dekho [10_backend_database_integration.md](10_backend_database_integration.md)) — agar aapka backend Laravel/Node already alag se hai, to Route Handlers ki zaroorat kam parti hai, seedha us backend ko fetch karo.

## External Backend (Laravel/Node) Se Data Lana

Sabse common real-world scenario aapke liye: Next.js frontend hai, backend Laravel/Node hai.

```tsx
// lib/api.ts
const API_BASE = process.env.NEXT_PUBLIC_API_URL;

export async function getUsers() {
  const res = await fetch(`${API_BASE}/api/users`, {
    headers: { Authorization: `Bearer ${token}` },
    cache: "no-store",
  });
  if (!res.ok) throw new Error("API error");
  return res.json();
}
```

## Client-Side Fetching — Jab User Interaction Ke Baad Data Chahiye

Agar data sirf button click, search input, ya filter change pe fetch karna ho (page load pe nahi), to Client Component mein karna parta hai. Yahan raw `useEffect` + `fetch` ki jagah library use karo — cache, refetch, loading/error state khud handle karti hai:

**TanStack Query (react-query)** — sabse popular:

```tsx
"use client";
import { useQuery } from "@tanstack/react-query";

function ProductList() {
  const { data, isLoading, error } = useQuery({
    queryKey: ["products"],
    queryFn: () => fetch("/api/products").then((res) => res.json()),
  });

  if (isLoading) return <p>Loading...</p>;
  if (error) return <p>Error!</p>;
  return <ul>{data.map((p: any) => <li key={p.id}>{p.name}</li>)}</ul>;
}
```

Faida: automatic caching, background refetch, deduplication, mutation handling (`useMutation`) — sab manually likhne se bachata hai.

**SWR** — Vercel ka hi banaya hua, lighter alternative, similar API (`useSWR(key, fetcher)`).

## axios vs fetch

`fetch` built-in hai, extra install ki zaroorat nahi. `axios` extra features deta hai: automatic JSON parsing, interceptors (jaise Laravel middleware — token auto-attach karna, 401 pe auto-logout), better error handling.

```tsx
// lib/axios.ts
import axios from "axios";

export const api = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_URL,
});

api.interceptors.request.use((config) => {
  const token = getToken();
  if (token) config.headers.Authorization = `Bearer ${token}`;
  return config;
});

api.interceptors.response.use(
  (res) => res,
  (error) => {
    if (error.response?.status === 401) logout();
    return Promise.reject(error);
  }
);
```

Senior projects mein zyada tar `axios` + interceptors pattern use hota hai kyun ke token attach/refresh logic ek jagah reusable hoti hai.

## Practice

1. Ek public API (jaise `https://jsonplaceholder.typicode.com/posts`) ko Server Component se fetch karke list dikhao.
2. `app/api/todos/route.ts` banao jisme GET (list) aur POST (add) implement karo, in-memory array use karke.
3. TanStack Query install karke ek search box banao jo type karte hi (debounce ke sath) API call kare aur results dikhaye.

Agli file: [09_forms_server_actions.md](09_forms_server_actions.md)
</content>
