# ContentMakers Backend — Development Standards

## Table of Contents

1. [Project Overview](#project-overview)
2. [Technology Stack](#technology-stack)
3. [Backend Standards](#backend-standards)
4. [Authentication](#authentication)
5. [Authorization](#authorization)
6. [Database](#database)
7. [File Storage Architecture](#file-storage-architecture)
8. [Security](#security)
9. [Queues & Jobs](#queues--jobs)
10. [Notifications](#notifications)
11. [Performance](#performance)
12. [API Standards](#api-standards)
13. [Cache](#cache)
14. [Code Quality](#code-quality)
15. [Before Writing Code](#before-writing-code)
16. [General Rule](#general-rule)

---

## Project Overview

This project will be developed using **modern Laravel best practices** with a strong focus on:

- ✅ Security
- ✅ Maintainability
- ✅ Scalability
- ✅ Clean Architecture

---

## Technology Stack

| Technology | Version |
| --- | --- |
| Laravel | 13 |
| PHP | 8.4 |
| Database | PostgreSQL |
| Package Manager | Composer |
| Node.js | Latest LTS |
| Bundler | Vite |
| CSS Framework | Tailwind CSS |

> **Note:** Always assume these versions unless explicitly instructed otherwise.

---

## Backend Standards

Follow **Laravel's official best practices** throughout the entire codebase.

| Layer | Responsibility |
| --- | --- |
| Controller | Thin — only receives requests and returns responses |
| Form Request | Handles all input validation |
| Service Class | Contains complex business logic |
| API Resource | Formats and structures all API responses |
| Eloquent ORM | All database interactions |
| Policy / Gate | All authorization logic |

### Key Rules

- Use **Eloquent ORM** for all database interactions.
- Use **Form Requests** for validation — never validate inside controllers.
- Use **API Resources** for all API responses — never return raw models.
- Use **Service classes** when business logic becomes complex.
- Keep **Controllers thin** — delegate logic to services.
- Use **Policies and Gates** for all authorization.
- Follow **SOLID principles** in every class and module.
- Follow **PSR-12** coding standards for consistent formatting.
- Write **clean, readable, and maintainable** code at all times.

### Model Rules

- Every model **must** define its relationships (`hasMany`, `belongsTo`, `belongsToMany`, etc.) as proper Eloquent relationship methods — never fetch related data with manual queries when a relationship method should exist.
- Controllers and Services **must eager load** relationships they will use (`with()`, `load()`) instead of relying on lazy loading. This prevents N+1 query problems (see [Performance](#performance)).
- On `create()` or `insert()`, only fields listed in the model's `$fillable` array may be mass-assigned. Never bypass this with `$guarded = []` or `forceFill()` unless there is a specific, documented reason.

---

## Authentication

Authentication must use **JWT (JSON Web Token)**.

### Requirements

- [x] Secure login and logout
- [x] Refresh token support where appropriate
- [x] Password reset functionality
- [x] Email verification
- [x] Secure password hashing using Laravel defaults (`bcrypt`)
- [x] All private endpoints protected using **JWT middleware**

### Flow Overview

```
Client → POST /login → JWT issued
Client → Authorization: Bearer <token> → Access protected routes
Client → POST /refresh → New JWT issued
Client → POST /logout → Token invalidated
```

---

## Authorization

Authorization must use **Role-Based Access Control (RBAC)**.

### Requirements

- [x] Roles
- [x] Permissions
- [x] Role ↔ Permission relationships
- [x] User ↔ Role relationships
- [x] Middleware for permission checks
- [x] Policies where appropriate

### Database Design

```
users
  └── user_role        (pivot)
        └── roles
              └── role_permission  (pivot)
                    └── permissions
```

> Design must be **flexible and easily extendable** for future roles and permissions.

---

## Database

The database engine is **PostgreSQL**.

### Requirements

| Requirement | Description |
| --- | --- |
| Foreign Keys | Enforce referential integrity on all relationships |
| Cascading Rules | Apply `ON DELETE CASCADE` or `RESTRICT` where appropriate |
| Indexes | Add indexes on frequently queried columns |
| Unique Constraints | Enforce uniqueness at the database level |
| Transactions | Wrap critical multi-step operations in transactions |
| Normalization | Normalize tables to reduce redundancy |
| Efficient Design | Avoid over-engineering; keep schema clean and minimal |

### Enums

- Every field that represents a fixed set of states (e.g. `status`, `type`, `role`) **must** be backed by a native PHP `enum` (PHP 8.1+ backed enum), defined in its own class under `app/Enums/`.
- The database column itself **must** use PostgreSQL's native `ENUM` type, not a plain `string`/`varchar` column. The list of allowed values must be enforced at the database level, matching the PHP Enum's cases exactly — not left as free text.
- Never scatter raw string literals (`'pending'`, `'approved'`, ...) across controllers, services, or seeders. Always reference the Enum case (e.g. `ApplicationStatus::Pending`).
- Both layers must always stay in sync: adding, renaming, or removing a case requires updating the PHP Enum class **and** a migration that alters the PostgreSQL enum type.

---

## File Storage Architecture

Use Laravel's **Filesystem abstraction** with multiple **disks**. Code never talks to a specific provider — it talks to a _disk name_ that comes from configuration (and from the database, per file).

| Disk | Purpose | Web-accessible? | Auth required? |
| --- | --- | :-: | :-: |
| `public` | Non-sensitive assets: avatars, public logos | Yes (direct URL / CDN) | No |
| `private` (`local`) | Sensitive docs: employee contracts, customer docs, invoices | No | Yes (Policy) |

---

## Security

> ⚠️ **Security is a top priority. Never bypass security for convenience.**

### Security Checklist

| Threat | Prevention |
| --- | --- |
| CSRF | Laravel CSRF middleware (enabled by default) |
| SQL Injection | Eloquent ORM & query bindings |
| XSS | Blade auto-escaping `{{ }}` |
| Mass Assignment | `$fillable` / `$guarded` on all models |
| Weak Passwords | Laravel Hashing (`bcrypt`) |
| Unauthorized Access | Middleware + Policies + Gates |
| Unauthenticated Access | JWT middleware on all private routes |
| Abuse / DDoS | Rate Limiting via `throttle` middleware |
| Insecure File Uploads | Validate MIME types, size, and storage location |
| Exposed Secrets | Environment variables via `.env` (never commit) |
| Sensitive Data Exposure | Encrypt sensitive fields; avoid logging secrets |

---

## Queues & Jobs

> Long-running operations must **never block user requests**.

Use **Redis** as the queue driver.

### When to Use Queues

- Sending emails
- Sending notifications
- Report generation
- File and image processing
- Heavy database operations
- Any background task
- Third-party API integrations

### Job Design Rules

- Jobs must be **retryable** — define `$tries` and `$backoff`.
- Jobs must be **failure-safe** — implement `failed()` method.
- Jobs should be **idempotent** — safe to run more than once if needed.

---

## Notifications

Use **Laravel Notifications** for all notification logic.

### Supported Channels

| Channel | Use Case |
| --- | --- |
| Database | In-app notification center |
| Email | Transactional emails |
| Broadcast | Real-time notifications via WebSocket |

### Rules

- Notification logic must be **clean and reusable**.
- Each notification class handles **one notification type**.
- Notifications should be dispatched via **queues** to avoid blocking.

---

## Performance

Optimize performance at every layer.

### Strategies

| Strategy | Purpose |
| --- | --- |
| Eager Loading | Prevent N+1 query problems |
| Database Indexing | Speed up frequent queries |
| Caching | Reduce repeated database hits |
| Queue Workers | Offload heavy tasks from the request cycle |
| Lazy Collections | Handle large datasets without memory overflow |
| Efficient Queries | Select only needed columns; avoid `SELECT *` |

> **Eager Loading rule:** Any time a model or collection is returned to a Controller, Service, or API Resource and its relationships will be accessed (directly or in Blade/JSON), those relationships must be eager loaded with `with()` beforehand. Never trigger relationship loading inside a loop.

---

## API Standards

All APIs must follow **RESTful conventions** and return **consistent JSON responses**.

### Requirements

- [x] RESTful route naming conventions
- [x] Consistent JSON response structure
- [x] API Resources for all responses
- [x] Proper HTTP status codes
- [x] Pagination for large datasets
- [x] Graceful exception handling

### Response Structure

```json
{
  "status": "success",
  "message": "User retrieved successfully.",
  "data": {},
  "meta": {
    "current_page": 1,
    "total": 100
  },
  "errors": {}
}
```

### HTTP Status Codes

| Code | Meaning |
| --- | --- |
| 200 | OK |
| 201 | Created |
| 204 | No Content (delete) |
| 400 | Bad Request |
| 401 | Unauthenticated |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 500 | Internal Server Error |

---

## Cache

The application must use **Redis** as the primary caching solution.

### Requirements

| Requirement | Description |
| --- | --- |
| Primary Driver | Redis for all application caching |
| Laravel Cache Abstraction | Use `Cache::` facade — never interact with Redis directly |
| TTL (Expiration) | Apply appropriate expiration to all cached data |
| Cache Invalidation | Automatically invalidate cache when related data changes |
| Cache Tags | Use tags when supported and appropriate for grouped invalidation |
| Sensitive Data | Never cache sensitive or user-specific data unless properly isolated |
| Expensive Queries | Cache heavy database queries and frequently requested resources |

### Rules

- [x] Use Redis as the cache driver (`CACHE_DRIVER=redis` in `.env`)
- [x] Always use **Laravel's Cache abstraction** (`Cache::remember`, `Cache::forget`, etc.)
- [x] Set meaningful **TTL values** — never cache indefinitely without reason
- [x] Invalidate cache on **model updates and deletes** using observers or events
- [x] Use **cache tags** for grouped invalidation when Redis supports it
- [x] Follow **Laravel's caching best practices** at all times

---

## Code Quality

> Write code you would be proud to review six months from now.

### Principles

| Principle | Description |
| --- | --- |
| Reusability | Extract shared logic into helpers, traits, or services |
| Clean Architecture | Separate concerns — each class has one clear purpose |
| Maintainability | Code should be easy to change without breaking things |
| Readability | Name things clearly; avoid cryptic abbreviations |
| Small Focused Classes | One class, one responsibility (SRP from SOLID) |
| Dependency Injection | Inject dependencies; never instantiate them directly |

### Naming & Style Rules

- Function and method names **must clearly describe what they do** (e.g. `calculateFinalGrade()`, not `process()` or `handle2()`).
- Code must read as **human-written, plain, and straightforward** — no unnecessary abstraction layers, no over-commented obvious lines, no verbose boilerplate added just to look thorough.
- Prefer the simplest correct solution over a clever one. If two approaches are equally correct, choose the one a mid-level developer can understand in under a minute.

### What to Avoid

- ❌ Duplicated logic — extract it, don't copy it.
- ❌ Fat controllers — move logic to services.
- ❌ God classes — split large classes into focused ones.
- ❌ Magic numbers or strings — use constants, config values, or Enums (see [Enums](#enums)).
- ❌ Vague function names (`doStuff()`, `handleData()`) — always name for intent.
- ❌ Over-explained, template-like code with excessive inline comments restating what the code already says.

---

## Before Writing Code

Before implementing **any** feature, follow this checklist:

1. **Analyze the requirement** — fully understand what is needed.
2. **Choose the best Laravel approach** — use the right tool for the job.
3. **Consider scalability** — will this work at 10x the current load?
4. **Consider security** — could this be exploited?
5. **Consider performance** — are there N+1 risks or heavy queries?
6. **Follow Laravel conventions** — don't reinvent the wheel.

> If there are multiple valid implementations, always choose the most **maintainable** and **production-ready** solution.

---

## General Rule

> Every piece of code should be written **as if it will be deployed to production**.

### Priority Order

| Priority | Value |
| --- | --- |
| 1st | 🔒 Security |
| 2nd | ⚡ Performance |
| 3rd | 📈 Scalability |
| 4th | 🛠️ Maintainability |
| 5th | 📖 Readability |
| 6th | ✅ Laravel Best Practices |
