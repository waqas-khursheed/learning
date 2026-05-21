# Laravel + Docker Complete Guide (Local + Live Server)

## 1. Introduction

Docker ek containerization platform hai jo aapke application ko uski 
dependencies ke sath ek isolated environment mai run karta hai.

Is guide mai aap seekhoge:

* Laravel ko Docker mai local run karna
* Server (live) par deploy karna
* Basic concepts samajhna

---

# 2. Docker Kya Hai?

Docker ek system hai jo:

* PHP
* MySQL
* Redis
* Nginx
  ko alag containers mai run karta hai.

Is se benefit:

* Same environment har machine par
* No dependency issues

---

# 3. Local vs Live Server

## Local (Development)

* Windows / Laptop
* Docker Desktop use hota hai
* Testing & development ke liye
* URL: [http://localhost:8000](http://localhost:8000)

## Live Server (Production)

* Linux VPS (Ubuntu)
* Docker Engine install hota hai
* Real users access karte hain
* URL: [https://yourdomain.com](https://yourdomain.com)

---

# 4. Required Tools

## Local:

* Docker Desktop
* Laravel Project
* Code Editor

## Live Server:

* Ubuntu Server
* SSH access
* Docker Engine
* Docker Compose

---

# 5. Laravel Docker Structure

Project structure:

```
my-laravel-app/
│
├── Dockerfile
├── docker-compose.yml
├── .env
├── nginx/
│   └── default.conf
├── app/
└── artisan
```

---

# 6. Dockerfile

```
FROM php:8.2-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    zip unzip curl git libpng-dev

RUN docker-php-ext-install pdo pdo_mysql

COPY . .

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

CMD ["php-fpm"]
```

---

# 7. docker-compose.yml

```
version: '3.8'

services:

  app:
    build: .
    container_name: laravel_app
    working_dir: /var/www
    volumes:
      - ./:/var/www
    depends_on:
      - mysql

  nginx:
    image: nginx:latest
    container_name: nginx_server
    ports:
      - "8000:80"
    volumes:
      - ./:/var/www
      - ./nginx/default.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - app

  mysql:
    image: mysql:8
    container_name: mysql_db
    restart: always
    environment:
      MYSQL_DATABASE: laravel
      MYSQL_ROOT_PASSWORD: root
    ports:
      - "3307:3306"
```

---

# 8. Nginx Config

```
server {
    listen 80;
    index index.php;
    root /var/www/public;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

# 9. .env Configuration

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=root
```

---

# 10. Local Setup Steps

## Step 1: Start containers

```
docker compose up -d --build
```

## Step 2: Enter container

```
docker exec -it laravel_app bash
```

## Step 3: Install dependencies

```
composer install
```

## Step 4: Setup Laravel

```
php artisan key:generate
php artisan migrate
```

## Step 5: Open browser

```
http://localhost:8000
```

---

# 11. Live Server Setup Steps

## Step 1: Server login

```
ssh root@server-ip
```

## Step 2: Install Docker

```
apt update
apt install docker.io -y
apt install docker-compose -y
```

## Step 3: Clone project

```
git clone your-repo
cd your-project
```

## Step 4: Run project

```
docker compose up -d --build
```

## Step 5: Access site

```
http://server-ip
```

or domain connect

---

# 12. Key Concepts Summary

## Docker does:

* App ko isolate karta hai
* Same environment har jagah
* Dependency issues khatam

## You still need:

* Docker install (local + server)
* docker-compose run command
* Laravel setup commands (first time)

---

# 13. Important Notes

✔ First time composer install zaroor hota hai
✔ DB_HOST container name hota hai (mysql)
✔ Same docker setup local aur live mai use hota hai
✔ Production mai SSL + domain add hota hai

---

# 14. Final Flow

## Local:

```
docker compose up -d --build
composer install
php artisan migrate
```

## Live:

```
git clone repo
docker compose up -d --build
```

---

# END

Agar aap chaho to next step mai mai aapko:

* Production-ready Docker setup (queues + redis + supervisor)
* CI/CD auto deploy (GitHub Actions)

bhi sikha sakta hoon.
