# Reusable Components — @apply, clsx, cva

Jab same classes baar baar repeat hone lagein, ye 3 tareeqe hain reusability ke liye — har ek ki apni jagah hai.

## Approach 1: React/Next.js Component (Sabse Recommended)

Tailwind mein reusability ka **sabse best tareeqa** naya CSS class banana nahi, balke ek **React component** banana hai:

```tsx
// components/ui/Button.tsx
type ButtonProps = {
  children: React.ReactNode;
  variant?: "primary" | "secondary";
} & React.ButtonHTMLAttributes<HTMLButtonElement>;

export function Button({ children, variant = "primary", ...props }: ButtonProps) {
  const baseClasses = "px-4 py-2 rounded-lg font-medium transition-colors";
  const variantClasses =
    variant === "primary"
      ? "bg-blue-600 hover:bg-blue-700 text-white"
      : "bg-gray-200 hover:bg-gray-300 text-gray-900";

  return (
    <button className={`${baseClasses} ${variantClasses}`} {...props}>
      {children}
    </button>
  );
}
```

```tsx
<Button variant="primary">Save</Button>
<Button variant="secondary">Cancel</Button>
```

Isse classes ek hi jagah define hain, poore app mein `<Button>` reuse hota hai — ye components ke through DRY (Don't Repeat Yourself) achieve karne ka Tailwind-recommended tareeqa hai.

## Approach 2: `clsx` — Conditional Classes Cleanly Likhna

Jab classes conditionally apply karni hon, template strings messy ho jati hain — `clsx` isse clean karta hai:

```bash
npm install clsx
```

```tsx
import clsx from "clsx";

function Alert({ type }: { type: "success" | "error" }) {
  return (
    <div
      className={clsx(
        "p-4 rounded-lg border",
        type === "success" && "bg-green-50 text-green-700 border-green-200",
        type === "error" && "bg-red-50 text-red-700 border-red-200"
      )}
    >
      Message
    </div>
  );
}
```

## Approach 3: `cva` (class-variance-authority) — Multiple Variants Ke Liye

Jab component ke multiple dimensions ho (variant + size + state), `cva` ise structured tareeqe se manage karta hai — shadcn/ui bhi isi pattern pe based hai:

```bash
npm install class-variance-authority
```

```tsx
// components/ui/Button.tsx
import { cva, type VariantProps } from "class-variance-authority";

const buttonVariants = cva(
  "rounded-lg font-medium transition-colors inline-flex items-center justify-center", // base classes (hamesha lagti hain)
  {
    variants: {
      variant: {
        primary: "bg-blue-600 hover:bg-blue-700 text-white",
        secondary: "bg-gray-200 hover:bg-gray-300 text-gray-900",
        danger: "bg-red-600 hover:bg-red-700 text-white",
      },
      size: {
        sm: "px-3 py-1.5 text-sm",
        md: "px-4 py-2 text-base",
        lg: "px-6 py-3 text-lg",
      },
    },
    defaultVariants: {
      variant: "primary",
      size: "md",
    },
  }
);

type ButtonProps = React.ButtonHTMLAttributes<HTMLButtonElement> & VariantProps<typeof buttonVariants>;

export function Button({ variant, size, className, ...props }: ButtonProps) {
  return <button className={buttonVariants({ variant, size, className })} {...props} />;
}
```

```tsx
<Button variant="danger" size="lg">Delete</Button>
<Button variant="secondary" size="sm">Cancel</Button>
```

Faida: variants ek jagah organized hain, TypeScript autocomplete milta hai (`variant` sirf `"primary" | "secondary" | "danger"` accept karega), naya variant add karna ek line ka kaam hai.

## `tailwind-merge` — Conflicting Classes Handle Karna

Jab component ko bahar se extra className pass ho aur wo kisi existing class se conflict kare:

```tsx
import { twMerge } from "tailwind-merge";

<Button className="bg-purple-600" /> // chahte hain ye Button ka default bg-blue-600 override kare
```

Bina `twMerge` ke, `"bg-blue-600 bg-purple-600"` dono classes CSS mein chali jayengi aur jo baad mein hai (CSS source order pe depend) wo jeetegi — unpredictable. `twMerge` intelligently sirf last wali rakhta hai:

```tsx
import { twMerge } from "tailwind-merge";
twMerge("bg-blue-600 px-4", "bg-purple-600"); // → "px-4 bg-purple-600"
```

**`cn()` helper — clsx + twMerge combine (shadcn/ui pattern, standard ban chuka hai):**

```tsx
// lib/utils.ts
import { clsx, type ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}
```

```tsx
<button className={cn("px-4 py-2 bg-blue-600", className)}>Click</button>
```

## Approach 4: `@apply` — Kab Aur Kyun (Sparingly Use Karo)

`@apply` Tailwind classes ko ek custom CSS class mein group karne deta hai:

```css
.btn-primary {
  @apply px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium;
}
```

```html
<button class="btn-primary">Click</button>
```

**Ye Tailwind team khud discourage karti hai zyada use karne se** — kyun ke ye wapas "traditional CSS" wale problem mein le jata hai (naye class names invent karna, CSS file grow hona). `@apply` sirf tab use karo jab:
- Third-party library ke markup ko style karna ho (jaha className add karne ka control na ho)
- Bohat repetitive utility combos ho jo component approach se better na ho (rare)

**Zyada tar cases mein React component (Approach 1) hi sahi tareeqa hai.**

## Practice

1. `cva` se ek `Badge` component banao — variants: `success`/`warning`/`error`, sizes: `sm`/`md`.
2. `cn()` helper banao (`clsx` + `twMerge`), ek component mein use karo jahan bahar se `className` prop accept ho aur merge ho.
3. Socho: `@apply` kab use karoge — ek example likho jahan ye genuinely justified ho (third-party markup styling).

Agli file: [14_best_practices_plugins.md](14_best_practices_plugins.md)
</content>
