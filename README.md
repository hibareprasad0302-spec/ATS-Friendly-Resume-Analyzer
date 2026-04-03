# ATS Friendly Resume Analyzer

A full-featured web application that scores your resume against any job description using ATS (Applicant Tracking System) compatibility analysis. Get a detailed 100-point score across 7 categories with actionable improvement suggestions and downloadable PDF reports.

## Features

- **Resume Upload** — Supports PDF and DOCX formats (up to 10 MB)
- **7-Category Scoring** — Keywords, Skills, Sections, Projects, Experience, Education, and Formatting
- **Keyword Analysis** — Identifies matched and missing keywords from the job description
- **Skill Matching** — Compares your skills against a curated database of 150+ skills with alias support
- **Section Detection** — Verifies your resume has all the sections ATS systems look for
- **Smart Suggestions** — Prioritized, actionable tips to improve your score (high/medium/low priority)
- **PDF Report Download** — Download a complete color-coded analysis report
- **Interactive Charts** — Radar chart and category bar visualization with distinct colors
- **Analysis History** — View and download past reports (requires login)
- **User Authentication** — Register/login system with session management
- **Admin Panel** — Manage users, view all reports, and curate the skills database
- **Input Validation** — Rejects non-resume files and gibberish job descriptions using category-based phrase detection
- **Responsive Design** — Works on desktop and mobile with a modern UI

## Screenshots

### Landing Page
The landing page features animated gradient blobs, a hero section with an animated score gauge, and scroll-reveal animations.

### Analyzer
Upload your resume, paste a job description, optionally enter a job role, and get your score.

### Results
Interactive results page with a radar chart, color-coded category bars, tabbed keyword/skill views, and expandable suggestions.

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.4+ |
| Frontend | Tailwind CSS (CDN), Chart.js |
| Database | MySQL (InnoDB, utf8mb4) |
| PDF Parsing | smalot/pdfparser |
| DOCX Parsing | phpoffice/phpword |
| PDF Generation | dompdf/dompdf |
| Font | Inter (Google Fonts) |

## Project Structure

```
.
├── admin/                  # Admin panel (users, reports, skills management)
├── api/                    # REST API endpoints
│   ├── analyze.php         # Main analysis endpoint
│   ├── download_report.php # PDF report generation & download
│   ├── history.php         # Paginated report history
│   ├── login.php           # User login
│   ├── register.php        # User registration
│   ├── report.php          # Fetch single report
│   └── upload.php          # File upload handler
├── assets/
│   ├── css/custom.css      # Custom styles, animations, blob effects
│   ├── images/             # Logo & favicon (SVG)
│   └── js/                 # Frontend scripts (analyzer, result, history, auth, admin)
├── classes/                # PHP classes
│   ├── ContentValidator.php    # Input quality validation
│   ├── FileUploader.php        # Secure file upload handling
│   ├── KeywordExtractor.php    # TF-IDF keyword extraction
│   ├── ReportGenerator.php     # Report storage & retrieval
│   ├── ResumeAnalyzer.php      # Main analysis orchestrator
│   ├── ScoringEngine.php       # 7-category scoring logic
│   ├── SuggestionsEngine.php   # Improvement suggestions generator
│   ├── TextExtractor.php       # PDF/DOCX text extraction
│   ├── TextProcessor.php       # Text normalization & cleaning
│   └── User.php                # User authentication model
├── data/                   # Static data (section headers, stopwords, default skills SQL)
├── includes/               # Shared PHP includes (config, db, header, footer, session, auth)
├── sql/schema.sql          # Database schema
├── index.php               # Landing page
├── analyzer.php            # Upload & analysis page
├── result.php              # Results display page
├── history.php             # Analysis history page
├── login.php               # Login page
├── register.php            # Registration page
└── composer.json           # PHP dependencies
```

## Scoring Breakdown

| Category | Weight | What It Measures |
|----------|--------|-----------------|
| Keywords | 30 pts | Job description keyword matches in your resume |
| Skills | 20 pts | Technical and soft skills matched against a curated database |
| Sections | 15 pts | Presence of standard resume sections (education, experience, skills, etc.) |
| Projects | 10 pts | Project-related content and descriptions |
| Experience | 10 pts | Work experience details and quantifiable achievements |
| Education | 5 pts | Educational qualifications and certifications |
| Formatting | 10 pts | Resume structure, length, and formatting quality |

## Setup & Installation

### Prerequisites

- PHP 8.4 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Composer
- A local web server (Apache, Nginx, or PHP built-in server)

### Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/hibareprasad0302-spec/ATS-Friendly-Resume-Analyzer.git
   cd ATS-Friendly-Resume-Analyzer
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Create the database**
   ```bash
   mysql -u root -p < sql/schema.sql
   ```

4. **Load default skills** (optional but recommended)
   ```bash
   mysql -u root -p ats_resume_analyzer < data/default_skills.sql
   ```

5. **Configure the database connection**

   Edit `includes/config.php` and update the database credentials if needed:
   ```php
   define('DB_HOST', '127.0.0.1');
   define('DB_PORT', 3306);
   define('DB_NAME', 'ats_resume_analyzer');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

6. **Create the uploads directory** (if not exists)
   ```bash
   mkdir -p uploads
   chmod 755 uploads
   ```

7. **Start the development server**
   ```bash
   php -S localhost:8080
   ```

8. **Open in browser**

   Visit `http://localhost:8080`

## How It Works

1. **Upload** — User uploads a resume (PDF/DOCX) and pastes a job description
2. **Extract** — Text is extracted from the document using PDFParser or PHPWord
3. **Validate** — Content is validated to ensure it's a real resume and a real job description
4. **Analyze** — The resume is scored across 7 categories using keyword extraction, skill matching, section detection, and formatting analysis
5. **Report** — Results are saved to the database and displayed with interactive charts, matched/missing keywords, and prioritized suggestions

## Built With

Developed with the assistance of [Claude Code](https://claude.ai) (Claude Opus 4) by Anthropic.

## License

This project is open source and available for personal and educational use.
