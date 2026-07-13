# Build Process Aur AWS Pe Deployment (Detail Mein)

Ye file [14_local_dev_aur_deployment.md](14_local_dev_aur_deployment.md) ka deep-dive hai — khaas kar **build kaise hota hai**, **Node.js live pe chahiye ya nahi**, aur **AWS pe exact steps** kya hain.

## 1. `npm run build` Andar Se Kya Karta Hai

```bash
npm run build
```

Ye command 3 kaam karta hai:

1. **Type-check + Lint** — TypeScript errors hon to build yahin fail ho jata hai (production mein broken code jane se rukta hai)
2. **Compile + Bundle** — har route ka JS bundle banta hai, minify hota hai, code-splitting hoti hai (har page ka alag chota bundle, poori app ka ek bada bundle nahi)
3. **Route Analysis** — har route ko decide karta hai: Static (○), SSG (●), ya Dynamic/SSR (ƒ) — jo humne pichli file mein dekha

Output `.next/` folder mein jata hai:

```
.next/
├── static/          → JS, CSS bundles (browser ye directly serve karta hai)
├── server/           → server-rendered pages ka compiled code
├── cache/             → ISR/build cache
└── build-manifest.json, routes-manifest.json waghera
```

`.next/` folder ko **kabhi git mein commit nahi karte** (already `.gitignore` mein hota hai) — ye har deployment pe fresh generate hota hai, bilkul Laravel ke `vendor/` ya `node_modules/` jaisa (dependency/build artifact hai, source code nahi).

## 2. Kya Next.js Ko Live Pe Node.js Chahiye? (Seedha Jawab)

**Depends on kis mode mein deploy kar rahe ho:**

| Build Mode | Node.js Chahiye Live Pe? | Kya Milta Hai |
|---|---|---|
| **Default (Server mode)** — `next build` + `next start` | ✅ **Haan, zaroori hai** | SSR, Server Actions, Route Handlers, ISR — sab features kaam karte hain |
| **Standalone** — `output: "standalone"` | ✅ **Haan** (lekin minimal node_modules ke sath, chota deployment) | Docker ke liye best, sab features kaam karte hain |
| **Static Export** — `output: "export"` | ❌ **Nahi, bilkul nahi chahiye** | Sirf static HTML/CSS/JS files — S3, CDN, kisi bhi static host pe chal jata hai. Lekin SSR, Server Actions, Route Handlers, Image Optimization (default) **kaam nahi karte** |

Simple rule: **Agar aapki site mein koi bhi dynamic/server-side cheez hai (API calls jo request-time pe hon, auth, forms via Server Actions, database) — Node.js chahiye hi hoga.** Sirf tab Node skip ho sakta hai jab poori site 100% static ho (jaise ek portfolio jisme koi backend interaction na ho).

```ts
// next.config.ts — static export ke liye (sirf agar zaroorat ho)
const nextConfig = {
  output: "export",
};
```

Aapke case mein (Laravel backend ke sath integrate karna hai) — zyada tar aapko **Server mode ya Standalone mode** hi chahiye hoga, kyun ke Server Components server pe fetch karte hain, aur wo sirf Node runtime mein chalta hai.

## 3. Production Mein Chalane Ka Minimum Setup

Kisi bhi server (AWS EC2 ho ya koi bhi VPS) pe ye chahiye:

```bash
node -v      # v18.18+ ya latest LTS (v20/v22)
npm -v
```

Phir:

```bash
npm ci                # exact lockfile versions install (npm install se zyada reliable production ke liye)
npm run build          # .next/ banega
npm run start           # production server chalu (default port 3000)
```

`npm run start` background mein hamesha chalta rehna chahiye — is ke liye **PM2** (process manager) use karte hain, taake crash hone pe auto-restart ho, aur server reboot pe bhi wapas chalu ho:

```bash
npm install -g pm2
pm2 start npm --name "nextjs-app" -- start
pm2 startup       # server reboot pe auto-start
pm2 save
```

## 4. AWS Pe Deploy Karne Ke 4 Tareeqe

### Option A: AWS Amplify Hosting (Sabse Aasan — Vercel Jaisa)

Amplify Next.js ko natively support karta hai (SSR, ISR, Server Actions sab included) — GitHub connect karo, baaki khud ho jata hai.

**Steps:**
1. AWS Console → Amplify → "New app" → "Host web app"
2. GitHub repo connect karo, branch select karo
3. Build settings auto-detect ho jati hain (`amplify.yml` khud generate hota hai):
```yaml
version: 1
frontend:
  phases:
    preBuild:
      commands:
        - npm ci
    build:
      commands:
        - npm run build
  artifacts:
    baseDirectory: .next
    files:
      - '**/*'
  cache:
    paths:
      - node_modules/**/*
```
4. Environment variables Amplify console mein "Environment variables" section mein add karo
5. Deploy — har git push pe auto-rebuild/deploy hota hai
6. Custom domain: Amplify console → Domain management → apna domain add karo, SSL automatic milta hai

**Kab use karo:** Jab Vercel jaisi simplicity chahiye lekin AWS ecosystem mein rehna zaroori ho (company policy, existing AWS infra ke sath integration).

### Option B: EC2 + PM2 + Nginx (Aapke Laravel Deploy Jaisa — Full Control)

Ye bilkul waisa hi hai jaise aap Laravel ko EC2 pe deploy karte ho, bas PHP-FPM ki jagah Node process hoga.

**Step 1 — EC2 Instance Banao**
- AWS Console → EC2 → Launch Instance → Ubuntu 22.04/24.04 LTS
- Security Group mein ports allow karo: `22` (SSH), `80` (HTTP), `443` (HTTPS)
- Key pair download karo (SSH access ke liye)

**Step 2 — Server Setup**
```bash
ssh -i your-key.pem ubuntu@your-ec2-ip

# Node.js install (NodeSource se latest LTS)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install -y nodejs

# PM2 aur Nginx
sudo npm install -g pm2
sudo apt install -y nginx
```

**Step 3 — Code Deploy Karo**
```bash
git clone your-repo-url
cd your-repo
npm ci
cp .env.example .env.production   # ya scp/manually values daalo
npm run build
pm2 start npm --name "nextjs-app" -- start
pm2 startup
pm2 save
```

**Step 4 — Nginx Reverse Proxy**
```nginx
# /etc/nginx/sites-available/nextjs-app
server {
    listen 80;
    server_name yourdomain.com;

    location / {
        proxy_pass http://localhost:3000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
    }
}
```
```bash
sudo ln -s /etc/nginx/sites-available/nextjs-app /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

**Step 5 — SSL (Let's Encrypt)**
```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com
```

**Step 6 — Domain (Route 53, agar wahan manage ho)**
- Route 53 → Hosted Zone → A record → EC2 ka Elastic IP point karo

**Kab use karo:** Jab aapka backend bhi EC2 pe hai aur aap sab kuch ek jagah, familiar tareeqe se manage karna chahte ho (jaise aap Laravel ke sath karte ho).

### Option C: Docker + ECS Fargate (Container-Based, Scalable)

[14_local_dev_aur_deployment.md](14_local_dev_aur_deployment.md) mein diya Dockerfile use karo (`output: "standalone"` ke sath), image ko ECR pe push karo, ECS Fargate service banao.

```bash
# ECR pe image push
aws ecr get-login-password --region us-east-1 | docker login --username AWS --password-stdin <account-id>.dkr.ecr.us-east-1.amazonaws.com
docker build -t nextjs-app .
docker tag nextjs-app:latest <account-id>.dkr.ecr.us-east-1.amazonaws.com/nextjs-app:latest
docker push <account-id>.dkr.ecr.us-east-1.amazonaws.com/nextjs-app:latest
```

Phir ECS Console mein: Cluster → Task Definition (image ECR se, port 3000, env vars) → Service (desired count, Application Load Balancer attach) banao.

**Kab use karo:** Jab traffic scale karna ho automatically, multiple containers chalane hon, ya team already Docker/Kubernetes workflow use kar rahi ho.

### Option D: Static Export + S3 + CloudFront (Sirf Static Sites Ke Liye — Node Bilkul Nahi Chahiye)

Agar site 100% static hai (koi Server Action, koi dynamic SSR, koi Route Handler nahi):

```ts
// next.config.ts
const nextConfig = { output: "export" };
```

```bash
npm run build   # ab .next/ ki jagah "out/" folder banega — pure HTML/CSS/JS
```

```bash
aws s3 sync out/ s3://your-bucket-name --delete
```

CloudFront distribution S3 bucket ko origin bana kar CDN se serve karta hai, custom domain + SSL CloudFront se attach hota hai.

**Kab use karo:** Sirf marketing site, documentation, ya portfolio jaisi purely static content ke liye — sabse sasta aur fastest option, lekin aapke Laravel-integrated dynamic app ke liye ye **kaam nahi karega** kyun ke Server Components ko har request pe kuch fetch/compute karna hota hai.

## 5. AWS Options Ka Comparison

| Option | Node Chahiye? | Setup Difficulty | Best For |
|---|---|---|---|
| **Amplify Hosting** | Managed (aapko manage nahi karna) | Easy | Vercel jaisi simplicity, full Next.js features |
| **EC2 + PM2 + Nginx** | Haan, khud manage karo | Medium | Full control, Laravel jaisa familiar setup |
| **ECS Fargate (Docker)** | Managed inside container | Medium-Hard | Auto-scaling, microservices architecture |
| **S3 + CloudFront (static export)** | Bilkul nahi | Easy | Sirf pure static sites (SSR/API na ho) |

**Aapke use case (Laravel backend + dynamic Next.js frontend) ke liye recommendation: Amplify Hosting (fastest start) ya EC2 (agar Laravel bhi EC2 pe hai aur ek hi jagah manage karna chahte ho).**

## 6. Environment Variables — Har AWS Option Mein Kahan Set Hote Hain

| Option | Kahan Set Karo |
|---|---|
| Amplify | Console → App settings → Environment variables |
| EC2 | `.env.production` file server pe, ya AWS Systems Manager Parameter Store se secure inject |
| ECS | Task Definition → Environment variables (ya Secrets Manager reference) |
| S3/CloudFront | Build time pe hi `NEXT_PUBLIC_*` values bake ho jati hain (static export mein runtime env nahi chalta) |

**Production secrets (DB passwords, API keys) ke liye AWS Secrets Manager ya Systems Manager Parameter Store use karo** — plain `.env` file server pe rakhna chota project ke liye theek hai, lekin senior-level setup mein secrets manager better practice hai.

## Practice

1. `npm run build` chala kar `.next/server/` aur `.next/static/` folders explore karo — farak samjho.
2. Ek free-tier EC2 instance pe apna practice project deploy karo (Option B ke steps follow karke) — end to end khud karo.
3. Ek chota static-only page (`output: "export"`) bana kar S3 pe upload karo, bina CloudFront ke seedha S3 static website hosting se access karo — dekho Node ki zaroorat hi nahi padi.

Peeche: [14_local_dev_aur_deployment.md](14_local_dev_aur_deployment.md) | Aage: [16_practice_projects_roadmap.md](16_practice_projects_roadmap.md)
</content>
