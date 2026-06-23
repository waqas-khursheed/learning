<?php

/**
 * ============================================================================
 *                    GITHUB ACTIONS — MUKAMMAL GUIDE
 *          Workflows, Reusable Actions, aur Advanced Patterns
 * ============================================================================
 */


// =============================================================================
// 1. GITHUB ACTIONS KYA HAI?
// =============================================================================

/*
 * GitHub Actions = GitHub ka built-in CI/CD tool
 * Code push karo → Tests chalein → Deploy ho jaye — sab automatic!
 *
 * FREE TIER:
 *   - Public repos: Unlimited minutes
 *   - Private repos: 2000 minutes/month free
 *   - Storage: 500 MB free
 *
 *
 * FILE LOCATION:
 *   .github/workflows/    ← Sab workflow files yahan rakhho
 *   .github/workflows/ci.yml
 *   .github/workflows/deploy.yml
 *   .github/workflows/schedule.yml
 */


// =============================================================================
// 2. LARAVEL CI WORKFLOW (Tests + Code Quality)
// =============================================================================

/*
 * ─── .github/workflows/ci.yml ───
 *
 *   name: Laravel CI
 *
 *   on:
 *     pull_request:
 *       branches: [main, develop]
 *
 *   jobs:
 *     tests:
 *       name: PHP ${{ matrix.php }} Tests
 *       runs-on: ubuntu-latest
 *
 *       strategy:
 *         matrix:
 *           php: ['8.2', '8.3']    # Multiple PHP versions par test karo
 *
 *       services:
 *         mysql:
 *           image: mysql:8.0
 *           env:
 *             MYSQL_ROOT_PASSWORD: password
 *             MYSQL_DATABASE: testing
 *           ports: ['3306:3306']
 *           options: --health-cmd="mysqladmin ping" --health-interval=10s --health-timeout=5s --health-retries=3
 *
 *       steps:
 *         - uses: actions/checkout@v4
 *
 *         - name: Setup PHP ${{ matrix.php }}
 *           uses: shivammathur/setup-php@v2
 *           with:
 *             php-version: ${{ matrix.php }}
 *             extensions: mbstring, xml, ctype, json, bcmath, pdo_mysql
 *
 *         - name: Cache Composer
 *           uses: actions/cache@v4
 *           with:
 *             path: vendor
 *             key: composer-${{ matrix.php }}-${{ hashFiles('composer.lock') }}
 *
 *         - name: Install Dependencies
 *           run: composer install --no-progress
 *
 *         - name: Setup Env
 *           run: cp .env.example .env && php artisan key:generate
 *
 *         - name: Run Tests
 *           env:
 *             DB_HOST: 127.0.0.1
 *             DB_DATABASE: testing
 *             DB_USERNAME: root
 *             DB_PASSWORD: password
 *           run: php artisan test
 */


// =============================================================================
// 3. USEFUL WORKFLOW PATTERNS
// =============================================================================

/*
 * ── PATTERN 1: Scheduled Tasks (Cron) ──
 *
 *   name: Daily Database Backup
 *
 *   on:
 *     schedule:
 *       - cron: '0 2 * * *'    # Har roz subah 2 baje (UTC)
 *
 *   jobs:
 *     backup:
 *       runs-on: ubuntu-latest
 *       steps:
 *         - name: Trigger Backup
 *           uses: appleboy/ssh-action@v1
 *           with:
 *             host: ${{ secrets.EC2_HOST }}
 *             username: ubuntu
 *             key: ${{ secrets.EC2_SSH_KEY }}
 *             script: |
 *               cd /var/www/my-laravel-app
 *               php artisan backup:run
 *
 *
 * ── PATTERN 2: Manual Deployment (Button se) ──
 *
 *   name: Manual Deploy
 *
 *   on:
 *     workflow_dispatch:           # GitHub UI se manually trigger
 *       inputs:
 *         environment:
 *           description: 'Kahan deploy karna hai'
 *           required: true
 *           type: choice
 *           options:
 *             - staging
 *             - production
 *
 *   jobs:
 *     deploy:
 *       runs-on: ubuntu-latest
 *       steps:
 *         - name: Deploy to ${{ github.event.inputs.environment }}
 *           run: echo "Deploying to ${{ github.event.inputs.environment }}"
 *
 *
 * ── PATTERN 3: Multi-Environment Deploy ──
 *
 *   name: Deploy Pipeline
 *
 *   on:
 *     push:
 *       branches:
 *         - develop    # → Staging par deploy
 *         - main       # → Production par deploy
 *
 *   jobs:
 *     deploy-staging:
 *       if: github.ref == 'refs/heads/develop'
 *       runs-on: ubuntu-latest
 *       environment: staging
 *       steps:
 *         - name: Deploy to Staging
 *           run: echo "Staging par deploy ho raha hai"
 *
 *     deploy-production:
 *       if: github.ref == 'refs/heads/main'
 *       runs-on: ubuntu-latest
 *       environment: production
 *       steps:
 *         - name: Deploy to Production
 *           run: echo "Production par deploy ho raha hai"
 *
 *
 * ── PATTERN 4: Slack Notification ──
 *
 *     - name: Slack Notification
 *       if: always()
 *       uses: 8398a7/action-slack@v3
 *       with:
 *         status: ${{ job.status }}
 *         text: 'Deploy ${{ job.status == 'success' && 'kamyab ✅' || 'nakaam ❌' }}'
 *       env:
 *         SLACK_WEBHOOK_URL: ${{ secrets.SLACK_WEBHOOK }}
 */


// =============================================================================
// 4. ENVIRONMENTS AUR SECRETS
// =============================================================================

/*
 * ENVIRONMENTS:
 *   GitHub → Repo → Settings → Environments
 *
 *   1. "staging" environment banayen
 *      - Protection rules: koi nahi (direct deploy)
 *      - Secrets: staging ke .env values
 *
 *   2. "production" environment banayen
 *      - Protection rules: Required reviewers (kisi ki approval zaruri)
 *      - Secrets: production ke .env values
 *
 * Ab production deploy se pehle approval mangega!
 *
 *
 * SECRETS KI LEVELS:
 *
 *   Organization Secrets → Sab repos mein available
 *   Repository Secrets   → Sirf is repo mein
 *   Environment Secrets  → Sirf khaas environment mein (staging/production)
 */


// =============================================================================
// 5. GITHUB ACTIONS BEST PRACTICES
// =============================================================================

/*
 * ✅ Cache use karo (Composer, npm — pipeline tez hoti hai)
 * ✅ Matrix strategy use karo (kai PHP versions par test)
 * ✅ Secrets GitHub Secrets mein rakhho
 * ✅ Environments use karo (staging/production alag)
 * ✅ production mein required reviewers lagao
 * ✅ Tests fail hon toh deploy na ho (needs: test)
 * ✅ Notifications lagao (Slack/Email)
 *
 * ❌ Secrets echo/print mat karo
 * ❌ Bina test ke deploy mat karo
 * ❌ Main branch par seedha push mat karo
 * ❌ Latest tag use mat karo actions ke liye (version pin karo: @v4)
 */
