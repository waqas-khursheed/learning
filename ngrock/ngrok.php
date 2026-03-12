<?php

// Ngrok 
//  1. Ngrok kya hai?

// Ngrok ek secure tunneling service hai jo tumhare local server (localhost) ko internet se publicly access karne ki facility deta hai.
// Normally, agar tumhara Laravel ya Node.js project http://127.0.0.1:8000 pe chal raha ho — to wo sirf local system pe accessible hota hai.
// Ngrok use karne ke baad tumhara local project ek temporary public HTTPS URL par available ho jata hai, jaisa:

https://f1a2b3c4d5.ngrok.io → http://127.0.0.1:8000


// 2. Ngrok ka kaam kya hai?

// | Use Case               | Description                                                                           |
// | ---------------------- | ------------------------------------------------------------------------------------- |
// | 🌍 **Public Access**   | Localhost project ko internet se accessible banata hai (temporary domain ke sath)     |
// | 🧾 **Webhook Testing** | Stripe, PayPal, Razorpay, etc. ko local Laravel/Node project se connect karne ke liye |
// | 💻 **Mobile Testing**  | Mobile, tablet, ya remote client se local project test kar sakte ho                   |
// | 🧑‍💻 **Client Demo**  | Local project ka temporary link client ko share kar sakte ho                          |
// | ⚙️ **API Debugging**   | APIs ke real-time request logs dekhne ke liye (ngrok dashboard se)                    |



// 3. Ngrok kaise kaam karta hai?
// Internally, Ngrok tumhare system me ek secure tunnel banata hai.
// Step-by-Step flow:
// Tumhara Laravel/Node server local pe run ho raha hai (localhost:8000)
// Ngrok us port ke liye ek tunnel kholta hai
// Ngrok apne cloud servers par ek HTTPS domain generate karta hai
// Sab requests (https://xyz.ngrok.io) tunnel ke zariye tumhare local server tak pohchti hain

//  Iska matlab:
// Internet → Ngrok Cloud → Tunnel → Tumhara Localhost

//  4. Ngrok Setup (Free Version)
// Step 1: Install ngrok
// Linux/macOS:
// sudo npm install -g ngrok

// or
// official binary se install karo:
// https://ngrok.com/download

// Step 2: Account banao (Free)

// Sign up at:
//  https://dashboard.ngrok.com/signup


// Step 3: Auth Token lo
// Dashboard → Your Authtoken
// Copy command jaise:
// ngrok config add-authtoken 2h3aKExampleTokenxYZabc123

// Run it in terminal.

// Step 4: Laravel project run karo
// php artisan serve --port=8000

// Step 5: Ngrok tunnel open karo
// ngrok http 8000

// Output:
// Forwarding  https://abc123.ngrok.io -> http://127.0.0.1:8000


// Ab ye public URL use kar sakte ho (mobile, webhook, etc.)

// 5. Example Use Case
// 🔸 Stripe Webhook Local Testing
// Laravel me Stripe webhook route hota hai:

// POST /api/stripe/webhook

// Normally Stripe ko internet URL chahiye hota hai.
// Tum webhook URL me ye doge:
// https://abc123.ngrok.io/api/stripe/webhook

// Ab jab Stripe event bhejega → ngrok → tumhara local Laravel API

// Useful Commands
// | Command              | Description                                |
// | -------------------- | ------------------------------------------ |
// | `ngrok http 8000`    | Port 8000 ke liye public tunnel banata hai |
// | `ngrok version`      | Installed version check karta hai          |
// | `ngrok config check` | Config verify karta hai                    |
// | `ngrok status`       | Tunnel status check karta hai              |
// | `ngrok kill`         | Running tunnels stop karta hai             |


// 7. Ngrok Dashboard

// Ngrok ek web dashboard deta hai:
// http://127.0.0.1:4040

// Isme tum dekh sakte ho:
// Recent requests
// Headers & body data
// Response codes
// Replay request feature (bina webhook repeat kiye test karne ke liye)

// 8. Free vs Paid

// | Feature          | Free    | Paid      |
// | ---------------- | ------- | --------- |
// | Session Time     | 2 hours | Unlimited |
// | Custom Domains   | ❌       | ✅         |
// | Multiple Tunnels | ❌       | ✅         |
// | Reserved URLs    | ❌       | ✅         |

// Summary
// | Step | Action                           |
// | ---- | -------------------------------- |
// | 1️⃣  | Install ngrok                    |
// | 2️⃣  | Create free account              |
// | 3️⃣  | Add auth token                   |
// | 4️⃣  | Run local Laravel/Node server    |
// | 5️⃣  | Run `ngrok http PORT`            |
// | 6️⃣  | Use generated HTTPS URL anywhere |
