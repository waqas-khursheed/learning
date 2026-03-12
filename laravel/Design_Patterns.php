<?php 


// Laravel me use hone wale Common Design Patterns

// Laravel khud object-oriented design patterns par bana hai
// (especially Service Container, Facades, Repositories, etc).
// Yahan practical categorized list di gayi hai 👇

// 🔹 1. Creational Patterns (Object create karne ke tarike)

// | Pattern              | Laravel Example                        | Explanation                                                       |
// | -------------------- | -------------------------------------- | ----------------------------------------------------------------- |
// | **Singleton**        | `App::make()`, `Config`, `DB`          | Ek hi instance throughout app (e.g. Database Connection)          |
// | **Factory Method**   | `Model::factory()`, `Cache::store()`   | Object creation delegate hota hai subclasses ya helper methods ko |
// | **Abstract Factory** | Different drivers (Mail, Cache, Queue) | Related objects banata hai without specifying class               |
// | **Builder**          | `Query Builder`, `FormBuilder`         | Complex object ko step-by-step build karna                        |
// | **Prototype**        | `clone()` karna on model instances     | Object cloning ka mechanism                                       |


// 2. Structural Patterns (Code ko organize karne ke tarike)

// | Pattern       | Laravel Example                                  | Explanation                                        |
// | ------------- | ------------------------------------------------ | -------------------------------------------------- |
// | **Facade**    | `Route`, `Cache`, `DB`, `Auth`                   | Static interface for complex subsystems            |
// | **Decorator** | Middleware layers                                | Functionality extend karna without modifying class |
// | **Adapter**   | `MailDriver`, `CacheDriver`, `FilesystemAdapter` | Different APIs ko uniform interface dena           |
// | **Composite** | Collections, nested components                   | Tree-like structure (e.g. Blade components)        |
// | **Proxy**     | Lazy loading in Eloquent relationships           | Access control / deferred loading                  |


// 3. Behavioral Patterns (Object interaction & logic flow)

// | Pattern                     | Laravel Example                              | Explanation                                              |
// | --------------------------- | -------------------------------------------- | -------------------------------------------------------- |
// | **Observer**                | `Model::observe()`, Events & Listeners       | Event-driven communication                               |
// | **Strategy**                | Multiple Payment/Cache Drivers               | Interchangeable algorithms (e.g. PayPal vs Stripe)       |
// | **Command**                 | `Artisan Commands`, `Jobs`, `Queue`          | Request ko object me wrap karna                          |
// | **Chain of Responsibility** | Middleware Stack                             | Sequential request handling                              |
// | **Template Method**         | `Illuminate\Foundation\Console\Command`      | Base class me skeleton define, child override karte hain |
// | **State**                   | Order status system                          | Object behavior depends on internal state                |
// | **Iterator**                | Collections (`foreach($users as $user)`)     | Sequential traversal of elements                         |
// | **Mediator**                | Event Dispatcher                             | Central communication hub                                |
// | **Memento**                 | Old model version (auditing)                 | Object state restore karna                               |
// | **Visitor**                 | Collection transformations (`map`, `filter`) | Operations ko elements se separate karna                 |


// 4. Architectural Patterns (High-level structure)

// | Pattern                                             | Use in Laravel                  | Purpose                              |
// | --------------------------------------------------- | ------------------------------- | ------------------------------------ |
// | **MVC (Model-View-Controller)**                     | Laravel core structure          | Separate data, UI, and logic         |
// | **Repository Pattern**                              | Custom Data Layer               | DB logic isolate from controller     |
// | **Service Layer Pattern**                           | Business Logic isolate          | Controller se heavy logic alag karna |
// | **Dependency Injection (DI)**                       | Automatic via Service Container | Loosely coupled code                 |
// | **Domain-Driven Design (DDD)**                      | Large Laravel apps              | Business domain based structure      |
// | **Event-Driven Architecture**                       | Events, Listeners, Jobs         | Loose coupling between modules       |
// | **CQRS (Command Query Responsibility Segregation)** | Custom pattern for reads/writes | Separate query and write logic       |
// | **Hexagonal / Clean Architecture**                  | Service + Repository + DTO      | Framework-independent core design    |
// | **Decorator Architecture**                          | Middleware chain                | Request/response processing          |


// 🔹 5. Laravel-specific Internal Patterns

// | Pattern                     | Example                                          |
// | --------------------------- | ------------------------------------------------ |
// | **Service Container / IoC** | Dependency resolve automatically                 |
// | **Service Providers**       | Bindings and bootstrapping of app components     |
// | **Facades**                 | Static proxies to underlying services            |
// | **Macroable Trait**         | Runtime method extension                         |
// | **Pipeline Pattern**        | Request → Middleware → Response                  |
// | **Policy Pattern**          | Authorization via separate classes               |
// | **Provider Pattern**        | Mail, Queue, Cache providers register hotay hain |


// 6. Bonus – Commonly Used in Real Projects

// | Purpose                | Recommended Pattern                |
// | ---------------------- | ---------------------------------- |
// | Data Access Layer      | Repository + Specification         |
// | Business Logic Layer   | Service Pattern                    |
// | Asynchronous Work      | Command / Job Pattern              |
// | Validation Flow        | Strategy / Chain of Responsibility |
// | Notifications / Events | Observer Pattern                   |
// | Payment Gateways       | Strategy Pattern                   |
// | Logging / Monitoring   | Decorator Pattern                  |
// | Caching System         | Proxy Pattern                      |


// 7. Learning Order (Recommended Path)

// MVC + Service + Repository

// Observer + Strategy + Command

// Decorator + Chain of Responsibility (Middleware)

// Event-driven + CQRS + DDD (Advanced)