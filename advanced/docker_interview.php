<?php


// Docker Interview Questions + Answers
// 1. Docker kya hai?

// Answer:
// Docker ek containerization platform hai jo application ko uski dependencies ke sath isolate
//  karke run karta hai.

// 👉 Is se app har system par same behave karti hai.

// 2. Container kya hota hai?

// Answer:
// Container ek running instance hota hai Docker image ka jo lightweight isolated environment mai application run karta hai.

// 3. Docker image kya hoti hai?

// Answer:
    // Docker image ek read-only template hota hai jisme application + dependencies defined hoti hain.

// 👉 Container image se run hota hai.

// 4. Dockerfile kya hota hai?

// Answer:
// Dockerfile ek script hoti hai jisme instructions hoti hain ke image kaise build hogi.

// Example:

// FROM
// RUN
// COPY
// CMD
// 5. Docker Compose kya hota hai?

// Answer:
// Docker Compose multiple containers ko ek file (docker-compose.yml) se manage karne ke liye use hota hai.

// 👉 Example: Laravel + MySQL + Redis

// 6. Container aur Virtual Machine mai difference?

// Answer:

// Container	VM
// Lightweight	Heavy
// OS share karta hai	Full OS hota hai
// Fast startup	Slow startup
// 7. Docker ka main benefit kya hai?

// Answer:

// Same environment everywhere
// Dependency issues khatam
// Easy deployment
// Scalable architecture
// 8. Docker volumes kya hotay hain?

// Answer:
// Volumes data persistence ke liye use hotay hain taake container delete hone ke baad data loss na ho.

// 9. Docker networking kya hoti hai?

// Answer:
// Networking containers ko ek dusre se connect karne ke liye use hoti hai (e.g. Laravel → MySQL).

// 10. docker ps kya karta hai?

// Answer:
// Running containers ki list show karta hai.

// 11. docker images kya karta hai?

// Answer:
// System mai available images show karta hai.

// 12. docker run kya karta hai?

// Answer:
// Image ko container mai convert karke run karta hai.

// 13. docker-compose up kya karta hai?

// Answer:
// docker-compose.yml mai defined sare containers ko start karta hai.

// 14. Docker production mai kaise use hota hai?

// Answer:
// Production mai Docker se:

// app deploy hoti hai
// same environment maintain hota hai
// CI/CD pipelines use hoti hain
// 15. Docker aur Kubernetes mai difference?

// Answer:

// Docker = containers run karta hai
// Kubernetes = multiple containers ko manage + scale karta hai
// 16. Laravel Docker mai kaise run hota hai?

// Answer:
// Laravel run hota hai:

// PHP container
// MySQL container
// Nginx container

// via docker-compose.yml

// 17. Docker ka real-world use case?

// Answer:

// Microservices
// CI/CD pipelines
// Cloud deployment
// Scalable applications
// 18. Docker image build ka command?
// docker build -t app-name .
// 19. Container stop ka command?
// docker stop container_id
// 20. Container remove ka command?
// docker rm container_id
// 🚀 BONUS (Interview tip)

// Agar interviewer pooche:

// ❓ “Docker kyun use karte ho?”

// 👉 Best answer:

// “Docker is used to ensure consistency across development, staging, and production environments. It eliminates dependency issues and makes deployment faster and scalable.”


// Docker Definitions (Line by Line)
// 1. Docker

// Docker ek platform hai jo applications ko containers mai run karta hai taake woh har system par same behave karein.

// 2. Container

// Container ek lightweight isolated environment hota hai jisme application aur uski dependencies run hoti hain.

// 3. Image

// Image ek read-only template hoti hai jisme application ka code, dependencies aur configuration hoti hai.

// 4. Dockerfile

// Dockerfile ek text file hoti hai jisme instructions hoti hain ke Docker image kaise build hogi.

// 5. Docker Compose

// Docker Compose ek tool hai jo multiple containers ko ek file (docker-compose.yml) se manage aur run karta hai.

// 6. Volume

// Volume ek storage system hai jo container ke data ko persist (save) karta hai even after container stops or deletes.

// 7. Network (Docker Networking)

// Docker networking containers ko ek dusre se connect karne ka system hai, jese Laravel ka MySQL se connect hona.

// 8. Port Mapping

// Port mapping host system ke port ko container ke port se connect karta hai taake application browser se access ho sake.

// 9. docker run

// docker run command image ko container mai convert karke run karti hai.

// 10. docker ps

// Running containers ki list show karta hai.

// 11. docker images

// System mai available Docker images show karta hai.

// 12. docker build

// Dockerfile se image create karta hai.

// 13. docker stop

// Running container ko stop karta hai.

// 14. docker rm

// Stopped container ko delete karta hai.

// 15. docker-compose up

// docker-compose.yml file ke through saare containers ko ek sath start karta hai.

// 16. docker-compose down

// Saare running containers ko stop aur remove karta hai.

// 17. Microservices

// Microservices ek architecture hai jisme application multiple small services mai divide hoti hai jo independent run hoti hain (Docker is ke liye use hota hai).

// 18. Virtual Machine vs Docker

// Virtual Machine complete OS run karta hai jabke Docker sirf application environment share karta hai is liye lightweight hota hai.

// 19. Image vs Container

// Image blueprint hoti hai aur container us image ka running instance hota hai.

// 20. CI/CD (Docker context)

// CI/CD ek process hai jisme code automatically test, build aur deploy hota hai Docker ke sath production mai.

// 🔥 Super Simple Summary

// 👉 Docker = app run karne ka system
// 👉 Image = template
// 👉 Container = running app
// 👉 Dockerfile = instructions


// 1. AWS server milne ke baad pehla step

// 👉 Tumhe server ka access milta hai:

// IP address + SSH key / password
// 🔑 Login kaise karte ho?
// ssh root@your-server-ip

// 👉 ya agar key file ho:

// ssh -i key.pem ubuntu@your-server-ip
// 🖥 2. Server mai tum kya dekhte ho?

// 👉 Ab tum Linux terminal mai ho:

// Ubuntu / CentOS system
// koi GUI nahi hoti
// sirf commands
// ⚙️ 3. First setup (VERY IMPORTANT)
// 🐳 Docker install karo:
// apt update
// apt install docker.io -y
// apt install docker-compose -y
// check:
// docker --version
// 📦 4. Project kaise chalayega?
// Step 1: Code server pe lao
// Option A (Git):
// git clone your-repo
// cd project
// Option B (upload):
// SCP / FTP
// Step 2: Docker run karo
// docker compose up -d --build

// 👉 Bas project live ho gaya 🚀

// 🧠 5. Database kaise handle hota hai?

// 👉 AWS server pe 2 options hotay hain:

// 🟢 Option 1: MySQL Docker container (BEST)

// docker-compose.yml:

// mysql:
//   image: mysql:8
//   environment:
//     MYSQL_DATABASE: laravel
//     MYSQL_ROOT_PASSWORD: root

// 👉 DB container mai run hota hai

// 🟡 Option 2: AWS RDS (Professional way)

// 👉 AWS ka managed database service

// No installation
// Auto backup
// scalable
// 🔗 Laravel DB connection
// .env file:
// DB_HOST=mysql
// DB_DATABASE=laravel
// DB_USERNAME=root
// DB_PASSWORD=root

// 👉 agar RDS use karo:

// DB_HOST=aws-rds-endpoint
// 🧪 6. Database commands kaise chalayenge?

// 👉 Server terminal mai:

// docker exec -it laravel_app bash

// phir:

// php artisan migrate
// php artisan db:seed
// 🌐 7. Website kaise open hogi?
// http://server-ip:8000

// 👉 ya domain:

// https://yourdomain.com
// 🧠 8. Daily server workflow
// Deploy:
// git pull
// docker compose up -d --build
// logs check:
// docker logs laravel_app
// containers check:
// docker ps
// 💡 Simple samajh
// Cheez	Kaise hoti hai
// Server login	SSH
// Project run	Docker compose
// Database	MySQL container ya RDS
// Laravel commands	docker exec
// 🔥 Real industry flow

// 👉 AWS server pe:

// SSH login
// Docker install (once)
// Git clone
// docker compose up
// Done 🚀
// 🏁 FINAL SIMPLE ANSWER

// 👉 AWS server pe tum terminal (SSH) se login karte ho, Docker install karte ho, project pull karte ho, aur docker compose se app run kar dete ho. Database ya to Docker container hota hai ya AWS RDS service hoti hai.



// Docker setup

// wsl --install

// Kaise pata chale WSL hai ya nahi?

// PowerShell mai:

// wsl --list --verbose


// sudo apt install docker.io
// sudo usermod -aG docker $USER



// Docker Essential Commands (Cheat Sheet)
// 📦 1. Images commands
// ✔️ images list dekho
// docker images
// ✔️ image download karo
// docker pull nginx
// docker pull mysql:8
// ✔️ image delete
// docker rmi image_id
// 🚀 2. Container commands
// ✔️ container run
// docker run hello-world
// ✔️ background run (detached)
// docker run -d nginx
// ✔️ running containers list
// docker ps
// ✔️ all containers (including stopped)
// docker ps -a
// ✔️ stop container
// docker stop container_id
// ✔️ start container
// docker start container_id
// ✔️ remove container
// docker rm container_id
// 🧠 3. Logs & debugging
// ✔️ logs dekho
// docker logs container_id
// ✔️ live logs
// docker logs -f container_id
// ⚙️ 4. System commands
// ✔️ Docker info
// docker info
// ✔️ version check
// docker --version
// 🧱 5. Docker Compose (Laravel use case ⭐)
// ✔️ start services
// docker compose up -d
// ✔️ stop services
// docker compose down
// ✔️ rebuild
// docker compose up --build
// 🧠 6. Container ke andar jana
// ✔️ shell access
// docker exec -it container_id bash
// 🚀 7. Clean up (important)
// ✔️ unused containers remove
// docker system prune
// ✔️ full cleanup (danger)
// docker system prune -a
// 💡 Simple samajh:
// Category	Kaam
// images	software download
// containers	running apps
// logs	debugging
// compose	full project run








// STEP 0 — Folder banao
// cd ~
// mkdir projects
// cd projects
// mkdir laravel-docker
// cd laravel-docker
// 🐳 STEP 1 — Docker files banao
// 📁 1.1 folder structure
// laravel-docker/
// ├── docker/
// │   ├── nginx/
// │   │   └── default.conf
// │   └── php/
// │       └── Dockerfile
// ├── docker-compose.yml
// 📄 STEP 1.2 — Dockerfile banao
// mkdir -p docker/php
// nano docker/php/Dockerfile
// Paste this:
// FROM php:8.2-fpm

// WORKDIR /var/www

// RUN apt-get update && apt-get install -y \
//     git curl unzip zip libpng-dev libonig-dev libxml2-dev

// RUN docker-php-ext-install pdo pdo_mysql mbstring

// COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
// 🌐 STEP 1.3 — Nginx config
// mkdir -p docker/nginx
// nano docker/nginx/default.conf
// Paste:
// server {
//     listen 80;
//     server_name localhost;

//     root /var/www/public;

//     index index.php;

//     location / {
//         try_files $uri $uri/ /index.php?$query_string;
//     }

//     location ~ \.php$ {
//         include fastcgi_params;
//         fastcgi_pass app:9000;
//         fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
//     }
// }
// 🐳 STEP 1.4 — docker-compose.yml
// nano docker-compose.yml
// Paste:
// version: "3.8"

// services:

//   app:
//     build:
//       context: .
//       dockerfile: docker/php/Dockerfile
//     container_name: laravel_app
//     volumes:
//       - .:/var/www
//     working_dir: /var/www

//   nginx:
//     image: nginx:latest
//     container_name: laravel_nginx
//     ports:
//       - "8080:80"
//     volumes:
//       - .:/var/www
//       - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
//     depends_on:
//       - app

//   db:
//     image: mysql:8
//     container_name: laravel_db
//     restart: always
//     environment:
//       MYSQL_DATABASE: laravel
//       MYSQL_ROOT_PASSWORD: root
//     ports:
//       - "3306:3306"
// 🚀 STEP 2 — Laravel install (Docker se)

// 👉 ab sabse important step ⭐

// docker run --rm -v $(pwd):/app composer create-project laravel/laravel .

// 👉 ye Laravel 12 install karega

// ⚙️ STEP 3 — Start project
// docker compose up -d --build
// 🌐 STEP 4 — Browser open karo
// http://localhost:8080
// 🧠 STEP 5 — DB connect (Laravel .env)

// .env file open karo:

// DB_CONNECTION=mysql
// DB_HOST=db
// DB_PORT=3306
// DB_DATABASE=laravel
// DB_USERNAME=root
// DB_PASSWORD=root
// 🚀 FLOW SAMJHO
// Browser → Nginx → PHP Container → Laravel → MySQL Container
// 💡 SIMPLE RULES

// ✔ No XAMPP
// ✔ No local PHP
// ✔ No local Composer
// ✔ Everything inside Docker


// docker run --rm -v $(pwd):/app composer create-project laravel/laravel laravel-app

// install compose plugin
// sudo apt update
// sudo apt install docker-compose-plugin -y


// sudo apt install docker-compose -y













// LARAVEL + DOCKER FULL INSTALL FLOW (DOCUMENT)
// 🧱 STEP 1 — Project folder setup
// mkdir -p ~/projects/laravel-docker
// cd ~/projects/laravel-docker
// 📦 STEP 2 — Laravel install (via Docker Composer)
// docker run --rm -v $(pwd):/app composer create-project laravel/laravel laravel-app
// ✔ Result:
// Laravel project created inside:
// laravel-app/
// 🐳 STEP 3 — Docker structure create
// mkdir -p docker/php
// mkdir -p docker/nginx
// 📄 STEP 4 — PHP Dockerfile
// nano docker/php/Dockerfile
// 🌐 STEP 5 — Nginx config
// nano docker/nginx/default.conf
// ⚙️ STEP 6 — docker-compose file
// nano docker-compose.yml
// 🚀 STEP 7 — Docker start command (IMPORTANT)
// docker compose up -d --build
// ✔ This command:
// builds PHP container
// pulls nginx
// starts MySQL
// runs Laravel stack
// 🧠 STEP 8 — check running containers
// docker ps
// 🌍 STEP 9 — browser open
// http://localhost:8080
// 🧠 💡 COMPLETE FLOW (VERY IMPORTANT)
// Laravel install (composer container)
//         ↓
// Docker files create
//         ↓
// docker compose up
//         ↓
// Nginx + PHP + MySQL start
//         ↓
// Browser access localhost:8080
// 📂 IMPORTANT STRUCTURE
// laravel-docker/
// │
// ├── docker-compose.yml
// ├── docker/
// │   ├── php/Dockerfile
// │   └── nginx/default.conf
// │
// └── laravel-app/   ← Laravel code
// ⚠️ KEY RULES

// ✔ Always run command in:

// laravel-docker/




// DIRECTORY (FOLDER) COMMANDS
// pwd

// 👉 current location check

// ls

// 👉 folder files list

// cd folder_name

// 👉 folder ke andar jana

// cd ..

// 👉 back jana (1 level up)

// cd ~

// 👉 home directory

// cd /path/to/folder

// 👉 direct path pe jana

// 🐳 🚀 DOCKER BASIC COMMANDS (IMPORTANT)
// docker ps

// 👉 running containers

// docker ps -a

// 👉 all containers

// docker images

// 👉 images list

// docker logs container_name

// 👉 logs check

// docker exec -it container_name bash

// 👉 container ke andar jana

// 🚀 DOCKER COMPOSE COMMANDS
// docker compose up -d

// 👉 start containers

// docker compose up -d --build

// 👉 build + start (IMPORTANT)

// docker compose down

// 👉 stop + remove containers

// docker compose restart

// 👉 restart containers

// docker compose ps

// 👉 status check

// 📁 🧠 REAL WORKFLOW (TUMHARA CASE)
// cd ~/projects/laravel-docker
// docker compose up -d --build
// docker ps

docker compose exec app bash






// Example 
# Dockerfile

# Base image
FROM php:8.2-fpm

# System packages install
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev

# PHP extensions install
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Composer copy
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Working directory
WORKDIR /var/www

# Project files copy
COPY . .

# Laravel dependencies install
RUN composer install

# Permissions
RUN chmod -R 777 storage bootstrap/cache

# Port expose
EXPOSE 9000

# PHP-FPM start
CMD ["php-fpm"]



# docker-compose.yml

version: '3.8'

services:

  app:
    build:
      context: .
      dockerfile: Dockerfile

    container_name: laravel_app

    working_dir: /var/www

    volumes:
      - ./:/var/www

    ports:
      - "9000:9000"

    depends_on:
      - mysql

  mysql:
    image: mysql:8.0

    container_name: laravel_mysql

    restart: always

    environment:
      MYSQL_DATABASE: laravel
      MYSQL_ROOT_PASSWORD: root
      MYSQL_PASSWORD: root
      MYSQL_USER: laravel

    ports:
      - "3306:3306"

    volumes:
      - mysql_data:/var/lib/mysql

volumes:
  mysql_data:



  # Build containers
docker compose build

# Start containers
docker compose up -d

# Enter app container
docker compose exec app bash

# Laravel migrate
php artisan migrate




// Project ka structure banayein:
// Sab se pehle ek folder banayein jisme aap ki Dockerfile, docker-compose file aur project code hoga. 
// Yani ek central folder mein sari cheezein rakhein.
// Dockerfile likhein:
// Is file mein apni base image select karein (misal ke taur par PHP ke liye php:8.1-fpm)
// ۔ Phir tamam zaroori installations karein, jaise Composer, packages, aur project files. Misal ke taur par pehle updates karein, phir Composer install karein, aur apna code copy karein.
// docker-compose.yml banayein:
// Is mein apni tamam services (jaise web server, database) define karein. Har service ko naam dein,
//  konsa image use hoga, konsi ports open hongi, aur agar database hai to us ki configuration bhi dein (jaise MySQL ya PostgreSQL).
// Local development:
// Terminal mein project directory mein ja kar docker-compose up --build chalayein. 
// Yeh local tor par aap ki services run karega. Aap browser mein localhost par project dekh sakenge.
// Environment configuration:
// Local aur production dono ke liye alag .env files banayein taake secure strings, 
// database username, password waghera alag se set ho sakein. Production mein secure tareeqe se environment variables use karein.
// Production deployment:
// Jab aap production mein jayein to apni docker image ko kisi registry (jaise Docker Hub ya private registry)
//  mein push kar sakte hain. Phir production server par docker-compose ko run karein aur ensure karein ke ports aur services properly secure hon.
// Security aur monitoring:
// Production mein security ka khas khayal rakhein, jaise HTTPS, firewall settings, aur logs.
//  Aap monitoring aur logging tools bhi use kar sakte hain taake agar koi issue aaye to foran pata chal sake.



sudo apt update -y && \
sudo apt install -y docker.io docker-compose-plugin && \
sudo systemctl enable docker && \
sudo systemctl start docker && \
docker --version && docker compose version

// Docker Basic Commands List
# ----------------------------
# CHECK / INFO (Docker ki info check karna)
# ----------------------------
docker --version              # Docker ka version check karna
docker compose version        # Docker compose version check karna
docker info                   # Docker system ki full info

# ----------------------------
# IMAGES (Docker images manage karna)
# ----------------------------
docker images                # Sab images list dekhna
docker build -t myapp .      # Image build karna (Dockerfile se)
docker pull ubuntu           # Docker Hub se image download karna
docker rmi image_name        # Image delete karna

# ----------------------------
# CONTAINERS (Running apps manage karna)
# ----------------------------
docker ps                    # Running containers list
docker ps -a                 # All containers (running + stopped)
docker run ubuntu            # Simple container run karna
docker run -it ubuntu bash   # Interactive mode (terminal ke andar jana)
docker run -d nginx          # Background mein run karna
docker run -p 8000:80 nginx  # Port mapping (browser access)

docker stop container_id     # Container stop karna
docker start container_id    # Container dobara start karna
docker restart container_id   # Restart container
docker rm container_id       # Container delete karna
docker rm -f container_id    # Force delete container

# ----------------------------
# ACCESS CONTAINER (andar enter hona)
# ----------------------------
docker exec -it container_id bash   # Container ke andar bash open
docker exec -it container_id sh     # Agar bash na ho to sh use karo

# ----------------------------
# LOGS (errors ya output dekhna)
# ----------------------------
docker logs container_id     # Logs dekhna
docker logs -f container_id  # Live logs dekhna (real-time)

# ----------------------------
# SYSTEM / CLEANUP (space clear karna)
# ----------------------------
docker stats                 # CPU/RAM usage dekhna
docker system prune         # Unused data delete karna
docker system prune -a      # Sab unused images + containers delete
docker container prune      # Sirf stopped containers delete
docker image prune          # Unused images delete

# ----------------------------
# COMPOSE (MULTI CONTAINER apps)
# ----------------------------
docker compose up            # Containers start karna
docker compose up --build    # Build + run
docker compose up -d         # Background mein run
docker compose down          # Sab containers stop + remove
docker compose restart       # Restart services
docker compose ps            # Compose containers list
docker compose logs          # All logs
docker compose logs app      # Specific service logs

# Laravel specific commands
docker compose exec app bash                 # Laravel container ke andar jana
docker compose exec app php artisan migrate  # Database migrate karna
docker compose exec app php artisan serve    # Laravel server run
docker compose exec app composer install     # Dependencies install

# ----------------------------
# VOLUMES (data save karna)
# ----------------------------
docker volume ls             # Volumes list
docker volume create myvolume # New volume banana
docker volume rm myvolume    # Volume delete karna

# ----------------------------
# NETWORK (containers connect karna)
# ----------------------------
docker network ls            # Networks list
docker network create mynetwork # New network banana

# ----------------------------
# COPY FILES (host ↔ container)
# ----------------------------
docker cp file.txt container_id:/tmp   # File container mein copy
docker cp container_id:/tmp/file.txt .  # Container se file host par

# ----------------------------
# DOCKERFILE BASIC (image banane ke rules)
# ----------------------------
# FROM php:8.2-fpm          -> Base image (PHP install hota hai)
# WORKDIR /var/www/html     -> Working folder set karna
# COPY . .                  -> Code container mein copy karna
# RUN composer install      -> Dependencies install karna
# EXPOSE 9000              -> Port open karna
# CMD ["php-fpm"]          -> Container start command