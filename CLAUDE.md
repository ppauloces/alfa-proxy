# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Alfa Proxy is a Laravel 12 application for managing and selling proxy services (SOCKS5). Users can register, purchase proxies with balance credits, manage their proxy inventory, and track transactions. The system includes payment postback integration for automatic balance recharging.

## Development Commands

### Running the Application
```bash
# Start development servers (Laravel server, queue worker, and Vite)
composer dev

# Or start services individually:
php artisan serve          # Start Laravel dev server
php artisan queue:listen --tries=1  # Start queue worker
npm run dev                # Start Vite dev server
```

### Testing
```bash
# Run all tests
composer test
# Or: php artisan test

# Run specific test file
php artisan test tests/Feature/SomeTest.php

# Run Pest tests directly
./vendor/bin/pest
```

### Code Quality
```bash
# Format code with Laravel Pint
./vendor/bin/pint

# View real-time logs
php artisan pail
```

### Database Operations
```bash
# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Seed database
php artisan db:seed
```

### Asset Building
```bash
# Build for production
npm run build

# Development with hot reload
npm run dev
```

## Architecture

### Core Business Logic

**User Management**
- Users have balance (`saldo`), roles (`cargo`: usuario/admin/super), plan types, and status flags
- `CheckUserStatus` middleware prevents banned users (status=0) from accessing protected routes
- `AdminMiddleware` restricts admin routes to users with 'admin' or 'super' cargo

**Proxy Stock System**
- `Stock` model represents individual proxies with IP, port, credentials, type, country, expiration, and availability
- Stocks belong to users (foreign key relationship)
- Each stock has availability tracking for rental management

**Transaction & Payment Flow**
1. User initiates payment, `Transaction` record created with status=0 (pending)
2. Payment gateway sends postback to `/api/postback/transacao` (PostbackController.php:14)
3. Postback verifies transaction, updates status to 1 if paid
4. User balance automatically increased by transaction amount (PostbackController.php:50)
5. Idempotency check prevents double-processing (PostbackController.php:32)

**Coupon System**
- `Coupom` model manages discount codes
- Users can view available coupons at `/cupons` route

### Route Structure

**Public Routes** (routes/web.php)
- `/` - Landing page
- `/faq` - FAQ page
- `/login`, `/register` - Authentication
- `/esqueci-senha`, `/redefinir-senha/{token}` - Password recovery

**Authenticated Routes** (auth + CheckUserStatus middleware)
- `/dashboard` - Main user panel
- `/saldo` - Balance management
- `/socks5` - Proxy listing
- `/transacoes` - Transaction history
- `/comprar` - Purchase proxies
- `/pagamento` - Payment processing
- `/api` - API documentation/keys

**Admin Routes** (AdminMiddleware)
- Currently empty group at routes/web.php:65, ready for admin panel expansion

**API Routes** (routes/api.php)
- `GET /api/transacao/{transacao_id}` - Check transaction status
- `POST /api/postback/transacao` - Payment gateway webhook (no auth)

### Key Controllers

**LogadoController** (authenticated user operations)
- All methods fetch user via `Auth::id()` and pass to views
- `transacoes()` calculates total approved payments sum (line 52-54)
- Inconsistent query pattern: uses `where('id', Auth::id())` in transacoes() but should use `where('user_id', Auth::id())` (lines 46-50)

**PostbackController** (payment webhooks)
- Handles payment status updates from external gateway
- Critical security consideration: Should validate webhook signature/source in production
- Converts amount from centavos by dividing by 100 (line 50)

**ApiController** (API endpoints)
- Simple transaction status lookup endpoint
- Should add null check before accessing `$transacao->status` (line 17)

### Models

**User** (app/Models/User.php)
- Custom fields: `username`, `saldo`, `cargo`, `status`, `plano`, `expiracao`, `foto_perfil`
- Auto-hashes password via mutator (line 30)
- `hasFeatureAccess($feature)` method for permission checking (line 58), but references undefined `adminPermissions()` relationship
- `stocks()` relationship defined (line 67)

**Transaction** (app/Models/Transaction.php)
- Links user_id to transactions
- Status: 0=pending, 1=approved/paid

**Stock** (app/Models/Stock.php)
- Stores proxy credentials and metadata
- `expiracao` cast to datetime (line 23)
- `user()` belongsTo relationship (line 16)

### Frontend Stack

- **Tailwind CSS 4.0** with Vite plugin for styling
- **Blade templates** in `resources/views/`
- Layout structure: `logado/partials/app.blade.php` (main layout), `navbar.blade.php`, `sidebar.blade.php`
- Vite configuration compiles `resources/css/app.css` and `resources/js/app.js`

### Testing Framework

Uses **Pest PHP** (version 3.8) with Laravel plugin for testing. Test configuration in `tests/Pest.php`.

## Important Notes

### Potential Issues to Address

1. **Query Bug in LogadoController**: Lines 46-50 use `where('id', Auth::id())` instead of `where('user_id', Auth::id())` when filtering transactions
2. **Missing Null Checks**: ApiController.php:17 doesn't handle missing transactions
3. **Undefined Relationship**: User model references `adminPermissions()` relationship that doesn't exist (line 64)
4. **Security**: PostbackController should validate webhook authenticity before processing payments

### User Role System

- `usuario` - Standard user
- `admin` - Admin with feature-based permissions
- `super` - Full access to all features

### Database Schema Notes

- Users table includes plan/subscription tracking (`plano`, `expiracao`)
- All foreign keys use cascade deletion
- Stock availability boolean controls rental status
- Transaction amounts stored as decimals
