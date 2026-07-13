# Local Development Aur Deployment

## Local Development

```bash
npm run dev
```

- `http://localhost:3000` pe app chalti hai
- File save karte hi Fast Refresh se browser update ho jata hai
- Errors browser mein overlay ki tarah dikhte hain (stack trace ke sath)

Port change karna ho: `npm run dev -- -p 4000`

## Environment Variables Per Environment

```
.env.local           → local machine ke liye (git ignored, secrets yahan)
.env.development      → npm run dev ke waqt
.env.production        → npm run build/start ke waqt
.env                   → sab environments mein common (agar git mein rakhna ho to koi secret na ho)
```

Priority: `.env.local` hamesha sabse zyada priority leta hai (production ke `.env.production.local` ke alawa).

```
# .env.local
NEXT_PUBLIC_API_URL=http://localhost:8000
DATABASE_URL=postgresql://localhost:5432/mydb
```

## Production Build

```bash
npm run build     # optimized production build banata hai (.next/ folder)
npm run start     # us build ko production mode mein serve karta hai
```

`npm run dev` production mein **kabhi** use nahi hota — slow hota hai aur dev-only features (source maps, hot reload) enabled rehte hain.

Build ke output ko samjho:
```
Route (app)                     Size     First Load JS
┌ ○ /                           142 B    87.3 kB
├ ● /blog/[slug]                156 B    89 kB
└ ƒ /dashboard                  2.1 kB   102 kB

○  Static   (SSG — prerendered)
●  SSG      (with generateStaticParams)
ƒ  Dynamic  (SSR — server-rendered per request)
```

Ye batata hai har route kis strategy se render hoga — deploy se pehle check kar lena chahiye.

## Deployment Option 1: Vercel (Sabse Aasan, Recommended)

Vercel khud Next.js banane wali company hai — zero-config deployment.

```bash
npm install -g vercel
vercel login
vercel          # preview deployment
vercel --prod   # production deployment
```

Ya GitHub se connect karo (`vercel.com` → Import Project) — har `git push` pe automatic deploy ho jata hai, har PR ka apna preview URL milta hai. Environment variables Vercel dashboard mein set karte ho (`.env.local` wahan upload nahi hoti, manually add karni parti hai).

## Deployment Option 2: Docker (VPS/Any Cloud Ke Liye)

Aapke backend experience se ye familiar lagega:

```dockerfile
# Dockerfile
FROM node:20-alpine AS base

FROM base AS deps
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci

FROM base AS builder
WORKDIR /app
COPY --from=deps /app/node_modules ./node_modules
COPY . .
RUN npm run build

FROM base AS runner
WORKDIR /app
ENV NODE_ENV=production
COPY --from=builder /app/public ./public
COPY --from=builder /app/.next/standalone ./
COPY --from=builder /app/.next/static ./.next/static
EXPOSE 3000
CMD ["node", "server.js"]
```

`next.config.ts` mein `output: "standalone"` set karna zaroori hai isse chalane ke liye — ye minimal production server bundle banata hai.

```bash
docker build -t my-app .
docker run -p 3000:3000 --env-file .env.production my-app
```

## Deployment Option 3: VPS + PM2 + Nginx (Traditional, Aapke Laravel Deploy Jaisa)

```bash
npm run build
pm2 start npm --name "next-app" -- start
pm2 save
```

Nginx reverse proxy config (bilkul waise jaise Laravel ke liye karte ho):

```nginx
server {
    listen 80;
    server_name mysite.com;

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

Phir SSL ke liye `certbot` se Let's Encrypt certificate lagao — same jaisa PHP apps mein karte ho.

## Deployment Option Comparison

| Option | Best For |
|---|---|
| **Vercel** | Fastest setup, auto CI/CD, generous free tier — chote/medium projects |
| **Docker + Cloud Run/ECS/Railway** | Full control, containerized microservices setup |
| **VPS + PM2 + Nginx** | Jab aapka poora infra already self-managed hai (jaise Laravel apps) |
| **Netlify** | Vercel jaisa hi alternative |

## CI/CD Basics — GitHub Actions

```yaml
# .github/workflows/ci.yml
name: CI
on: [push, pull_request]
jobs:
  build:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with: { node-version: 20 }
      - run: npm ci
      - run: npm run lint
      - run: npm run build
```

Vercel already isse khud handle karta hai (git push → build → deploy), lekin custom VPS setup mein ye workflow zaroori hai.

## Practice

1. Apna project Vercel pe deploy karo (GitHub se connect karke), environment variables dashboard mein set karo.
2. `output: "standalone"` set karke Dockerfile se local image build aur run karo.
3. `npm run build` ke output mein routes ke aage ○/●/ƒ symbols dekho, samjho kaun se static hain kaun se dynamic.

Agli file: [15_senior_level_structure_best_practices.md](15_senior_level_structure_best_practices.md)
</content>
