# States — Hover, Focus, Group, Peer

## Basic Pseudo-Class States

```html
hover:bg-blue-700       → mouse hover pe
focus:ring-2               → input/button focused hone pe
active:bg-blue-800            → click ke waqt (pressed state)
disabled:opacity-50              → disabled element
visited:text-purple-600             → visited link
```

```html
<button class="bg-blue-600 hover:bg-blue-700 active:bg-blue-800 disabled:opacity-50 disabled:cursor-not-allowed">
  Submit
</button>
```

**Multiple states combine bhi ho sakte hain:**
```html
<input class="border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none" />
```

## Form-Specific States

```html
checked:bg-blue-600        → checkbox/radio checked hone pe
invalid:border-red-500      → form validation fail (HTML5 validation)
valid:border-green-500       → form validation pass
placeholder:text-gray-400      → placeholder text ka color
required:border-orange-300      → required field
```

```html
<input
  type="email"
  required
  class="border border-gray-300 invalid:border-red-500 valid:border-green-500"
/>
```

## First, Last, Odd, Even — Child Selectors

```html
first:mt-0      → list ka pehla item
last:mb-0        → list ka aakhri item
odd:bg-gray-50     → odd rows (table striping ke liye)
even:bg-white        → even rows
```

```html
<table>
  <tr class="odd:bg-gray-50 even:bg-white">...</tr>
</table>
```

## Group — Parent Hover Pe Child Style Karna

Jab parent pe hover ho aur usse **andar ka koi child** style change karna ho:

```html
<div class="group hover:bg-gray-50 p-4 rounded-lg">
  <h3 class="text-gray-900">Title</h3>
  <p class="text-gray-500 group-hover:text-blue-600">
    Ye text sirf tab blue hoga jab PARENT (group) hover ho, khud is element ka hover nahi.
  </p>
</div>
```

Real use case — card jahan poore card pe hover se andar ka icon/arrow move ho:

```html
<a href="#" class="group flex items-center gap-2 p-4 hover:bg-gray-50">
  <span>Read More</span>
  <span class="group-hover:translate-x-1 transition-transform">→</span>
</a>
```

## Peer — Sibling Element Ke State Se Style Karna

Jab ek element (jaise input) ke state se **uske sibling** ko style karna ho:

```html
<input type="email" class="peer border border-gray-300" />
<p class="hidden peer-invalid:block text-red-500 text-sm">
  Valid email daalein
</p>
```

Yahan `input` ko `peer` class di, aur uske baad wale sibling mein `peer-invalid:block` — jab input invalid ho tab error message dikhega.

```html
<!-- Custom checkbox jahan label checkbox ke checked state se style ho -->
<input type="checkbox" id="terms" class="peer hidden" />
<label
  for="terms"
  class="border-2 border-gray-300 peer-checked:border-blue-600 peer-checked:bg-blue-600 ..."
>
  Accept Terms
</label>
```

**Group vs Peer ka farak:** `group` **parent → child** control karta hai, `peer` **ek sibling → doosra sibling** control karta hai.

## Named Groups/Peers (Nested Groups Ke Liye)

Jab ek se zyada nested `group` hon aur specific wale ko target karna ho:

```html
<div class="group/card">
  <div class="group/button">
    <span class="group-hover/card:text-blue-600">Card hover se ye change hoga</span>
    <span class="group-hover/button:text-red-600">Button hover se ye change hoga</span>
  </div>
</div>
```

## Has — Child Ke Basis Pe Parent Style Karna (Naya Feature)

```html
<div class="has-[:checked]:bg-blue-50 p-4 border rounded-lg">
  <input type="checkbox" />
  <span>Agar andar ka checkbox checked hai to poora box blue background lega</span>
</div>
```

## Practice

1. Ek button banao jisme `hover:`, `active:`, aur `disabled:` teeno states alag dikhein.
2. `group` use karke ek card banao jisme parent hover pe andar ka title color change ho.
3. `peer` use karke ek custom checkbox/toggle banao (checked state pe background color change ho).

Agli file: [10_dark_mode.md](10_dark_mode.md)
</content>
