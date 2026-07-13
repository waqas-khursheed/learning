# Advanced Prompting Techniques

Jab basics comfortable ho jayen, ye advanced techniques aapko AI se aur zyada nikalne mein madad karengi.

## 1. Prompt Chaining (Bade Task Ko Todna)

Ek hi prompt mein pura complex system mangna kabhi accha result nahi deta. Iski jagah task ko chote steps mein todo aur har step alag prompt ke through complete karo.

**Galat tareeqa (ek hi prompt mein sab kuch):**
> "Mujhe pura e-commerce system bana do — database, backend, frontend, payment, sab kuch."

**Sahi tareeqa (chaining):**
```
Prompt 1: "Pehle database schema design karo e-commerce ke liye (products, orders, users, payments)"
   ↓ (result use karke agla prompt)
Prompt 2: "Ab is schema ke hisab se Laravel models aur relationships banao"
   ↓
Prompt 3: "Ab OrderController banao jo checkout process handle kare"
   ↓
Prompt 4: "Ab Stripe payment integration add karo is controller mein"
```

Har step ka output agle step ka context banta hai — is se AI ka focus narrow rehta hai aur quality behtar hoti hai.

## 2. Structured Output (JSON/Table Mangna)

Agar aapko response ko code mein use karna hai (parse karna hai), to AI se structured format mangwao:

> "Mujhe 5 API endpoints ka structure JSON format mein do: `{ method, url, description, auth_required }` ke fields ke sath."

Isse aap directly response ko apne code/documentation mein use kar sakte ho, bina manually reformat kiye.

## 3. Meta-Prompting (AI Se Prompt Likhwana)

Agar aapko khud accha prompt likhna mushkil lag raha hai, AI se hi prompt improve karwao:

> "Mera ye prompt hai: '{apna weak prompt paste karo}'. Isay behtar banao taake mujhe zyada specific aur useful jawab mile."

Ye ek chota lekin bohot powerful trick hai — AI khud apne liye behtar instructions likh sakta hai.

## 4. Negative Prompting (Kya Nahi Chahiye)

Sirf ye mat batao kya chahiye — ye bhi batao kya avoid karna hai:

> "Ye function likho, lekin recursion use mat karo, aur koi external package install na karo."

## 5. Context Window Management

AI ki memory (context window) limited hoti hai. Lambi conversation mein purani details "bhool" sakta hai. Isliye:

- Agar conversation lambi ho jaye aur AI confuse ho raha ho, **important context dobara repeat karo**.
- Bade code files ko poora paste karne ki jagah, sirf relevant hissa paste karo.
- Naye topic pe jane se pehle, agar zaroori ho to purana context summarize kar ke naye message mein daal do.

## 6. Multi-Persona Prompting (Do Perspectives)

Ek hi problem ko do alag roles se dekho — bug ya architecture decision ki quality check karne ke liye achha tareeqa hai.

> "Pehle ek Security Expert ki tarah is code ko review karo. Phir ek Performance Engineer ki tarah review karo. Dono perspectives alag alag do."

## 7. Temperature/Creativity Ka Concept (Awareness)

AI tools mein kabhi kabhi "temperature" ya "creativity" setting hoti hai:
- **Low temperature** = predictable, factual, consistent answers (code, documentation ke liye best)
- **High temperature** = creative, varied answers (brainstorming, naming ideas ke liye best)

Agar tool mein ye setting available ho, task ke hisab se adjust karo. Agar nahi hai, to prompt mein khud bol sakte ho: "Creative aur unique ideas do" ya "Sirf factual, proven approach batao".

## 8. Verification Prompting (Double-Check Karwana)

AI kabhi kabhi confidently galat jawab de sakta hai ("hallucination"). Important cheezon ke liye verify karwao:

> "Kya ye approach Laravel 12 mein actually available hai? Agar koi deprecated method use hua hai to batao."

Aur khud bhi official docs se cross-check karna na bhoolo — especially jab AI koi specific function/API name bataye jo aapne pehle na dekha ho.

## Practice (Khud Karo)

Ek medium-size feature socho (jaise "user notification system"). Usay prompt chaining technique se 4 steps mein todo aur har step alag prompt likh kar AI se implement karwao.

Agli file: [06_common_mistakes.md](06_common_mistakes.md)
