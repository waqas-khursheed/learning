# Typography

## Font Size

```html
text-xs     → 0.75rem  (12px)
text-sm      → 0.875rem (14px)
text-base     → 1rem     (16px, default)
text-lg        → 1.125rem (18px)
text-xl         → 1.25rem  (20px)
text-2xl          → 1.5rem   (24px)
text-3xl           → 1.875rem (30px)
text-4xl            → 2.25rem  (36px)
text-5xl             → 3rem     (48px)
text-6xl              → 3.75rem  (60px)
```

Heading ke liye `text-3xl`/`text-4xl`, body text `text-base`, chota caption `text-sm`/`text-xs` — common convention.

## Font Weight

```html
font-thin       → 100
font-light        → 300
font-normal         → 400
font-medium          → 500
font-semibold          → 600
font-bold                → 700
font-extrabold             → 800
font-black                   → 900
```

## Line Height (Leading)

```html
leading-none     → 1
leading-tight      → 1.25
leading-normal       → 1.5
leading-relaxed        → 1.625
leading-loose            → 2
```

Zyada line-height (`leading-relaxed`) lambe paragraphs mein readability behtar karta hai.

## Text Alignment

```html
text-left   text-center   text-right   text-justify
```

## Text Color

```html
text-gray-900    → dark text (headings ke liye)
text-gray-500     → medium text (secondary content)
text-blue-600      → links/accents
text-white           → dark background pe
```

Color scale: har color ka `50` (bohat halka) se `950` (bohat gehra) tak shade hota hai — `text-red-500` medium red hai, `text-red-900` bohat dark red.

## Font Family

```html
font-sans   → default sans-serif stack
font-serif   → serif fonts
font-mono     → monospace (code ke liye)
```

Custom font (Google Fonts, `next/font`) use karna ho to [12_custom_theme_config.md](12_custom_theme_config.md) mein detail hai.

## Text Decoration Aur Transform

```html
underline    line-through    no-underline
uppercase     lowercase       capitalize
italic         not-italic
```

## Text Overflow — Truncate Aur Line Clamp

```html
<p class="truncate">Bohat lamba text jo ek line mein cut ho jayega with ellipsis...</p>

<p class="line-clamp-3">
  Ye paragraph sirf 3 lines dikhayega, baaki "..." se cut ho jayega — 
  card descriptions mein bohat use hota hai.
</p>
```

## Letter Spacing (Tracking)

```html
tracking-tighter   tracking-tight   tracking-normal   tracking-wide   tracking-widest
```

Headings ko thora tight (`tracking-tight`), uppercase labels ko wide (`tracking-wide`) karna common design pattern hai.

## Poora Example — Typical Blog Card Text

```html
<article class="max-w-md">
  <h2 class="text-2xl font-bold text-gray-900 leading-tight">
    Next.js Mein Server Components Kaise Kaam Karte Hain
  </h2>
  <p class="mt-2 text-sm text-gray-500 uppercase tracking-wide">
    5 min read
  </p>
  <p class="mt-4 text-base text-gray-700 leading-relaxed line-clamp-3">
    Server Components Next.js App Router ka core feature hain jo bohat sara
    JavaScript browser tak bhejne se bachate hain...
  </p>
</article>
```

## Practice

1. Upar wala blog card example khud likh kar test karo, alag-alag `text-*` sizes try karke dekho.
2. Ek lamba paragraph banao, `line-clamp-2` lagao, dekho automatically truncate hota hai.
3. `tracking-widest` + `uppercase` + `text-xs` combine karke ek "badge label" banao.

Agli file: [05_colors_backgrounds.md](05_colors_backgrounds.md)
</content>
