CI3 Peta - Bagian 1 (lightweight application + public + DB SQL)
Generated: 2025-08-14T22:26:58.339137 UTC

How to use:
1. Download CodeIgniter 3 (official) and extract on your machine.
2. Copy/replace the 'application/' folder in your CI3 project with this package's 'application/'.
3. Copy/merge 'public/' into your project's public folder (ensure files end up under web root, e.g., public/data/...).
4. Import 'database.sql' into your MySQL database (e.g., create database 'ci3_peta' and import).
5. Update application/config/database.php with your DB credentials.
6. Make sure application/cache and application/logs are writable.
7. Open /login and login as admin/admin123 (viewer/view123).

Notes:
- This package intentionally excludes the CodeIgniter 'system/' folder to keep the ZIP small.
- KML upload parsing is simple and may not handle all KML variants; for complex KML use external tools.
