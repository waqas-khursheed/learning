<?php 


// 1. “Architecture” hoti kya hai?
// Software Architecture ka matlab hai:
// “System kis tarah design aur organize hoga — uske modules, data flow, communication, scalability, aur maintainability ka overall structure.”
// Simple lafzon mein:
// Code likhna = Implementation
// Code ka structure sochna = Architecture

// Example:
// Laravel project likhna ek cheez hai,
// lekin decide karna ke:

// Controller, Service, Repository, Model kis tarah separate hon
// Data flow kis route se guzrega
// Scaling ke liye microservices chahiye ya monolith
// Queue system, cache, aur database replication kahan use hoga
// yeh sab Architecture hai.

// 🔹 2. Developer vs. Architect mindset
// Role	Focus
// Junior Developer	"Kaise code likhun ke feature kaam kare?"
// Senior Developer	"Kaise code likhun ke maintainable aur scalable ho?"
// Architect	"Kaise system design karun ke long-term me reliable, secure aur performant rahe?"
// 🔹 3. Architecture ke major concepts jo samajhne chahiye

// System Design Fundamentals

// Scalability (Horizontal vs Vertical)
// Load Balancing
// Caching (Redis, Memcached)
// Queue Systems (RabbitMQ, SQS)
// CDN (CloudFront, Cloudflare)
// Database Sharding & Replication
// API Gateway, Microservices, Service Discovery
// Code Architecture Patterns

// MVC (Laravel)

// Service Layer / Repository Pattern
// Event-Driven Architecture
// Domain-Driven Design (DDD)
// Clean Architecture / Hexagonal Architecture

// Data & Communication
// RESTful APIs, GraphQL, gRPC
// WebSockets / Real-time data
// Message Brokers (Kafka, Redis Streams)
// Security & Authentication
// JWT, OAuth2
// Encryption, Hashing, CSRF, XSS protection
// Role-Based Access Control (RBAC)
// DevOps / Deployment
// Docker, Kubernetes basics
// CI/CD pipelines
// Cloud architecture (AWS, GCP, Azure)
// Environment variables, config management
// Testing & Observability
// Unit, Integration, E2E Testing
// Logging, Monitoring (Prometheus, Grafana)
// Error tracking (Sentry, Bugsnag)

// 🔹 4. Kis cheez par zyada focus karna chahiye (as a backend developer)
// Agar tum Laravel background se ho (jaise tum ho ), to focus karo:
// Clean Code + Separation of Concerns
// Controllers me business logic mat likho.
// Use Service classes, Repositories, DTOs, Jobs.
// Scalable Architecture Sochna
// Jitni badi traffic ho sakti hai, us hisaab se design karo.
// Cache, queues aur async processing ka use seekho.
// System Design Thinking
// Har feature se pehle socho:
// “Agar ye 1 million users use karein to kya system handle karega?”
// Database Design
// Indexing, relationships, normalization vs denormalization
// Query optimization aur performance
// APIs aur Integration Design
// Versioning, rate limiting, and pagination ka sahi use
// Testing aur Deployment automation
// CI/CD aur test coverage seekho

// 5. Learning Roadmap (to become Senior / Architect)
// | Level       | Focus                                                          |
// | ----------- | -------------------------------------------------------------- |
// | **Step 1:** | Clean Laravel Codebase (SOLID principles)                      |
// | **Step 2:** | Service/Repository Pattern & Event-Driven Design               |
// | **Step 3:** | Advanced DB Design + Query Optimization                        |
// | **Step 4:** | Queues, Jobs, Caching, Redis                                   |
// | **Step 5:** | Docker & Cloud Deployment (AWS ECS/EKS or Forge)               |
// | **Step 6:** | System Design Concepts (Load balancing, scalability)           |
// | **Step 7:** | Architecture Patterns (Microservices, DDD, Clean Architecture) |

// 6. Bonus: Architect ki soch

// Jab koi problem mile, to ye 4 sawal khud se pucho:
// Scalability: Ye design traffic handle karega?
// Maintainability: Kya dusra developer samajh payega?
// Reliability: Agar ek service fail ho jaye to system chalega?
// Security: Kya isme koi vulnerability to nahi?