# Tailwind CSS Roadmap — Zero Se Poori Tarah Samajhne Tak

Ye roadmap Tailwind CSS ko zero se lekar advanced level tak, poori tarah samajhne ke liye hai. Aap Next.js already seekh rahe ho, is folder mein sirf Tailwind pe deep-dive karenge — kyun ke real projects mein styling ka 90% kaam Tailwind hi karega.

## Kaise Follow Karna Hai

Sequence se parho. Har file ke andar bohat sare code examples hain — sirf parho mat, ek chote HTML/Next.js page mein khud likh kar test karo browser mein. Tailwind seekhne ka sabse best tareeqa hai classes likhna aur turant result dekhna.

| # | File | Kya Seekhoge |
|---|------|----------------|
| 1 | [01_tailwind_kya_hai_aur_installation.md](01_tailwind_kya_hai_aur_installation.md) | Tailwind kya hai, utility-first kyun, Next.js/Vite mein install karna |
| 2 | [02_utility_first_fundamentals.md](02_utility_first_fundamentals.md) | Utility classes ka pattern, naming convention, arbitrary values |
| 3 | [03_spacing_sizing.md](03_spacing_sizing.md) | Margin, padding, width, height, spacing scale |
| 4 | [04_typography.md](04_typography.md) | Font, text size, weight, line-height, alignment |
| 5 | [05_colors_backgrounds.md](05_colors_backgrounds.md) | Color palette, background, gradients, opacity |
| 6 | [06_flexbox_grid_layout.md](06_flexbox_grid_layout.md) | Flexbox, Grid, positioning, container |
| 7 | [07_borders_shadows_effects.md](07_borders_shadows_effects.md) | Border, radius, shadow, ring |
| 8 | [08_responsive_design.md](08_responsive_design.md) | Breakpoints, mobile-first design |
| 9 | [09_states_hover_focus_group_peer.md](09_states_hover_focus_group_peer.md) | hover, focus, disabled, group, peer states |
| 10 | [10_dark_mode.md](10_dark_mode.md) | Dark mode setup aur usage |
| 11 | [11_animations_transitions_transforms.md](11_animations_transitions_transforms.md) | Transition, animation, transform |
| 12 | [12_custom_theme_config.md](12_custom_theme_config.md) | tailwind.config customize karna, apna design system banana |
| 13 | [13_reusable_components_apply_cva.md](13_reusable_components_apply_cva.md) | `@apply`, `clsx`, `cva` se reusable components |
| 14 | [14_best_practices_plugins.md](14_best_practices_plugins.md) | Best practices, official plugins, common mistakes |

## Golden Rules

1. **Utility-first soch badlo.** Alag CSS file mein class likhne ki bajaye, HTML/JSX mein hi directly styling karo.
2. **Docs hamesha khula rakho.** `tailwindcss.com/docs` — itni classes hain ke sab yaad rakhna zaroori nahi, dhoondhna aana chahiye.
3. **Mobile-first socho.** Bina prefix wali class mobile ke liye hai, `md:`/`lg:` se upar wale screens ke liye override karte ho.
4. **Design tokens use karo, random values nahi.** `p-4` likho `p-[17px]` ki jagah — consistency important hai.
5. **Har class ka matlab samjho, sirf copy-paste na karo** — is roadmap ka poora maqsad yehi hai.

Shuru karne ke liye [01_tailwind_kya_hai_aur_installation.md](01_tailwind_kya_hai_aur_installation.md) kholo.
</content>
