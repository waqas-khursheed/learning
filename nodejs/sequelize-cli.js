
// | Feature | sequelize | sequelize - cli |
// | -------- | -------------------- | ---------------------------- |
// | Type | Library | CLI Tool |
// | Use | Code me use hota hai | Terminal commands |
// | Work | Database queries | Migrations & models generate |
// | Required | Yes | Optional but recommended |


// Sequelize CLI Complete Commands List

// npx sequelize-cli init
// Project structure banata hai: models/, migrations/, seeders/, config/.


// Database Commands

// Create Database

// npx sequelize-cli db:create

// Drop Database

// npx sequelize-cli db:drop

// Migrate Database (Run Migrations)

// npx sequelize-cli db:migrate
//  npx sequelize-cli db:migrate --debug
// Rollback Last Migration

// npx sequelize-cli db:migrate:undo

// Rollback All Migrations

// npx sequelize-cli db:migrate:undo:all

// Check Migration Status

// npx sequelize-cli db:migrate:status

// Run Migrations in Specific Environment

// npx sequelize-cli db:migrate --env production


// Model Commands

// Generate Model + Migration

// npx sequelize-cli model:generate --name User --attributes name:string,email:string,password:string

// Generate Model Only

// npx sequelize-cli model:generate --name Post --attributes title:string,content:text
// 4️⃣ Migration Commands

// Generate Migration

// npx sequelize-cli migration:generate --name add-status-to-user

// Run Specific Migration

// npx sequelize-cli db:migrate --to XXXXXXXXXXXXXX-add-status-to-user.js

// Undo Specific Migration

// npx sequelize-cli db:migrate:undo --to XXXXXXXXXXXXXX-add-status-to-user.js
// 5️⃣ Seeder Commands

// Generate Seeder

// npx sequelize-cli seed:generate --name demo-user

// Run All Seeders

// npx sequelize-cli db:seed:all

// Run Specific Seeder

// npx sequelize-cli db:seed --seed 202401010101-demo-user.js

// Undo Last Seeder

// npx sequelize-cli db:seed:undo

// Undo All Seeders

// npx sequelize-cli db:seed:undo:all
// 6️⃣ Other Useful Commands

// List CLI Commands

// npx sequelize-cli --help

// Version Check

// npx sequelize-cli --version


// Quick Reference Table

// | Type      | Command                  | Purpose                   |
// | --------- | ------------------------ | ------------------------- |
// | Init      | `npx sequelize-cli init` | Project structure create  |
// | DB        | `db:create`              | Create database           |
// | DB        | `db:drop`                | Drop database             |
// | Migration | `db:migrate`             | Run migrations            |
// | Migration | `db:migrate:undo`        | Undo last migration       |
// | Migration | `db:migrate:undo:all`    | Undo all migrations       |
// | Migration | `db:migrate:status`      | Check migration status    |
// | Migration | `migration:generate`     | Create new migration file |
// | Model     | `model:generate`         | Create model + migration  |
// | Seeder    | `seed:generate`          | Create new seeder         |
// | Seeder    | `db:seed:all`            | Run all seeders           |
// | Seeder    | `db:seed`                | Run specific seeder       |
// | Seeder    | `db:seed:undo`           | Undo last seeder          |
// | Seeder    | `db:seed:undo:all`       | Undo all seeders          |
// | CLI       | `--help`                 | Show CLI commands         |
// | CLI       | `--version`              | Show CLI version          |


// npm vs npx basic farq

// | Feature        | `npm`                                     | `npx`                                                       |
// | -------------- | ----------------------------------------- | ----------------------------------------------------------- |
// | Full form      | Node Package Manager                      | Node Package Execute                                        |
// | Purpose        | Packages install/run/manage karne ke liye | Direct package run karne ke liye bina install kiye globally |
// | Global install | `npm install -g package` required         | No need to install globally                                 |
// | Local package  | Runs from `node_modules/.bin/`            | Runs directly, even agar local ho ya global                 |


// | Feature        | `npm`                                          | `npx`                                                                    |
// | -------------- | ---------------------------------------------- | ------------------------------------------------------------------------ |
// | Purpose        | Packages install, manage aur run karne ke liye | Packages **directly run** karne ke liye (without global install)         |
// | Global install | Required agar CLI run karna hai                | Usually **no global install needed**                                     |
// | Local install  | Install karta hai `node_modules` me            | Run karta hai **existing local package** ya temporary download karta hai |
// | Offline        | Local package ho → yes                         | Local package ho → yes; local nahi → fail                                |
// | Use case       | `npm install package`                          | `npx package command` (run without installing globally)                  |
// | Example        | `npm install -g sequelize-cli`                 | `npx sequelize-cli db:migrate`                                           |


// Simple rule:

// npm → install/manage packages

// npx → run package commands easily

// | Feature              | `npm install`                                                   | `npx`                                                                                   |
// | -------------------- | --------------------------------------------------------------- | --------------------------------------------------------------------------------------- |
// | Purpose              | Packages **install karna** (local ya global)                    | Packages **run karna** bina globally install kiye                                       |
// | Node_modules me save | ✅ Local install: `node_modules/` <br> Global install: OS folder | ❌ npx **temporary download** karta hai agar local exist nahi, project me save nahi hota |
// | package.json me save | ✅ Yes (dependencies ya devDependencies)                         | ❌ No, package.json update nahi hota                                                     |
// | Internet required    | ✅ Agar package local me nahi hai                                | ✅ Sirf temporary download ke liye, agar local exist nahi                                |
// | CLI tool use         | ✅ Global install se direct command line                         | ✅ Run command directly, local or cache se                                               |
