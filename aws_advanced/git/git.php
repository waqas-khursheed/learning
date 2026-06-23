<?php

/**
 * ============================================================================
 *                    GIT — DEPLOYMENT KE LIYE GUIDE
 *            Branching Strategy, Workflow, aur Best Practices
 * ============================================================================
 */


// =============================================================================
// 1. GIT WORKFLOW FOR DEPLOYMENT
// =============================================================================

/*
 * PRODUCTION KE LIYE BRANCHING STRATEGY:
 *
 *   main (production)     ← Live code — sirf tested code yahan aaye
 *     │
 *     ├── staging         ← Testing — deploy se pehle yahan test karo
 *     │     │
 *     │     ├── develop   ← Development — daily code yahan merge karo
 *     │           │
 *     │           ├── feature/user-login    ← Naya feature
 *     │           ├── feature/payment       ← Naya feature
 *     │           ├── bugfix/cart-error     ← Bug fix
 *     │           └── hotfix/security-patch ← Fori fix
 *
 *
 * FLOW:
 *   1. feature/xyz branch banao → code likho → PR banao
 *   2. PR review ho → develop mein merge karo
 *   3. develop se staging mein merge karo → staging par test karo
 *   4. Sab theek hai → staging se main mein merge karo
 *   5. main par CI/CD trigger hota hai → Production par deploy!
 *
 *
 * COMMANDS:
 *
 *   # Naya feature shuru karo
 *   git checkout develop
 *   git pull origin develop
 *   git checkout -b feature/user-profile
 *
 *   # Code likho aur commit karo
 *   git add .
 *   git commit -m "feat: user profile page add ki"
 *
 *   # Push karo aur PR banayen
 *   git push origin feature/user-profile
 *   # GitHub par PR banayein: feature/user-profile → develop
 *
 *   # Staging par deploy ke liye
 *   git checkout staging
 *   git merge develop
 *   git push origin staging
 *
 *   # Production par deploy ke liye
 *   git checkout main
 *   git merge staging
 *   git push origin main
 *   # ↑ CI/CD pipeline trigger hoga aur deploy hoga!
 */


// =============================================================================
// 2. COMMIT MESSAGE CONVENTION
// =============================================================================

/*
 * FORMAT: type: description
 *
 *   feat:     Naya feature         → feat: user registration form add ki
 *   fix:      Bug fix              → fix: cart total ghalat calculate ho raha tha
 *   refactor: Code behtar kiya     → refactor: payment service clean up
 *   docs:     Documentation        → docs: API endpoints document kiye
 *   test:     Test add/fix kiya    → test: order creation tests add kiye
 *   chore:    Chhota kaam          → chore: dependencies update kiye
 *   ci:       CI/CD changes        → ci: GitHub Actions workflow add kiya
 *   perf:     Performance          → perf: products query optimize ki
 */


// =============================================================================
// 3. BRANCH PROTECTION RULES
// =============================================================================

/*
 * GitHub → Repo → Settings → Branches → Branch protection rules
 *
 * main branch ke liye:
 *   ✅ Require a pull request before merging
 *   ✅ Require approvals: 1 (kam az kam 1 review)
 *   ✅ Require status checks to pass (tests pass hone zaruri)
 *   ✅ Require branches to be up to date before merging
 *   ✅ Include administrators (admin bhi seedha push nahi kar sakta)
 *
 * develop branch ke liye:
 *   ✅ Require a pull request before merging
 *   ✅ Require status checks to pass
 */


// =============================================================================
// 4. .GITIGNORE FOR LARAVEL
// =============================================================================

/*
 * ─── .gitignore ───
 *
 *   /node_modules
 *   /public/build
 *   /public/hot
 *   /public/storage
 *   /storage/*.key
 *   /vendor
 *   .env
 *   .env.backup
 *   .env.production
 *   .phpunit.result.cache
 *   Homestead.json
 *   Homestead.yaml
 *   auth.json
 *   npm-debug.log
 *   yarn-error.log
 *   /.fleet
 *   /.idea
 *   /.vscode
 *
 * ⚠️ .env KABHI git mein mat daalein!
 *    Secrets GitHub Secrets ya AWS Secrets Manager mein rakhho.
 */


// =============================================================================
// 5. GIT TAGS FOR RELEASES
// =============================================================================

/*
 * Production deploy ke liye tags use karo:
 *
 *   # Tag banayen
 *   git tag -a v1.0.0 -m "Pehla production release"
 *   git push origin v1.0.0
 *
 *   # Sab tags dekho
 *   git tag -l
 *
 *   # Kisi tag par jao
 *   git checkout v1.0.0
 *
 *   # Rollback — purane version par jao
 *   git checkout v0.9.0
 *
 *
 * VERSION FORMAT: v{MAJOR}.{MINOR}.{PATCH}
 *   v1.0.0 → Pehla release
 *   v1.1.0 → Naye features add kiye (backward compatible)
 *   v1.1.1 → Bug fix
 *   v2.0.0 → Breaking changes (purana code compatible nahi)
 */
