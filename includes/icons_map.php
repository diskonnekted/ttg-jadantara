<?php
// includes/icons_map.php

// Map Categories to SVG files
// Keys are Category Names (lowercase, trimmed)
// Values are filenames in public/assets/icons/

return [
    'pertanian' => 'crop-svgrepo-com.svg',
    'peternakan' => 'farm-svgrepo-com.svg',
    'perikanan' => 'farm-svgrepo-com (1).svg', // Or specific fish icon if available
    'kesehatan' => 'milk-food-and-restaurant-svgrepo-com.svg', // Placeholder
    'energi' => 'energy-svgrepo-com.svg',
    'lingkungan' => 'farm-agriculture-hand-plant-grow-nature-svgrepo-com.svg',
    'pengolahan limbah' => 'stove-svgrepo-com.svg', // Placeholder
    'kerajinan' => 'craft-art-svgrepo-com.svg',
    'teknologi tepat guna' => 'technology-lamp-svgrepo-com.svg',
    'budidaya' => 'farm-wheat-paddy-farm-agriculture-svgrepo-com.svg',
    'umum' => 'farm-fence-garden-farm-svgrepo-com.svg',
    
    // Specific mappings based on imported data
    'budidaya ayam' => 'beehive-farm-svgrepo-com.svg', // Fallback
    'budidaya ikan' => 'farm-svgrepo-com (1).svg',
    'tenaga surya' => 'solar-power-plant-clean-energy-solar-farm-solar-enrgy-svgrepo-com.svg',
    'pembangkit listrik' => 'farm-mill-wind-svgrepo-com.svg',
];
?>
