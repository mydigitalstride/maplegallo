<?php
// Generates a theme screenshot as an SVG for WordPress admin
header('Content-Type: image/svg+xml');
?>
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="900" viewBox="0 0 1200 900">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%" stop-color="#faf8f2"/>
      <stop offset="100%" stop-color="#f2ede0"/>
    </linearGradient>
  </defs>
  <rect width="1200" height="900" fill="url(#bg)"/>
  <!-- Header -->
  <rect width="1200" height="70" fill="#faf8f2" opacity=".95"/>
  <text x="60" y="44" font-family="Georgia,serif" font-size="28" fill="#4a2c0a">🌿 Maple Gallo</text>
  <!-- Hero area -->
  <rect y="70" width="1200" height="360" fill="#f2ede0"/>
  <text x="600" y="200" text-anchor="middle" font-family="Georgia,serif" font-size="56" fill="#4a2c0a">Congratulations</text>
  <text x="600" y="265" text-anchor="middle" font-family="Georgia,serif" font-size="52" fill="#4e7260" font-style="italic">Maple Gallo</text>
  <text x="600" y="305" text-anchor="middle" font-family="Arial,sans-serif" font-size="22" fill="#8b5e3c">Class of 2026 · EMT Graduate</text>
  <!-- String lights -->
  <path d="M0 430 Q150 450 300 430 Q450 410 600 430 Q750 450 900 430 Q1050 410 1200 430" stroke="#4a2c0a" stroke-width="2" fill="none"/>
  <?php for ($i = 0; $i <= 10; $i++): $x = $i * 120; ?>
  <ellipse cx="<?php echo $x; ?>" cy="445" rx="8" ry="11" fill="#fff8dc"/>
  <?php endfor; ?>
  <!-- Colors swatch bar -->
  <rect y="470" width="1200" height="8" fill="#7a9e87"/>
  <!-- Gallery preview -->
  <rect y="490" width="380" height="260" x="20" rx="8" fill="#b2cdb9"/>
  <rect y="490" width="380" height="260" x="420" rx="8" fill="#7a9e87"/>
  <rect y="490" width="340" height="260" x="820" rx="8" fill="#4e7260"/>
  <text x="600" y="640" text-anchor="middle" font-family="Arial,sans-serif" font-size="20" fill="white" opacity=".7">Photo Gallery</text>
  <!-- Bottom bar -->
  <rect y="780" width="1200" height="120" fill="#4a2c0a"/>
  <text x="600" y="845" text-anchor="middle" font-family="Georgia,serif" font-size="28" fill="#c8a96e">🌿 Maple Gallo · Class of 2026</text>
</svg>
