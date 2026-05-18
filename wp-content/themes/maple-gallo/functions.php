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
    $ver = '1.0.1';
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
        'questions' => mg_get_quiz_questions(),
    ]);
});

// ── Custom Post Type: Gallery Photos ─────────────────────────
add_action('init', function () {
    register_post_type('mg_photo', [
        'labels' => [
            'name'          => 'Memories',
            'singular_name' => 'Memory',
            'add_new_item'  => 'Add New Memory',
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

// ── Custom Post Type: Stories / Tips ─────────────────────────
add_action('init', function () {
    register_post_type('mg_story', [
        'labels' => [
            'name'          => 'Stories & Tips',
            'singular_name' => 'Story',
        ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => true,
        'supports'     => ['title','custom-fields'],
        'menu_icon'    => 'dashicons-format-quote',
    ]);
});

// ── AJAX: Upload Photo (auto-publish, no review) ─────────────
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
        'post_status' => 'publish',   // auto-publish, no review needed
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
        'message'  => 'Memory added! Your photo is now in the gallery.',
        'thumb'    => wp_get_attachment_image_url($attachment_id, 'medium'),
        'full'     => wp_get_attachment_image_url($attachment_id, 'large'),
        'name'     => $name,
        'caption'  => $caption,
    ]);
}

// ── AJAX: Get Published Photos ───────────────────────────────
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
            'cat'     => get_post_meta($photo->ID, '_mg_category', true) ?: 'party',
        ];
    }
    wp_send_json_success(['photos' => $data]);
}

// ── AJAX: Submit Quiz Score ──────────────────────────────────
add_action('wp_ajax_nopriv_mg_submit_score', 'mg_submit_score');
add_action('wp_ajax_mg_submit_score',        'mg_submit_score');

function mg_submit_score() {
    check_ajax_referer('maple_gallo_nonce', 'nonce');

    $name      = sanitize_text_field($_POST['name'] ?? '');
    $score     = intval($_POST['score'] ?? 0);
    $total     = intval($_POST['total'] ?? 0);
    $maxPoints = intval($_POST['maxPoints'] ?? $total);
    $seconds   = intval($_POST['seconds'] ?? 999);

    if (empty($name) || $maxPoints < 1) {
        wp_send_json_error(['message' => 'Invalid submission.']);
    }
    $score = max(0, min($score, $maxPoints));

    wp_insert_post([
        'post_type'   => 'mg_score',
        'post_title'  => $name,
        'post_status' => 'publish',
        'meta_input'  => [
            '_mg_score'     => $score,
            '_mg_total'     => $total,
            '_mg_maxpoints' => $maxPoints,
            '_mg_seconds'   => $seconds,
            '_mg_pct'       => round(($score / $maxPoints) * 100),
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
        $maxPts = (int) get_post_meta($entry->ID, '_mg_maxpoints', true);
        $total  = (int) get_post_meta($entry->ID, '_mg_total', true);
        $data[] = [
            'rank'      => $i + 1,
            'name'      => $entry->post_title,
            'score'     => (int) get_post_meta($entry->ID, '_mg_score', true),
            'total'     => $total,
            'maxPoints' => $maxPts ?: $total,
            'pct'       => (int) get_post_meta($entry->ID, '_mg_pct', true),
            'seconds'   => (int) get_post_meta($entry->ID, '_mg_seconds', true),
            'date'      => get_the_date('M j', $entry),
        ];
    }
    wp_send_json_success(['scores' => $data]);
}

// ── AJAX: Submit Story / Life Tip ────────────────────────────
add_action('wp_ajax_nopriv_mg_submit_story', 'mg_submit_story');
add_action('wp_ajax_mg_submit_story',        'mg_submit_story');

function mg_submit_story() {
    check_ajax_referer('maple_gallo_nonce', 'nonce');

    $author = sanitize_text_field($_POST['author'] ?? '');
    $title  = sanitize_text_field($_POST['title']  ?? '');
    $body   = sanitize_textarea_field($_POST['body'] ?? '');

    if (empty($author)) wp_send_json_error(['message' => 'Please enter your name.']);
    if (empty($body))   wp_send_json_error(['message' => 'Please write your tip or story.']);
    if (mb_strlen($body) > 800) wp_send_json_error(['message' => 'Story must be 800 characters or fewer.']);

    $post_id = wp_insert_post([
        'post_type'   => 'mg_story',
        'post_title'  => $author,
        'post_status' => 'publish',
        'meta_input'  => [
            '_mg_story_title'  => $title,
            '_mg_story_body'   => $body,
            '_mg_submitted_at' => current_time('mysql'),
        ],
    ]);

    if (is_wp_error($post_id)) {
        wp_send_json_error(['message' => 'Could not save your story. Please try again.']);
    }

    wp_send_json_success([
        'message' => "Thank you, $author! Your tip has been shared.",
        'story'   => [
            'id'     => $post_id,
            'author' => $author,
            'title'  => $title,
            'body'   => $body,
            'date'   => date('M j'),
        ],
    ]);
}

// ── AJAX: Get Stories ─────────────────────────────────────────
add_action('wp_ajax_nopriv_mg_get_stories', 'mg_get_stories');
add_action('wp_ajax_mg_get_stories',        'mg_get_stories');

function mg_get_stories() {
    check_ajax_referer('maple_gallo_nonce', 'nonce');

    $stories = get_posts([
        'post_type'      => 'mg_story',
        'post_status'    => 'publish',
        'posts_per_page' => 40,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);

    $data = [];
    foreach ($stories as $s) {
        $data[] = [
            'id'     => $s->ID,
            'author' => $s->post_title,
            'title'  => get_post_meta($s->ID, '_mg_story_title', true),
            'body'   => get_post_meta($s->ID, '_mg_story_body',  true),
            'date'   => get_the_date('M j', $s),
        ];
    }
    wp_send_json_success(['stories' => $data]);
}

// ── Quiz Questions Helper ─────────────────────────────────────
function mg_get_quiz_questions(): array {
    $saved = get_option('maple_quiz_questions', '');
    if ($saved) {
        $decoded = json_decode($saved, true);
        if (is_array($decoded) && count($decoded)) return $decoded;
    }
    return mg_default_quiz_questions();
}

function mg_default_quiz_questions(): array {
    return [
        [
            'type'    => 'multiple',
            'points'  => 1,
            'q'       => 'What certification is Maple earning alongside their high school diploma?',
            'opts'    => ['EMT / Emergency Medical Technician','Nurse Aide','Firefighter I','Phlebotomist'],
            'correct' => 0,
            'fact'    => 'Maple is earning their EMT certification at the same time as graduating high school — an incredible double achievement!',
        ],
        [
            'type'    => 'multiple',
            'points'  => 1,
            'q'       => "What is Maple's favorite season?",
            'opts'    => ['Summer','Autumn','Spring','Winter'],
            'correct' => 1,
            'fact'    => 'Maple loves the golden colors and crisp air of autumn — fitting for a farm party!',
        ],
        [
            'type'    => 'yesno',
            'points'  => 2,
            'q'       => 'Has Maple ever been outside the United States?',
            'opts'    => ['Yes','No'],
            'correct' => 1,
            'fact'    => "Maple dreams of traveling abroad — Japan is at the top of their bucket list!",
        ],
        [
            'type'    => 'multiple',
            'points'  => 1,
            'q'       => "What is Maple's go-to comfort food?",
            'opts'    => ['Tacos','Mac and Cheese','Pizza','Ramen'],
            'correct' => 3,
            'fact'    => "Maple never says no to a big bowl of ramen on a cold evening!",
        ],
        [
            'type'    => 'multiple',
            'points'  => 1,
            'q'       => "Which best describes Maple's personality?",
            'opts'    => ['Calm & Introspective','Bold & Adventurous','Warm & Empathetic','Witty & Sarcastic'],
            'correct' => 2,
            'fact'    => "Maple's warmth and empathy are exactly what makes them such a perfect fit for a career in emergency medicine.",
        ],
        [
            'type'    => 'fill',
            'points'  => 3,
            'q'       => "What baked good is Maple known for making from scratch?",
            'opts'    => ['sourdough'],
            'correct' => 0,
            'fact'    => 'Maple can bake an amazing loaf of sourdough — they started during the pandemic and never stopped!',
        ],
        [
            'type'    => 'yesno',
            'points'  => 2,
            'q'       => "Does Maple prefer the outdoors over staying in?",
            'opts'    => ['Yes','No'],
            'correct' => 0,
            'fact'    => "Maple loves getting out in nature — trail walks clear their head like nothing else.",
        ],
        [
            'type'    => 'multiple',
            'points'  => 1,
            'q'       => 'Which quote best resonates with Maple?',
            'opts'    => [
                '"Be the change you wish to see in the world."',
                '"In the middle of difficulty lies opportunity."',
                '"The purpose of life is to contribute in some way to making things better."',
                '"You miss 100% of the shots you don\'t take."',
            ],
            'correct' => 2,
            'fact'    => 'Maple lives by this mindset — driven by purpose and a desire to make life better for those around them.',
        ],
    ];
}

// ── Admin Menu ────────────────────────────────────────────────
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
    add_submenu_page(
        'maple-gallo-settings',
        'Quiz Questions',
        'Quiz Questions',
        'manage_options',
        'maple-gallo-quiz',
        'mg_quiz_admin_page'
    );
});

// ── Settings Page ─────────────────────────────────────────────
function mg_settings_page() {
    if (isset($_POST['mg_save_settings'])) {
        check_admin_referer('mg_settings');
        update_option('maple_party_date',  sanitize_text_field($_POST['party_date']  ?? ''));
        update_option('maple_party_venue', sanitize_text_field($_POST['party_venue'] ?? ''));
        update_option('maple_venmo_url',   esc_url_raw($_POST['venmo_url']           ?? ''));
        update_option('maple_about_text',  sanitize_textarea_field($_POST['about_text'] ?? ''));
        echo '<div class="updated"><p>Settings saved!</p></div>';
    }

    $date  = get_option('maple_party_date',  '2026-06-15T18:00:00');
    $venue = get_option('maple_party_venue', 'The Old Oak Farm');
    $venmo = get_option('maple_venmo_url',   'https://venmo.com/maplegallo');
    $about = get_option('maple_about_text',  "Maple Gallo is officially a graduate! After years of hard work, late nights studying, and an unstoppable drive to serve their community, Maple has earned their high school diploma and their EMT Certification — at the same time.\n\nJoin us as we celebrate this incredible double milestone at a rustic farm gathering filled with good food, great music, and even better company.");

    $story_count = (int) (wp_count_posts('mg_story')->publish ?? 0);
    $photo_count = (int) (wp_count_posts('mg_photo')->publish ?? 0);
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
                    <th>Venmo Link (EMT Fund)</th>
                    <td>
                        <input type="url" name="venmo_url" value="<?php echo esc_attr($venmo); ?>" class="regular-text" placeholder="https://venmo.com/username">
                        <p class="description">Full Venmo profile URL, e.g. https://venmo.com/Maple-Gallo</p>
                    </td>
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
        <h2>Site Stats</h2>
        <ul style="font-size:14px;line-height:2">
            <li><strong><?php echo $photo_count; ?></strong> memories uploaded — <a href="<?php echo admin_url('edit.php?post_type=mg_photo'); ?>">View all</a></li>
            <li><strong><?php echo $story_count; ?></strong> stories &amp; tips shared — <a href="<?php echo admin_url('edit.php?post_type=mg_story'); ?>">View all</a></li>
        </ul>
        <p><a href="<?php echo admin_url('admin.php?page=maple-gallo-quiz'); ?>" class="button button-secondary">Manage Quiz Questions →</a></p>
    </div>
    <?php
}

// ── Quiz Questions Admin Page ─────────────────────────────────
function mg_quiz_admin_page() {
    if (isset($_POST['mg_save_quiz'])) {
        check_admin_referer('mg_quiz');
        $raw = $_POST['questions'] ?? [];
        $clean = [];
        foreach ($raw as $q) {
            $type = in_array($q['type'] ?? '', ['multiple','yesno','twooption','fill']) ? $q['type'] : 'multiple';
            $opts = array_map('sanitize_text_field', (array) ($q['opts'] ?? []));
            $clean[] = [
                'type'    => $type,
                'points'  => max(1, intval($q['points'] ?? 1)),
                'q'       => sanitize_text_field($q['q'] ?? ''),
                'opts'    => array_values($opts),
                'correct' => intval($q['correct'] ?? 0),
                'fact'    => sanitize_textarea_field($q['fact'] ?? ''),
            ];
        }
        $clean = array_filter($clean, fn($q) => !empty($q['q']));
        update_option('maple_quiz_questions', json_encode(array_values($clean)));
        echo '<div class="updated"><p>Quiz questions saved!</p></div>';
    }

    $questions = mg_get_quiz_questions();
    ?>
    <div class="wrap">
        <h1>Quiz Questions</h1>
        <p>Add, edit, or remove trivia questions about Maple. Guests answer these during the quiz.</p>

        <form method="post" id="quiz-admin-form">
            <?php wp_nonce_field('mg_quiz'); ?>
            <div id="questions-list">
                <?php foreach ($questions as $i => $q): ?>
                <?php mg_render_question_row($i, $q); ?>
                <?php endforeach; ?>
            </div>

            <p style="margin-top:16px;">
                <button type="button" class="button" id="add-question-btn">+ Add Question</button>
            </p>

            <p class="submit">
                <input type="submit" name="mg_save_quiz" class="button-primary" value="Save All Questions">
            </p>
        </form>
    </div>

    <!-- Template for new question rows -->
    <template id="question-template">
        <?php mg_render_question_row('__INDEX__', ['type'=>'multiple','points'=>1,'q'=>'','opts'=>['','','',''],'correct'=>0,'fact'=>'']); ?>
    </template>

    <style>
        .question-row { background:#fff; border:1px solid #ddd; border-radius:6px; padding:20px; margin-bottom:16px; }
        .question-row h3 { margin:0 0 16px; display:flex; justify-content:space-between; align-items:center; }
        .q-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
        .q-full { grid-column:1/-1; }
        .q-grid label { display:block; font-weight:600; margin-bottom:4px; font-size:13px; }
        .q-grid input, .q-grid textarea, .q-grid select { width:100%; }
        .correct-row { display:flex; gap:12px; align-items:center; flex-wrap:wrap; margin-top:4px; }
        .correct-row label { font-weight:normal; display:flex; align-items:center; gap:4px; cursor:pointer; }
        .q-type-meta { display:grid; grid-template-columns:1fr auto; gap:12px; }
        .fill-answer-wrap input { width:100%; }
    </style>
    <script>
    let qCount = <?php echo count($questions); ?>;
    document.getElementById('add-question-btn').addEventListener('click', () => {
        const tpl   = document.getElementById('question-template').innerHTML;
        const html  = tpl.replace(/__INDEX__/g, qCount);
        const div   = document.createElement('div');
        div.innerHTML = html;
        document.getElementById('questions-list').appendChild(div.firstElementChild);
        qCount++;
        renumberRows();
    });
    document.getElementById('questions-list').addEventListener('click', e => {
        if (e.target.classList.contains('remove-question-btn')) {
            if (!confirm('Remove this question?')) return;
            e.target.closest('.question-row').remove();
            renumberRows();
        }
    });
    document.getElementById('questions-list').addEventListener('change', e => {
        if (e.target.classList.contains('q-type-select')) {
            updateQuestionType(e.target);
        }
    });
    function updateQuestionType(sel) {
        const row = sel.closest('.question-row');
        const type = sel.value;
        row.querySelector('.opts-multiple').style.display   = type === 'multiple'  ? '' : 'none';
        row.querySelector('.opts-yesno').style.display      = type === 'yesno'     ? '' : 'none';
        row.querySelector('.opts-twooption').style.display  = type === 'twooption' ? '' : 'none';
        row.querySelector('.opts-fill').style.display       = type === 'fill'      ? '' : 'none';
    }
    // Init all existing rows on page load
    document.querySelectorAll('.q-type-select').forEach(updateQuestionType);
    function renumberRows() {
        document.querySelectorAll('.question-row').forEach((row, i) => {
            row.querySelector('.q-number').textContent = `Question ${i + 1}`;
        });
    }
    </script>
    <?php
}

function mg_render_question_row(int|string $i, array $q): void {
    $letters = ['A','B','C','D'];
    $correct = (int) ($q['correct'] ?? 0);
    $type    = in_array($q['type'] ?? '', ['multiple','yesno','twooption','fill']) ? $q['type'] : 'multiple';
    $points  = max(1, (int) ($q['points'] ?? 1));

    $show_multiple   = $type === 'multiple'  ? '' : 'display:none;';
    $show_yesno      = $type === 'yesno'     ? '' : 'display:none;';
    $show_twooption  = $type === 'twooption' ? '' : 'display:none;';
    $show_fill       = $type === 'fill'      ? '' : 'display:none;';
    ?>
    <div class="question-row">
        <h3>
            <span class="q-number">Question <?php echo is_int($i) ? $i + 1 : ''; ?></span>
            <button type="button" class="button-link remove-question-btn" style="color:#b32d2e;">Remove</button>
        </h3>
        <div class="q-grid">
            <div class="q-full">
                <label>Question Text</label>
                <input type="text" name="questions[<?php echo $i; ?>][q]"
                       value="<?php echo esc_attr($q['q'] ?? ''); ?>"
                       placeholder="e.g. What is Maple's favorite season?" required>
            </div>

            <!-- Type + Points row -->
            <div>
                <label>Question Type</label>
                <select name="questions[<?php echo $i; ?>][type]" class="q-type-select">
                    <option value="multiple"  <?php selected($type, 'multiple'); ?>>Multiple Choice (4 options)</option>
                    <option value="yesno"    <?php selected($type, 'yesno'); ?>>Yes / No</option>
                    <option value="twooption"<?php selected($type, 'twooption'); ?>>Two Options (custom labels)</option>
                    <option value="fill"     <?php selected($type, 'fill'); ?>>Fill in the Blank</option>
                </select>
            </div>
            <div>
                <label>Points</label>
                <input type="number" name="questions[<?php echo $i; ?>][points]"
                       value="<?php echo $points; ?>" min="1" max="100" style="width:100px;">
            </div>

            <!-- Multiple choice options -->
            <div class="q-full opts-multiple" style="<?php echo $show_multiple; ?>">
                <label>Answer Options</label>
                <div class="q-grid" style="margin-top:8px;">
                    <?php foreach ([0,1,2,3] as $oi): ?>
                    <div>
                        <label><?php echo $letters[$oi]; ?></label>
                        <input type="text" name="questions[<?php echo $i; ?>][opts][<?php echo $oi; ?>]"
                               value="<?php echo esc_attr($q['opts'][$oi] ?? ''); ?>"
                               placeholder="Option <?php echo $letters[$oi]; ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="correct-row" style="margin-top:12px;">
                    <strong style="font-size:13px;">Correct Answer:</strong>
                    <?php foreach ([0,1,2,3] as $oi): ?>
                    <label>
                        <input type="radio" name="questions[<?php echo $i; ?>][correct]"
                               value="<?php echo $oi; ?>"
                               <?php if ($type === 'multiple') checked($correct, $oi); ?>>
                        <?php echo $letters[$oi]; ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Yes / No options -->
            <div class="q-full opts-yesno" style="<?php echo $show_yesno; ?>">
                <label>Correct Answer</label>
                <div class="correct-row" style="margin-top:8px;">
                    <label>
                        <input type="radio" name="questions[<?php echo $i; ?>][correct]"
                               value="0"
                               <?php if ($type === 'yesno') checked($correct, 0); ?>>
                        Yes
                    </label>
                    <label>
                        <input type="radio" name="questions[<?php echo $i; ?>][correct]"
                               value="1"
                               <?php if ($type === 'yesno') checked($correct, 1); ?>>
                        No
                    </label>
                    <!-- Hidden opts so they post correctly -->
                    <input type="hidden" name="questions[<?php echo $i; ?>][opts][0]" value="Yes">
                    <input type="hidden" name="questions[<?php echo $i; ?>][opts][1]" value="No">
                </div>
            </div>

            <!-- Two Options (custom labels) -->
            <div class="q-full opts-twooption" style="<?php echo $show_twooption; ?>">
                <label>Option Labels &amp; Correct Answer</label>
                <div class="q-grid" style="margin-top:8px;">
                    <div>
                        <label>Option A</label>
                        <input type="text" name="questions[<?php echo $i; ?>][opts][0]"
                               value="<?php echo esc_attr($q['opts'][0] ?? ''); ?>"
                               placeholder="e.g. True">
                    </div>
                    <div>
                        <label>Option B</label>
                        <input type="text" name="questions[<?php echo $i; ?>][opts][1]"
                               value="<?php echo esc_attr($q['opts'][1] ?? ''); ?>"
                               placeholder="e.g. False">
                    </div>
                </div>
                <div class="correct-row" style="margin-top:10px;">
                    <strong style="font-size:13px;">Correct Answer:</strong>
                    <label>
                        <input type="radio" name="questions[<?php echo $i; ?>][correct]"
                               value="0"
                               <?php if ($type === 'twooption') checked($correct, 0); ?>>
                        A
                    </label>
                    <label>
                        <input type="radio" name="questions[<?php echo $i; ?>][correct]"
                               value="1"
                               <?php if ($type === 'twooption') checked($correct, 1); ?>>
                        B
                    </label>
                </div>
            </div>

            <!-- Fill in the blank -->
            <div class="q-full opts-fill" style="<?php echo $show_fill; ?>">
                <label>Accepted Answer <small>(case-insensitive; separate alternatives with |)</small></label>
                <div class="fill-answer-wrap" style="margin-top:6px;">
                    <input type="text" name="questions[<?php echo $i; ?>][opts][0]"
                           value="<?php echo esc_attr($q['opts'][0] ?? ''); ?>"
                           placeholder="e.g. sourdough | sourdough bread">
                    <input type="hidden" name="questions[<?php echo $i; ?>][correct]" value="0">
                </div>
            </div>

            <div class="q-full">
                <label>Fun Fact (shown after answer)</label>
                <textarea name="questions[<?php echo $i; ?>][fact]"
                          rows="2"
                          placeholder="A fun fact revealed after the guest answers…"><?php echo esc_textarea($q['fact'] ?? ''); ?></textarea>
            </div>
        </div>
    </div>
    <?php
}
