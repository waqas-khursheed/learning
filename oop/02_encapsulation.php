<?php

// ============================================================================
// 02 — ENCAPSULATION (1st Pillar of OOP)
// ============================================================================

// Encapsulation ka matlab hai: Data (properties) aur us data par kaam karne
// wale methods ko EK class ke andar "band" (wrap) kar dena, aur bahar se
// direct access ROKNA — sirf controlled tareeqe (methods) se access dena.

// Real Life Example:
// ATM Machine — tum apna balance dekh sakte ho, paisa nikal sakte ho,
// lekin tum machine ke andar jaake directly cash drawer ka counter
// badal nahi sakte. Sab kuch CONTROLLED interface (buttons/screen) se hota hai.


// ============================================================================
// ACCESS MODIFIERS
// ============================================================================

// | Modifier  | Same Class | Child Class | Outside Class |
// |-----------|------------|-------------|----------------|
// | public    | ✅         | ✅          | ✅             |
// | protected | ✅         | ✅          | ❌             |
// | private   | ✅         | ❌          | ❌             |


// ============================================================================
// REAL EXAMPLE — BANK ACCOUNT (Encapsulation WITHOUT it = disaster)
// ============================================================================

// ❌ BAD APPROACH — agar balance public ho:
//
// class BadBankAccount {
//     public float $balance = 1000;
// }
// $acc = new BadBankAccount();
// $acc->balance = -999999;   // Koi bhi seedha balance change kar sakta hai! DANGEROUS!


// ✅ GOOD APPROACH — Encapsulation ke sath:

class BankAccount
{
    private float $balance = 0;          // Bahar se direct access NAHI
    private array $transactionLog = [];  // Ye bhi hidden hai

    public function __construct(
        private readonly string $accountNumber,   // readonly = constructor ke baad change nahi ho sakta
        float $openingBalance = 0
    ) {
        $this->balance = $openingBalance;
    }

    // Controlled WRITE access (Setter jaisa kaam, lekin validation ke sath)
    public function deposit(float $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Deposit amount positive hona chahiye');
        }

        $this->balance += $amount;
        $this->transactionLog[] = "Deposit: +{$amount}";
    }

    public function withdraw(float $amount): void
    {
        if ($amount > $this->balance) {
            throw new RuntimeException('Insufficient balance');
        }

        $this->balance -= $amount;
        $this->transactionLog[] = "Withdraw: -{$amount}";
    }

    // Controlled READ access (Getter)
    public function getBalance(): float
    {
        return $this->balance;
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    // Read-only view of internal log — copy return hoti hai, original array nahi
    public function getTransactionHistory(): array
    {
        return $this->transactionLog;
    }
}


// ============================================================================
// USAGE
// ============================================================================

$account = new BankAccount('PK-001-2024', 5000);
$account->deposit(2000);
$account->withdraw(1000);

echo $account->getBalance();   // 6000
// $account->balance = 999999;  // ❌ ERROR — private property, direct access nahi ho sakta
// $account->accountNumber = 'X'; // ❌ ERROR — readonly property, change nahi ho sakta


// ============================================================================
// GETTERS / SETTERS — KAB ZAROORAT HOTI HAI?
// ============================================================================

// Har property ke liye getter/setter banana ZAROORI NAHI hota (ye anti-pattern
// bhi ban sakta hai agar bewajah har property ke liye banao).

// Getter/Setter TAB banao jab:
// 1. VALIDATION chahiye ho (jaise deposit() mein amount > 0 check)
// 2. SIDE-EFFECT chahiye ho (jaise transactionLog mein entry add karna)
// 3. COMPUTED value return karni ho (jaise getFullName() = first + last name)
// 4. Property ko READ-ONLY (sirf bahar se padhne layak) banana ho


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. readonly properties (PHP 8.1+) — Encapsulation ka modern tareeqa.
//    Constructor mein set hone ke baad kabhi change nahi ho sakti.
//    Immutable objects banane ke liye best (DTOs, Value Objects).

// 2. "Tell, Don't Ask" Principle:
//    Bura: if ($account->getBalance() > 100) { $account->setBalance($account->getBalance() - 100); }
//    Acha: $account->withdraw(100);
//    Object ko khud apna data manage karne do, bahar se micromanage mat karo.

// 3. Arrays/Objects properties return karte waqt copy return karo (jaisa
//    getTransactionHistory() karta hai) taake caller original internal state
//    ko corrupt na kar sake.

// 4. Encapsulation sirf "private rakhna" nahi hai — asal maqsad hai
//    INVARIANTS (rules) ko protect karna. Jaise: balance kabhi negative
//    nahi ho sakta — ye rule sirf class ke andar enforce ho sakta hai
//    agar balance private ho aur sirf controlled methods se change ho.
