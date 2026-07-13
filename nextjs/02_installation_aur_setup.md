# Installation Aur Project Setup

## Prerequisite: Node.js

Next.js ko Node.js chahiye (version 18.18 ya usse upar, latest LTS recommended). Check karo:

```bash
node -v
npm -v
```

Agar multiple projects mein alag-alag Node versions chahiye hon (jaise aap PHP mein alag Laravel versions ke liye version manager use karte ho), to **nvm** (Node Version Manager) install kar lo.

## Naya Project Banana

```bash
npx create-next-app@latest my-app
```

Ye interactive questions poochega:

```
Would you like to use TypeScript?      → Yes (hamesha Yes bolo)
Would you like to use ESLint?          → Yes
Would you like to use Tailwind CSS?    → Yes
Would you like to use `src/` directory? → Yes (bada project ho to organize rehta hai)
Would you like to use App Router?      → Yes (hamesha Yes)
Would you like to customize import alias? → No (default @/* theek hai)
```

Isse ek poora project structure ban jayega, dependencies install ho jayengi.

## Project Chalana

```bash
cd my-app
npm run dev
```

`http://localhost:3000` pe app open ho jayega. Ye **dev server** hai — file save karte hi browser auto-refresh ho jata hai (Hot Reload / Fast Refresh), jaise `php artisan serve` + browser refresh, bas automatic.

## package.json Ke Scripts Samjho

```json
{
  "scripts": {
    "dev": "next dev",       // local development server (hot reload ke sath)
    "build": "next build",   // production build banata hai (optimized, minified)
    "start": "next start",   // production build ko run karta hai (build ke baad)
    "lint": "next lint"      // ESLint se code check karta hai
  }
}
```

Yaad rakho: `npm run dev` sirf development ke liye hai. Production mein pehle `npm run build` phir `npm run start` chalta hai — bilkul waise jaise Laravel mein `php artisan optimize` production ke liye alag hota hai.

## Zaroori Config Files

| File | Kaam |
|---|---|
| `next.config.ts` (ya `.js`) | Next.js ki settings — images domains, redirects, env config waghera. Laravel ke `config/app.php` jaisa. |
| `tsconfig.json` | TypeScript settings, `@/*` import alias yahan define hota hai |
| `.eslintrc.json` / `eslint.config.mjs` | Code quality rules |
| `tailwind.config.ts` | Tailwind CSS customization (agar Tailwind chuna ho) |
| `.env.local` | Local secrets/env vars (jaise Laravel ka `.env`, **git mein commit nahi hoti**) |
| `.gitignore` | `node_modules/`, `.next/`, `.env*.local` already ignore hote hain by default |

## Ek Zaroori Package Manager Baat

`npm` default hai, lekin `pnpm` ya `yarn` bhi chal sakte hain. Senior/professional projects mein **pnpm** kaafi popular hai (disk space save karta hai, faster install). Agar seekh rahe ho to `npm` se start karo, baad mein `pnpm` try kar lena — commands almost same hote hain:

```bash
npx create-next-app@latest my-app   →   pnpm create next-app my-app
npm install <pkg>                    →   pnpm add <pkg>
npm run dev                          →   pnpm dev
```

## Recommended VS Code Extensions

- **ES7+ React/Redux/React-Native snippets** — component boilerplate fast likhne ke liye
- **Tailwind CSS IntelliSense** — class name autocomplete
- **Prettier** — code formatting
- **ESLint** — inline errors dikhata hai

## Practice

1. `create-next-app` se ek project banao, TypeScript + Tailwind + App Router ke sath.
2. `npm run dev` chalao, `app/page.tsx` kholo, text change karo, dekho browser auto-refresh hota hai.
3. `npm run build` chala kar dekho — `.next/` folder mein production build ban jayega. Us folder ko explore karo (khali dekhne ke liye, edit nahi karna).

Agli file: [03_project_folder_structure.md](03_project_folder_structure.md)
</content>
