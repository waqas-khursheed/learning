# Tailwind CSS Kya Hai Aur Installation

## Tailwind Kya Hai

Tailwind ek **utility-first CSS framework** hai. Matlab: har ek chota CSS property (jaise `padding: 16px` ya `color: blue`) ke liye ek pehle se bani hui class milti hai, aur aap in classes ko HTML/JSX mein directly combine karke poora design bana lete ho — bina khud `.css` file mein custom class likhe.

```html
<!-- Traditional CSS approach -->
<div class="card">Hello</div>
<style>
  .card { padding: 16px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
</style>

<!-- Tailwind approach -->
<div class="p-4 bg-white rounded-lg shadow-md">Hello</div>
```

Dono ka result same hai — bas Tailwind mein aapko naye class names invent nahi karne parte (`.card`, `.header-wrapper` waghera), aur alag file mein jaake CSS nahi likhna parta.

## Kyun Use Karo (Bootstrap/Plain CSS Se Farak)

| Approach | Problem |
|---|---|
| Plain CSS | Har component ke liye naye class names sochna parta hai, CSS file badhti jati hai, unused CSS accumulate hoti hai |
| Bootstrap/Component libraries | Pre-designed components milte hain lekin customize karna mushkil hota hai, sab sites "Bootstrap jaisi" lagti hain |
| **Tailwind** | Chote building blocks (utilities) milte hain, unse **kuch bhi** design bana sakte ho, unused CSS automatically build se hat jati hai |

Tailwind ek design system nahi deta — ek **toolkit** deta hai jisse aap apna design system banate ho.

## Next.js Mein Installation

Agar `create-next-app` ke waqt Tailwind "Yes" chuna tha, to already installed hai. Manually install karna ho:

```bash
npm install tailwindcss @tailwindcss/postcss postcss
```

```css
/* app/globals.css */
@import "tailwindcss";
```

```js
// postcss.config.mjs
const config = {
  plugins: {
    "@tailwindcss/postcss": {},
  },
};
export default config;
```

Bas itna hi — koi `content` paths config karna Tailwind v4 mein zaroori nahi (v3 mein zaroori tha, neeche note dekho). Ab kisi bhi `.tsx` file mein class likho, kaam karega.

## Plain HTML Project Mein (Bina Framework Ke)

```bash
npm install tailwindcss @tailwindcss/cli
```

```css
/* input.css */
@import "tailwindcss";
```

```bash
npx @tailwindcss/cli -i ./input.css -o ./output.css --watch
```

`output.css` ko apne HTML mein link karo.

## CDN Se (Sirf Quick Prototyping Ke Liye, Production Mein Nahi)

```html
<script src="https://cdn.tailwindcss.com"></script>
```

Ye sirf demo/testing ke liye hai — production app mein kabhi use nahi karte (bohat bada file size, koi customization/purging nahi hoti).

## Tailwind v3 vs v4 — Zaroori Farak

Tailwind v4 (naya, 2025 se) mein setup simpler hai — CSS-first config (`@theme` block CSS file mein hi), koi `tailwind.config.js` mandatory nahi, koi `content` array specify karne ki zaroorat nahi (automatic detection). Purani Tailwind v3 tutorials mein `tailwind.config.js` + `content: ["./app/**/*.{js,ts,jsx,tsx}"]` dikhega — agar aap v3 use kar rahe ho to wo zaroori hai, v4 mein optional hai. Is roadmap mein hum **v4** ka syntax use karenge, jahan farak hoga wahan bata denge.

## VS Code Setup

**Tailwind CSS IntelliSense** extension install karo — class name autocomplete, hover pe CSS preview, aur invalid class warning deta hai. Isके bina classes yaad rakhna mushkil hoga.

## Practice

1. Apne Next.js project mein `app/page.tsx` kholo, `<div className="p-8 bg-blue-500 text-white rounded-xl">Test</div>` likho, dekho style apply hoti hai.
2. VS Code IntelliSense extension install karo, `bg-` type karo, dekho autocomplete kitni colors suggest karta hai.
3. `npx @tailwindcss/cli` se ek standalone HTML project set up karo (bina Next.js ke) — samajh aayega Tailwind kisi bhi project mein independently kaise lagta hai.

Agli file: [02_utility_first_fundamentals.md](02_utility_first_fundamentals.md)
</content>
