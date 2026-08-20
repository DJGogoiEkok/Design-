<?php
/**
 * Gallery Photos Database Setup
 * Run once to create table and seed initial data
 * Usage: php setup-gallery.php
 */

require_once 'db.php';

try {
    // Create gallery_photos table (SQLite syntax)
    $db->exec("
        CREATE TABLE IF NOT EXISTS gallery_photos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            photo_image TEXT NOT NULL,
            photo_label TEXT NOT NULL,
            position_x INTEGER DEFAULT 0,
            position_y INTEGER DEFAULT 0,
            figure_image TEXT,
            gallery_order INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "✓ Table created: gallery_photos\n";

    // Check if data already exists
    $stmt = $db->query("SELECT COUNT(*) as count FROM gallery_photos");
    $result = $stmt->fetch();

    if ($result['count'] > 0) {
        echo "✓ Gallery data already exists (" . $result['count'] . " rows)\n";
        echo "Skipping seed step.\n";
    } else {
        // Seed initial data (8 photos + 1 figure)
        $photos = [
            ['images/team/12.jpg', 'Creative', 50, 80, NULL, 1],
            ['images/team/13.jpg', 'Ambitious', 200, 50, NULL, 2],
            ['images/team/14.jpg', 'Innovative', 350, 120, NULL, 3],
            ['images/team/15.jpg', 'Collaborative', 100, 220, NULL, 4],
            ['images/team/16.jpg', 'Driven', 280, 200, NULL, 5],
            ['images/team/17.jpg', 'Artistic', 450, 180, NULL, 6],
            ['images/team/18.jpg', 'Strategic', 150, 320, NULL, 7],
            ['images/team/19.jpg', 'Dedicated', 380, 280, NULL, 8],
            ['images/team/figure.png', '', 320, 100, 'images/team/figure.png', 9],
        ];

        $stmt = $db->prepare("
            INSERT INTO gallery_photos (photo_image, photo_label, position_x, position_y, figure_image, gallery_order)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        foreach ($photos as $photo) {
            $stmt->execute($photo);
        }
        echo "✓ Seeded " . count($photos) . " gallery photos\n";
    }

    // Verify data
    $stmt = $db->query("SELECT * FROM gallery_photos ORDER BY gallery_order ASC");
    $rows = $stmt->fetchAll();

    echo "\n✓ Gallery photos in database:\n";
    foreach ($rows as $row) {
        echo "  [" . $row['gallery_order'] . "] " . basename($row['photo_image']) . " - " . $row['photo_label'] . "\n";
    }

    echo "\n✓ Setup complete! Gallery database ready.\n";

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
