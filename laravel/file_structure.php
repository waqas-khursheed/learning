<!-- Laravel File Storage Structure Overview

Laravel ke paas ek unified file system hai jo config/filesystems.php mein define hota hai.
Default storage path hai storage/app, jahan aap apni files ko logically organize karte ho (public ya private use case ke hisab se). -->

<!-- | Feature                         | Description                                                                 |
| ------------------------------- | --------------------------------------------------------------------------- |
|  **Path**                     | `storage/app/public/`                                                       |
|  **Access**                   | Publicly accessible **after running** `php artisan storage:link`            |
|  **URL**                      | Files access hoti hain via `/storage/...` URL                               |
|  **Use Case**                 | Images, profile pictures, documents, etc. jo users ko directly dikhani hain |
| **Code Example (Save File)** |                                                                             | -->


Storage::disk('public')->put('avatars/user1.png', $imageContent);
``` |
| 🌐 **Access Example (Blade or API)** |  
```php
asset('storage/avatars/user1.png');
``` |

---

<!-- ## 🔒 **2. storage/app/private**

| Feature | Description |
|----------|--------------|
| 📍 **Path** | `storage/app/private/` |
| 🚫 **Access** | Browser se **direct access nahi hoti** |
| 🧱 **Use Case** | Sensitive files (documents, invoices, confidential reports, etc.) |
| ⚙️ **Code Example (Save File)** |  
```php
Storage::disk('local')->put('private/user1/document.pdf', $file);
``` |
| 🧾 **Access Example (Controller route)** |  
```php
public function showPrivateFile($filename)
{
    $path = storage_path('app/private/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
}
``` |
| ✅ **Benefit** | Full control — sirf authorized users hi file dekh sakte hain |

---

## 🌐 **3. public/** (root folder)

| Feature | Description |
|----------|--------------|
| 📍 **Path** | `public/` (Laravel project root ke andar) |
| 🌍 **Access** | Directly browser accessible without storage link |
| ⚠️ **Risk** | Publicly open folder — anyone can access URLs |
| 🧱 **Use Case** | Static assets (CSS, JS, images) jo app ke frontend ke liye use hote hain |
| ⚙️ **Example** |   -->


<!-- public/css/app.css
public/js/app.js
public/images/logo.png


---

## 🔍 **Quick Comparison Table**

| Feature | storage/app/public | storage/app/private | public/ |
|----------|--------------------|---------------------|----------|
| Direct URL Access | ✅ Yes (via `/storage/`) | ❌ No | ✅ Yes |
| Security Level | Medium | High | Low |
| Needs `storage:link` | ✅ Yes | ❌ No | ❌ No |
| Use Case | Publicly visible uploads | Confidential data | Static frontend files |
| Example URL | `/storage/avatars/user1.png` | Route-based access only | `/images/logo.png` |

---

## ⚙️ **Best Practice Recommendations**

✅ User-uploaded **non-sensitive files** → `storage/app/public`  
✅ **Sensitive / private files** → `storage/app/private`  
✅ **Static assets (JS, CSS, icons)** → `public/`

---
