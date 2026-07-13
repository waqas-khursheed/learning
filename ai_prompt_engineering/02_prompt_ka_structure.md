# Har Achy Prompt Ka Structure

Achy prompts random nahi hote — unka ek pattern hota hai. Ye pattern yaad rakho: **R-G-C-C-O**

```
R — Role         (AI kaun bane?)
G — Goal          (Aap kya chahte ho?)
C — Context        (Situation/background kya hai?)
C — Constraints     (Kis cheez ka khayal rakhna hai?)
O — Output Format    (Jawab kis shape mein chahiye?)
```

## Har Part Ki Detail

### 1. Role (Kirdar)
AI ko batao wo kis "hat" mein jawab de. Isse tone, depth, aur vocabulary sahi set ho jati hai.

- "Tum ek senior Laravel developer ho"
- "Tum ek expert code reviewer ho"
- "Tum ek beginner-friendly programming teacher ho"

### 2. Goal (Maqsad)
Ek line mein saaf saaf batao aap kya achieve karna chahte ho. Ye prompt ka "asal sawal" hota hai.

- "Mujhe payment integration banani hai"
- "Mujhe ye error fix karna hai"
- "Mujhe ye concept samajhna hai"

### 3. Context (Pas-e-Manzar)
Ye sabse zyada miss ki jane wali cheez hai. Context mein ye batao:
- Tech stack aur uska **version** (Laravel 12, React 18, PHP 8.2, Python 3.12)
- Project ka type (e-commerce, SaaS, internal tool)
- Aapka experience level (beginner/intermediate/senior) — isse explanation ka level set hota hai
- Koi relevant existing code ya architecture

### 4. Constraints (Pabandiyan)
Wo rules jo follow honi chahiye:
- Security requirements ("SQL injection se bachao")
- Performance requirements ("bade dataset ke liye optimized ho")
- Coding standards ("PSR-12 follow karo", "PEP8 follow karo")
- Time/scope limits ("simple solution chahiye, over-engineering mat karo")

### 5. Output Format (Jawab Ka Shape)
AI ko batao result kis form mein chahiye:
- Pure code (bina explanation ke)
- Code + explanation
- Step-by-step points
- Table
- Markdown document
- Sirf bullet summary

## Poora Example (Sab Parts Combine)

> **Role:** Tum ek senior Laravel developer ho.
> **Goal:** Mujhe ek referral system banana hai jahan user apna referral link share kar sake aur reward mile.
> **Context:** Laravel 12 use kar raha hoon, database MySQL hai, mera intermediate level hai.
> **Constraints:** Fraud prevention hona chahiye (same user do dafa reward na le sake), code clean aur maintainable ho.
> **Output:** Pehle database schema do (tables + columns), phir controller logic ka code do, phir short explanation.

Is ek prompt mein AI ko sab kuch pata hai — is liye jawab bhi directly usable hoga, generic nahi.

## Chota Version (Roz Marra Ke Liye)

Har prompt itna lamba likhna zaroori nahi. Chote sawalon ke liye bhi kam se kam ye 3 cheezein zaroor include karo:

```
Context + Goal + Output format
```

Example: "Mai Laravel 12 use kar raha hoon (context). Eloquent relationship hasMany aur belongsToMany ka farak samjhao (goal), ek chota real example ke sath (output format)."

## Practice (Khud Karo)

Neeche diye gaye weak prompts ko R-G-C-C-O structure use karke rewrite karo:

1. "React hooks samjhao"
2. "Ye code slow hai, fix karo"
3. "Database design chahiye"

Agli file: [03_prompt_techniques.md](03_prompt_techniques.md)
