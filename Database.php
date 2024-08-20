<?php
class Database {
    private static $pdo;

    public static function connect() {
        if (!self::$pdo) {
            self::$pdo = new PDO('sqlite:charity_donations.db');
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Enable foreign key support in SQLite
            self::$pdo->exec("PRAGMA foreign_keys = ON");
        }
        return self::$pdo;
    }

    public static function initialize() {
        $pdo = self::connect();
        // Create charities table with auto-incrementing ID
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS charities (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                representative_email TEXT NOT NULL
            );
        ");
        // Create donations table with auto-incrementing ID and foreign key constraint
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS donations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                donor_name TEXT NOT NULL,
                amount REAL NOT NULL,
                charity_id INTEGER NOT NULL,
                date_time TEXT NOT NULL,
                FOREIGN KEY (charity_id) REFERENCES charities(id) ON DELETE CASCADE
            );
        ");
    }
}
