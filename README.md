# National County Sports System

A full-featured web-based sports management system for the Ministry of Youth & Sports, Republic of Liberia. Manage player registrations, approval workflows, matches, live scores, league standings, documents, and reports across multiple sports disciplines.

## Features

### 👥 Role-Based Access
- **Super Admin** — full system control: manage players, matches, documents, counties, reports, seed data
- **County Coordinator** — register and manage players within their assigned county group (A/B/C/D)
- **Association Admin** — approve/reject player registrations for their specific sport (LFA, LKA, LBA, LAA)

### 📋 Player Registration
- National ID (NIR) number, full name, DOB, gender, nationality
- Age dropdown (0–35), city, last club, current club
- County of representation (grouped by A/B/C/D)
- Sport discipline with primary level (1st Division–Virgin, Mass)
- Emergency contact and medical fitness tracking
- Photo upload with preview
- Save as draft or submit for approval

### ✅ Approval Workflow
- Submit → Association Admin reviews → Approve / Return for Revision / Reject
- Full audit trail via `approval_workflow` table
- Notifications sent to registrant on action

### 🏟️ Games & Live Scores
- Create matches with home/away teams, date, round, group
- Quick score entry for live/in-progress matches
- Auto-calculated league standings (P/W/D/L/GF/GA/GD/Pts)
- Kickball-specific standings with HRF/HRA/HRD columns
- Live scores page auto-refreshes every 30 seconds
- Color-coded status badges (Scheduled / LIVE / Completed)

### 📄 Document Management
- Upload documents (PDF, DOC, DOCX, XLS, XLSX, TXT, JPG, PNG) — Super Admin only
- All users can browse and download documents
- Paginated listing with file size and upload info

### 📊 Reports
- Overview stats, per county, per sport, and per group views
- CSV export for offline analysis

### 📈 Dashboard
- Stat cards: Total Players, Female, Male, Approved, Rejected, Counties
- Recent registrations table with inline actions
- Charts: gender distribution (doughnut), sports distribution (bar), players by county (polar area)
- Pending approvals widget (Association Admin only)

## Tech Stack

| Component | Technology |
|-----------|-----------|
| **Backend** | PHP 8.x (PDO, prepared statements) |
| **Database** | MySQL / MariaDB (InnoDB, utf8mb4) |
| **Server** | Apache (XAMPP) |
| **CSS** | Bootstrap 5.3.2, Bootstrap Icons 1.11.3 |
| **JavaScript** | jQuery 3.7.1, Select2 4.1.0, Chart.js 4.4.1 |
| **Auth** | bcrypt password hashing, PHP sessions |

## Installation

### Prerequisites
- XAMPP (or any Apache + PHP + MySQL stack)
- PHP 8.0+
- MySQL 5.7+ or MariaDB 10.3+

### Setup
```bash
# 1. Clone the repository into XAMPP's htdocs
git clone https://github.com/your-username/sports-meet-portal.git
# or copy the folder to C:\xampp\htdocs\National County Sports System

# 2. Import the database schema
# Open phpMyAdmin (http://localhost/phpmyadmin) and run:
#   database/schema.sql
# Or via command line:
mysql -u root < database/schema.sql

# 3. Configure database credentials (if different from defaults)
# Edit: includes/config.php
#   DB_HOST, DB_NAME, DB_USER, DB_PASS

# 4. Start Apache and MySQL from XAMPP Control Panel

# 5. Seed the database with default data
# Visit: http://localhost/National%20County%20Sports%20System/seed.php
# Login: admin / admin123

# 6. Access the application
# Visit: http://localhost/National%20County%20Sports%20System/
```

> **Note:** The URL contains spaces (`National%20County%20Sports%20System`). You may rename the folder to avoid URL encoding issues.

## Default Users

All default users use password: `admin123`

### Super Admin
| Username | Role |
|----------|------|
| `admin` | System Administrator |

### County Coordinators
| Username | County | Group |
|----------|--------|-------|
| `coordinator` | Montserrado | A |
| `rivercess_coord` | River Cess | A |
| `bong_coord` | Bong | B |
| `grandgedeh_coord` | Grand Gedeh | C |
| `grandkru_coord` | Grand Kru | D |

### Association Admins
| Username | Association | Sport |
|----------|-------------|-------|
| `lfa_admin` | LFA | Football |
| `lka_admin` | LKA | Kickball |
| `lba_admin` | LBA | Basketball |
| `laa_admin` | LAA | Athletics |

## Database Overview

### Tables (7)
| Table | Purpose |
|-------|---------|
| `users` | System users with role-based access |
| `counties` | 15 counties grouped into A/B/C/D |
| `sports_disciplines` | Football, Kickball, Basketball, Athletics |
| `players` | Core player registration with 20+ fields |
| `approval_workflow` | Audit trail for registration approvals |
| `matches` | Fixtures, scores, status per sport |
| `documents` | Uploaded file metadata |
| `notifications` | User notifications |
| `activity_logs` | Audit trail for all actions |

## County Groupings

| Group A | Group B | Group C | Group D |
|---------|---------|---------|---------|
| Montserrado | Nimba | Grand Gedeh | Grand Cape Mount |
| Margibi | Lofa | River Gee | Bomi |
| Grand Bassa | Bong | Sinoe | Grand Kru |
| River Cess | Gbarpolu | Maryland | |

## Roles & Capabilities

| Capability | Super Admin | County Coordinator | Association Admin |
|------------|:-----------:|:------------------:|:-----------------:|
| Register players | ✅ | ✅ (group-scoped) | ❌ |
| List players | ✅ (all) | ✅ (group-scoped) | ✅ (sport-scoped) |
| Edit players | ✅ (any) | ✅ (own group + own drafts) | ❌ |
| Delete players | ✅ | ❌ | ❌ |
| Approve/Reject players | ❌ | ❌ | ✅ (sport-scoped) |
| Manage matches | ✅ | ❌ | ❌ |
| Upload documents | ✅ | ❌ | ❌ |
| View reports | ✅ | ✅ (group-scoped) | ✅ (sport-scoped) |
| Seed database | ✅ | ❌ | ❌ |

## Project Structure

```
├── auth/                    # Login / logout
├── assets/
│   ├── css/style.css        # Custom styles
│   ├── js/main.js           # jQuery UI interactions, Chart.js
│   └── images/              # Logos, county flags, default avatar
├── database/schema.sql      # Full MySQL schema
├── includes/
│   ├── config.php           # Constants, session start
│   ├── db.php               # PDO singleton
│   └── functions.php        # 30+ helper functions
├── pages/
│   ├── dashboard.php        # Home page with stats, charts, recent players
│   ├── profile.php          # User profile
│   ├── change_password.php  # Password change
│   ├── players/             # Register, list, view, edit, delete
│   ├── approvals/           # Pending reviews, history
│   ├── games/               # Live scores, manage, standings, kickball standings
│   ├── counties/manage.php  # County reference
│   ├── documents/list.php   # Document browser
│   └── reports/index.php    # Tabbed reports with CSV export
├── templates/
│   ├── header.php           # Navbar, sidebar, HTML head
│   └── footer.php           # JS includes, closing tags
├── uploads/
│   ├── photos/              # Player photos
│   └── documents/           # Uploaded documents
├── index.php                # Entry point
└── seed.php                 # Database seeder
```

## Screenshots

*(Add screenshots of the dashboard, registration form, games page, and standings here.)*

## License

This project is developed for the Ministry of Youth & Sports, Republic of Liberia.
