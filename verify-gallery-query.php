<?php
require_once 'db.php';

// Fetch gallery photos (same as in team.php)
$stmt_gallery = $db->query("SELECT * FROM gallery_photos ORDER BY gallery_order ASC");
$gallery_photos = $stmt_gallery->fetchAll();

echo "✓ Gallery query successful\n";
echo "✓ Gallery photos count: " . count($gallery_photos) . "\n";
echo "\nGallery data:\n";

foreach ($gallery_photos as $photo) {
    echo json_encode($photo) . "\n";
}

echo "\n✓ Query verification complete. Ready for HTML template integration.\n";
?>
