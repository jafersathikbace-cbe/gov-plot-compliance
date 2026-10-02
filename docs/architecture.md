# Architecture

## Layers

- **HTTP controllers** orchestrate authenticated requests and delegate workflow operations.
- **Models** represent cases, plots, milestones, submissions, claims, policy templates, users, and audit records.
- **Services** contain lifecycle, notification, audit, and identifier logic.
- **Blade views** provide dashboards, workflow forms, review screens, and PDF templates.
- **MongoDB** stores the domain entities configured for the application.

## Case lifecycle

`CaseLifecycleService` generates milestone records from a policy template, recalculates the case status from milestone state, and checks claim eligibility. Audit events are emitted when lifecycle state changes.

## Authorization

Routes are grouped by role. Administrative policy and case-management actions are restricted to elevated roles, while allottee submission actions and officer review actions use separate role groups.

## Reporting

Reports are exposed through authenticated role-restricted routes and can render PDF documents using the configured Dompdf integration.
