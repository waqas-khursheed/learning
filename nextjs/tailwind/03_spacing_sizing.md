# Spacing Aur Sizing

## Padding Aur Margin — Poori List

```html
p-4    → padding: 1rem (sab sides)
px-4   → padding-left + padding-right
py-4   → padding-top + padding-bottom
pt-4   pr-4   pb-4   pl-4   → individual sides
ps-4   pe-4   → padding-inline-start/end (RTL-aware — Urdu/Arabic sites ke liye important!)

m-4    → margin (sab sides)
mx-4   my-4   mt-4   mr-4   mb-4   ml-4   → same pattern
mx-auto → margin-left/right: auto (horizontally center karne ke liye)
-mt-4   → negative margin (minus sign class se pehle lagao)
```

**RTL note:** Chunki aap Urdu content ke sath kaam karte ho, `ps-`/`pe-` (start/end) `pl-`/`pr-` (left/right) se better hain agar kabhi RTL layout bhi support karna ho — wo direction ke hisaab se automatically flip hoti hain.

## Width Aur Height

```html
w-4        → width: 1rem (fixed, spacing scale se)
w-1/2       → width: 50% (fraction)
w-full       → width: 100%
w-screen      → width: 100vw
w-auto         → width: auto
w-fit           → width: fit-content
w-min            → width: min-content
w-max             → width: max-content

h-4   h-1/2   h-full   h-screen   h-auto   → same pattern height ke liye
```

## Max/Min Width Aur Height

```html
max-w-sm     → max-width: 24rem  (small containers)
max-w-md      → max-width: 28rem
max-w-lg       → max-width: 32rem
max-w-xl        → max-width: 36rem
max-w-2xl        → max-width: 42rem
max-w-4xl         → max-width: 56rem
max-w-screen-lg    → max-width: 1024px (breakpoint ke barabar)
max-w-full          → max-width: 100%
max-w-none           → max-width: none

min-w-0   min-w-full   → similarly
min-h-screen  → min-height: 100vh (poori screen height cover karne ke liye — page layouts mein bohat use hota hai)
```

**Common pattern — page container:**
```html
<div class="max-w-4xl mx-auto px-4">
  <!-- content center mein, max width tak limited, responsive padding ke sath -->
</div>
```

## Fraction-Based Widths (Grid Ke Bina Column Layout)

```html
<div class="flex">
  <div class="w-1/3">1/3 width</div>
  <div class="w-2/3">2/3 width</div>
</div>
```

Available fractions: `1/2`, `1/3`, `2/3`, `1/4`, `2/4`, `3/4`, `1/5` ... `1/12` tak (12-column grid jaisa).

## Gap — Flex/Grid Children Ke Beech Spacing

```html
<div class="flex gap-4">          <!-- sab directions gap -->
<div class="flex gap-x-4 gap-y-2"> <!-- alag horizontal/vertical gap -->
```

`gap` `margin` se better hai children ke beech spacing ke liye — last child pe extra space nahi lagta (margin mein manually last-child handle karna parta tha).

## Space Between — Purane Pattern (Gap Se Pehle)

```html
<div class="flex space-x-4">
  <button>One</button>
  <button>Two</button>
  <button>Three</button>
</div>
```

`space-x-4` har child ke beech horizontal gap deta hai (pehle child ko chhod kar). **Aaj kal `gap` zyada recommended hai** (flex/grid dono mein consistent kaam karta hai), `space-x`/`space-y` legacy pattern hai lekin abhi bhi kaam karta hai.

## Aspect Ratio

```html
<div class="aspect-video">   <!-- 16:9 -->
<div class="aspect-square">   <!-- 1:1 -->
<div class="aspect-[4/3]">     <!-- custom ratio -->
```

Images/videos ko fixed ratio mein rakhne ke liye — layout shift avoid karta hai.

## Practice

1. Ek page banao jisme `<div class="max-w-2xl mx-auto px-4">` container ho, andar 3 cards `gap-4` ke sath.
2. `w-1/3` + `w-2/3` se ek 2-column layout banao (flex ke sath).
3. `aspect-video` use karke ek placeholder video box banao (`bg-gray-300`).

Agli file: [04_typography.md](04_typography.md)
</content>
