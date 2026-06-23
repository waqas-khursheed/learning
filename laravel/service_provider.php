<?php


// Service Provider Kya Hota Hai?

// Service Provider Laravel ka startup point hota hai jahan application boot hone par services register aur configure ki jati hain.

// Jab Laravel start hota hai to sab sy pehly Service Providers load hoty hain.


// Real Life Example

// Maan lo tumhara restaurant hai.

// Kitchen = Services
// Manager = Service Provider

// Manager decide karta hai:

// Kis employee ko hire karna hai
// Kis ko kis kaam par lagana hai
// Restaurant open hony par kya setup karna hai

// Laravel mai bhi Service Provider yahi kaam karta hai.

// Service Provider Kahan Hota Hai?

// Default provider:

// app/Providers/AppServiceProvider.php

// Laravel 12 mai bhi ye available hota hai.

// namespace App\Providers;

// use Illuminate\Support\ServiceProvider;

// class AppServiceProvider extends ServiceProvider
// {
//     public function register(): void
//     {
//         //
//     }

//     public function boot(): void
//     {
//         //
//     }
// }
// register() Method

// Yahan services register hoti hain.

// Example:

// public function register(): void
// {
//     $this->app->bind(
//         PaymentInterface::class,
//         StripePayment::class
//     );
// }

// Ab jahan bhi:

// PaymentInterface

// inject hoga Laravel automatically:

// StripePayment

// provide karega.

// boot() Method

// Jab tamam services register ho jati hain tab:

// boot()

// run hota hai.

// Example:

// public function boot(): void
// {
//     View::share('appName', 'My App');
// }

// Ab har view mai:

// {{ $appName }}

// available hoga.

// register() vs boot()


// | register()                  | boot()                      |
// | --------------------------- | --------------------------- |
// | Services register karta hai | Services use karta hai      |
// | Early stage                 | Late stage                  |
// | bind/singleton              | routes/views/events         |
// | Dependency use na karo      | Dependency use kar sakty ho |


// bind()

// Har request par naya object.

// $this->app->bind(
//     UserRepositoryInterface::class,
//     UserRepository::class
// );

// Har injection par:

// new UserRepository()

// banega.

// singleton()

// Puri request mai ek hi object.

// $this->app->singleton(
//     UserRepositoryInterface::class,
//     UserRepository::class
// );

// Ek hi instance reuse hoga.

// Practical Repository Example

// Interface:

// interface UserRepositoryInterface
// {
//     public function all();
// }

// Repository:

// class UserRepository implements UserRepositoryInterface
// {
//     public function all()
//     {
//         return User::all();
//     }
// }

// Provider:

// public function register(): void
// {
//     $this->app->bind(
//         UserRepositoryInterface::class,
//         UserRepository::class
//     );
// }

// Controller:

// class UserController extends Controller
// {
//     public function __construct(
//         private UserRepositoryInterface $userRepository
//     ) {}

//     public function index()
//     {
//         return $this->userRepository->all();
//     }
// }

// Laravel khud object inject kar dega.

// Custom Service Provider Banana

// Command:

// php artisan make:provider PaymentServiceProvider

// File:

// app/Providers/PaymentServiceProvider.php
// Service Provider Register Karna

// Laravel 12 mai:

// bootstrap/providers.php

// Mai add karo:

// return [
//     App\Providers\AppServiceProvider::class,
//     App\Providers\PaymentServiceProvider::class,
// ];
// Common Uses
// 1. Repository Binding
// $this->app->bind(
//     UserRepositoryInterface::class,
//     UserRepository::class
// );
// 2. Singleton Services
// $this->app->singleton(
//     PaymentGateway::class,
//     StripeGateway::class
// );
// 3. View Share
// View::share('company', 'ABC');
// 4. Custom Validation Rule
// Validator::extend(...);
// 5. Observer Register
// User::observe(UserObserver::class);
// 6. Event Listener Register
// Event::listen(...);
// 7. Macros Register
// Response::macro(...);
// Service Container aur Service Provider Relation

// Laravel mai do cheezen sath chalti hain:

// Service Container

// Object create karta hai.

// app()->make(UserRepository::class);
// Service Provider

// Container ko batata hai kya create karna hai.

// $this->app->bind(
//     UserRepositoryInterface::class,
//     UserRepository::class
// );

// Flow:

// Controller
//     ↓
// Interface mangta hai
//     ↓
// Container check karta hai
//     ↓
// Provider se mapping milti hai
//     ↓
// Repository object create hota hai
//     ↓
// Controller ko mil jata hai
// Senior Developers Service Provider Kab Use Karte Hain?
// Repository Pattern
// Service Pattern
// Payment Gateways
// Third Party SDK Registration
// Observer Registration
// Event Registration
// Global Configurations
// Multi-Tenant Applications
// Package Development
// Interview Question

// Q: Service Provider kya hai?

// Answer:

// Service Provider Laravel application ka bootstrap mechanism hai jo 
// services ko Service Container mai register karta hai aur application startup ke dauran configuration, 
// bindings, observers, events aur other services ko initialize karta hai.


// Senior Developer Rule

// register()

// Bindings
// Singletons
// Interfaces
// Service registration

// boot()

// Observers
// Events
// View sharing
// Route macros
// Validation rules
// Anything that depends on already-registered services
// Ek line mein

// register() mein services define/register hoti hain, jab ke boot() mein un services ko initialize ya 
// use kiya jata hai jab Laravel poori tarah load ho chuka ho.

// boot() Laravel ka lifecycle method hai jo tab run hota hai jab application
//  fully ready hoti hai, aur is ka use framework ke components
//  (models, events, views, routes, validation) ko initialize aur configure karne ke liye hota hai.


        //         ┌─────────────────────┐
        //         │   Laravel Start     │
        //         └─────────┬───────────┘
        //                   │
        //                   ▼
        // ┌──────────────────────────────┐
        // │  Service Providers Load      │
        // └─────────┬────────────────────┘
        //           │
        //           ▼
        // ┌──────────────────────────────┐
        // │      register() runs         │
        // │                              │
        // │ - bind services              │
        // │ - singleton services         │
        // │ - interfaces mapping         │
        // └─────────┬────────────────────┘
        //           │
        //           ▼
        // ┌──────────────────────────────┐
        // │ All Providers registered     │
        // └─────────┬────────────────────┘
        //           │
        //           ▼
        // ┌──────────────────────────────┐
        // │        boot() runs           │
        // │                              │
        // │ - Observers attach           │
        // │ - Events register            │
        // │ - View sharing               │
        // │ - Route macros              │
        // │ - Validation rules          │
        // └─────────┬────────────────────┘
        //           │
        //           ▼
        // ┌──────────────────────────────┐
        // │   Application Ready          │
        // │   (Request handling start)   │
        // └─────────┬────────────────────┘
        //           │
        //           ▼
        // ┌──────────────────────────────┐
        // │ Controller / Routes run      │
        // │ DB / Cache / Queue use       │
        // └──────────────────────────────┘