# Utility-First Fundamentals

## Class Naming Pattern Samjho

Zyada tar Tailwind classes is pattern mein hoti hain: `property-value`

```
p-4        →  padding: 1rem;        (p = padding, 4 = spacing scale ka 4th step)
text-lg     →  font-size: 1.125rem;  (text = font-size, lg = large)
bg-red-500   →  background-color: #ef4444;  (bg = background, red-500 = color shade)
flex          →  display: flex;      (kuch classes standalone hoti hain, value nahi lagti)
```

## Spacing Scale — Number Ka Matlab

Tailwind ka spacing scale fixed hai — random pixel values nahi, ek consistent scale hai:

| Class Suffix | Value | Pixels (16px base) |
|---|---|---|
| `0` | 0 | 0px |
| `1` | 0.25rem | 4px |
| `2` | 0.5rem | 8px |
| `4` | 1rem | 16px |
| `6` | 1.5rem | 24px |
| `8` | 2rem | 32px |
| `12` | 3rem | 48px |
| `16` | 4rem | 64px |

```html
<div class="p-1">4px padding</div>
<div class="p-4">16px padding</div>
<div class="p-8">32px padding</div>
```

Ye scale margin, padding, width, height, gap — sab jagah consistent hai. Isi wajah se Tailwind se banayi gayi sites "aligned" lagti hain — random values use hi nahi hoti.

## Directional Variants

```html
<div class="p-4">        <!-- sab sides -->
<div class="px-4">        <!-- left + right (x-axis) -->
<div class="py-4">         <!-- top + bottom (y-axis) -->
<div class="pt-4">          <!-- sirf top -->
<div class="pr-4">           <!-- sirf right -->
<div class="pb-4">            <!-- sirf bottom -->
<div class="pl-4">             <!-- sirf left -->
```

Same pattern `m-` (margin) ke liye bhi: `m-4`, `mx-4`, `my-4`, `mt-4` waghera.

## Arbitrary Values — Jab Scale Mein Value Na Mile

Kabhi kabhi exact value chahiye hoti hai jo default scale mein nahi hai — square bracket syntax use karo:

```html
<div class="w-[327px]">Exact width</div>
<div class="top-[13px]">Exact position</div>
<div class="bg-[#1da1f2]">Exact hex color</div>
<div class="text-[15px]">Exact font size</div>
```

**Lekin ye last resort hona chahiye** — jahan tak ho sake default scale (`p-4`, `w-64`) use karo, consistency ke liye. Arbitrary values sirf tab jab design specifically ek exact value maangta ho (jaise Figma se pixel-perfect match).

## Multiple Classes Combine Karna

```html
<button class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700">
  Click Me
</button>
```

Har class ek chota, single-purpose rule hai — jitni chahiye utni combine karo. Isse dartne ki zaroorat nahi, ye normal Tailwind pattern hai (class attribute lamba dikhta hai, lekin readable rehta hai jab organize kiya jaye).

## Class Order Matter Nahi Karta (Zyada Tar)

```html
<div class="bg-blue-500 p-4 text-white">   <!-- same result -->
<div class="p-4 text-white bg-blue-500">   <!-- same result -->
```

CSS specificity ka jhagda nahi hota kyun ke har class apni cheez control karti hai — koi overlap nahi. (Exception: `@apply` ke andar order matter kar sakta hai, advanced topic.)

## Prettier Plugin — Classes Auto-Sort

```bash
npm install -D prettier-plugin-tailwindcss
```

Ye plugin classes ko ek consistent, recommended order mein automatically arrange kar deta hai save karte hi — bade projects mein zaroor use karo, code review mein diffs clean rehte hain.

## Common Beginner Mistake

```html
<!-- GALAT: dono classes same property control kar rahi hain, dusri wali jeetegi (last one wins agar same specificity) -->
<div class="p-4 p-8">...</div>

<!-- theek: sirf ek value do -->
<div class="p-8">...</div>
```

## Practice

1. Ek card banao: `p-6`, `bg-white`, `rounded-lg`, `shadow-md` — result dekho.
2. Sirf top aur bottom padding do (`py-6`), left-right nahi — dekho farak.
3. `w-[250px]` (arbitrary) aur `w-64` (scale) dono try karo, dekho values kitni close hain — samjho kab scale kaafi hai.

Agli file: [03_spacing_sizing.md](03_spacing_sizing.md)
</content>
