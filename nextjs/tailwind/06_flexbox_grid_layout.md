# Flexbox, Grid Aur Layout

## Display

```html
block    inline-block    inline    flex    inline-flex    grid    hidden
```

`hidden` = `display: none` — element ko poori tarah remove karta hai (screen reader ke liye bhi).

## Flexbox — Container Properties

```html
<div class="flex">                    <!-- display: flex, row direction default -->
<div class="flex flex-col">            <!-- column direction -->
<div class="flex flex-row-reverse">     <!-- reverse order -->
<div class="flex flex-wrap">              <!-- items wrap karein agar space kam ho -->
```

### Justify Content — Main Axis Pe Alignment (Horizontal, Row Mein)

```html
justify-start     → shuru mein
justify-center      → center mein
justify-end           → aakhir mein
justify-between         → dono taraf, beech mein equal gap
justify-around            → har item ke around equal space
justify-evenly              → sab gaps bilkul equal
```

### Items — Cross Axis Pe Alignment (Vertical, Row Mein)

```html
items-start    items-center    items-end    items-stretch    items-baseline
```

### Common Flex Patterns

```html
<!-- Navbar: logo left, links right -->
<div class="flex justify-between items-center">
  <div>Logo</div>
  <div>Links</div>
</div>

<!-- Perfectly centered content -->
<div class="flex justify-center items-center h-screen">
  <div>Centered Box</div>
</div>

<!-- Card row jo wrap ho jaye -->
<div class="flex flex-wrap gap-4">
  <div class="w-64">Card 1</div>
  <div class="w-64">Card 2</div>
</div>
```

## Flexbox — Child (Item) Properties

```html
flex-1       → grow aur shrink dono, available space le le
flex-none     → na grow na shrink, fixed size rahe
flex-grow      → sirf grow
flex-shrink     → sirf shrink
```

```html
<div class="flex">
  <div class="w-32">Fixed sidebar</div>
  <div class="flex-1">Baaki poori jagah ye le lega</div>
</div>
```

## Grid — Basics

```html
<div class="grid grid-cols-3 gap-4">
  <div>1</div>
  <div>2</div>
  <div>3</div>
  <div>4</div>
</div>
```

`grid-cols-3` = 3 equal-width columns, items automatically agli row mein wrap hote hain.

```html
grid-cols-1   grid-cols-2   grid-cols-3   grid-cols-4   grid-cols-6   grid-cols-12
grid-rows-2   grid-rows-3
```

### Column/Row Span

```html
<div class="grid grid-cols-3 gap-4">
  <div class="col-span-2">2 columns wide</div>
  <div>1 column</div>
</div>
```

### Responsive Grid (Sabse Common Real-World Pattern)

```html
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  <!-- mobile: 1 column, tablet: 2 columns, desktop: 3 columns -->
</div>
```

## Positioning

```html
relative     absolute     fixed     sticky     static
```

```html
<div class="relative">
  <div class="absolute top-0 right-0">Top-right corner badge</div>
</div>
```

- `relative` — parent ko reference point banata hai
- `absolute` — nearest `relative` parent ke against position hota hai
- `fixed` — poori screen ke against fixed rehta hai (scroll pe bhi nahi hilta)
- `sticky` — normal flow mein rehta hai jab tak scroll threshold cross na ho, phir stick ho jata hai

```html
<nav class="sticky top-0 bg-white z-10">Sticky navbar jo scroll pe upar chipak jaye</nav>
```

## Z-Index

```html
z-0    z-10    z-20    z-50    z-auto
```

## Container Aur Centering Pattern

```html
<div class="container mx-auto px-4">
  <!-- container = responsive max-width automatically set karta hai per breakpoint -->
</div>
```

Zyada control chahiye ho to manual pattern behtar hai:
```html
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
```

## Flex Vs Grid — Kab Konsa

| Use Case | Best Choice |
|---|---|
| Navbar, button group, single row/column | Flexbox |
| Card gallery, dashboard layout, image grid | Grid |
| Content jo dynamically wrap ho | Flexbox (`flex-wrap`) |
| Precise 2D layout (rows aur columns dono control) | Grid |

## Practice

1. Ek navbar banao: `flex justify-between items-center p-4` — left mein logo text, right mein 3 links `gap-6` ke sath.
2. Ek product grid banao: `grid grid-cols-1 md:grid-cols-3 gap-6` — 6 cards ke sath.
3. Ek badge banao jo card ke top-right corner pe absolute position ho (`relative` parent + `absolute top-2 right-2`).

Agli file: [07_borders_shadows_effects.md](07_borders_shadows_effects.md)
</content>
