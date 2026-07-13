# Best Practices, Plugins Aur Common Mistakes

## Official Plugins

### `@tailwindcss/forms`
Form elements (input, select, checkbox, radio) ka default browser styling reset karta hai, taake unhe Tailwind classes se aasani se style kiya ja sake.

```bash
npm install @tailwindcss/forms
```
```js
// tailwind.config.ts
plugins: [require("@tailwindcss/forms")],
```

### `@tailwindcss/typography`
Jab CMS/Markdown se aane wala content render karna ho (blog post body jaisa) jiske individual tags control nahi kar sakte, `prose` class laga do:

```html
<article class="prose lg:prose-xl dark:prose-invert">
  <!-- Markdown se generated HTML: h1, p, ul, blockquote waghera -->
  <!-- Automatically achi typography styling mil jayegi bina har tag ko manually style kiye -->
</article>
```

### `@tailwindcss/container-queries`
Container queries (`@container`, `@sm:`, `@lg:`) enable karta hai — [08_responsive_design.md](08_responsive_design.md) mein dekha tha.

## Best Practices — Senior Level

### 1. Component Extraction, Naye CSS Classes Nahi
Repetitive classes dekh kar naya `.card` class banane ki bajaye, React `<Card>` component banao ([13_reusable_components_apply_cva.md](13_reusable_components_apply_cva.md)).

### 2. Design Tokens Consistent Rakho
`p-4` ya `p-6` use karo — `p-[17px]`, `p-[23px]` jaisi random arbitrary values se site inconsistent lagti hai. Arbitrary values sirf last resort.

### 3. Prettier Plugin Se Classes Sort Karo
```bash
npm install -D prettier-plugin-tailwindcss
```
Isse team ke sab members ke classes same consistent order mein hongi, PR diffs clean rahengi.

### 4. Long className Ko Break Karo (Readability)
```tsx
// Mushkil parhna
<div className="flex items-center justify-between p-4 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow border border-gray-200">

// Better — cn() se logical groups mein tor do
<div className={cn(
  "flex items-center justify-between",
  "p-4 rounded-lg border border-gray-200",
  "bg-white shadow-md hover:shadow-lg transition-shadow"
)}>
```

### 5. Har `dark:` Ke Sath Light Variant Zaroor Do
[10_dark_mode.md](10_dark_mode.md) mein cover kiya — `dark:bg-gray-900` akela mat likho, `bg-white dark:bg-gray-900` likho.

### 6. Semantic HTML Bhool Mat Jao
Tailwind sirf styling hai — accessibility ka khayal khud rakhna hai:
```html
<!-- GALAT -->
<div onClick={...}>Click me</div>

<!-- THEEK -->
<button onClick={...}>Click me</button>
```

### 7. `focus:outline-none` Akela Kabhi Mat Lagao
Keyboard navigation ke liye focus indicator zaroori hai — hataoge to `focus:ring-2` se replace karo, gayab mat karo.

## Common Mistakes

| Mistake | Fix |
|---|---|
| `class` use karna JSX mein (`class="..."`) | JSX mein `className="..."` hota hai |
| Dynamic class string banana: `` className={`text-${color}-500`} `` | Ye **kaam nahi karega** — Tailwind build-time pe classes scan karta hai, template string se banayi gayi class ko detect nahi kar sakta. Poori class name string mein likhni hoti hai: `color === "red" ? "text-red-500" : "text-blue-500"` |
| `p-4 p-8` dono ek sath (conflict) | Sirf ek value do, ya `cn()`/`twMerge` use karo agar dynamically combine ho rahi hain |
| Har chota UI piece ke liye naya `@apply` class | React component banao, isse behtar reusability milti hai |
| Arbitrary values ka overuse (`w-[327px]`, `p-[13px]`) | Default scale use karo jab tak pixel-perfect match zaroori na ho |

## Dynamic Classes Ka Sahi Tareeqa (Zaroori Gotcha)

```tsx
// GALAT — Tailwind build ke waqt is class ko dekh hi nahi payega, kyun ke
// scanner literal strings dhoondhta hai, JS template ko evaluate nahi karta
function Badge({ color }: { color: string }) {
  return <span className={`bg-${color}-500`}>Badge</span>;
}

// THEEK — poori class names explicitly likho, taake build scanner unhe dekh sake
function Badge({ color }: { color: "red" | "green" | "blue" }) {
  const colorMap = {
    red: "bg-red-500",
    green: "bg-green-500",
    blue: "bg-blue-500",
  };
  return <span className={colorMap[color]}>Badge</span>;
}
```

Ye sabse common Tailwind bug hai jo beginners ko confuse karta hai — "class likhi hai lekin apply nahi ho rahi" ka 90% case yehi hota hai.

## Performance — Production Build

Tailwind automatically **unused classes ko final CSS se hata deta hai** (build ke waqt scan karke) — is liye chahe aap 10,000 utility classes available rakho, production CSS file sirf wahi classes include karegi jo aapne actually use ki hain. Typical production Tailwind CSS file **10-15kb** (gzip) hoti hai chahe app kitna hi bada ho — ye traditional CSS frameworks se kaafi lightweight hai.

## Poora Learning Path Complete — Ab Kya

1. Ek real component library banao (Button, Input, Card, Badge, Modal) `cva` pattern se
2. shadcn/ui explore karo — dekho professional-grade components kaise likhe jate hain
3. Koi real design (Figma/Dribbble se) le kar, sirf Tailwind se hubahu replicate karne ki koshish karo — ye sabse best practice hai

## Practice

1. Apne kisi purane project mein dynamic class string (`` `text-${x}-500` ``) dhoondo agar hai, fix karo object-map pattern se.
2. `@tailwindcss/typography` install karke ek Markdown-jaisa content block `prose` class se style karo.
3. Ek poora chota landing page banao (navbar + hero + 3 feature cards + footer) — sab kuch is roadmap mein seekhe concepts use karke, dark mode support ke sath.
</content>
