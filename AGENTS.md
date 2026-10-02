# Backend Engineering System Rules & AI Agent Instructions

> **Universal Core Mandate**: **ALWAYS WRITE CLEAN CODE.**
> Every line of code written in this repository must be purposeful, minimal, elegant, strictly typed, and free of clutter. Dead code, commented-out blocks, unused files, and redundant abstractions are strictly forbidden.

---

## 1. Non-Negotiable Backend Tenets

1. **Always Write Clean Code (Zero Bloat Policy)**:
   - Never introduce unnecessary files, placeholder folders, or dead code.
   - Delete any temporary scripts, debug artifacts, or unused imports immediately.
   - Keep files concise, focused, and aligned with the Single Responsibility Principle (SRP).
2. **Zero Inline Business Logic in Controllers**:
   - HTTP Controllers must only parse incoming requests/DTOs, delegate execution to a single dedicated Action, and return an API Resource or View.
   - Controllers exceeding 25 lines per method violate architectural standards.
3. **Stateless API vs Stateful Admin Isolation**:
   - API routes (`/api/v1/*`) are strictly stateless, authenticated via Bearer tokens, and MUST return JSON with explicit HTTP status codes (never redirect on unauthenticated 401).
   - Admin routes (`/admin/*`) are stateful, protected by session cookies, CSRF tokens, and render atomic Blade components (`<x-layout>`, `<x-card>`, `<x-table>`).
4. **Strict Typing Everywhere (PHP 8.4+)**:
   - `declare(strict_types=1);` on every PHP file.
   - All method arguments, properties, and returns must have explicit native types. No untyped parameters.
5. **No N+1 Queries & Safe Eloquent Access**:
   - Eloquent strict mode enabled in non-production environments (`preventLazyLoading`, `preventSilentlyDiscardingAttributes`, `preventAccessingMissingAttributes`).
   - All relations must be eager-loaded using `with()` or cached Redis projections.

---

## 2. Backend (Laravel 13 Octane / FrankenPHP) Architecture

### 2.1 Directory Structure & Boundaries
```
app/
├── Actions/{Domain}/          # Single-purpose invocable domain operations
├── DTOs/{Domain}/             # Readonly typed data transfer objects
├── Enums/                     # Backed PHP enums for domain states
├── Http/
│   ├── Controllers/
│   │   ├── Api/v1/            # API Responders (Stateless)
│   │   └── Admin/             # Blade Responders (Stateful)
│   ├── Requests/{Domain}/     # Form Requests (Validation & Authorization)
│   └── Resources/Api/v1/      # JSON API transformers
├── Jobs/{Domain}/             # Async queue jobs with backoff & retry
├── Models/                    # Strict Eloquent Entities (no raw query building)
├── Providers/                 # Application service providers (Strict Eloquent, Rate Limits)
└── Services/{Domain}/         # Infrastructure services (Redis caching, Payment gateways)
bootstrap/app.php              # Dual routing (stateless API v1 + stateful web admin)
database/migrations/           # Strictly typed migrations with composite indexes
resources/views/
├── components/                # Reusable Blade components (<x-layout>, <x-card>, <x-table>)
└── admin/                     # Admin views utilizing atomic Blade components
routes/
├── api.php                    # Stateless API routes
├── web.php                    # Stateful admin dashboard routes
└── console.php                # CLI commands
```

### 2.2 ADR & Action Design
- Every Action must have a single public method (either `execute(...)` or `__invoke(...)`).
- Database transactions (`DB::transaction(...)`) live inside Actions, never in controllers or jobs.
- Actions receive primitive types or validated DTOs, never `Illuminate\Http\Request` instances.

### 2.3 Database, Migrations & Indexes
- Foreign keys must always use `cascadeOnDelete()` or `restrictOnDelete()` explicitly.
- Add composite indexes for frequently combined queries (e.g., `['tenant_id', 'status', 'created_at']`).
- Use monetary values as 64-bit integers (cents) or strict `decimal(12, 4)`.

### 2.4 High-Performance Caching & Queues
- Use Redis via `phpredis` for all transient, high-read endpoints.
- High-concurrency read queries must use atomic locks (`Cache::lock(...)`) to prevent cache stampedes.
- Asynchronous queued jobs must implement `ShouldQueue`, explicit retry count (`$tries`), and backoff strategies (`$backoff`).

---

## 3. DRY Enforcement Matrix: When to Extract?

| Scenario | Clean Code Rule | Extraction Destination |
| :--- | :--- | :--- |
| Database query repeated in 2+ places | Never duplicate SQL/query logic | Custom Model Scope or Query Class |
| Multi-step mutation / side effect | Single Responsibility | `app/Actions/{Domain}/{ActionName}.php` |
| Request validation logic | Never validate in Controller | `app/Http/Requests/{Domain}/{RequestName}.php` |
| Data transformation logic | Never format arrays in Controller | `app/Http/Resources/Api/v1/{ResourceName}.php` |

---

## 4. Backend Clean Code Audit Checklist

Before declaring any backend task or PR complete:
1. **Zero Unused Code**: Are there any unused imports, dead functions, commented-out code, or obsolete files? Delete them.
2. **Strict Types Checked**: Does every PHP file have `declare(strict_types=1);` and complete native type annotations?
3. **SRP Maintained**: Are controllers thin (delegating immediately to Actions)?
4. **Database & Queries Safe**: Are relationships eager-loaded with `with(...)`? No N+1 queries?
5. **No Broken Migrations**: Are composite indexes and foreign key constraints explicitly declared?
