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