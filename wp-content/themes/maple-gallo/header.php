<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Celebrate Maple Gallo's graduation! Join us for a rustic farm party with eucalyptus decor, string lights, and great memories.">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header" id="site-header">
    <div class="container header-inner">
        <a class="site-logo" href="<?php echo esc_url(home_url('/')); ?>">
            🌿 <span>Maple</span> Gallo
        </a>

        <nav class="site-nav" id="site-nav" aria-label="Primary">
            <ul>
                <li><a href="#about">Her Story</a></li>
                <li><a href="#schedule">Schedule</a></li>
                <li><a href="#gallery">Gallery</a></li>
                <li><a href="#upload">Share a Photo</a></li>
                <li><a href="#quiz">Trivia</a></li>
                <li><a href="#leaderboard">Leaderboard</a></li>
                <li><a href="#donate" class="nav-cta">Donate 💚</a></li>
            </ul>
        </nav>

        <button class="menu-toggle" id="menu-toggle" aria-label="Toggle menu" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>

<!-- Spacer for fixed header -->
<div style="height:64px;"></div>
