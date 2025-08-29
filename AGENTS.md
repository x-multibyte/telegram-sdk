# Repository Guidelines

## Project Structure & Module Organization
- `src/`: SDK source code
  - `Contracts/`: Interfaces and DTO contracts
  - `Exceptions/`: Domain exceptions
  - `Http/`: HTTP client abstractions and Guzzle implementation
  - `Methods/`: Grouped Telegram API method sets
  - `Models/`: DTOs and response models
  - `Utils/`: Internal helpers
- `tests/`: Pest tests (`Unit/`), bootstrap in `tests/Pest.php`
- `docs/`: Docs site assets (Docsify)
- `laravel-bridge/`: Optional package with Laravel-specific providers, console commands, controllers, and config

## Build, Test, and Development Commands
- Install deps: `composer install`
- Run unit/feature tests (Pest): `vendor/bin/pest -q`
- Run with PHPUnit: `vendor/bin/phpunit`
- Static analysis (if configured): `vendor/bin/phpstan analyse src tests`
- Autoload dump after refactors: `composer dump-autoload -o`

## Coding Style & Naming Conventions
- Standard: PSR-12, `declare(strict_types=1);` at file top
- Indentation: 4 spaces; one class/interface per file
- Namespaces: `XBot\Telegram\...` mirroring folder structure
- Classes/Interfaces: `PascalCase` (interfaces may end with `Interface`)
- Exceptions: end with `Exception`; methods and properties `camelCase`
- PHPDocs: concise, type-accurate for public APIs

## Testing Guidelines
- Framework: Pest (on top of PHPUnit)
- Location: place tests in `tests/Unit`
- Naming: `FooBarTest.php`; use `describe()/it()` with clear behavior-oriented names
- Run locally: `vendor/bin/pest --coverage` (requires Xdebug) when validating changes
- New features must include tests; cover error paths and DTO serialization

## Commit & Pull Request Guidelines
- Commit style: Prefer Conventional Commits, e.g. `feat: add reply keyboard builder`,
  `fix(bot): handle rate limit retry`, `docs(api): update usage`.
- Scope: small, focused commits with clear subjects (≤ 72 chars)
- PRs: include description, motivation, screenshots/logs where relevant, and linked issues.
- Checklist: tests passing, no breaking API changes without notes, updated docs if behavior changes.

## Security & Configuration Tips
- Never commit tokens or secrets; use `.env` and `config/telegram.php` bindings
- Set and rotate webhook secrets; avoid logging sensitive payloads
- Validate bot configuration via `BotManager` and prefer least-privilege timeouts/rate limits

