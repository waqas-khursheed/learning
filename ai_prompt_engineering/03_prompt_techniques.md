# Prompt Techniques — Alag Alag Tareeqe

Ye woh techniques hain jo professional prompt engineers use karte hain. Har technique alag situation ke liye best hai.

## 1. Zero-Shot Prompting
Sirf sawal poocho, koi example diye baghair. Simple, direct tasks ke liye theek hai.

> "PHP mein string ko reverse karne ka function likho."

**Kab use karo:** Jab task simple ho aur AI ko already pata ho aisa pattern kaise likhna hai.

## 2. Few-Shot Prompting
AI ko 1-2 examples do taake wo aapka exact desired pattern samjhe.

> "Mujhe function names is style mein chahiye:
> `getUserById()`, `getOrderByStatus()`
> Ab isi pattern mein `getProductByCategory` jaisa function banao."

**Kab use karo:** Jab aapko specific format/style chahiye jo AI khud guess na kar sake.

## 3. Chain-of-Thought (Step-by-Step Sochna)
AI ko bolo "step by step socho" ya "reasoning explain karo" — isse complex problems mein accuracy badh jaati hai.

> "Is bug ko step by step analyze karo: pehle possible causes list karo, phir har cause ko rule in/out karo, phir final fix do."

**Kab use karo:** Debugging, algorithm design, ya koi bhi multi-step logical problem.

## 4. Role Prompting
AI ko koi specific persona do — expert, teacher, reviewer, interviewer, etc.

> "Tum ek FAANG interviewer ho. Mujse ek medium-level DSA question pucho aur mera answer evaluate karo."

**Kab use karo:** Jab aapko ek particular perspective ya expertise level chahiye ho.

## 5. Iterative Refinement (Follow-up Se Behtar Karna)
Pehla jawab shayad perfect na ho — usay refine karo follow-up prompts se, poora prompt dobara mat likho.

> Pehla prompt: "Ye function likho..."
> Follow-up: "Isay aur short karo" ya "Isme error handling bhi add karo" ya "Isay TypeScript mein convert karo"

**Kab use karo:** Hamesha! Prompting ek conversation hai, ek-shot order nahi.

## 6. Constraint-Based Prompting
Explicitly bolo kya **nahi** chahiye — isse AI ka scope control mein rehta hai.

> "Simple solution do, koi external library use mat karo, sirf native PHP functions."

**Kab use karo:** Jab AI overthink kar ke complex/unnecessary solution de raha ho.

## 7. Comparison Prompting
Do options ko compare karwao taake decision lena easy ho.

> "MySQL aur PostgreSQL ka farak batao mere use case ke liye: high-traffic e-commerce app, real-time inventory updates."

**Kab use karo:** Architecture/technology decisions lene ke liye.

## 8. Self-Critique Prompting
AI se bolo apna khud ka jawab review kare.

> "Upar jo code diya, usay ab khud review karo — kya koi security issue, edge case, ya performance problem hai?"

**Kab use karo:** Important/production code ke liye extra confidence chahiye ho to.

## Quick Reference Table

| Technique | Best For |
|---|---|
| Zero-Shot | Simple, common tasks |
| Few-Shot | Specific format/style chahiye |
| Chain-of-Thought | Debugging, complex logic |
| Role Prompting | Expertise/perspective chahiye |
| Iterative Refinement | Har normal conversation |
| Constraint-Based | AI ka scope control karna |
| Comparison | Decision making |
| Self-Critique | Production-quality code |

## Practice (Khud Karo)

Ek real problem lo jo aap abhi face kar rahe ho (koi bug ya feature). Usay 3 alag techniques se prompt karke dekho — Chain-of-Thought, Role Prompting, aur Constraint-Based. Dekho kaunsa jawab sabse useful nikla.

Agli file: [04_ready_templates.md](04_ready_templates.md)
