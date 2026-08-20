<?php
require 'db.php';

try {
    // Create the gallery_settings table if it doesn't exist
    $db->exec("
        CREATE TABLE IF NOT EXISTS gallery_settings (
            id INTEGER PRIMARY KEY,
            background_image_path TEXT NULL
        )
    ");

    echo "✓ Table created: gallery_settings\n";

    // Check if initial row exists
    $stmt = $db->query("SELECT COUNT(*) as count FROM gallery_settings WHERE id = 1");
    $result = $stmt->fetch();

    if ($result['count'] == 0) {
        // Insert initial row
        $db->exec("INSERT INTO gallery_settings (id, background_image_path) VALUES (1, NULL)");
        echo "✓ Inserted initial row\n";
    } else {
        echo "✓ Initial row already exists\n";
    }

    // Verify the table is ready
    $stmt = $db->query("SELECT * FROM gallery_settings WHERE id = 1");
    $settings = $stmt->fetch();

    echo "✓ Gallery settings ready\n";

    // Display current background
    $bg = $settings['background_image_path'] ?? 'None';
    echo "Current background: " . $bg . "\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
