# Responsive Design

## Breakpoints — Default Scale

| Prefix | Min Width | Typical Device |
|---|---|---|
| (none) | 0px | Mobile (default, hamesha applies) |
| `sm:` | 640px | Bada mobile / chota tablet |
| `md:` | 768px | Tablet |
| `lg:` | 1024px | Laptop |
| `xl:` | 1280px | Desktop |
| `2xl:` | 1536px | Bada desktop |

## Mobile-First — Sabse Important Concept

**Bina prefix wali class hamesha applies hoti hai (mobile se shuru), prefix wali class us breakpoint aur usse upar apply hoti hai.**

```html
<div class="text-sm md:text-lg lg:text-2xl">
  Mobile pe chota text, tablet pe medium, desktop pe bada
</div>
```

Ye "mobile-first" approach hai — soch mobile se shuru karo, phir bade screens ke liye override karo. **Desktop-first mat socho** (jaise `text-2xl md:text-sm` — ye ulta hai aur confusing pattern banata hai).

## Common Responsive Patterns

### Grid Columns Badalna

```html
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
  <!-- mobile: 1 column, small: 2 columns, large: 4 columns -->
</div>
```

### Flex Direction Badalna

```html
<div class="flex flex-col md:flex-row gap-4">
  <!-- mobile: stacked (column), tablet+: side by side (row) -->
</div>
```

### Element Hide/Show Per Breakpoint

```html
<div class="hidden md:block">Sirf tablet aur upar dikhega</div>
<div class="block md:hidden">Sirf mobile pe dikhega (hamburger menu jaisa)</div>
```

### Padding/Spacing Responsive

```html
<div class="px-4 sm:px-6 lg:px-8">
  Mobile pe kam padding, desktop pe zyada
</div>
```

## Poora Real Example — Responsive Navbar

```html
<nav class="flex items-center justify-between p-4">
  <div class="text-xl font-bold">Logo</div>

  <!-- Desktop links -->
  <div class="hidden md:flex gap-6">
    <a href="#">Home</a>
    <a href="#">About</a>
    <a href="#">Contact</a>
  </div>

  <!-- Mobile hamburger -->
  <button class="md:hidden">☰</button>
</nav>
```

## Container Queries (Tailwind v4 Feature)

Normal breakpoints **viewport** (poori screen) ke hisaab se hain. Kabhi kabhi aapko component ke **parent container** ke size ke hisaab se style change karni hoti hai (jaise sidebar mein card chota render ho, main area mein bada) — is ke liye:

```html
<div class="@container">
  <div class="@sm:text-lg @lg:text-2xl">
    Parent container ke size ke hisaab se text size badlega
  </div>
</div>
```

Ye advanced feature hai, jab component reusable hona ho different-sized containers mein tab useful hota hai.

## Breakpoint Range — Sirf Ek Range Ke Liye

```html
<div class="md:max-lg:flex">
  Sirf md se lg tak (768px-1023px) apply hoga
</div>
```

## Custom Breakpoints (Agar Default Kaafi Na Hon)

```css
/* app/globals.css (Tailwind v4) */
@theme {
  --breakpoint-xs: 480px;
}
```

Phir `xs:` prefix available ho jayega.

## Testing Responsive Design

Chrome DevTools → Toggle Device Toolbar (Ctrl+Shift+M) — different device sizes pe test karo. Sirf browser window resize karna kaafi nahi, actual device presets (iPhone, iPad) pe bhi check karo.

## Practice

1. Ek 3-column pricing table banao jo mobile pe stack ho (`grid-cols-1`), desktop pe side-by-side ho (`md:grid-cols-3`).
2. Navbar banao jisme desktop pe links dikhein, mobile pe sirf hamburger icon (`hidden md:flex` + `md:hidden`).
3. DevTools mein apna page kholo, mobile/tablet/desktop teeno sizes pe test karo.

Agli file: [09_states_hover_focus_group_peer.md](09_states_hover_focus_group_peer.md)
</content>
