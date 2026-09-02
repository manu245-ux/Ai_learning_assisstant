# Deployment Guide

## Recommended architecture

```text
Browser
   │
   ▼
PHP + Apache application
   │
   ├── MySQL database
   └── Google Gemini API
```

The repository includes a Dockerfile so the same application can run locally
or on a Docker-compatible PHP host.

## Before pushing to GitHub

Check that these are NOT present:

- `.env`
- API keys
- database passwords
- runtime logs
- real user uploads

The repository `.gitignore` is configured to exclude them.

## Railway / Docker-compatible PaaS

1. Push the project to GitHub.
2. Create a web service from the repository.
3. Deploy using the included `Dockerfile`.
4. Provision/connect a MySQL database.
5. Set:

```text
APP_ENV=production
APP_URL=https://your-domain.example
GEMINI_API_KEY=...
DATABASE_URL=...
```

If your provider gives separate MySQL variables instead, set:

```text
DB_HOST=...
DB_PORT=3306
DB_NAME=...
DB_USER=...
DB_PASS=...
```

6. Import `database/schema.sql`.
7. Register a user at `/register.php`.
8. Test `/health.php`.
9. Test login, AI chat, quiz, flashcards and PDF Q&A.

## Persistent PDF storage

The application writes uploaded PDFs to:

```text
/var/www/html/uploads
```

On an ephemeral container platform, attach persistent storage to this path if
uploads need to survive restarts/redeployments.

## Shared PHP hosting

1. Create a MySQL database/user.
2. Import `database/schema.sql`.
3. Upload the project files.
4. Configure environment variables or a server-side `.env`.
5. Use PHP 8.2+.
6. Enable `pdo_mysql`, `curl`, `fileinfo`, `mbstring`, `zlib`.
7. Ensure `uploads/` is writable.
8. Enable HTTPS.
9. Visit `/register.php`.

## Production checklist

- [ ] `.env` is not in Git
- [ ] Gemini key is stored as a secret/environment variable
- [ ] `APP_ENV=production`
- [ ] HTTPS enabled
- [ ] MySQL schema imported
- [ ] `APP_URL` matches the real URL
- [ ] `/health.php` returns `status: ok`
- [ ] PDF upload storage is persistent if required
- [ ] AI endpoints have an appropriate quota/rate-limit strategy
- [ ] Admin credentials are controlled securely
