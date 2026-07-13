# Prompt Engineering Kya Hai?

## Simple Definition

Prompt engineering matlab hai — AI ko sawal ya instruction is tarah likhna ke wo aapko **best possible, accurate, aur useful** jawab de. Ye "AI se baat karne ka tareeqa" hai.

Socho AI ek bohot smart junior developer hai jo bohot fast kaam karta hai, lekin **mind-reader nahi hai**. Agar aap usay adhoori (incomplete) information doge, wo apni taraf se guess karega — aur guess kabhi bhi galat ho sakta hai.

## AI Kaise "Sochta" Hai (Simplified)

- AI (LLM — Large Language Model) sirf wahi information use karta hai jo:
  1. Aapne prompt mein di
  2. Uski training mein pehle se maujood hai (jo purani/general ho sakti hai)
  3. Conversation mein pehle discuss ho chuki hai
- AI **aapki screen nahi dekh sakta**, aapka project structure nahi jaanta, aapka business logic nahi samajhta — jab tak aap na batao.
- AI **word by word** (technically token by token) response generate karta hai — is liye shuru mein prompt jitna clear hoga, response utna hi sahi direction mein jayega.

## Kyun Zaroori Hai?

| Bina Prompt Engineering | Achy Prompt Engineering Ke Sath |
|---|---|
| Generic, halka jawab milta hai | Specific, deep, apki need ke mutabiq jawab milta hai |
| Baar baar dobara pucho pare | Pehli dafa mein sahi jawab qareeb milta hai |
| AI apni marzi ki assumptions leta hai | AI aapke context ke hisab se sochta hai |
| Copy-paste code kaam nahi karta | Code aapke exact stack/version ke mutabiq hota hai |

## Ek Chota Example

**Weak Prompt:**
> "Laravel mein API kaise banau?"

Ye prompt bohot generic hai. AI ko nahi pata:
- Konsa Laravel version?
- REST API ya GraphQL?
- Authentication chahiye ya nahi?
- Kis cheez ki API — users? payments? products?

**Strong Prompt:**
> "Main Laravel 12 use kar raha hoon. Mujhe ek REST API banani hai jo products list return kare, Sanctum authentication ke sath. Response JSON format mein ho aur pagination bhi included ho. Controller aur route code do."

Ye prompt specific hai — AI ko exact pata hai kya chahiye, is liye jawab bhi exact milega.

## Practice (Khud Karo)

1. Apna koi purana chat history uthao jahan aapne AI se koi generic sawal poocha tha.
2. Us sawal ko upar diye gaye "Strong Prompt" pattern se dobara likho.
3. Dono jawab compare karo — farak khud dekhoge.

Agli file: [02_prompt_ka_structure.md](02_prompt_ka_structure.md)
