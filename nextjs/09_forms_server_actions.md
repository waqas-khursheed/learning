# Forms Aur Server Actions

## Server Actions — Next.js Ka Unique Feature

Server Action ek aisa function hai jo **client se call hota hai lekin server pe chalta hai** — bina alag API route banaye. Form submit seedha function call ban jata hai.

```tsx
// app/actions.ts
"use server";

export async function createUser(formData: FormData) {
  const name = formData.get("name") as string;
  const email = formData.get("email") as string;

  await db.user.create({ data: { name, email } });
}
```

```tsx
// app/users/new/page.tsx (Server Component)
import { createUser } from "@/app/actions";

export default function NewUserPage() {
  return (
    <form action={createUser}>
      <input name="name" placeholder="Name" />
      <input name="email" placeholder="Email" />
      <button type="submit">Create</button>
    </form>
  );
}
```

Ye JavaScript disabled hone pe bhi kaam karta hai (real `<form>` submission), aur JS enabled hone pe progressively enhanced ho jata hai (bina full page reload).

## Loading Aur Error State — `useFormStatus` / `useActionState`

```tsx
"use client";
import { useFormStatus } from "react-dom";

function SubmitButton() {
  const { pending } = useFormStatus();
  return <button disabled={pending}>{pending ? "Saving..." : "Save"}</button>;
}
```

```tsx
"use client";
import { useActionState } from "react";
import { createUser } from "@/app/actions";

function Form() {
  const [state, formAction, isPending] = useActionState(createUser, { error: null });
  return (
    <form action={formAction}>
      <input name="email" />
      {state.error && <p className="text-red-500">{state.error}</p>}
      <button disabled={isPending}>Submit</button>
    </form>
  );
}
```

## Validation — Zod

Zod ek TypeScript-first schema validation library hai — Laravel ke `$request->validate([...])` jaisa kaam karta hai:

```tsx
import { z } from "zod";

const userSchema = z.object({
  name: z.string().min(2, "Name kam se kam 2 characters ka ho"),
  email: z.string().email("Valid email daalein"),
  age: z.number().min(18, "18 saal se zyada umar chahiye"),
});

// Server Action ke andar:
"use server";
export async function createUser(formData: FormData) {
  const result = userSchema.safeParse({
    name: formData.get("name"),
    email: formData.get("email"),
    age: Number(formData.get("age")),
  });

  if (!result.success) {
    return { error: result.error.flatten().fieldErrors };
  }

  await db.user.create({ data: result.data });
  return { success: true };
}
```

## react-hook-form + Zod — Rich Client-Side Forms Ke Liye

Jab form complex ho (multi-field validation, real-time error, better UX), `react-hook-form` + `@hookform/resolvers` (Zod ke sath):

```tsx
"use client";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";

const schema = z.object({
  email: z.string().email(),
  password: z.string().min(6),
});
type FormData = z.infer<typeof schema>;

export default function LoginForm() {
  const { register, handleSubmit, formState: { errors } } = useForm<FormData>({
    resolver: zodResolver(schema),
  });

  const onSubmit = (data: FormData) => {
    console.log(data);   // yahan API call ya server action
  };

  return (
    <form onSubmit={handleSubmit(onSubmit)}>
      <input {...register("email")} />
      {errors.email && <p>{errors.email.message}</p>}
      <input type="password" {...register("password")} />
      {errors.password && <p>{errors.password.message}</p>}
      <button type="submit">Login</button>
    </form>
  );
}
```

## Kab Server Action, Kab API Route Handler

| Scenario | Use Karo |
|---|---|
| Sirf internal form submit (add/edit/delete) | Server Action |
| External clients (mobile app, third-party) bhi call karenge | Route Handler (`route.ts`) — ye ek real REST endpoint hai |
| Aapka backend already Laravel/Node hai | Na Server Action na Route Handler — seedha us backend ko fetch/axios se call karo |

## Cache Revalidate Karna Submit Ke Baad

```tsx
"use server";
import { revalidatePath } from "next/cache";

export async function createUser(formData: FormData) {
  await db.user.create({ data: { /* ... */ } });
  revalidatePath("/users");   // is route ka cached data refresh ho jayega
}
```

## Practice

1. Zod schema se validate hone wala "Contact Us" form banao — Server Action ke through submit ho.
2. `react-hook-form` + Zod se ek signup form banao jisme real-time field errors dikhein.
3. Ek "delete" button banao jo Server Action call kare aur `revalidatePath` se list refresh kare.

Agli file: [10_backend_database_integration.md](10_backend_database_integration.md)
</content>
