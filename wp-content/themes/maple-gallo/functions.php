<?php
defined('ABSPATH') || exit;

// ── Theme Setup ──────────────────────────────────────────────
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption','style','script']);

    register_nav_menus(['primary' => __('Primary Menu', 'maple-gallo')]);
});

// ── Enqueue Assets ───────────────────────────────────────────
add_action('wp_enqueue_scripts', function () {
    $ver = '1.0.0';
    $uri = get_template_directory_uri();

    wp_enqueue_style('google-fonts',
        'https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Lato:wght@400;600;700&display=swap',
        [], null
    );
    wp_enqueue_style('maple-gallo', get_stylesheet_uri(), ['google-fonts'], $ver);
    wp_enqueue_script('maple-gallo', $uri . '/js/main.js', [], $ver, true);

    wp_localize_script('maple-gallo', 'mapleGallo', [
        'ajaxUrl'   => admin_url('admin-ajax.php'),
        'nonce'     => wp_create_nonce('maple_gallo_nonce'),
        'partyDate' => get_option('maple_party_date', '2026-06-15T18:00:00'),
    ]);
});

// ── Custom Post Type: Gallery Photos ─────────────────────────
add_action('init', function () {
    register_post_type('mg_photo', [
        'labels' => [
            'name'          => 'Gallery Photos',
            'singular_name' => 'Photo',
            'add_new_item'  => 'Add New Photo',
        ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => true,
        'supports'     => ['title','thumbnail','custom-fields'],
        'menu_icon'    => 'dashicons-camera',
    ]);
});

// ── Custom Post Type: Quiz Scores ────────────────────────────
add_action('init', function () {
    register_post_type('mg_score', [
        'labels' => [
            'name'          => 'Quiz Scores',
            'singular_name' => 'Score',
        ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => true,
        'supports'     => ['title','custom-fields'],
        'menu_icon'    => 'dashicons-awards',
    ]);
});

// ── Custom Post Type: Donations ──────────────────────────────
add_action('init', function () {
    register_post_type('mg_donation', [
        'labels' => [
            'name'          => 'Donations',
            'singular_name' => 'Donation',
        ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => true,
        'supports'     => ['title','custom-fields'],
        'menu_icon'    => 'dashicons-heart',
    ]);
});

// ── AJAX: Upload Photo ───────────────────────────────────────
add_action('wp_ajax_nopriv_mg_upload_photo', 'mg_handle_photo_upload');
add_action('wp_ajax_mg_upload_photo',        'mg_handle_photo_upload');

function mg_handle_photo_upload() {
    check_ajax_referer('maple_gallo_nonce', 'nonce');

    $name    = sanitize_text_field($_POST['uploader_name'] ?? '');
    $caption = sanitize_textarea_field($_POST['caption'] ?? '');

    if (empty($name)) {
        wp_send_json_error(['message' => 'Please enter your name.']);
    }

    if (empty($_FILES['photo'])) {
        wp_send_json_error(['message' => 'No photo selected.']);
    }

    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $allowed_types = ['image/jpeg','image/png','image/gif','image/webp'];
    if (!in_array($_FILES['photo']['type'], $allowed_types, true)) {
        wp_send_json_error(['message' => 'Only JPEG, PNG, GIF, or WebP images are allowed.']);
    }

    // 10 MB limit
    if ($_FILES['photo']['size'] > 10 * 1024 * 1024) {
        wp_send_json_error(['message' => 'Photo must be under 10 MB.']);
    }

    $attachment_id = media_handle_upload('photo', 0);

    if (is_wp_error($attachment_id)) {
        wp_send_json_error(['message' => $attachment_id->get_error_message()]);
    }

    $post_id = wp_insert_post([
        'post_type'   => 'mg_photo',
        'post_title'  => $name,
        'post_status' => 'pending',
        'meta_input'  => [
            '_mg_caption'       => $caption,
            '_mg_attachment_id' => $attachment_id,
            '_mg_uploader'      => $name,
            '_mg_uploaded_at'   => current_time('mysql'),
        ],
    ]);

    if (is_wp_error($post_id)) {
        wp_send_json_error(['message' => 'Could not save photo.']);
    }

    set_post_thumbnail($post_id, $attachment_id);

    wp_send_json_success([
        'message'   => 'Your photo has been submitted! It will appear in the gallery after review.',
        'thumb_url' => wp_get_attachment_image_url($attachment_id, 'thumbnail'),
    ]);
}

// ── AJAX: Get Approved Photos ────────────────────────────────
add_action('wp_ajax_nopriv_mg_get_photos', 'mg_get_photos');
add_action('wp_ajax_mg_get_photos',        'mg_get_photos');

function mg_get_photos() {
    check_ajax_referer('maple_gallo_nonce', 'nonce');

    $category = sanitize_key($_POST['category'] ?? 'all');

    $args = [
        'post_type'      => 'mg_photo',
        'post_status'    => 'publish',
        'posts_per_page' => 60,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];

    if ($category !== 'all') {
        $args['meta_query'] = [['key' => '_mg_category', 'value' => $category]];
    }

    $photos = get_posts($args);
    $data   = [];

    foreach ($photos as $photo) {
        $att_id = get_post_meta($photo->ID, '_mg_attachment_id', true);
        $data[] = [
            'id'      => $photo->ID,
            'name'    => $photo->post_title,
            'caption' => get_post_meta($photo->ID, '_mg_caption', true),
            'thumb'   => wp_get_attachment_image_url($att_id, 'medium'),
            'full'    => wp_get_attachment_image_url($att_id, 'large'),
            'cat'     => get_post_meta($photo->ID, '_mg_category', true),
        ];
    }

    wp_send_json_success(['photos' => $data]);
}

// ── AJAX: Admin Approve Photo ────────────────────────────────
add_action('wp_ajax_mg_approve_photo', function () {
    check_ajax_referer('maple_gallo_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error();

    $id = intval($_POST['photo_id'] ?? 0);
    wp_update_post(['ID' => $id, 'post_status' => 'publish']);
    wp_send_json_success();
});

add_action('wp_ajax_mg_reject_photo', function () {
    check_ajax_referer('maple_gallo_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error();

    $id = intval($_POST['photo_id'] ?? 0);
    wp_delete_post($id, true);
    wp_send_json_success();
});

// ── AJAX: Submit Quiz Score ──────────────────────────────────
add_action('wp_ajax_nopriv_mg_submit_score', 'mg_submit_score');
add_action('wp_ajax_mg_submit_score',        'mg_submit_score');

function mg_submit_score() {
    check_ajax_referer('maple_gallo_nonce', 'nonce');

    $name    = sanitize_text_field($_POST['name'] ?? '');
    $score   = intval($_POST['score'] ?? 0);
    $total   = intval($_POST['total'] ?? 0);
    $seconds = intval($_POST['seconds'] ?? 999);

    if (empty($name) || $total < 1) {
        wp_send_json_error(['message' => 'Invalid submission.']);
    }

    $score = max(0, min($score, $total));

    wp_insert_post([
        'post_type'   => 'mg_score',
        'post_title'  => $name,
        'post_status' => 'publish',
        'meta_input'  => [
            '_mg_score'   => $score,
            '_mg_total'   => $total,
            '_mg_seconds' => $seconds,
            '_mg_pct'     => round(($score / $total) * 100),
        ],
    ]);

    wp_send_json_success(['message' => 'Score saved!']);
}

// ── AJAX: Get Leaderboard ────────────────────────────────────
add_action('wp_ajax_nopriv_mg_get_leaderboard', 'mg_get_leaderboard');
add_action('wp_ajax_mg_get_leaderboard',        'mg_get_leaderboard');

function mg_get_leaderboard() {
    check_ajax_referer('maple_gallo_nonce', 'nonce');

    $scores = get_posts([
        'post_type'      => 'mg_score',
        'post_status'    => 'publish',
        'posts_per_page' => 100,
        'meta_key'       => '_mg_pct',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
    ]);

    $data = [];
    foreach ($scores as $i => $entry) {
        $data[] = [
            'rank'    => $i + 1,
            'name'    => $entry->post_title,
            'score'   => (int) get_post_meta($entry->ID, '_mg_score', true),
            'total'   => (int) get_post_meta($entry->ID, '_mg_total', true),
            'pct'     => (int) get_post_meta($entry->ID, '_mg_pct', true),
            'seconds' => (int) get_post_meta($entry->ID, '_mg_seconds', true),
            'date'    => get_the_date('M j', $entry),
        ];
    }

    wp_send_json_success(['scores' => $data]);
}

// ── AJAX: Donation ───────────────────────────────────────────
add_action('wp_ajax_nopriv_mg_donate', 'mg_handle_donation');
add_action('wp_ajax_mg_donate',        'mg_handle_donation');

function mg_handle_donation() {
    check_ajax_referer('maple_gallo_nonce', 'nonce');

    $name    = sanitize_text_field($_POST['donor_name'] ?? '');
    $email   = sanitize_email($_POST['donor_email'] ?? '');
    $amount  = floatval($_POST['amount'] ?? 0);
    $message = sanitize_textarea_field($_POST['message'] ?? '');

    if (empty($name)) wp_send_json_error(['message' => 'Please enter your name.']);
    if ($amount < 1)  wp_send_json_error(['message' => 'Please enter a valid amount.']);

    // Record donation (payment processor integration goes here)
    $post_id = wp_insert_post([
        'post_type'   => 'mg_donation',
        'post_title'  => $name . ' — $' . number_format($amount, 2),
        'post_status' => 'publish',
        'meta_input'  => [
            '_mg_donor_name'  => $name,
            '_mg_donor_email' => $email,
            '_mg_amount'      => $amount,
            '_mg_message'     => $message,
            '_mg_donated_at'  => current_time('mysql'),
            '_mg_status'      => 'pending_payment',
        ],
    ]);

    // Send notification email to admin
    $admin_email = get_option('admin_email');
    wp_mail(
        $admin_email,
        'New Donation Pledge — Maple Gallo Graduation',
        sprintf(
            "%s has pledged \$%s to Maple's EMT fund.\n\nMessage: %s\n\nEmail: %s\n\nPlease follow up to collect payment.",
            $name,
            number_format($amount, 2),
            $message ?: '(none)',
            $email
        )
    );

    // Thank-you email to donor
    if (!empty($email)) {
        wp_mail(
            $email,
            'Thank you for supporting Maple\'s EMT Future Fund!',
            sprintf(
                "Hi %s,\n\nThank you for pledging \$%s to Maple's EMT Future Fund!\n\nYour generosity means the world to us. We'll be in touch about collecting your donation.\n\nWith gratitude,\nThe Gallo Family",
                $name,
                number_format($amount, 2)
            )
        );
    }

    $total_raised = mg_get_total_raised();

    wp_send_json_success([
        'message'      => "Thank you, $name! Your pledge of \$$amount has been recorded.",
        'total_raised' => $total_raised,
    ]);
}

// ── Donation total helper ─────────────────────────────────────
function mg_get_total_raised(): float {
    global $wpdb;
    $total = $wpdb->get_var(
        "SELECT SUM(meta_value) FROM {$wpdb->postmeta}
         INNER JOIN {$wpdb->posts} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id
         WHERE {$wpdb->posts}.post_type = 'mg_donation'
           AND {$wpdb->posts}.post_status = 'publish'
           AND {$wpdb->postmeta}.meta_key = '_mg_amount'"
    );
    return (float) ($total ?? 0);
}

// ── Settings Page ─────────────────────────────────────────────
add_action('admin_menu', function () {
    add_menu_page(
        'Maple Gallo Party',
        'Party Settings',
        'manage_options',
        'maple-gallo-settings',
        'mg_settings_page',
        'dashicons-palmtree',
        3
    );
});

function mg_settings_page() {
    if (isset($_POST['mg_save_settings'])) {
        check_admin_referer('mg_settings');
        update_option('maple_party_date',   sanitize_text_field($_POST['party_date'] ?? ''));
        update_option('maple_party_venue',  sanitize_text_field($_POST['party_venue'] ?? ''));
        update_option('maple_party_goal',   floatval($_POST['party_goal'] ?? 2000));
        update_option('maple_about_text',   sanitize_textarea_field($_POST['about_text'] ?? ''));
        echo '<div class="updated"><p>Settings saved!</p></div>';
    }

    $date  = get_option('maple_party_date',  '2026-06-15T18:00:00');
    $venue = get_option('maple_party_venue', 'The Old Oak Farm, 123 Country Lane');
    $goal  = get_option('maple_party_goal',  2000);
    $about = get_option('maple_about_text',  "Maple Gallo is officially a graduate! After years of hard work, late nights studying, and an unstoppable drive to serve her community, Maple has earned her degree and is on her way to becoming a certified EMT.\n\nJoin us as we celebrate this incredible milestone at a rustic farm gathering filled with good food, great music, and even better company.");

    ?>
    <div class="wrap">
        <h1>🌿 Maple Gallo Party Settings</h1>
        <form method="post" action="">
            <?php wp_nonce_field('mg_settings'); ?>
            <table class="form-table">
                <tr>
                    <th>Party Date/Time</th>
                    <td><input type="datetime-local" name="party_date" value="<?php echo esc_attr(str_replace(' ', 'T', $date)); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th>Venue</th>
                    <td><input type="text" name="party_venue" value="<?php echo esc_attr($venue); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th>Donation Goal ($)</th>
                    <td><input type="number" name="party_goal" value="<?php echo esc_attr($goal); ?>" step="100" min="0" class="small-text"></td>
                </tr>
                <tr>
                    <th>About Maple</th>
                    <td><textarea name="about_text" rows="6" class="large-text"><?php echo esc_textarea($about); ?></textarea></td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="mg_save_settings" class="button-primary" value="Save Settings">
            </p>
        </form>

        <hr>
        <h2>Donation Summary</h2>
        <p><strong>Total Raised:</strong> $<?php echo number_format(mg_get_total_raised(), 2); ?> of $<?php echo number_format((float) get_option('maple_party_goal', 2000), 2); ?> goal</p>
    </div>
    <?php
}
