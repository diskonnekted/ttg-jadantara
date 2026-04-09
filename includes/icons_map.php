<?php
// includes/icons_map.php

// Map Categories to SVG files
// Keys are Category Names (lowercase, trimmed)
// Values are filenames in public/assets/icons/
// Each category gets a UNIQUE icon

return [
    // Core categories
    'pertanian' => 'crop-svgrepo-com.svg',                    // Tanaman/padi
    'peternakan' => 'farm-milk-farm-product-fresh-svgrepo-com.svg',  // Susu/sapi
    'perikanan' => 'farm-svgrepo-com (1).svg',                // Ikan/air
    'energi' => 'energy-svgrepo-com.svg',                     // Energi
    'kerajinan' => 'craft-art-svgrepo-com.svg',               // Kerajinan tangan
    'pengolahan limbah' => 'stove-svgrepo-com.svg',           // Daur ulang limbah
    'daur ulang' => 'cargo-farm-svgrepo-com.svg',             // Recycle
    'pengolahan' => 'farm-wheat-paddy-farm-agriculture-svgrepo-com.svg', // Pengolahan hasil
    'pengairan' => 'farm-pinwheel-garden-farm-wind-svgrepo-com.svg',    // Air/irigasi
    'tanaman obat' => 'farm-agriculture-hand-plant-grow-nature-svgrepo-com.svg', // Tanaman/herbal
    'ramah lingkungan' => 'farm-fence-garden-farm-svgrepo-com.svg',     // Eco-friendly
    'berita' => 'farm-vehicle-truck-farm-transportation-svgrepo-com.svg', // Info/berita
    'tak berkategori' => 'farm-hive-bee-garden-farm-svgrepo-com.svg',   // General

    // Legacy/alternative mappings
    'teknologi tepat guna' => 'technology-lamp-svgrepo-com.svg',
    'budidaya' => 'beehive-farm-svgrepo-com.svg',
    'tenaga surya' => 'solar-power-plant-clean-energy-solar-farm-solar-enrgy-svgrepo-com.svg',
    'pembangkit listrik' => 'farm-mill-wind-svgrepo-com.svg',
];
?>
