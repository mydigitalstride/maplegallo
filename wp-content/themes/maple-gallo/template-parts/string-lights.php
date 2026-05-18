<?php
$top = $args['top'] ?? true;
$fill = $top ? '#faf8f2' : '#4a2c0a';
?>
<div class="string-lights" aria-hidden="true">
<svg viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
  <defs>
    <radialGradient id="bulb-glow" cx="50%" cy="50%" r="50%">
      <stop offset="0%"   stop-color="#fff8dc" stop-opacity="1"/>
      <stop offset="100%" stop-color="#c8a96e" stop-opacity="0"/>
    </radialGradient>
  </defs>

  <?php
  // Draw catenary curves + bulbs
  $num_spans = 9;
  $span_w    = 1440 / $num_spans;
  for ($i = 0; $i < $num_spans; $i++):
    $x1 = $i * $span_w;
    $x2 = ($i + 1) * $span_w;
    $mid = ($x1 + $x2) / 2;
    $sag = 28 + ($i % 3) * 5; // vary sag for realism
  ?>
  <!-- Wire span <?php echo $i; ?> -->
  <path d="M<?php echo $x1; ?> 4 Q<?php echo $mid; ?> <?php echo $sag; ?> <?php echo $x2; ?> 4"
        stroke="#4a2c0a" stroke-width="1.5" fill="none" opacity=".8"/>
  <!-- Bulb at attachment points -->
  <line x1="<?php echo $x1; ?>" y1="4"
        x2="<?php echo $x1; ?>" y2="14"
        stroke="#4a2c0a" stroke-width="1.2"/>
  <!-- Bulb body -->
  <ellipse cx="<?php echo $x1; ?>" cy="20" rx="5" ry="7" fill="#fff8dc" opacity=".9"/>
  <ellipse cx="<?php echo $x1; ?>" cy="20" rx="7" ry="9" fill="url(#bulb-glow)" opacity=".6"/>
  <?php endfor; ?>
  <!-- Last post bulb -->
  <line x1="1440" y1="4" x2="1440" y2="14" stroke="#4a2c0a" stroke-width="1.2"/>
  <ellipse cx="1440" cy="20" rx="5" ry="7" fill="#fff8dc" opacity=".9"/>
</svg>
</div>
