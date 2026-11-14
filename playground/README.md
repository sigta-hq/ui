# Sigta UI playground

A Laravel app that uses the package from the repository root through a path
repository. It is the docs site (`/docs`), the kit pages for every component
(`/kit`) and the visual check for changes. It is not part of the Composer
download.

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite && php artisan migrate
npm run dev & php artisan serve      # http://localhost:8000/kit
php artisan test                     # every kit page must render
```

- [KIT-AUTHORING.md](KIT-AUTHORING.md): how to write a kit page.
- [../CONTRIBUTING.md](../CONTRIBUTING.md): setup, rules and pull requests.
- `resources/content/sigta-landing.php`: Sigta's own landing copy, which the
  `/kit/landing` page passes into the sections.
