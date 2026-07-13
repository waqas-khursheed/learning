# Borders, Shadows Aur Effects

## Border Width Aur Color

```html
border          → border-width: 1px (sab sides)
border-2          → 2px
border-t            → sirf top border
border-b             → sirf bottom
border-x               → left + right
border-gray-300          → color

<div class="border border-gray-300">Simple bordered box</div>
```

## Border Radius (Rounded Corners)

```html
rounded-none    → 0
rounded-sm       → 0.125rem
rounded            → 0.25rem
rounded-md           → 0.375rem
rounded-lg            → 0.5rem
rounded-xl             → 0.75rem
rounded-2xl              → 1rem
rounded-3xl                → 1.5rem
rounded-full                 → 9999px (perfect circle/pill)
```

```html
<div class="rounded-lg">Card corners</div>
<img class="rounded-full w-16 h-16" src="avatar.jpg" /> <!-- circular avatar -->
<button class="rounded-full px-6 py-2">Pill button</button>
```

Individual corners: `rounded-t-lg`, `rounded-tr-xl`, `rounded-bl-md` waghera.

## Box Shadow

```html
shadow-sm     → chota subtle shadow
shadow          → default
shadow-md        → medium
shadow-lg         → large
shadow-xl          → extra large
shadow-2xl           → bohat bada, dramatic
shadow-none            → shadow hatana
shadow-inner              → andar ki taraf shadow (inset)
```

```html
<div class="bg-white rounded-lg shadow-md p-6">
  Card jo thora ubhra hua lage
</div>
```

Colored shadow (v3.4+):
```html
<div class="shadow-lg shadow-blue-500/50">Blue-tinted shadow</div>
```

## Ring — Outline Jaisa, Lekin Better

```html
ring       → 3px ring (default)
ring-2      → 2px
ring-4       → 4px
ring-blue-500 → color
ring-offset-2  → ring aur element ke beech gap
```

```html
<input class="focus:ring-2 focus:ring-blue-500 focus:outline-none" />
```

`ring` mostly focus states mein use hota hai — normal `border` ki tarah layout shift nahi karta (box ke bahar render hota hai), is liye focus indicators ke liye better hai `border` badalne se.

## Opacity

```html
opacity-0     → invisible (lekin space le ga)
opacity-50      → 50% transparent
opacity-100       → fully visible
```

## Blur Aur Backdrop Effects

```html
blur-sm   blur   blur-md   blur-lg   blur-xl    → element ko khud blur karna
backdrop-blur-sm   backdrop-blur-md              → element ke peeche jo hai use blur karna (glassmorphism effect)
```

```html
<div class="bg-white/30 backdrop-blur-md rounded-xl p-6">
  Glassmorphism card — semi-transparent + blurred background
</div>
```

## Outline (Accessibility Ke Liye Zaroori)

```html
outline-none    → default browser outline hata do (lekin phir manually focus state dena zaroori hai — accessibility)
outline          → outline add karo
outline-2 outline-blue-500  → styled outline
```

**Zaroori:** `outline-none` kabhi akela mat lagao — keyboard users ke liye focus indicator zaroor rakho (`focus:ring-2` se replace karo).

## Poora Example — Modern Card

```html
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
  <img class="rounded-full w-12 h-12" src="avatar.jpg" />
  <h3 class="mt-4 text-lg font-semibold">Card Title</h3>
  <p class="mt-2 text-gray-600">Description text here.</p>
</div>
```

## Practice

1. Upar wala card example banao, `hover:shadow-xl` ka farak dekho mouse le jaake.
2. `ring-2 ring-blue-500 ring-offset-2` se ek focused input banao, dekho ring border se kaise farak hai.
3. Ek glassmorphism card banao: `bg-white/20 backdrop-blur-lg` ek background image ke upar.

Agli file: [08_responsive_design.md](08_responsive_design.md)
</content>
