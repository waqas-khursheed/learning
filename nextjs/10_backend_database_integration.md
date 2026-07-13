# Backend Aur Database Se Integration

Aapke liye ye sabse important file hai kyun ke aap already Laravel/Node/Python backend dev ho — is file mein wo exact cheez hai jo aapko chahiye: **Next.js ko apne existing backend se kaise jodna hai.**

## Do Architecture Patterns

### Pattern 1: Next.js Sirf Frontend (Aapke Liye Most Likely)

Laravel/Node backend already alag se chal raha hai (REST API ya GraphQL serve karta hai). Next.js sirf UI hai jo us backend ko call karta hai.

```
Next.js (frontend, port 3000)  ──fetch/axios──>  Laravel API (port 8000)
                                                        │
                                                     Database
```

Is pattern mein:
- Route Handlers (`app/api/*`) bilkul use nahi honge (ya sirf BFF/proxy ke liye kabhi kabhi)
- Server Components seedha Laravel API ko `fetch` karenge
- Auth token Laravel se milega, Next.js sirf store/attach karega

```tsx
// lib/api.ts
const API_URL = process.env.NEXT_PUBLIC_API_URL;   // e.g. http://localhost:8000

export async function getPosts() {
  const res = await fetch(`${API_URL}/api/posts`, { cache: "no-store" });
  return res.json();
}
```

**CORS zaroor allow karo Laravel side pe** (`config/cors.php` mein Next.js ka origin `http://localhost:3000` add karo), warna browser requests block ho jayengi (Server Component se fetch mein CORS issue nahi hota kyun ke wo server-to-server hai, lekin Client Component se browser fetch mein hota hai).

### Pattern 2: Next.js Full-Stack (Khud Ka Backend Bhi)

Chote/medium projects, ya jab aap ek hi codebase mein sab rakhna chahte ho. Route Handlers backend ka kaam karte hain, database seedha Next.js se connect hota hai.

```
Next.js (frontend + Route Handlers)  ──Prisma/ORM──>  Database
```

## Database Connect Karna — Prisma (Sabse Popular ORM)

```bash
npm install prisma @prisma/client
npx prisma init
```

```prisma
// prisma/schema.prisma
datasource db {
  provider = "postgresql"
  url      = env("DATABASE_URL")
}

generator client {
  provider = "prisma-client-js"
}

model User {
  id    String @id @default(cuid())
  name  String
  email String @unique
  posts Post[]
}

model Post {
  id       String @id @default(cuid())
  title    String
  authorId String
  author   User   @relation(fields: [authorId], references: [id])
}
```

```bash
npx prisma migrate dev --name init
```

```tsx
// lib/db.ts
import { PrismaClient } from "@prisma/client";
export const db = new PrismaClient();
```

```tsx
// Server Component ya Route Handler mein
const users = await db.user.findMany({ include: { posts: true } });
```

Prisma ke Laravel Eloquent se comparisons: `schema.prisma` = migrations + models combined, `prisma migrate` = `php artisan migrate`, `db.user.findMany()` = `User::all()`.

**Alternatives:** Drizzle ORM (lightweight, SQL-close, fast growing popularity), Mongoose (agar MongoDB use kar rahe ho).

## Environment Variables — Zaroori Farak

```
# .env.local
DATABASE_URL="postgresql://..."          → sirf server pe accessible, secret rehta hai
NEXT_PUBLIC_API_URL="http://localhost:8000"  → browser mein bhi accessible (public!)
```

**Rule: `NEXT_PUBLIC_` prefix wali koi bhi cheez browser JS bundle mein chali jati hai — kabhi bhi secret key, DB password, private API key is prefix ke sath mat rakho.**

```tsx
process.env.DATABASE_URL           // sirf Server Component/Route Handler mein kaam karega
process.env.NEXT_PUBLIC_API_URL    // Server aur Client dono mein kaam karega
```

## Laravel API Se Real Example — Poora Flow

```tsx
// lib/api.ts
import axios from "axios";

export const api = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_URL,
  headers: { "Content-Type": "application/json" },
});
```

```tsx
// app/posts/page.tsx (Server Component)
import { api } from "@/lib/api";

export default async function PostsPage() {
  const { data: posts } = await api.get("/api/posts");
  return (
    <div>
      {posts.map((post: any) => (
        <div key={post.id}>{post.title}</div>
      ))}
    </div>
  );
}
```

```tsx
// app/posts/create/actions.ts — client se Laravel ko call karna (Server Action ke through, taake token/secret leak na ho)
"use server";
import { cookies } from "next/headers";

export async function createPost(formData: FormData) {
  const token = (await cookies()).get("token")?.value;
  const res = await fetch(`${process.env.API_URL}/api/posts`, {
    method: "POST",
    headers: {
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      title: formData.get("title"),
      body: formData.get("body"),
    }),
  });
  if (!res.ok) throw new Error("Failed to create post");
}
```

Is pattern ka faida: sensitive logic (token) hamesha server pe rehta hai, browser mein expose nahi hota.

## GraphQL Ka Option

Agar backend GraphQL serve karta hai (REST ki jagah), to `graphql-request` (lightweight) ya Apollo Client use hota hai:

```tsx
import { GraphQLClient, gql } from "graphql-request";

const client = new GraphQLClient(process.env.NEXT_PUBLIC_GRAPHQL_URL!);
const query = gql`{ posts { id title } }`;
const data = await client.request(query);
```

## Practice

1. Apni kisi Laravel project ka ek simple GET endpoint (jaise `/api/products`) lo, Next.js Server Component se fetch karo, list dikhao.
2. `.env.local` mein `NEXT_PUBLIC_API_URL` set karo, Laravel side CORS allow karo, poora connection test karo.
3. Optional: Prisma se ek chota standalone Next.js full-stack CRUD banao (bina Laravel ke) — samajh aayega dono patterns ka farak.

Agli file: [11_authentication.md](11_authentication.md)
</content>
