# 🤖 AI Learning Assistant

An AI-powered study companion built with **PHP, MySQL, Bootstrap, JavaScript, and Google Gemini**.

It combines conversational AI with practical study tools so students can learn, revise, and organize study material from one web application.

## ✨ Features

- 💬 **AI Chat** — conversational study help with saved chat history
- 📝 **Notes Summarizer** — turn study material into concise notes
- ❓ **Quiz Generator** — generate practice questions from a topic
- 🗂️ **Flashcards** — create AI-generated flashcard decks
- 📄 **PDF Q&A** — upload a text-based PDF and ask questions about it
- 💻 **Code Generator** — coding-focused AI assistance
- ➗ **Math Mode** — math-focused AI assistance
- 🔎 **Chat Search** — find previous conversations
- 📥 **Chat Export** — export conversation history
- 👤 **Authentication & Profiles** — registration, login, profile and password management
- 🛡️ **Admin Panel** — users and activity logs
- 🌙 **Dark Mode**
- 🔐 **Security controls** — password hashing, CSRF protection, prepared SQL statements, login throttling and upload validation

## 📚 Topic-wise Learning Content

The `content/` directory contains individual Markdown files for each subject and topic, plus machine-readable metadata in `content/topics.json`.

## 🧰 Tech Stack

| Layer | Technology |
|---|---|
| Frontend | HTML5, CSS3, Bootstrap 5, Vanilla JavaScript |
| Backend | PHP 8.2+ |
| Database | MySQL |
| AI | Google Gemini API |
| HTTP | PHP cURL |
| PDF | Dependency-free text extraction |
| Deployment | Docker / Apache; suitable for Railway or traditional PHP hosting |

## 📁 Project Structure

```text
AI-Learning-Assistant/
├── admin/                 # Admin dashboard, users and activity logs
├── api/                   # Gemini integration and AJAX endpoints
├── assets/                # CSS, JavaScript and images
├── chat/                  # Chat CRUD and export endpoints
├── config/                # Environment and database configuration
├── database/              # MySQL schema
├── flashcards/            # Flashcard generator
├── includes/              # Shared PHP helpers/components
├── notes/                 # Notes summarizer
├── pdf/                   # PDF upload and Q&A
├── quiz/                  # Quiz generator
├── uploads/               # Runtime PDF storage
├── .env.example           # Safe configuration template
├── Dockerfile             # Production container
├── composer.json          # PHP/runtime requirements
├── health.php             # Deployment health check
└── README.md
```

## 🚀 Run Locally with XAMPP

### 1. Requirements

- PHP 8.2+
- Apache
- MySQL
- PHP extensions: `pdo_mysql`, `curl`, `mbstring`, `fileinfo`, `zlib`
- A Google Gemini API key

### 2. Clone the repository

```bash
git clone <YOUR_GITHUB_REPO_URL>
cd AI-Learning-Assistant
```

### 3. Configure environment variables

Copy `.env.example` to `.env` and fill in your local values.

```text
DB_HOST=localhost
DB_PORT=3306
DB_NAME=ai_learning_assistant
DB_USER=root
DB_PASS=

GEMINI_API_KEY=your-key
GEMINI_MODEL=gemini-2.0-flash

APP_URL=http://localhost/AI-Learning-Assistant
APP_ENV=local
```

**Never commit `.env`.**

### 4. Create the database

Open phpMyAdmin and import:

```text
database/schema.sql
```

This creates the application's database and tables.

### 5. Start Apache and MySQL

Open:

```text
http://localhost/AI-Learning-Assistant/
```

Create your account through `register.php`.

## ☁️ Deployment

This application uses **MySQL**, so choose a host that supports PHP + MySQL.

### Recommended: Docker + Railway

1. Push this repository to GitHub.
2. Create a new project on Railway and deploy from the GitHub repository.
3. Railway will use the included `Dockerfile`.
4. Add a MySQL database/service.
5. Add these environment variables to the web service:

```text
APP_ENV=production
APP_URL=https://YOUR-APP-DOMAIN
GEMINI_API_KEY=YOUR_GEMINI_KEY
DATABASE_URL=YOUR_MYSQL_CONNECTION_URL
```

The application supports either `DATABASE_URL` or individual `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and `DB_PASS` variables.

6. Import `database/schema.sql` into the MySQL database.
7. Open `/register.php` and create your account.
8. Check `/health.php` to confirm the web container is responding.

### Important: PDF uploads

Uploaded PDFs are stored on the server filesystem. Container platforms may use ephemeral storage. If uploaded PDFs must survive redeployments, attach persistent storage/volume to:

```text
/var/www/html/uploads
```

### Traditional PHP hosting

For a PHP/MySQL host:

1. Create a MySQL database and user.
2. Import `database/schema.sql`.
3. Upload the repository files to the web root.
4. Configure environment variables (or a server-side `.env` where your host supports it).
5. Set PHP to 8.2+ and enable required extensions.
6. Make `uploads/` writable by PHP.
7. Enable HTTPS.
8. Open `/register.php`.

See `DEPLOYMENT.md` for more deployment details.

## 🔐 Security Notes

The application includes:

- `password_hash()` / `password_verify()` for passwords
- PDO prepared statements
- CSRF protection on state-changing requests
- session hardening and session regeneration
- basic login throttling
- MIME/type and extension validation for PDF uploads
- output escaping to reduce XSS risk
- role checks for admin routes
- non-executable upload-directory rules

For production:

- keep `APP_ENV=production`,
- use HTTPS,
- keep secrets in environment variables,
- never commit API keys,
- use persistent storage for important uploads,
- consider adding application-level rate limiting before exposing the AI endpoints publicly.

## 📄 PDF Limitations

The built-in PDF extractor is intentionally lightweight. It works best with normal text-based PDFs.

It does **not** perform OCR, so scanned/image-only PDFs may not produce useful text. Very large documents are also limited by `MAX_PDF_CHARS_FOR_PROMPT`.

For production-grade document processing, replace the lightweight extractor with a maintained PDF parsing/OCR pipeline.

## 🧪 Testing

The repository includes a GitHub Actions workflow that checks PHP syntax on pushes and pull requests.

For a local syntax check:

```bash
find . -type f -name "*.php" -not -path "./vendor/*" -exec php -l {} \;
```

## 📌 Environment Variables

| Variable | Required | Description |
|---|---:|---|
| `APP_ENV` | Yes | `local` or `production` |
| `APP_URL` | Yes | Public/local base URL |
| `GEMINI_API_KEY` | Yes | Google Gemini API key |
| `GEMINI_MODEL` | No | Gemini model name |
| `DATABASE_URL` | Preferred on PaaS | MySQL connection URL |
| `DB_HOST` | Alternative | MySQL hostname |
| `DB_PORT` | Alternative | MySQL port |
| `DB_NAME` | Alternative | Database name |
| `DB_USER` | Alternative | Database user |
| `DB_PASS` | Alternative | Database password |

## 🤝 Contributing

See `CONTRIBUTING.md`.

## 🛡️ Security

See `SECURITY.md` for responsible vulnerability reporting.

## 📜 License

MIT License — see `LICENSE`.

---

### Project status

**Student/portfolio project — actively suitable for learning, demonstrations, hackathons, and further development.**
