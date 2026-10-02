# Government Plot Compliance Management System

A Laravel-based workflow application for tracking government plot allotments, policy milestones, evidence submissions, incentive claims, legal actions, audit events, and compliance reporting.

## Core workflow

1. Configure policy templates and milestone rules.
2. Create and assign allotment cases to responsible officers.
3. Generate case timelines from policy milestones.
4. Collect evidence submissions and route them for review.
5. Track milestone progress and case compliance status.
6. Process incentive claims with role-based approvals.
7. Record legal notices, extensions, reviews, and terminations.
8. Produce monthly, non-compliance, and case-certificate reports.
9. Maintain an audit trail for material case actions.

## Technology

- PHP 8.2+
- Laravel 12
- MongoDB via `mongodb/laravel-mongodb`
- Spatie Laravel Permission for roles
- Blade, Tailwind CSS, Alpine.js, Vite
- Dompdf for generated PDF documents
- PHPUnit for automated tests

## Roles

The application uses role-based route protection for super administrators, state administrators, district officers, inspection officers, and allottees. Authorization is enforced at route-group boundaries and should be reviewed alongside the business rules before production deployment.

## Local setup

1. Install PHP 8.2+, Composer, Node.js, npm, and MongoDB.
2. Copy `.env.example` to `.env`.
3. Configure `MONGODB_URI` and `MONGODB_DATABASE`.
4. Install dependencies:

```bash
composer install
npm install
```

5. Generate an application key:

```bash
php artisan key:generate
```

6. Run the application:

```bash
php artisan serve
npm run dev
```

For a production build:

```bash
npm run build
```

## Testing

Run the Laravel test suite with:

```bash
php artisan test
```

The repository also includes focused unit coverage for reusable compliance-status logic and model-key handling.

## Security and privacy

Do not commit `.env`, credentials, tokens, generated application keys, production database files, or user-submitted evidence. The sample environment file contains placeholders only. Production deployments should use secret management, HTTPS, restricted database access, backups, and appropriate retention policies.

## Project scope

This repository demonstrates an application architecture for compliance workflow management. It is not a substitute for legal advice, government policy, statutory interpretation, or an official records system. Policy rules and eligibility criteria must be configured and reviewed by the responsible organization.
