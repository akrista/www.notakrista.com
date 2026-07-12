---
title: Contributing
---

> Parts of this guide are adapted from [Filament's contribution guide](https://filamentphp.com/docs/3.x/support/contributing), which served as useful inspiration.

Thank you for your interest in contributing to **Bizkit**! Bizkit is a batteries-included Laravel starter kit built around Livewire, Flux UI, Octane, and a curated set of first-party Laravel packages. Every contribution — bug reports, documentation improvements, and pull requests — helps make Bizkit a better foundation for the community.

## Reporting bugs

If you find a bug in Bizkit, please report it by opening an issue on our [GitHub repository](https://github.com/akrista/bizkit/issues/new/choose). Before opening an issue, please search the [existing issues](https://github.com/akrista/bizkit/issues?q=is%3Aissue) to see if the bug has already been reported.

When creating an issue, please include as much information as possible:

- A clear, descriptive title.
- The version of Bizkit you are using (commit hash or release tag).
- The exact Laravel, PHP, and Node/Bun versions in your environment.
- A step-by-step description of how to reproduce the problem.
- The expected behavior and the actual behavior.
- Any relevant stack traces, logs, or screenshots.

**Please link to a minimal reproduction repository** rather than your real project. A fresh, isolated reproduction in a clean Laravel app allows us to fix the problem much faster. **Issues without a reproduction may be closed to preserve maintainer time and to keep the process fair for those who put effort into reporting.** If you believe a reproduction repository is not suitable for your issue, you may still open a regular issue, but please explain why in detail.

Remember, bug reports are created in the hope that others with the same problem will be able to collaborate with you on solving it. Do not expect that the bug report will automatically see any activity or that others will jump to fix it. Creating a bug report serves to help yourself and others start on the path of fixing the problem.

## Proposing new features

If you would like to propose a new feature or improvement to Bizkit, please open a discussion on our [GitHub discussions](https://github.com/akrista/bizkit/discussions). If you intend to implement the feature yourself in a pull request, we strongly recommend opening a discussion first and tagging a maintainer before writing code. This helps confirm the feature is aligned with the project's direction and prevents you from spending time on something that may not be accepted.

## Development setup

Bizkit is a Laravel application, so contributing is the same workflow as contributing to any Laravel project.

1. **Fork** the [Bizkit repository](https://github.com/akrista/bizkit) to your GitHub account.
2. **Clone your fork** locally:

    ```bash
    git clone https://github.com/<your-username>/bizkit.git
    cd bizkit
    ```

3. **Install dependencies** using the bundled `setup` script (requires PHP 8.3+, Composer, and Bun):

    ```bash
    composer setup
    ```

    This runs `composer install`, copies `.env.example` to `.env`, generates an application key, runs migrations, installs Bun packages, and builds the frontend assets.

4. **Create a branch** for your change:

    ```bash
    git checkout -b fix/short-descriptive-name
    ```

5. **Run the development server** while you work:

    ```bash
    composer dev
    ```

    This concurrently starts the HTTP server, queue worker, log tail (`pail`), and Vite dev server.

### Quality checks

Before opening a pull request, make sure the full test suite passes locally. Bizkit enforces strict quality gates:

```bash
composer test
```

This runs, in order:

- **Lint** — `pint --test` and `rector --dry-run`
- **Static analysis** — `phpstan` (Larastan)
- **Type coverage** — `pest --type-coverage --min=100`
- **Unit & feature tests** — `pest --parallel --coverage --exactly=100.0`

Pull requests are expected to keep coverage and type coverage at 100%. If you add new code, please add corresponding tests.

### Code style

- Follow the existing conventions in the codebase. When in doubt, look at neighboring files.
- Run `composer lint` to auto-fix formatting with Pint and Rector.
- Use PHP 8 constructor property promotion, explicit return types, and PHPDoc for array shapes.
- Do not introduce new dependencies without prior discussion.

## Pull request process

1. Ensure your branch is up to date with `master` and rebased (not merged) onto it.
2. Confirm `composer test` passes locally.
3. Write a clear pull request description that explains the **what** and the **why**, and links any related issues or discussions.
4. If your change introduces user-facing changes, update the relevant documentation in `README.md` or this file.
5. A maintainer will review your pull request. Be open to feedback — review is collaborative, not adversarial.

## Security vulnerabilities

If you discover a security vulnerability within Bizkit, please email [info@notakrista.com](mailto:info@notakrista.com). All security vulnerabilities will be promptly addressed. **Please do not open a public issue for security-related problems.**

## Code of Conduct

Please note that Bizkit is released with a [Contributor Code of Conduct](https://github.com/akrista/bizkit/blob/master/CODE_OF_CONDUCT.md). By participating in this project you agree to abide by its terms.

## License

By contributing to Bizkit, you agree that your contributions will be licensed under the [MIT License](https://github.com/akrista/bizkit/blob/master/LICENSE.md) that covers the project.
