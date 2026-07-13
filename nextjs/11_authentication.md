# Authentication

Do main scenarios: (A) aapka existing backend (Laravel Sanctum/Passport, Node JWT) auth handle karta hai, Next.js sirf consume karta hai. (B) Next.js khud auth handle karta hai (NextAuth/Auth.js).

## Scenario A: Existing Backend Auth (Laravel Sanctum Example)

Flow: user login karta hai → Laravel token deta hai → Next.js token ko **httpOnly cookie** mein store karta hai (localStorage nahi — XSS se protect karne ke liye) → har request pe cookie automatically bheja jata hai.

```tsx
// app/login/actions.ts
"use server";
import { cookies } from "next/headers";
import { redirect } from "next/navigation";

export async function login(formData: FormData) {
  const res = await fetch(`${process.env.API_URL}/api/login`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      email: formData.get("email"),
      password: formData.get("password"),
    }),
  });

  if (!res.ok) return { error: "Invalid credentials" };

  const { token } = await res.json();

  (await cookies()).set("token", token, {
    httpOnly: true,
    secure: process.env.NODE_ENV === "production",
    sameSite: "lax",
    maxAge: 60 * 60 * 24 * 7,   // 7 din
  });

  redirect("/dashboard");
}
```

```tsx
// lib/auth.ts — server-side current user nikalne ke liye
import { cookies } from "next/headers";

export async function getCurrentUser() {
  const token = (await cookies()).get("token")?.value;
  if (!token) return null;

  const res = await fetch(`${process.env.API_URL}/api/user`, {
    headers: { Authorization: `Bearer ${token}` },
    cache: "no-store",
  });
  if (!res.ok) return null;
  return res.json();
}
```

## Protected Routes — `middleware.ts`

Root mein `middleware.ts` (Laravel middleware jaisa concept — har matching request se pehle chalta hai):

```tsx
// middleware.ts
import { NextRequest, NextResponse } from "next/server";

export function middleware(request: NextRequest) {
  const token = request.cookies.get("token")?.value;

  if (!token && request.nextUrl.pathname.startsWith("/dashboard")) {
    return NextResponse.redirect(new URL("/login", request.url));
  }

  return NextResponse.next();
}

export const config = {
  matcher: ["/dashboard/:path*", "/settings/:path*"],
};
```

## Scenario B: NextAuth / Auth.js (Next.js Khud Auth Handle Kare)

Jab Next.js hi backend bhi hai (full-stack pattern), ya third-party login chahiye (Google, GitHub OAuth):

```bash
npm install next-auth@beta
```

```tsx
// auth.ts
import NextAuth from "next-auth";
import Google from "next-auth/providers/google";
import Credentials from "next-auth/providers/credentials";

export const { handlers, auth, signIn, signOut } = NextAuth({
  providers: [
    Google({ clientId: process.env.GOOGLE_ID, clientSecret: process.env.GOOGLE_SECRET }),
    Credentials({
      credentials: { email: {}, password: {} },
      authorize: async (credentials) => {
        const user = await verifyUser(credentials.email, credentials.password);
        return user ?? null;
      },
    }),
  ],
});
```

```tsx
// app/api/auth/[...nextauth]/route.ts
export { GET, POST } from "@/auth";
```

```tsx
// Server Component mein current session:
import { auth } from "@/auth";
const session = await auth();
if (!session) redirect("/login");
```

```tsx
// Client Component mein
"use client";
import { useSession, signOut } from "next-auth/react";

const { data: session } = useSession();
<button onClick={() => signOut()}>Logout</button>
```

## Token Storage — Do Aur Nahi

| Storage | Secure? | Kyun |
|---|---|---|
| `localStorage` | ❌ Nahi | JS se accessible, XSS attack se token chori ho sakta hai |
| httpOnly Cookie | ✅ Haan | JavaScript is cookie ko read nahi kar sakta, sirf server aur browser ka request cycle |

**Hamesha httpOnly cookies use karo tokens ke liye — localStorage kabhi nahi.**

## Practice

1. Apni Laravel Sanctum API ko Next.js se connect karo: login form → cookie mein token → protected `/dashboard` page.
2. `middleware.ts` banao jo bina token wale user ko `/login` pe redirect kare.
3. NextAuth se Google login try karo ek standalone test project mein (samajhne ke liye ki OAuth flow kaisa lagta hai).

Agli file: [12_important_packages_list.md](12_important_packages_list.md)
</content>
