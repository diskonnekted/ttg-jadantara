# Role
Act as a Senior Full-Stack Developer specializing in lightweight, high-performance web applications. You are an expert in PHP, SQLite, Tailwind CSS, and Progressive Web Apps (PWA).

# Project Overview
I need you to build a "How-To" platform similar to Instructables, specifically tailored for "Teknologi Tepat Guna" (Appropriate Technology) in Kelurahan Pondokrejo, Sleman, Yogyakarta, Indonesia. 
The primary goal is to provide villagers with easy-to-follow technical tutorials (agriculture, waste management, basic repairs) via a **mobile-first** interface.

# Critical Constraints
1. **NO WordPress/CMS:** Build this using native PHP (version 8+) to ensure maximum speed and minimal resource usage.
2. **Database:** Use **SQLite** (file-based database) for simplicity and portability.
3. **Mobile-First:** The design must prioritize mobile UX. Desktop is secondary.
4. **Performance:** The site must be extremely lightweight. Aim for a Google PageSpeed Insights score of 90+ on mobile.
5. **Language:** The interface code should be in English, but the content structure must support Indonesian language (Bahasa Indonesia).

# Tech Stack
- **Backend:** Native PHP 8 (Procedural or Simple OOP). No heavy frameworks.
- **Database:** SQLite3.
- **Frontend CSS:** Tailwind CSS (via CDN for development, compiled for production).
- **Frontend JS:** Alpine.js for interactivity (modals, mobile menu, tabs).
- **PWA:** Implement Service Workers for offline capability.
- **Images:** All images must be served in WebP format with lazy loading.

# Project Directory Structure (IMPORTANT)
You must organize the files systematically for security and maintainability. Follow this exact structure:

```text
/project-root
│
├── /database                # OUTSIDE public root for security
│   └── ttg_pondokrejo.db    # SQLite database file
│
├── /public                  # Web root (accessible via browser)
│   ├── /assets
│   │   ├── /css             # Compiled Tailwind CSS
│   │   ├── /js              # Alpine.js and custom scripts
│   │   └── /images          # Static icons/logos
│   │
│   ├── /uploads             # User uploaded tutorial images
│   │   └── /thumbnails      # Optimized versions
│   │
│   ├── index.php            # Homepage
│   ├── tutorial.php         # Single tutorial view
│   ├── category.php         # Category listing
│   ├── search.php           # Search results
│   │
│   ├── /admin               # Admin panel (protected)
│   │   ├── index.php        # Admin dashboard
│   │   ├── login.php        # Login form
│   │   ├── create.php       # Add tutorial
│   │   └── edit.php         # Edit tutorial
│   │
│   ├── manifest.json        # PWA Manifest
│   ├── sw.js                # Service Worker
│   └── .htaccess            # URL rewriting & security headers
│
├── /includes                # Reusable PHP logic (NOT accessible directly)
│   ├── config.php           # DB connection & constants
│   ├── functions.php        # Helper functions (sanitize, upload, etc.)
│   ├── header.php           # HTML head & Nav
│   └── footer.php           # Scripts & closing tags
│
└── README.md                # Documentation & Installation guide