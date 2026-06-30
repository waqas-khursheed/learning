// ============================================================================
// 11 — FETCH API & HTTP REQUESTS (Backend se baat karna)
// ============================================================================


// ============================================================================
// FETCH API BASICS — GET REQUEST
// ============================================================================

// fetch() browser ka BUILT-IN function hai — koi library install karne
// ki zaroorat nahi (Axios bhi popular hai, lekin fetch() NATIVE hai)

fetch("https://api.example.com/users")
    .then((response) => response.json())   // Response ko JSON mein convert karo
    .then((data) => console.log(data))
    .catch((error) => console.log("Error:", error));


// ============================================================================
// ASYNC/AWAIT KE SATH (Modern, recommended tareeqa)
// ============================================================================

async function getUsers() {
    try {
        const response = await fetch("https://api.example.com/users");

        // ⚠️ IMPORTANT: fetch() sirf NETWORK error par reject hota hai —
        // 404, 500 jaisi HTTP errors par reject NAHI hota! Manually check karo:
        if (!response.ok) {
            throw new Error(`HTTP Error: ${response.status}`);
        }

        const data = await response.json();
        console.log(data);
        return data;
    } catch (error) {
        console.log("Failed to fetch users:", error.message);
    }
}


// ============================================================================
// POST REQUEST — Data BHEJNA server ko
// ============================================================================

async function createUser(userData) {
    try {
        const response = await fetch("https://api.example.com/users", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Bearer YOUR_TOKEN_HERE",
            },
            body: JSON.stringify(userData),   // Object ko JSON STRING mein convert karna ZAROORI hai
        });

        if (!response.ok) {
            throw new Error(`HTTP Error: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.log("Failed to create user:", error.message);
    }
}

createUser({ name: "Ali", email: "ali@example.com" });


// ============================================================================
// PUT / PATCH / DELETE — REST API ke baaki methods
// ============================================================================

async function updateUser(id, updates) {
    const response = await fetch(`https://api.example.com/users/${id}`, {
        method: "PATCH",   // PUT = poora object replace, PATCH = sirf kuch fields update
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(updates),
    });
    return await response.json();
}

async function deleteUser(id) {
    const response = await fetch(`https://api.example.com/users/${id}`, {
        method: "DELETE",
    });
    return response.ok;   // true agar successfully delete hua
}


// ============================================================================
// REAL-WORLD EXAMPLE — Laravel API ke sath JS Frontend (bohot common setup)
// ============================================================================

// Laravel backend route: Route::post('/api/orders', [OrderController::class, 'store']);

async function placeOrder(cartItems) {
    try {
        const response = await fetch("/api/orders", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content,
                // Laravel CSRF protection ke liye token bhejna ZAROORI hai
            },
            body: JSON.stringify({ items: cartItems }),
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || "Order place nahi hua");
        }

        const order = await response.json();
        console.log("Order placed:", order);
        return order;
    } catch (error) {
        console.log("Checkout failed:", error.message);
    }
}


// ============================================================================
// LOADING STATES — Real UI ke liye ZAROORI pattern
// ============================================================================

async function loadDataWithUI() {
    // showLoadingSpinner();   // Pehle loading dikhao

    try {
        const response = await fetch("/api/products");
        const products = await response.json();
        // renderProducts(products);   // Data aane par UI update karo
    } catch (error) {
        // showErrorMessage("Data load nahi ho saka");
    } finally {
        // hideLoadingSpinner();   // HAR HALAT mein loading band karo (success ya fail)
    }
}


// ============================================================================
// QUERY PARAMETERS — URL mein filters/search bhejna
// ============================================================================

async function searchProducts(searchTerm, page = 1) {
    const params = new URLSearchParams({
        search: searchTerm,
        page: page,
        per_page: 20,
    });

    const response = await fetch(`/api/products?${params}`);
    // URL banega: /api/products?search=laptop&page=1&per_page=20
    return await response.json();
}


// ============================================================================
// AbortController — Request ko CANCEL karna (search-as-you-type ke liye useful)
// ============================================================================

let currentController = null;

async function searchWithCancel(query) {
    // Agar pehle se ek request chal rahi hai, usay CANCEL karo
    if (currentController) {
        currentController.abort();
    }

    currentController = new AbortController();

    try {
        const response = await fetch(`/api/search?q=${query}`, {
            signal: currentController.signal,
        });
        return await response.json();
    } catch (error) {
        if (error.name === "AbortError") {
            console.log("Pichli search cancel ho gayi");
        }
    }
}

// REAL USE CASE: User type kar raha hai search box mein — har keystroke
// par naya request jata hai, PURANA request CANCEL ho jana chahiye
// (warna PURANI search ka result BAAD mein aa kar NAYI search ko OVERWRITE kar sakta hai)


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. fetch() HTTP error codes (404, 500) par reject NAHI hota — hamesha
//    response.ok check karo, warna FAILED requests "successful" maan loge.

// 2. Laravel API ke sath kaam karte waqt CSRF token aur Authorization
//    header bhejna mat bhoolo — warna 419/401 errors aayenge.

// 3. Production apps mein Axios bhi popular hai kyunke: automatic JSON
//    parsing, request/response interceptors, automatic error handling
//    — lekin fetch() seekhna ZAROORI hai (fundamentals samajhne ke liye).

// 4. Loading states aur error handling HAMESHA UI mein dikhao — user ko
//    pata hona chahiye "kaam ho raha hai" ya "kuch ghalat hua".
