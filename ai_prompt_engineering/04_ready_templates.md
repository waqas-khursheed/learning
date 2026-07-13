# Ready-Made Prompt Templates (Copy-Paste Karo)

Ye templates R-G-C-C-O structure (dekho [02_prompt_ka_structure.md](02_prompt_ka_structure.md)) par based hain. Bas `{ }` wale blanks fill karo aur use karo.

## 1. Development / Feature Building Prompt

```
Role: Tum ek senior {tech stack} developer ho.
Goal: Mujhe {feature/module} banana hai.
Context: Mai {framework + version} use kar raha hoon.
Constraints: {security/performance/coding standard rules} follow honi chahiye.
Output: Mujhe {complete code/controller/model/API} markdown format me chahiye.
```

**Example:**
> Role: Tum ek senior Laravel developer ho.
> Goal: Mujhe Stripe payment API banana hai jisme card aur Google Pay dono support ho.
> Context: Laravel 12 use kar raha hoon aur frontend React me hai.
> Constraints: PCI compliance aur webhook validation required hai.
> Output: Backend code example markdown format me do.

## 2. Debugging / Error Fix Prompt

```
Role: Tum ek expert {tech stack} debugger ho.
Goal: Mujhe ye error samjha kar fix karna hai.
Context: Mai {framework + version} use kar raha hoon.
Error: "{paste error message}"
Output: Step-by-step reason samjhao aur fix batao.
```

**Example:**
> Role: Tum ek expert Laravel debugger ho.
> Goal: Mujhe SQLSTATE[23000]: Integrity constraint violation ka error fix karna hai.
> Context: Laravel 12 use kar raha hoon, users aur orders table me relation hai.
> Error: "SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row..."
> Output: Reason samjhao aur solution code ke sath do.

## 3. Architecture / System Design Prompt

```
Role: Tum ek senior software architect ho.
Goal: Mujhe {system/feature} ka architecture design chahiye.
Context: Mai {frameworks/technologies} use kar raha hoon.
Constraints: System secure aur scalable hona chahiye.
Output: Architecture diagram explanation, database tables aur flowchart explain karo.
```

**Example:**
> Role: Tum ek senior software architect ho.
> Goal: Mujhe referral system ka architecture banana hai jisme user reward aur visit tracking ho.
> Context: Laravel backend aur React frontend use kar raha hoon.
> Constraints: Security aur fraud prevention maintain rehni chahiye.
> Output: Database design, service layer aur API flow explain karo.

## 4. Learning / Concept Explanation Prompt

```
Role: Tum ek expert teacher ho jo {tech/topic} sikhata hai.
Goal: Mujhe {concept} simple language me samjha do.
Context: Mai beginner / intermediate level developer hoon.
Output: Explanation, real example aur code snippet ke sath samjhao.
```

**Example:**
> Role: Tum ek expert Laravel teacher ho.
> Goal: Mujhe Service Container aur Interface ka difference samjhao.
> Context: Mai intermediate level Laravel developer hoon.
> Output: Real project example ke sath explain karo jisme dono ka use ho.

## 5. Documentation / Code Review Prompt

```
Role: Tum ek professional {tech stack} reviewer ho.
Goal: Mujhe is code ka review chahiye aur improvements suggest karni hain.
Context: Ye code ek {module/controller/service class} ka hai.
Code: """{paste your code here}"""
Output: Performance, security, aur best practices ke hisaab se suggestions do.
```

**Example:**
> Role: Tum ek professional Laravel reviewer ho.
> Goal: Mujhe is controller code ka review chahiye aur improvements suggest karni hain.
> Context: Ye code ek PayPal payment controller ka hai.
> Code: """{controller code here}"""
> Output: Performance aur clean code best practices ke sath improvement points do.

## 6. Learning Roadmap Prompt

```
Tum ek coding tutor ho {aapka naam} ke liye. {aapka naam} abhi {technology} seekh raha hai aur uska is field mein {experience level} hai.
Use bilkul {starting point} se guide karo aur uski learning journey step by step plan karo.
Realistic timeframe ke sath complete roadmap banao. Basic se advanced tak dheere dheere le jao.
Har stage par relevant external resources bhi recommend karo.
```

## 7. Focused Practice Prompt (1-2 Ghante Ke Liye)

```
Mujhe {topic} ke bare mein sab kuch batao jo mai {time limit} mein seekh sakun aur practice kar sakun.
Mujhe aise exercises do jo mai solve kar sakun aur tricky questions bhi do jo meri limits push karein.
```

## 8. Pareto Principle Prompt (80/20 Learning)

```
Tum ek expert teacher ho jo complex concepts ko simplify karta hai.
Mujhe {technology} Pareto principle ka use karte hue sikhayein.
Sirf wo 20% key ideas, concepts, aur methods par focus karein jo 80% results achieve karne mai madad karte hain.
Practical examples ke sath step-by-step guidance dein aur actionable tips bhi dein.
```

## 9. Brainstorming Prompt

```
Mujhe {domain} ke liye project ideas brainstorm karne mai madad karein.
Features aur tools suggest karein jo in ideas ko implement karne mai kaam aayein.
```

## Bonus Tips

| Rule | Description |
|---|---|
| Be Specific | "Laravel me API" mat bolo — "Laravel 12 me payment API with Stripe Checkout" bolo |
| Break Big Tasks | Ek bada kaam chhote steps me likho (architecture → model → controller → route) |
| Add Examples | "Is tarah ka output chahiye" likhne se AI same pattern follow karta hai |
| Reuse Prompts | Har baar naye se likhne ki jagah, ye templates copy karke modify karo |
| Use Context | Apna version (Laravel 12, React 18, PHP 8.2) likhna zaroori hai |

Agli file: [05_advanced_prompting.md](05_advanced_prompting.md)
