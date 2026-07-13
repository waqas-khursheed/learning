# Common Mistakes Aur Unka Fix

Ye woh galtiyan hain jo naye log AI use karte waqt aksar karte hain.

## 1. Bohot Vague Sawal Poochna

**Galat:** "Ye kaam nahi kar raha, fix karo."
**Sahi:** "Ye {function/feature} kaam nahi kar raha. Expected behavior ye hai: {X}. Actual behavior ye hai: {Y}. Error/code ye hai: {paste}."

AI mind-reader nahi hai — jitna context doge, utna sahi jawab milega.

## 2. Context Na Dena

**Galat:** "Array sort karo."
**Sahi:** "PHP mein multidimensional array ko `price` key ke basis pe descending order mein sort karna hai."

Bina context, AI generic/wrong-language solution de sakta hai.

## 3. Pehle Jawab Ko Final Maan Lena

Bohot log pehla jawab copy-paste kar lete hain bina verify kiye. AI se milne wala code:
- Purana approach use kar sakta hai
- Aapke specific edge cases handle nahi karta
- Kabhi kabhi non-existent function/method bata sakta hai ("hallucination")

**Fix:** Hamesha code ko run/test karo, aur agar shak ho to AI se hi pucho: "Kya ye method Laravel 12 mein exist karta hai?"

## 4. Ek Hi Prompt Mein Bohot Zyada Maangna

**Galat:** "Mujhe pura SaaS app bana do — auth, payment, admin panel, sab kuch."

Itna bada scope AI ko confuse kar deta hai aur output shallow/incomplete hota hai.

**Fix:** [05_advanced_prompting.md](05_advanced_prompting.md) mein diya gaya Prompt Chaining technique use karo — chote steps mein todo.

## 5. Follow-up Prompts Mein Context Repeat Na Karna

Lambi conversation mein agar aap suddenly naya direction lo bina reference diye, AI confuse ho sakta hai konsi cheez pe baat ho rahi hai.

**Fix:** "Upar wale UserController mein..." jaisa reference do, ya zaroori context dobara mention karo.

## 6. Sirf Code Mangna, Explanation Skip Karna (Jab Seekh Rahe Ho)

Agar aap seekh rahe ho, sirf code copy karna seekhne mein madad nahi karega.

**Fix:** Prompt mein add karo: "Code ke sath ye bhi samjhao ke ye kaam kaise karta hai" — isse aap actually seekhte ho, sirf copy-paste nahi karte.

## 7. Output Format Na Batana

Agar aap na batao result kis form mein chahiye, AI apni marzi se format choose karega — jo hamesha aapke use case ke liye convenient nahi hota.

**Fix:** Hamesha bolo: "Table mein do", "Sirf code do, explanation nahi", "Step by step points mein do", etc.

## 8. Galat Prompt Ko Improve Karne Ki Jagah Poori Baat Chhod Dena

Agar jawab accha nahi aaya, log samajhte hain "AI ko nahi pata" — jabke asal masla prompt ki clarity hoti hai.

**Fix:** Follow-up karo: "Ye jawab bohot generic hai, mujhe {specific cheez} chahiye" ya "Isay {constraint} ke sath dobara karo."

## Quick Self-Check Before Sending Any Prompt

Prompt bhejne se pehle khud se pucho:
- [ ] Kya maine apna tech stack/version bataya?
- [ ] Kya maine clearly bataya mujhe kya chahiye (goal)?
- [ ] Kya maine output format bataya?
- [ ] Kya koi constraint/rule hai jo mention karna zaroori tha?

Agli file: [07_daily_practice_and_self_improvement.md](07_daily_practice_and_self_improvement.md)
