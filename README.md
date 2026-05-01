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

Project 051: Urbanix Gaming Portal

Directory Structure (Updated)

/urbanix
├── /api                      # REST API endpoints
│   ├── auth.php              # Google Auth & Session management
│   ├── wallet.php            # Coin transaction handler
│   └── telegram_webhook.php  # Telegram Bot listener
├── /config                   # Global configurations
│   ├── database.php          
│   └── globals.php           
├── /cron                     # Server background tasks
│   └── convert.php           # 5-hour Coin to MMK converter
├── /frontend                 # User-facing application
│   ├── /components           # Reusable UI (Modals, Cards)
│   │   ├── game_card.php
│   │   └── telegram_modal.php
│   ├── /games                # Isolated Game Logic (JS/HTML)
│   │   ├── tictactoe.php
│   │   └── cybermole.php
│   ├── /includes             # Layout files
│   │   ├── header.php
│   │   └── footer.php
│   ├── /pages                # Route handlers
│   │   ├── home.php
│   │   └── play.php
│   └── index.php             # Main Router
└── /database
    └── urbanix_db.sql        # Schema



Setup (Local Development)

Use XAMPP/MAMP.

Point your virtual host document root to /urbanix/frontend.

Create a MySQL database named urbanix_db.

Import the schema (to be provided in next steps).