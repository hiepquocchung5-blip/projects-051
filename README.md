# projects-051
666

Urbanix Architecture (Project 051)
Routing: A central index.php acts as a front-controller to cleanly load pages (e.g., /?route=play&game=tictactoe).

Modularity: Header, footer, and game cards are separated for DRY (Don't Repeat Yourself) code.

API: Separated /api/ directory for pure JSON endpoints (used by frontend JS).

Future-Proofing: Ready for the Python AI engine via planned cURL/API calls from the PHP backend.
Project 051: Urbanix Gaming Portal

Overview

A high-performance, purely PHP/MySQL web gaming portal featuring a "Circuit Chaos" Neon UI, automated ad monetization, and an Urban Coin to MMK economy.

Directory Structure

/urbanix
├── /api                # REST API endpoints (JSON returns)
│   └── wallet.php      # Handles coin additions securely
├── /config             # Global configuration files
│   ├── database.php    # PDO connection
│   └── globals.php     # Constants and settings
├── /frontend           # User-facing application
│   ├── /components     # Reusable UI parts
│   │   └── game_card.php
│   ├── /includes       # Global layout files
│   │   ├── header.php
│   │   └── footer.php
│   ├── /pages          # Route handlers
│   │   ├── home.php
│   │   └── play.php
│   └── index.php       # Main Front Controller / Router
└── /admin              # CMS (To be implemented)


Setup (Local Development)

Use XAMPP/MAMP.

Point your virtual host document root to /urbanix/frontend.

Create a MySQL database named urbanix_db.

Import the schema (to be provided in next steps).