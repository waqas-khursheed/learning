<!-- Laravel .env vs config/ — Complete Guide
1. Introduction
Laravel environment configuration do jagah handle hoti hai:
.env file: environment-specific values (like keys, database, URLs).
config/ files: application configuration logic (structured & cached).
Dono milkar Laravel ke configuration management system ko secure, flexible aur fast banate hain. -->

<!-- 2. .env File (Environment Variables)
📍 Location:

/.env
📘 Purpose:
Application ke liye environment-specific settings rakhne ke liye — jese ke:
Local, staging, production ke alag settings
Sensitive information (API keys, passwords)
Environment dependent configuration -->

<!-- Example
APP_NAME="MyApp"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=myapp_db
DB_USERNAME=root
DB_PASSWORD=secret

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=myemail@gmail.com
MAIL_PASSWORD=mailpassword -->

<!-- ✅ Advantages of .env -->
<!-- 
| Advantage                      | Description                                                                      |
| ------------------------------ | -------------------------------------------------------------------------------- |
| 🔐 **Security**                | API keys & credentials secure rehte hain (not pushed to Git)                     |
| 🌍 **Environment Flexibility** | Local, staging, production ke liye alag values possible                          |
| ⚡ **Instant Change**           | `.env` file change karne se system behavior change hota hai without editing code |
| 🧰 **Reusable**                | Same codebase different configs handle kar sakta hai                             | -->


<!-- Disadvantages of .env
| Disadvantage             | Description                                                               |
| ------------------------ | ------------------------------------------------------------------------- |
| ❌ **Slow in production** | Direct `.env` access thoda slow hota hai (every request me read hoti hai) |
| ⚠️ **Not cached**        | `php artisan config:cache` ke baad `.env` direct use nahi hoti            |
| 🚫 **Not for logic**     | Sirf values store karne ke liye hoti hai, logic yahan nahi likhna chahiye | -->


<!-- 3. config/ Files (Configuration Arrays)
📍 Location:

/config/

Laravel ke saare configuration modules yahan defined hote hain — jaise:

config/app.php

config/database.php

config/mail.php

config/filesystems.php -->


<!-- Example:
config/app.php -->
<!-- 
return [
    'name' => env('APP_NAME', 'Laravel'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),

    'timezone' => 'UTC',
    'locale' => 'en',
]; -->

<!-- Advantages of config/ Files -->

<!-- | Advantage                      | Description                                                       |
| ------------------------------ | ----------------------------------------------------------------- |
| ⚡ **Fast Performance**         | Config files cache ho sakte hain using `php artisan config:cache` |
| 🧠 **Centralized Logic**       | Sab app configuration ek jagah structured form me hoti hai        |
| 🧩 **Environment Friendly**    | Config me env variables use kiye ja sakte hain                    |
| 🧱 **Immutable in Production** | Cache hone ke baad stable & secure rehti hain                     | -->


<!-- ⚠️ Disadvantages of config/ Files -->

<!-- | Disadvantage                 | Description                                                            |
| ---------------------------- | ---------------------------------------------------------------------- |
| ❌ **Require Cache Refresh**  | `.env` change hone ke baad cache refresh karna padta hai               |
| 🧩 **Not for Secrets Alone** | Sensitive values directly config me store nahi karni chahiye (use env) |
| ⚠️ **Manual Deploy Update**  | Deployment ke waqt cache regenerate karna zaroori hai                  | -->


<!-- 4. Command: php artisan config:cache
📘 Purpose:

.env aur config dono ke combined values ko cache karta hai for performance.

🧱 How it Works:

Laravel sab config files load karta hai.

.env ke values merge karta hai.

Result ko bootstrap/cache/config.php me store karta hai.

💡 Result:

Next request me Laravel .env read nahi karta.

Cached array directly use hota hai → Fast response!

⚠️ Important Note:

Agar aap .env file me koi value change karte ho, to cache refresh karna zaroori hai: -->
php artisan config:clear
php artisan config:cache

<!-- 5. Practical Comparison Table
| Feature                | `.env`                     | `config/`                                     |
| ---------------------- | -------------------------- | --------------------------------------------- |
| 📁 **Location**        | Project root               | `/config/` directory                          |
| 🔐 **Security**        | High (never commit to Git) | Medium                                        |
| ⚡ **Performance**      | Slower (no cache)          | Faster (cached)                               |
| 🔄 **Change Handling** | Immediate                  | Needs `config:cache`                          |
| 🧩 **Structure**       | Key-value                  | Array / logical grouping                      |
| 🧱 **Use Case**        | Environment & secrets      | Application configuration                     |
| 🧰 **Example**         | `DB_PASSWORD=secret`       | `'database' => env('DB_DATABASE', 'default')` | -->


<!-- 6. Best Practices

✅ Always keep secrets in .env
(API keys, passwords, DB credentials)

✅ Reference env in config/ files only
Never use env() directly in app logic (like Controllers).

✅ Use config() helper
Always use config('app.name') instead of env('APP_NAME') inside your app.

✅ Cache configs in production -->

<!-- php artisan config:cache
Ignore .env in Git
Check .gitignore → .env file should never be pushed to repo. -->

<!-- 
7. Example Workflow
| Step      | Local                   | Staging        | Production       |
| --------- | ----------------------- | -------------- | ---------------- |
| `.env`    | Debug enabled, local DB | Testing keys   | Live credentials |
| `config/` | Same structure          | Same structure | Same structure   |
| Cache     | Optional                | Required       | Required         | -->

<!-- 
🔐 8. Summary

| Item          | `.env`                         | `config/`               |
| ------------- | ------------------------------ | ----------------------- |
| Purpose       | Environment settings           | App configuration       |
| Used For      | Credentials, environment setup | Grouped app logic       |
| Access        | `env('KEY')`                   | `config('file.key')`    |
| Cache         | ❌ No                           | ✅ Yes                   |
| Speed         | Slower                         | Faster                  |
| Best Practice | Keep secrets here              | Use env references here | -->

<!-- Conclusion

✅ Always store secrets in .env
✅ Reference them in config/ files using env()
✅ Access via config() helper in your code
✅ Cache config in production

👉 This approach = secure + fast + scalable -->