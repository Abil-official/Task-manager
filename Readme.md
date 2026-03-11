# 🚀 Task Manager

A premium, state-of-the-art task management application built with **Laravel 12**, **React 19**, and **Tailwind CSS 4**. This project showcases a modern development workflow, implementing the Service/Repository pattern and advanced caching strategies with Redis.

## ✨ Features

- **Intuitive Task Tracking**: Create, view, and manage tasks with ease.
- **Multi-Executor Support**: Assign multiple users to a single task with individual status tracking.
- **Advanced Caching**: Lightning-fast performance using Redis-backed caching for task lists and detail views.
- **Premium UI/UX**: Built with React 19 and Tailwind CSS 4 for a smooth, responsive, and visually stunning experience.
- **Activity Logging**: Comprehensive logs for every task action (creation, updates, status changes).
- **Inertia.js Integration**: Seamless SPA-like feel while maintaining the power of PHP on the backend.

## 🛠 Tech Stack

- **Backend**: PHP 8.2+ / Laravel 12
- **Frontend**: React 19 / Inertia.js / TypeScript
- **Styling**: Tailwind CSS 4
- **Database**: PostgreSQL / MySQL / SQLite
- **Caching**: Redis (Predis)
- **Tooling**: Vite, ESLint, Prettier, Pint

## 🏗 Architecture

The project follows a robust architectural pattern to ensure scalability and maintainability:

- **Controllers**: Thin controllers handling request/response logic.
- **Services**: Business logic encapsulation.
- **Repositories**: Data access layer abstraction.
- **Caching Layer**: Proactive caching and invalidation using the Laravel `Cache` facade.

## 🚀 Getting Started

### Prerequisites

- PHP 8.2 or higher
- Node.js & npm
- Composer
- Redis server (optional, but recommended for caching)

### Installation

The project includes a streamlined setup script:

```bash
composer run setup
```

This command will:
1. Install PHP dependencies.
2. Create your `.env` file and generate an application key.
3. Run database migrations.
4. Install Node dependencies.
5. Build the frontend assets.

### Development

To start the development server with Hot Module Replacement (HMR) and queue listeners:

```bash
composer run dev
```

## 🔌 API Reference

The application provides a JSON endpoint for task discovery:

- **GET `/tasks/json`**: Retrieve paginated tasks with filters support (`search`, `sort_by`, `limit`).

## 📜 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
