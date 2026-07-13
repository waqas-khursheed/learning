# Transitions, Animations Aur Transforms

## Transition — State Change Smooth Karna

Bina `transition` ke, `hover:bg-blue-700` jaise state changes **instant** hote hain (jhatke se). `transition` add karne se smooth ho jate hain:

```html
<button class="bg-blue-600 hover:bg-blue-700 transition-colors duration-200">
  Smooth Color Change
</button>
```

```html
transition          → sab common properties (color, background, border, shadow, transform, opacity)
transition-colors     → sirf color-related properties
transition-transform    → sirf transform
transition-opacity       → sirf opacity
transition-all             → literally sab properties (thora heavy, zaroorat pe hi use karo)
transition-none              → transition hata do
```

## Duration Aur Timing Function

```html
duration-75    duration-150    duration-300    duration-500    duration-1000
```

```html
ease-linear     → constant speed
ease-in           → slow start, fast end
ease-out            → fast start, slow end (buttons/hover ke liye best feel deta hai)
ease-in-out           → dono taraf slow
```

```html
<div class="transition-all duration-300 ease-out hover:scale-105">
  Hover pe smoothly bada ho jayega
</div>
```

## Delay

```html
delay-100    delay-300    delay-500
```

Multiple elements ko staggered animate karne ke liye useful (ek ke baad ek).

## Transform — Scale, Rotate, Translate, Skew

```html
scale-95    scale-100    scale-105    scale-110    → size badlo/ghatao
rotate-45    rotate-90     rotate-180                → ghumao
translate-x-4  translate-y-2  -translate-x-4          → move karo (position shift, layout affect nahi hoti)
skew-x-6                                                → tilt karo
```

```html
<button class="transition-transform hover:scale-105 active:scale-95">
  Hover pe thoda bada, click pe thoda chota — bohat satisfying feel deta hai
</button>
```

```html
<img class="transition-transform duration-500 hover:rotate-6 hover:scale-110" src="..." />
```

## Built-in Animations

Tailwind kuch ready-made animations deta hai:

```html
animate-spin      → loading spinner (continuous rotate)
animate-ping        → radar/notification ping effect
animate-pulse         → skeleton loading effect (fade in/out)
animate-bounce          → bounce up-down
```

```html
<!-- Loading spinner -->
<div class="w-8 h-8 border-4 border-gray-200 border-t-blue-600 rounded-full animate-spin"></div>

<!-- Skeleton loading card -->
<div class="animate-pulse">
  <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
  <div class="h-4 bg-gray-200 rounded w-1/2"></div>
</div>

<!-- Notification dot with ping effect -->
<span class="relative flex h-3 w-3">
  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
  <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
</span>
```

## Custom Animation (Tailwind v4 — CSS Mein Define Karo)

```css
/* app/globals.css */
@theme {
  --animate-fade-in: fade-in 0.5s ease-in-out;
}

@keyframes fade-in {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
```

```html
<div class="animate-fade-in">Ye smoothly fade + slide in hoga</div>
```

## Common Real-World Patterns

```html
<!-- Card hover lift effect -->
<div class="transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
  Card jo hover pe thoda upar uthe
</div>

<!-- Dropdown menu open/close -->
<div class="transition-all duration-200 origin-top scale-y-0 opacity-0 data-[open=true]:scale-y-100 data-[open=true]:opacity-100">
  Dropdown content
</div>

<!-- Mobile menu slide-in -->
<div class="transition-transform duration-300 translate-x-full data-[open=true]:translate-x-0 fixed right-0 top-0 h-full w-64">
  Slide-in mobile menu
</div>
```

## Framer Motion — Jab Tailwind Ki Animation Kaafi Na Ho

Complex animations (gesture-based, physics/spring, orchestrated sequences) ke liye `framer-motion` library use hoti hai — Tailwind sirf CSS transitions/animations tak limited hai, JS-driven animation nahi karta. Dono ek sath use hoti hain: layout/spacing Tailwind se, complex motion Framer Motion se.

## Practice

1. Ek button banao jisme `hover:scale-105 active:scale-95 transition-transform` ho.
2. Ek loading skeleton card banao `animate-pulse` se (image placeholder + 2 text lines).
3. Ek card banao jo hover pe `-translate-y-2` aur `shadow-xl` dono ho (lift effect), `transition-all duration-300` ke sath.

Agli file: [12_custom_theme_config.md](12_custom_theme_config.md)
</content>
