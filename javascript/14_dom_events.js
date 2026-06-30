// ============================================================================
// 14 — DOM MANIPULATION & EVENTS (Browser JavaScript)
// ============================================================================

// DOM (Document Object Model) = HTML page ka JS REPRESENTATION — JS se
// HTML elements ko SELECT, BADAL, ADD, REMOVE kar sakte ho.


// ============================================================================
// SELECTING ELEMENTS
// ============================================================================

// const heading = document.getElementById('main-heading');         // ID se
// const buttons = document.getElementsByClassName('btn');           // Class se (HTMLCollection)
// const allDivs = document.getElementsByTagName('div');             // Tag se

// MODERN tareeqa — CSS selectors use karte hain (zyada FLEXIBLE):
// const firstButton = document.querySelector('.btn');               // PEHLA match
// const allButtons = document.querySelectorAll('.btn');             // SAARE matches (NodeList)


// ============================================================================
// CHANGING CONTENT
// ============================================================================

// element.textContent = "Naya text";        // SIRF text (HTML tags IGNORE hote hain — SAFE)
// element.innerHTML = "<b>Bold text</b>";   // HTML PARSE hota hai (⚠️ XSS risk agar USER input ho)
// element.value = "Naya value";              // Input/textarea ke liye


// ============================================================================
// CHANGING STYLES/CLASSES
// ============================================================================

// element.style.color = "red";              // Inline style — DIRECT CSS
// element.style.display = "none";           // Element ko CHHUPA dena

// classList — MODERN aur RECOMMENDED tareeqa CSS classes manage karne ka:
// element.classList.add('active');
// element.classList.remove('hidden');
// element.classList.toggle('open');          // Agar hai to hatao, nahi hai to lagao
// element.classList.contains('active');      // true/false


// ============================================================================
// CREATING & ADDING NEW ELEMENTS
// ============================================================================

// const newDiv = document.createElement('div');
// newDiv.textContent = "Main naya hoon";
// newDiv.classList.add('card');
//
// document.body.appendChild(newDiv);          // END mein add karo
// parentElement.prepend(newDiv);               // START mein add karo
// parentElement.removeChild(newDiv);           // Remove karo
// newDiv.remove();                             // Modern tareeqa remove karne ka


// ============================================================================
// EVENT LISTENERS — User INTERACTION ko "sunna"
// ============================================================================

// const button = document.querySelector('#submit-btn');
//
// button.addEventListener('click', function (event) {
//     console.log('Button click hua!');
//     console.log(event.target);   // Jis element par click hua
// });

// ARROW FUNCTION ke sath (lekin 'this' ka khayal rakho — dekho 08_this.js):
// button.addEventListener('click', (event) => {
//     console.log('Clicked:', event.target.textContent);
// });


// ============================================================================
// COMMON EVENTS
// ============================================================================

/*
 * click          → Element par click hone par
 * submit         → Form submit hone par
 * input          → Input field mein TYPE karte waqt (HAR keystroke par)
 * change         → Input field se FOCUS hatne par (final value change)
 * keydown/keyup  → Keyboard key dabane/chhorne par
 * mouseenter/mouseleave → Mouse element ke andar/bahar jane par
 * load           → Page/Image poori tarah load hone par
 * DOMContentLoaded → HTML poora parse hone par (images ka wait NAHI karta)
 */


// ============================================================================
// REAL-WORLD EXAMPLE — FORM SUBMIT (Laravel backend ke sath, AJAX)
// ============================================================================

// const form = document.querySelector('#contact-form');
//
// form.addEventListener('submit', async (event) => {
//     event.preventDefault();   // ⚠️ ZAROORI — warna page REFRESH ho jayega (default behavior)
//
//     const formData = new FormData(form);
//     const data = Object.fromEntries(formData);   // FormData ko plain object mein convert
//
//     try {
//         const response = await fetch('/api/contact', {
//             method: 'POST',
//             headers: {
//                 'Content-Type': 'application/json',
//                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
//             },
//             body: JSON.stringify(data),
//         });
//
//         if (response.ok) {
//             alert('Message bhej diya gaya!');
//             form.reset();   // Form ko khaali kar do
//         }
//     } catch (error) {
//         alert('Kuch ghalat hua: ' + error.message);
//     }
// });


// ============================================================================
// EVENT DELEGATION — Performance ka IMPORTANT pattern
// ============================================================================

// Problem: 100 buttons par ALAG ALAG event listener lagana SLOW + memory-heavy hai
// Solution: PARENT par EK listener lagao, event "BUBBLE" ho kar uske pass aata hai

// ❌ INEFFICIENT — har item par alag listener:
// document.querySelectorAll('.list-item').forEach((item) => {
//     item.addEventListener('click', handleClick);
// });

// ✅ EFFICIENT — EVENT DELEGATION (parent par EK listener):
// document.querySelector('.list-container').addEventListener('click', (event) => {
//     if (event.target.classList.contains('list-item')) {
//         console.log('Item clicked:', event.target.textContent);
//     }
// });

// FAYDA: NAYE items DYNAMICALLY add hon (jaise AJAX se), tab bhi
// kaam karega — bina NAYA listener lagaye, kyunke listener PARENT par hai.


// ============================================================================
// EVENT BUBBLING vs CAPTURING
// ============================================================================

// BUBBLING (default): Event CHILD se PARENT ki taraf "uppar" jata hai
// CAPTURING: Event PARENT se CHILD ki taraf "neeche" jata hai (rarely use hota hai)

// parent.addEventListener('click', handler, true);   // true = capturing phase
// parent.addEventListener('click', handler, false);  // false (default) = bubbling phase

// event.stopPropagation();   // Event ko AAGE bubble/capture hone se rokna


// ============================================================================
// DEBOUNCE — Search input ke liye COMMON real-world pattern
// ============================================================================

function debounce(func, delay) {
    let timeoutId;
    return function (...args) {
        clearTimeout(timeoutId);   // Pichla TIMER cancel karo
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}

// const searchInput = document.querySelector('#search');
// const handleSearch = debounce((event) => {
//     console.log('Searching for:', event.target.value);
//     // fetch(`/api/search?q=${event.target.value}`)...
// }, 500);
//
// searchInput.addEventListener('input', handleSearch);

// FAYDA: User TYPE kar raha hai "laptop" — bina debounce ke 6 API calls
// jayenge (l,a,p,t,o,p har letter par). Debounce se SIRF 1 call jayega
// (jab user 500ms ke liye RUKE)


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. innerHTML mein USER INPUT seedha mat daalo — XSS attack ka risk hai.
//    textContent use karo agar plain text hai, ya properly SANITIZE karo.

// 2. Event Delegation BARI lists ke liye PERFORMANCE best-practice hai —
//    senior interviews mein commonly puchaa jata hai.

// 3. Modern frameworks (React, Vue, Laravel Livewire) ye saara DOM
//    manipulation KHUD handle karte hain — lekin UNDER THE HOOD yehi
//    concepts chal rahe hote hain, isliye samajhna zaroori hai.

// 4. debounce() aur throttle() (dekho 15_advanced_concepts) production
//    apps mein search, scroll, resize events ke liye STANDARD patterns hain.
