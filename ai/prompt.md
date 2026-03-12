<!-- 1. Development Prompt (Code Generation / Feature Building)

English Version:
Role: You are a senior {tech stack} developer.
Goal: I want to build {feature/module}.
Context: I am using {framework + version}.
Constraints: {security/performance/coding standard rules}.
Output: I need {complete code/controller/model/API} in markdown format.

Example:


Role: Tum ek senior {tech stack} developer ho.
Goal: Mujhe {feature/module} banana hai.
Context: Mai {framework + version} use kar raha hoon.
Constraints: {security/performance/coding standard rules} follow honi chahiye.
Output: Mujhe {complete code/controller/model/API} markdown format me chahiye.

Example:
Role: Tum ek senior Laravel developer ho.
Goal: Mujhe Stripe payment API banana hai jisme card aur Google Pay dono support ho.
Context: Laravel 12 use kar raha hoon aur frontend React me hai.
Constraints: PCI compliance aur webhook validation required hai.
Output: Backend code example markdown format me do.

2. Debugging / Error Fix Prompt

English Version:
Role: You are an expert {tech stack} debugger.
Goal: I want to understand and fix this error.
Context: I am using {framework + version}.
Error: "{paste error message}"
Output: Explain the reason step-by-step and give a fix.

Example:
Role: You are an expert Laravel debugger.
Goal: I want to fix the SQLSTATE[23000]: Integrity constraint violation error.
Context: Using Laravel 12, users and orders tables have a relation.
Error: "SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row..."
Output: Explain the reason and provide solution code.

Role: Tum ek expert {tech stack} debugger ho.
Goal: Mujhe ye error samjha kar fix karna hai.
Context: Mai {framework + version} use kar raha hoon.
Error: "{paste error message}"
Output: Step-by-step reason samjhao aur fix batao.

Example:
Role: Tum ek expert Laravel debugger ho.
Goal: Mujhe SQLSTATE[23000]: Integrity constraint violation ka error fix karna hai.
Context: Laravel 12 use kar raha hoon, users aur orders table me relation hai.
Error: "SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row..."
Output: Reason samjhao aur solution code ke sath do.

3. Architecture / System Design Prompt

English Version:
Role: You are a senior software architect.
Goal: I want to design the architecture for {system/feature}.
Context: I am using {frameworks/technologies}.
Constraints: System must be secure and scalable.
Output: Provide architecture diagram explanation, database tables, and flowchart.

Example:
Role: You are a senior software architect.
Goal: I want to design a referral system with user rewards and visit tracking.
Context: Laravel backend and React frontend.
Constraints: Security and fraud prevention must be maintained.
Output: Explain database design, service layer, and API flow.

Role: Tum ek senior software architect ho.
Goal: Mujhe {system/feature} ka architecture design chahiye.
Context: Mai {frameworks/technologies} use kar raha hoon.
Constraints: System secure aur scalable hona chahiye.
Output: Architecture diagram explanation, database tables aur flowchart explain karo.

Example:
Role: Tum ek senior software architect ho.
Goal: Mujhe referral system ka architecture banana hai jisme user reward aur visit tracking ho.
Context: Laravel backend aur React frontend use kar raha hoon.
Constraints: Security aur fraud prevention maintain rehni chahiye.
Output: Database design, service layer aur API flow explain karo.

4. Learning / Concept Explanation Prompt

English Version:
Role: You are an expert teacher who teaches {tech/topic}.
Goal: I want to understand {concept} in simple language.
Context: I am a beginner/intermediate developer.
Output: Explain with examples and code snippets.

Example:
Role: You are an expert Laravel teacher.
Goal: Explain the difference between Service Container and Interface.
Context: I am an intermediate Laravel developer.
Output: Explain with real project example showing their use.

Role: Tum ek expert teacher ho jo {tech/topic} sikhata hai.
Goal: Mujhe {concept} simple language me samjha do.
Context: Mai beginner / intermediate level developer hoon.
Output: Explanation, real example aur code snippet ke sath samjhao.

Example:
Role: Tum ek expert Laravel teacher ho.
Goal: Mujhe Service Container aur Interface ka difference samjhao.
Context: Mai intermediate level Laravel developer hoon.
Output: Real project example ke sath explain karo jisme dono ka use ho.

5. Documentation / Code Review Prompt

English Version:
Role: You are a professional {tech stack} reviewer.
Goal: I want a review of this code and improvement suggestions.
Context: This code is for {module/controller/service class}.
Code: """{paste your code here}"""
Output: Suggest improvements for performance, security, and best practices.

Example:
Role: You are a professional Laravel reviewer.
Goal: Review this controller code and suggest improvements.
Context: This code is for a PayPal payment controller.
Code: """{controller code here}"""
Output: Suggest improvements for performance and clean code best practices.

Role: Tum ek professional {tech stack} reviewer ho.
Goal: Mujhe is code ka review chahiye aur improvements suggest karni hain.
Context: Ye code ek {module/controller/service class} ka hai.
Code: """{paste your code here}"""
Output: Performance, security, aur best practices ke hisaab se suggestions do.

Example:
Role: Tum ek professional Laravel reviewer ho.
Goal: Mujhe is controller code ka review chahiye aur improvements suggest karni hain.
Context: Ye code ek PayPal payment controller ka hai.
Code: """{controller code here}"""
Output: Performance aur clean code best practices ke sath improvement points do.
Bonus Tips for Writing Perfect Prompts

| Rule               | Description                                                                       |
| ------------------ | --------------------------------------------------------------------------------- |
| 🎯 Be Specific     | “Laravel me API” mat bolo — bolo “Laravel 12 me payment API with Stripe Checkout” |
| 🧱 Break Big Tasks | Ek bada kaam chhote steps me likho (architecture → model → controller → route)    |
| 🧠 Add Examples    | “Is tarah ka output chahiye” likhne se AI same pattern follow karta hai           |
| 📦 Reuse Prompts   | Har baar naye se likhne ki jagah, ye templates copy karke modify karo             |
| 🕹️ Use Context    | Apna version (Laravel 12, React 18, PHP 8.2) likhna zaroori hai                   | -->
 -->

Ai Prompt 
<!-- 

AI for learning code 
Ai for code Completion 
Ai for Debugging & Optimization 
Ai for problem Solving 
Ai for the building project 



you are a coding tutor for waqas.  waqas is currently trying to learn python.
He has no prior programming experience. So, guide him on his journey and give him a roadmap with realistic timeframe.
Start with the basic and progress to advanced concept gradully. keep recommending external resources that waqas can google search or visit anytime.

Tum aik coding tutor ho Waqas ke liye. Waqas abhi Python seekhna shuru kar raha hai aur uska programming mein koi prior experience nahi hai. Is liye use bilkul basic se guide karo aur uski learning journey step by step plan karo. Uske liye ek realistic timeframe ke sath complete roadmap banao. Basic concepts se start karo aur dheere dheere advanced concepts tak le kar jao. Har stage par relevant external resources bhi recommend karo jinhein Waqas Google search kar sake ya kabhi bhi visit kar sake.



Tell me evereything about lists in python that i can learn and practice in 1 hour. Give me exercuess that i can sovle and challenge me tricky question and push my limits.

Mujhe Python ke lists ke bare mein sab kuch batao jo mai 1 ghante mein seekh sakun aur practice kar sakun. Mujhe aise exercises do jo mai solve kar sakun aur tricky questions bhi do jo meri limits push karein.




You are an expert teacher who simplifies complex concepts. Teach me about python using the pareto principles. focusing on the 20% of key idea. concepts. pr methods that will help me understand and achieve 80% of the results. provide practical example step-by-step guidance. and actionable tips to apply this knowledge effectively.

Ap ek expert teacher hain jo complex concepts ko simplify karta hai. Mujhe Python Pareto principle ka use karte hue sikhayein. Sirf wo 20% key ideas, concepts, aur methods par focus karein jo 80% results achieve karne mai madad karte hain. Practical examples ke sath step-by-step guidance dein aur actionable tips bhi dein jise mai effectively apply kar sakun.



"You are an expert in JavaScript. Teach me about JavaScript using the Pareto principle. Focus on the 20% of core concepts and techniques that will help me understand and achieve 80% of the results in real-world projects. Provide examples, explain key methods, and suggest practical ways to apply the knowledge."


"Ap JavaScript ke expert hain. Mujhe JavaScript Pareto principle ka use karte hue sikhayein. Sirf wo 20% core concepts aur techniques par focus karein jo real-world projects mai 80% results achieve karne mai madad karte hain. Examples dein, key methods samjhaein, aur practical tareeqay suggest karein jise mai knowledge apply kar sakun." 



Here is my Python code. It has a bug. Please analyze the code, identify the bug, and suggest a fix. If possible, explain the issue in simple terms for me to understand."

"Yeh mera Python code hai. Isme ek bug hai. Barae meherbani code analyze karein, bug identify karein, aur iska fix suggest karein. Agar mumkin ho to simple alfaaz mai issue samjha dein taake mai asani se samajh sakun."


Give me a step-by-step solution to this problem statement and then challenge me with a harder version of the problem."

"Mujhe is problem statement ka step-by-step solution dein aur phir mujhe is problem ka thoda harder version challenge ke taur par dein.


Explain this algorithm or data structure in simple terms. Then show me a real-world example where this is used."

"Is algorithm ya data structure ko simple alfaaz mai samjhaein. Phir ek real-world example dikhayein jahan iska use hota hai."



"Help me brainstorm project ideas in AI for driving cars. Suggest features and tools to implement these ideas."

"Mujhe AI ke liye driving cars projects ke ideas brainstorm karne mai madad karein. Features aur tools suggest karein jo in ideas ko implement karne mai kaam aayein."

-->
