# Colors Aur Backgrounds

## Color Palette Ka System

Tailwind mein har color 11 shades mein aata hai: `50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950` — `50` sabse halka, `950` sabse gehra.

```html
<div class="bg-blue-50">Bohat halka blue</div>
<div class="bg-blue-500">Medium blue (default-ish)</div>
<div class="bg-blue-950">Bohat dark blue</div>
```

Available color families: `slate, gray, zinc, neutral, stone, red, orange, amber, yellow, lime, green, emerald, teal, cyan, sky, blue, indigo, violet, purple, fuchsia, pink, rose`.

**Practical tip:** Zyada tar UI mein `500`/`600` primary actions ke liye, `100`/`50` backgrounds ke liye, `900`/`950` text ke liye use hota hai.

## Background Color

```html
bg-white    bg-black    bg-transparent
bg-gray-100   bg-red-500   bg-emerald-600
```

## Text, Border, Ring Colors — Same Pattern

```html
text-blue-600
border-gray-300
ring-red-500
```

Jahan bhi color property hoti hai, `{property}-{color}-{shade}` pattern follow hota hai.

## Opacity — Slash Syntax

```html
bg-blue-500/50    → 50% opacity wala blue background
text-black/70      → 70% opacity wala black text
border-white/20      → 20% opacity wala white border
```

Ye modern syntax hai (Tailwind v3+) — `bg-opacity-50` wala purana approach ab deprecated hai, slash syntax use karo.

## Gradients

```html
<div class="bg-gradient-to-r from-blue-500 to-purple-600">
  Left se right gradient
</div>

<div class="bg-gradient-to-br from-pink-400 via-red-500 to-yellow-500">
  Diagonal, 3-color gradient
</div>
```

Direction classes: `bg-gradient-to-t/b/l/r/tr/tl/br/bl` (top, bottom, left, right, aur diagonals).

## Custom Colors (Hex/RGB Arbitrary)

```html
<div class="bg-[#1da1f2]">Twitter blue exact hex</div>
<div class="bg-[rgb(29,161,242)]">RGB bhi chalega</div>
```

Agar brand ka apna specific color hai jo palette mein nahi milta, to isse use karo — ya better, [12_custom_theme_config.md](12_custom_theme_config.md) mein sikhaye tareeqe se `tailwind.config` mein permanently add karo.

## Background Images Aur Position

```html
<div class="bg-[url('/hero.jpg')] bg-cover bg-center bg-no-repeat h-96">
  Background image wala section
</div>
```

- `bg-cover` — poora area cover kare (image crop ho sakti hai)
- `bg-contain` — poori image fit ho (letterboxing ho sakti hai)
- `bg-center`, `bg-top`, `bg-bottom` — position

## Common Color Usage Pattern (Real Project)

```html
<!-- Primary button -->
<button class="bg-blue-600 hover:bg-blue-700 text-white">Submit</button>

<!-- Card background -->
<div class="bg-white border border-gray-200">Card</div>

<!-- Muted/secondary text -->
<p class="text-gray-500">Secondary information</p>

<!-- Success/Error states -->
<div class="bg-green-50 text-green-700 border border-green-200">Success message</div>
<div class="bg-red-50 text-red-700 border border-red-200">Error message</div>
```

## Practice

1. Ek "alert box" banao 4 variants ke sath: success (green), error (red), warning (amber), info (blue) — har ek `bg-{color}-50`, `text-{color}-700`, `border-{color}-200`.
2. Ek gradient hero banao: `bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500`.
3. `bg-black/50` se ek overlay banao image ke upar (position absolute ke sath — [06_flexbox_grid_layout.md](06_flexbox_grid_layout.md) mein positioning aayega).

Agli file: [06_flexbox_grid_layout.md](06_flexbox_grid_layout.md)
</content>
