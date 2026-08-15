# NtoshiSoft — Standard Build Instructions

**File:** `build_instructions.md`
**Framework:** NtoshiSoft WDE (Web Development Engine)
**Organisation:** Jongi Brands Tech Solutions
**Purpose:** Standard operating instructions for any AI coding agent or developer performing development, debugging, maintenance, enhancement, or refactoring work on an NtoshiSoft project.

---

## 1. PURPOSE AND AUTHORITY

This document defines the mandatory development rules and workflow that must be followed whenever code is created, modified, debugged, refactored, or otherwise changed within an NtoshiSoft project.

NtoshiSoft is a **custom PHP Web Development Engine/Framework developed by Jongi Brands Tech Solutions**. It is not to be treated as a conventional Laravel, CodeIgniter, Symfony, WordPress, or other mainstream PHP framework.

The coding agent must therefore work **with the NtoshiSoft architecture**, rather than attempting to impose conventions, patterns, or assumptions from another framework.

The primary architectural authority for the project is:

```text
/build_blueprint.md
```

The agent MUST read and understand `build_blueprint.md` before making any meaningful code changes.

Where this document and the existing NtoshiSoft implementation appear to conflict, the agent must first investigate the codebase and established NtoshiSoft patterns rather than automatically applying an external framework convention.

---

# 2. MANDATORY CODEBASE FAMILIARISATION

## 2.1 No premature coding

Before modifying or creating code, the agent MUST thoroughly familiarise itself with the relevant portion of the existing codebase.

Do not begin coding merely from the description of the requested feature or bug.

The agent must first establish:

* How the application is structured.
* How routing works.
* How controllers work.
* How models/entities are structured.
* How views/templates are loaded.
* How database access is performed.
* How authentication and authorisation work.
* How requests and responses flow through the framework.
* How modules/features are organised.
* How assets are loaded.
* How forms are processed.
* How validation is performed.
* How errors are handled.
* How reusable functions/helpers are implemented.
* How existing modules solve similar problems.
* Which conventions are consistently used throughout the project.

Where possible, the agent should identify an **existing working implementation that is structurally similar to the requested task** and use it as the primary reference.

### Principle

> **Understand first. Modify second.**

Do not redesign an existing NtoshiSoft mechanism simply because another implementation appears more conventional.

---

# 3. READ THE BLUEPRINT FIRST

The agent MUST inspect:

```text
/build_blueprint.md
```

before undertaking development work.

The blueprint defines the NtoshiSoft WDE architecture and should be treated as the framework's architectural specification.

The agent should use the blueprint to determine:

* Directory responsibilities.
* Application flow.
* Naming conventions.
* Controller conventions.
* Model conventions.
* View conventions.
* Database conventions.
* Routing conventions.
* Framework boundaries.
* Extensibility mechanisms.
* Core framework responsibilities.
* Application-level responsibilities.

The agent must not infer that NtoshiSoft behaves like another PHP framework merely because similar terminology is used.

---

# 4. PRESERVE THE EXISTING ARCHITECTURE

NtoshiSoft projects must be treated as **existing systems**, not blank-slate applications.

The default objective is:

> **Make the smallest correct change necessary to achieve the requested result while preserving the existing architecture and behaviour.**

Do not introduce architectural changes unless the task explicitly requires them or the existing implementation makes the requested functionality impossible without such a change.

Avoid:

* Unnecessary rewrites.
* Unrequested refactoring.
* Changing naming conventions merely for preference.
* Replacing existing mechanisms with third-party libraries without justification.
* Moving files unnecessarily.
* Changing framework architecture to resemble another framework.
* Introducing dependencies where existing NtoshiSoft functionality is sufficient.

---

# 5. RESERVED NtoshiSoft TERMINOLOGY

Certain names within NtoshiSoft have framework-level significance and must not be casually reused.

## 5.1 Reserved Controller Method: `view`

The word:

```php
view()
```

is reserved within the NtoshiSoft controller architecture.

A controller method such as:

```php
public function view()
{
    // ...
}
```

MUST NOT be created for normal module/object detail pages.

For example, if developing an Events module, do not create:

```php
public function view()
{
    // Event details
}
```

Instead use a descriptive method such as:

```php
public function eventView()
{
    // ...
}
```

or:

```php
public function eventDetails()
{
    // ...
}
```

The same principle should be applied to other modules.

Prefer descriptive, collision-resistant method names such as:

```text
eventView
eventDetails
productView
productDetails
userDetails
invoiceView
```

rather than relying on generic reserved terminology.

### Rule

> **Before introducing a controller method name, verify that the name does not conflict with an NtoshiSoft framework mechanism or established convention.**

---

# 6. `app/core` IS FRAMEWORK TERRITORY

The following directory is considered protected framework territory:

```text
/app/core
```

The agent MUST NOT modify files within `app/core` during ordinary feature development, debugging, or maintenance.

Application functionality should normally be implemented outside the core framework.

## 6.1 Exceptional Core Changes

A change to `app/core` is permitted only where the requested task genuinely requires a framework-level modification.

Examples may include:

* Adding a framework helper.
* Fixing a genuine framework-level defect.
* Extending a core service required by multiple applications.
* Adding functionality that is explicitly intended to become part of NtoshiSoft itself.

For example, if a new helper function genuinely belongs in:

```text
/app/core/functions.php
```

then that change may be made.

However, the agent MUST clearly identify:

1. That a core file was modified.
2. Why the modification was necessary.
3. What functionality was added or changed.
4. The exact file affected.
5. The exact lines affected.
6. Any potential impact on other modules or projects.

### Core Modification Principle

> **Application problems must not automatically become framework problems.**

Before changing `app/core`, the agent should first determine whether the requirement can be solved safely at application/module level.

---

# 7. FOLLOW EXISTING NtoshiSoft PATTERNS

When implementing functionality, the agent should first search the codebase for comparable functionality.

For example:

If asked to create an Events module, inspect existing modules such as:

```text
News
Products
Services
Users
Bookings
Posts
```

or any other structurally similar implementation.

Determine:

* How the controller is structured.
* How the model is structured.
* How routes are defined.
* How views are named.
* How database queries are performed.
* How forms are processed.
* How validation works.
* How redirects are handled.
* How permissions are checked.
* How messages/notifications are generated.

Then implement the new functionality using the same established pattern unless the task specifically requires a different approach.

---

# 8. DO NOT ASSUME — VERIFY

The agent must distinguish between:

* What it knows from `build_blueprint.md`.
* What it observes in the codebase.
* What it assumes based on general programming knowledge.

If the implementation differs from common PHP conventions, **the existing NtoshiSoft implementation takes precedence**.

For example, do not assume that:

```text
controllers
models
views
routes
middleware
```

operate exactly as they do in Laravel or another framework.

Inspect the actual NtoshiSoft implementation.

---

# 9. CHANGE SCOPE

Every task must have a clearly defined scope.

The agent should modify only files necessary to complete the requested task.

Do not make unrelated improvements while working on a specific task.

For example, if the task is:

> Fix the Events edit form.

Do not simultaneously:

* Rewrite the Events controller.
* Rename unrelated methods.
* Reformat the entire module.
* Upgrade dependencies.
* Change database architecture.
* Modify unrelated modules.

Unless those changes are necessary to resolve the requested issue.

---

# 10. LIVE-SYSTEM AWARENESS

NtoshiSoft projects may be deployed to live production servers.

Therefore, every code change must be treated as potentially affecting an operational system.

Before making a change, determine whether:

* Existing production functionality could be affected.
* Database structure is involved.
* Existing routes could be affected.
* Existing users could be affected.
* Existing data could be affected.
* Existing authentication/authorisation could be affected.
* Existing APIs or integrations could be affected.

Avoid destructive operations unless explicitly authorised.

Do not:

* Delete production data.
* Drop tables unnecessarily.
* Remove existing functionality without instruction.
* Rename database fields without assessing dependencies.
* Change URLs/routes without considering existing links.
* Replace working production code merely for stylistic reasons.

---

# 11. DATABASE CHANGES

Database modifications require additional caution.

Before changing the database:

1. Inspect the existing schema.
2. Identify existing relationships.
3. Search the codebase for references to affected tables/columns.
4. Determine whether existing records depend on the change.
5. Determine whether migration/backward compatibility is required.
6. Clearly document the database impact.

Never assume that a database column or table is unused simply because it is not referenced in the immediately visible file.

Search the project before removing or renaming database structures.

---

# 12. DEBUGGING WORKFLOW

When asked to debug an existing problem, do not immediately patch the first suspicious line.

Use the following workflow:

### Step 1 — Reproduce/understand the problem

Determine:

* What is expected?
* What is actually happening?
* Where does the failure occur?
* Is the failure frontend, backend, database, routing, configuration, or framework related?

### Step 2 — Trace the execution path

Follow the relevant flow through:

```text
Request
   ↓
Route
   ↓
Controller
   ↓
Model / Service / Database
   ↓
View / Response
```

where applicable to the actual NtoshiSoft architecture.

### Step 3 — Identify the root cause

Do not merely treat the visible symptom.

### Step 4 — Make the smallest appropriate correction

Avoid unnecessary changes.

### Step 5 — Verify the result

Check that:

* The original problem is resolved.
* Existing functionality still works.
* No unrelated functionality has been broken.
* The modified code follows NtoshiSoft conventions.

---

# 13. TESTING AND VALIDATION

After making a change, the agent should perform appropriate validation.

Depending on the task, this may include:

* PHP syntax validation.
* Application-level testing.
* Route testing.
* Form submission testing.
* Authentication/authorisation testing.
* Database testing.
* Browser/UI testing.
* Regression testing of affected functionality.

Where automated tests exist, use them.

Where automated tests do not exist, perform the most practical validation available and clearly state what was and was not tested.

Never claim that something was tested if it was not actually tested.

---

# 14. SECURITY REQUIREMENTS

All changes must preserve the application's security model.

Pay particular attention to:

* Authentication.
* Authorisation.
* Session handling.
* Input validation.
* Output escaping.
* SQL injection.
* XSS.
* CSRF.
* File upload handling.
* Access control.
* Sensitive data exposure.
* Privilege escalation.

Do not weaken existing security controls merely to make a feature easier to implement.

If a requested implementation introduces a security concern, identify it before proceeding.

---

# 15. DEPENDENCY DISCIPLINE

Do not introduce external libraries, packages, frameworks, APIs, or services merely because they are familiar or convenient.

Before introducing a dependency, determine whether:

1. NtoshiSoft already provides the required functionality.
2. The project already contains an equivalent dependency.
3. The functionality can reasonably be implemented using existing project infrastructure.
4. The new dependency is justified by the requirements.

Any new dependency should be explicitly documented.

---

# 16. FILE AND NAMING CONVENTIONS

Follow the naming conventions already established by the NtoshiSoft project.

Do not rename existing files, classes, methods, variables, routes, database objects, or directories simply because another naming convention is preferred.

When creating something new:

1. Inspect comparable existing implementations.
2. Determine the established naming pattern.
3. Follow that pattern consistently.

Consistency with the NtoshiSoft codebase takes precedence over personal preference.

---

# 17. COMMENTS AND DOCUMENTATION

Comments should explain **why** something is necessary where the reason is not obvious from the code.

Do not fill the codebase with comments that merely restate what the code does.

For framework-sensitive or non-obvious behaviour, useful comments may be appropriate.

Example:

```php
// `view` is reserved by NtoshiSoft; use eventDetails instead.
```

where such clarification prevents future accidental breakage.

---

# 18. CHANGE TRACKING — `changes_affected.md`

Every development interaction that results in a code change MUST update:

```text
/changes_affected.md
```

This file is the project's **development change register**.

The purpose is to maintain an accurate historical record of what has changed and to allow a developer working directly on the live server to identify precisely where the corresponding modifications must be applied.

## 18.1 Every change entry MUST contain

### 1. Task

Clearly describe what was requested.

### 2. Resolution

Describe what was done to resolve, implement, or improve the requested task.

### 3. Files affected

List every file actually changed.

For each file, record:

* File path.
* Starting line number.
* Ending line number.
* Brief description of the change.

Example:

```text
## 2026-08-08 — Events Details Bug Fix

### Task
Fix the Events details page failing to load.

### Resolution
Renamed the controller method from `view()` to `eventDetails()` because
`view` is reserved by the NtoshiSoft controller architecture.

### Files Affected

1. app/controllers/EventsController.php
   Lines: 42–58
   Change: Renamed `view()` to `eventDetails()` and updated the associated logic.

2. app/views/events/details.php
   Lines: 15–31
   Change: Corrected the event data references.

### Core Changes
None.

### Database Changes
None.

### Validation
Events details page tested successfully.
```

---

# 19. LINE-NUMBER ACCURACY

Line references in `changes_affected.md` must correspond to the **actual modified version of the file**.

If a change shifts line numbers elsewhere in the file, record the final affected range as accurately as practical.

Do not fabricate line numbers.

If the exact line range cannot be reliably established, explicitly state:

```text
Lines: Unable to determine precisely
```

and provide the relevant method/class/section name instead.

Accuracy is more important than pretending to have exact information.

---

# 20. CORE CHANGES MUST BE HIGHLIGHTED

If anything under:

```text
/app/core
```

is modified, `changes_affected.md` must contain a clearly identifiable section:

```text
### Core Changes
YES
```

followed by:

* File.
* Lines.
* Reason.
* Technical effect.
* Potential impact.

If no core files were modified:

```text
### Core Changes
None.
```

This distinction must be maintained for every task.

---

# 21. DATABASE CHANGES MUST BE HIGHLIGHTED

Every change entry should explicitly state whether the database was modified.

Use:

```text
### Database Changes
None.
```

or provide the relevant details.

Where applicable, document:

* Tables.
* Columns.
* Indexes.
* Constraints.
* Data migrations.
* Seed data.
* Backward compatibility considerations.

---

# 22. NO SILENT CHANGES

The agent must never silently modify files outside the immediate task scope.

If an additional file must be changed because of a dependency or architectural requirement, document why.

For example:

```text
The requested controller change required a corresponding route update.
Therefore routes.php was also modified.
```

Unexpected changes must be disclosed.

---

# 23. WHEN A TASK REQUIRES ARCHITECTURAL CHANGE

If the requested functionality cannot be implemented cleanly without changing the existing NtoshiSoft architecture, do not quietly redesign the architecture.

First identify:

1. The architectural limitation.
2. Why the existing architecture cannot satisfy the requirement.
3. The proposed architectural change.
4. Which core/application files would be affected.
5. The potential impact.

Then proceed only where the task authorisation permits such a change.

---

# 24. DO NOT BREAK BACKWARD COMPATIBILITY WITHOUT AUTHORISATION

Existing functionality should be presumed to be intentional unless proven otherwise.

Before removing or changing:

* Existing methods.
* Existing routes.
* Existing parameters.
* Existing database fields.
* Existing APIs.
* Existing configuration.
* Existing user workflows.

determine whether other parts of the application depend on them.

Where possible, prefer backward-compatible implementation.

---

# 25. AI CODING AGENT BEHAVIOUR

An AI coding agent working on NtoshiSoft must behave as a **codebase-aware engineering agent**, not as a generic code generator.

The agent must:

* Inspect before modifying.
* Search before assuming.
* Reuse established patterns.
* Respect framework boundaries.
* Minimise unnecessary changes.
* Preserve existing functionality.
* Document changes.
* Identify risks.
* Validate its work.
* Be explicit about uncertainty.
* Never claim to have performed an action it did not perform.

The agent must not treat the user's immediate instruction as permission to ignore framework constraints.

---

# 26. PRIORITY OF ENGINEERING DECISIONS

When making implementation decisions, use the following order of priority:

### 1. Explicit task requirement

What is the user actually asking to achieve?

### 2. NtoshiSoft architecture

What does `build_blueprint.md` prescribe?

### 3. Existing working implementation

How does NtoshiSoft already solve similar problems?

### 4. Existing project conventions

What patterns are consistently used in this particular project?

### 5. General software engineering best practice

Apply appropriate PHP, web-development, security, database, and software-engineering principles.

### 6. Personal preference

Personal coding preference must have the lowest priority.

---

# 27. DEFINITION OF DONE

A task is not considered complete merely because code has been written.

Before declaring the task complete, the agent should confirm:

* [ ] `build_blueprint.md` was considered.
* [ ] Relevant existing code was inspected.
* [ ] Existing NtoshiSoft patterns were followed.
* [ ] The task was implemented within the appropriate architectural layer.
* [ ] Reserved terminology was respected.
* [ ] `app/core` was not modified unnecessarily.
* [ ] Security implications were considered.
* [ ] Database implications were considered.
* [ ] Appropriate validation/testing was performed.
* [ ] No unrelated files were unnecessarily changed.
* [ ] `changes_affected.md` was updated.
* [ ] Every affected file has been documented.
* [ ] Affected line ranges have been recorded accurately where possible.
* [ ] Core changes have been explicitly identified.
* [ ] Database changes have been explicitly identified.
* [ ] Any limitations or unverified areas have been disclosed.

---

# 28. THE NTOSHISOFT GOLDEN RULE

Above all other implementation preferences, follow this principle:

> **Do not make NtoshiSoft conform to the developer. Make the developer conform to NtoshiSoft.**

NtoshiSoft is a deliberately developed custom framework with its own architecture, conventions, terminology, workflows, and constraints.

The objective of every development task is therefore not merely to produce working PHP code.

The objective is to produce:

**working code that belongs inside NtoshiSoft.**

---

## END OF BUILD INSTRUCTIONS
