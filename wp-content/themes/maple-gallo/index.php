<?php
defined('ABSPATH') || exit;
get_header();

$party_date  = get_option('maple_party_date',  '2026-06-15T18:00:00');
$party_venue = get_option('maple_party_venue', 'The Old Oak Farm');
$about_text  = get_option('maple_about_text',  "Maple Gallo is officially a graduate! After years of hard work, late nights studying, and an unstoppable drive to serve their community, Maple has earned their high school diploma and their EMT Certification — at the same time.\n\nJoin us as we celebrate this incredible double milestone at a rustic farm gathering filled with good food, great music, and even better company.");
$venmo_url   = get_option('maple_venmo_url',   'https://venmo.com/maplegallo');

$party_datetime = new DateTime($party_date);
$formatted_date = $party_datetime->format('F j, Y');
$formatted_time = $party_datetime->format('g:i A');

$quiz_count = count(mg_get_quiz_questions());
?>

<!-- ╔══════════════════════════════════════════════════════════╗
     ║  HERO                                                    ║
     ╚══════════════════════════════════════════════════════════╝ -->
<section class="hero" id="home">
    <div class="hero-eucalyptus-left" aria-hidden="true"><?php echo mg_eucalyptus_svg(); ?></div>
    <div class="hero-eucalyptus-right" aria-hidden="true"><?php echo mg_eucalyptus_svg(); ?></div>

    <div class="hero-content">
        <span class="hero-eyebrow">You're Invited to Celebrate</span>
        <h1 class="hero-title">
            Congratulations
            <span class="hero-title-name">Maple Gallo</span>
        </h1>
        <p class="hero-subtitle">Class of 2026 &nbsp;·&nbsp; High School Graduate &amp; EMT Certified</p>

        <div class="hero-details">
            <div class="hero-detail">
                <svg class="hero-detail-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span class="hero-detail-label">Date</span>
                <span class="hero-detail-value"><?php echo esc_html($formatted_date); ?></span>
            </div>
            <div class="hero-detail">
                <svg class="hero-detail-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span class="hero-detail-label">Time</span>
                <span class="hero-detail-value"><?php echo esc_html($formatted_time); ?></span>
            </div>
            <div class="hero-detail">
                <svg class="hero-detail-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <span class="hero-detail-label">Venue</span>
                <span class="hero-detail-value"><?php echo esc_html($party_venue); ?></span>
            </div>
        </div>

        <div class="hero-actions">
            <a href="#gallery" class="btn btn-primary">See the Gallery</a>
            <a href="#emt-fund" class="btn btn-outline">Support the EMT Fund</a>
        </div>
    </div>

    <div class="hero-divider">
        <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path d="M0 60 L1440 0 L1440 60 Z" fill="#8b5e3c" opacity=".15"/>
            <path d="M0 60 Q360 20 720 40 Q1080 60 1440 20 L1440 60 Z" fill="#faf8f2"/>
        </svg>
    </div>
</section>

<!-- ╔══════════════════════════════════════════════════════════╗
     ║  COUNTDOWN                                               ║
     ╚══════════════════════════════════════════════════════════╝ -->
<section class="countdown-bar" id="countdown">
    <h3>Counting down to the celebration</h3>
    <div class="countdown-units">
        <div class="countdown-unit"><span class="countdown-number" id="cd-days">--</span><span class="countdown-label">Days</span></div>
        <div class="countdown-unit"><span class="countdown-number" id="cd-hours">--</span><span class="countdown-label">Hours</span></div>
        <div class="countdown-unit"><span class="countdown-number" id="cd-minutes">--</span><span class="countdown-label">Minutes</span></div>
        <div class="countdown-unit"><span class="countdown-number" id="cd-seconds">--</span><span class="countdown-label">Seconds</span></div>
    </div>
</section>

<!-- ╔══════════════════════════════════════════════════════════╗
     ║  ABOUT                                                   ║
     ╚══════════════════════════════════════════════════════════╝ -->
<section class="about-section section-pad" id="about">
    <div class="container">
        <div class="about-grid">
            <div class="about-img-wrap">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/images/maple-hero.jpg'); ?>"
                     alt="Maple Gallo"
                     onerror="this.src='https://placehold.co/480x600/7a9e87/fff?text=Maple'">
                <div class="about-img-badge"><span class="badge-year">2026</span>Graduate</div>
            </div>
            <div class="about-text">
                <span class="section-label">Their Story</span>
                <h2 class="section-title">From Student<br>to EMT Hero</h2>
                <?php foreach (explode("\n\n", $about_text) as $p): if (trim($p)): ?>
                <p><?php echo esc_html(trim($p)); ?></p>
                <?php endif; endforeach; ?>
                <div class="about-stats">
                    <div class="about-stat"><span class="about-stat-number">K–12</span><span class="about-stat-label">Years of School</span></div>
                    <div class="about-stat"><span class="about-stat-number">EMT</span><span class="about-stat-label">Certified</span></div>
                    <div class="about-stat"><span class="about-stat-number">&#8734;</span><span class="about-stat-label">Lives to Impact</span></div>
                    <div class="about-stat"><span class="about-stat-number">2026</span><span class="about-stat-label">Farm Party!</span></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ╔══════════════════════════════════════════════════════════╗
     ║  PHOTO GALLERY                                           ║
     ╚══════════════════════════════════════════════════════════╝ -->
<section class="gallery-section section-pad" id="gallery">
    <div class="container">
        <div class="text-center">
            <span class="section-label">Memories</span>
            <h2 class="section-title">Photo Gallery</h2>
            <p class="section-subtitle">A collection of Maple's best moments — from childhood adventures to graduation day.</p>
        </div>

        <div class="gallery-filters" id="gallery-filters">
            <button class="gallery-filter active" data-cat="all">All Photos</button>
            <button class="gallery-filter" data-cat="childhood">Childhood</button>
            <button class="gallery-filter" data-cat="school">School Life</button>
            <button class="gallery-filter" data-cat="emt">EMT Training</button>
            <button class="gallery-filter" data-cat="friends">Friends &amp; Family</button>
            <button class="gallery-filter" data-cat="party">Party Uploads</button>
        </div>

        <div class="gallery-grid" id="gallery-grid">
            <?php
            $seed = [
                ['label'=>'Childhood',    'cat'=>'childhood', 'color'=>'b2cdb9'],
                ['label'=>'School Days',  'cat'=>'school',    'color'=>'c9956c'],
                ['label'=>'Study Nights', 'cat'=>'school',    'color'=>'7a9e87'],
                ['label'=>'EMT Training', 'cat'=>'emt',       'color'=>'4e7260'],
                ['label'=>'Best Friends', 'cat'=>'friends',   'color'=>'8b5e3c'],
                ['label'=>'Family Fun',   'cat'=>'friends',   'color'=>'a8c4a2'],
            ];
            foreach ($seed as $sp):
            ?>
            <div class="gallery-item" data-cat="<?php echo esc_attr($sp['cat']); ?>"
                 data-full="https://placehold.co/900x700/<?php echo $sp['color']; ?>/fff?text=<?php echo urlencode($sp['label']); ?>"
                 data-caption="<?php echo esc_attr($sp['label']); ?>">
                <img src="https://placehold.co/400x400/<?php echo $sp['color']; ?>/fff?text=<?php echo urlencode($sp['label']); ?>"
                     alt="<?php echo esc_attr($sp['label']); ?>" loading="lazy">
                <div class="gallery-overlay"><span class="gallery-overlay-text"><?php echo esc_html($sp['label']); ?></span></div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center" style="margin-top:40px;">
            <button class="btn btn-outline" id="load-more-photos">Load More</button>
        </div>
    </div>
</section>

<!-- Lightbox -->
<div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="Photo lightbox">
    <div class="lightbox-inner">
        <button class="lightbox-close" id="lightbox-close" aria-label="Close">&times;</button>
        <button class="lightbox-nav lightbox-prev" id="lightbox-prev" aria-label="Previous">&#8249;</button>
        <img class="lightbox-img" id="lightbox-img" src="" alt="">
        <button class="lightbox-nav lightbox-next" id="lightbox-next" aria-label="Next">&#8250;</button>
        <div class="lightbox-caption" id="lightbox-caption"></div>
    </div>
</div>

<!-- ╔══════════════════════════════════════════════════════════╗
     ║  LEAVE A MEMORY (photo upload — instant gallery)         ║
     ╚══════════════════════════════════════════════════════════╝ -->
<section class="upload-section section-pad" id="upload">
    <div class="container">
        <div class="text-center">
            <span class="section-label">Add to the Album</span>
            <h2 class="section-title">Leave a Memory</h2>
            <p class="section-subtitle">Upload a photo of Maple and it appears in the gallery instantly for everyone to see.</p>
        </div>

        <div class="upload-box" id="upload-drop-zone">
            <svg class="upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                <circle cx="12" cy="13" r="4"/>
            </svg>
            <h3>Drop your photo here</h3>
            <p>or click to browse — JPEG, PNG, WebP up to 10 MB</p>

            <form class="upload-form" id="photo-upload-form" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="uploader-name">Your Name <span style="color:#c0392b">*</span></label>
                    <input type="text" id="uploader-name" name="uploader_name" placeholder="e.g. Grandma Karen" required>
                </div>

                <div class="form-group">
                    <label for="photo-caption">Caption / Memory</label>
                    <textarea id="photo-caption" name="caption" placeholder="Share a little memory about this photo…" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label>Photo <span style="color:#c0392b">*</span></label>
                    <label class="file-input-label" for="photo-file">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;flex-shrink:0" aria-hidden="true"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                        <span id="file-label-text">Choose a photo…</span>
                        <input type="file" id="photo-file" name="photo" accept="image/jpeg,image/png,image/gif,image/webp" required>
                    </label>
                    <div class="upload-preview" id="upload-preview"></div>
                </div>

                <div class="upload-progress" id="upload-progress">
                    <div class="upload-progress-bar" id="upload-progress-bar"></div>
                </div>
                <div class="upload-message" id="upload-message"></div>

                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;" id="upload-submit-btn">
                    Add to Gallery
                </button>
            </form>
        </div>
    </div>
</section>

<!-- ╔══════════════════════════════════════════════════════════╗
     ║  TRIVIA QUIZ                                             ║
     ╚══════════════════════════════════════════════════════════╝ -->
<section class="quiz-section section-pad" id="quiz">
    <div class="container">
        <div class="text-center">
            <span class="section-label">How Well Do You Know Them?</span>
            <h2 class="section-title">Maple Gallo Trivia</h2>
            <p class="section-subtitle">Test your knowledge and compete for the top spot on the leaderboard!</p>
        </div>

        <div class="quiz-card" id="quiz-card">
            <div class="quiz-start" id="quiz-start">
                <h3>Ready to play?</h3>
                <p>Answer <?php echo $quiz_count; ?> questions about Maple and see how you stack up!</p>
                <input type="text" class="quiz-name-input" id="quiz-player-name" placeholder="Enter your name to start…" maxlength="40">
                <button class="btn btn-gold" id="quiz-start-btn" onclick="mgStartQuiz()">Start the Quiz!</button>
            </div>

            <div id="quiz-game" style="display:none;">
                <div class="quiz-progress-wrap">
                    <div class="quiz-progress-bar">
                        <div class="quiz-progress-fill" id="quiz-progress-fill" style="width:0%"></div>
                    </div>
                    <span class="quiz-progress-text" id="quiz-progress-text">Question 1 of <?php echo $quiz_count; ?></span>
                </div>
                <div class="quiz-question-number" id="quiz-q-number">Question 1</div>
                <div class="quiz-question-text" id="quiz-q-text"></div>
                <div class="quiz-options" id="quiz-options"></div>
                <div class="quiz-feedback" id="quiz-feedback"></div>
                <div class="quiz-nav">
                    <span id="quiz-score-display" style="color:var(--gold);font-weight:700;">Score: 0</span>
                    <button class="btn btn-gold" id="quiz-next-btn" onclick="mgNextQuestion()" style="display:none;">Next Question &rarr;</button>
                </div>
            </div>

            <div class="quiz-results" id="quiz-results" style="display:none;">
                <div class="quiz-score-circle">
                    <span class="quiz-score-number" id="result-score">0</span>
                    <span class="quiz-score-label" id="result-label">/ <?php echo $quiz_count; ?></span>
                </div>
                <div class="quiz-result-msg" id="result-msg"></div>
                <p class="quiz-result-sub" id="result-sub"></p>
                <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;">
                    <button class="btn btn-gold" onclick="mgRestartQuiz()">Play Again</button>
                    <a href="#leaderboard" class="btn btn-outline" style="color:var(--gold-lt);border-color:var(--gold);">View Leaderboard</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ╔══════════════════════════════════════════════════════════╗
     ║  LEADERBOARD                                             ║
     ╚══════════════════════════════════════════════════════════╝ -->
<section class="leaderboard-section section-pad" id="leaderboard">
    <div class="container">
        <div class="text-center">
            <span class="section-label">Top Players</span>
            <h2 class="section-title">Trivia Leaderboard</h2>
            <p class="section-subtitle">The Maple Gallo experts — can you climb to the top?</p>
        </div>
        <div class="leaderboard-table-wrap">
            <div class="leaderboard-header">
                <span>#</span><span>Name</span>
                <span style="text-align:center;">Score</span>
                <span style="text-align:center;">Date</span>
            </div>
            <div id="leaderboard-body">
                <div class="leaderboard-empty"><p>No scores yet — be the first to play!</p></div>
            </div>
        </div>
        <div class="text-center" style="margin-top:24px;">
            <button class="btn btn-outline" onclick="mgLoadLeaderboard()">Refresh Leaderboard</button>
        </div>
    </div>
</section>

<!-- ╔══════════════════════════════════════════════════════════╗
     ║  STORIES / LIFE TIPS                                     ║
     ╚══════════════════════════════════════════════════════════╝ -->
<section class="stories-section section-pad" id="stories">
    <div class="container">
        <div class="text-center">
            <span class="section-label">Words of Wisdom</span>
            <h2 class="section-title">Leave Maple a Tip<br>or Story</h2>
            <p class="section-subtitle">Share a life lesson, a favorite memory, or some advice to carry into the next chapter.</p>
        </div>

        <div class="stories-layout">
            <div class="story-form-card">
                <h3>Share Your Wisdom</h3>
                <p>Your tip or story will appear here for Maple and all the guests to read.</p>
                <form id="story-form">
                    <div class="form-group">
                        <label for="story-author">Your Name <span style="color:#f5a9a0">*</span></label>
                        <input type="text" id="story-author" name="author" placeholder="e.g. Uncle Dave" required maxlength="60">
                    </div>
                    <div class="form-group">
                        <label for="story-title">Give It a Title <span style="color:rgba(255,255,255,.4)">(optional)</span></label>
                        <input type="text" id="story-title" name="title" placeholder="e.g. The best advice I ever got…" maxlength="80">
                    </div>
                    <div class="form-group">
                        <label for="story-body">Your Tip or Story <span style="color:#f5a9a0">*</span></label>
                        <textarea id="story-body" name="body" rows="6"
                                  placeholder="Share a memory, a life tip, words of encouragement…"
                                  maxlength="800" required></textarea>
                        <div class="story-char-count"><span id="story-char-num">0</span> / 800</div>
                    </div>
                    <div class="story-submit-msg" id="story-submit-msg"></div>
                    <button type="submit" class="btn btn-gold" style="width:100%;margin-top:8px;">Leave My Tip</button>
                </form>
            </div>

            <div class="stories-display">
                <h3>What Everyone's Saying</h3>
                <div class="story-cards" id="story-cards">
                    <?php
                    $stories = get_posts(['post_type'=>'mg_story','post_status'=>'publish','posts_per_page'=>20,'orderby'=>'date','order'=>'DESC']);
                    if ($stories):
                        foreach ($stories as $s):
                            $body   = get_post_meta($s->ID, '_mg_story_body', true);
                            $stitle = get_post_meta($s->ID, '_mg_story_title', true);
                            $long   = mb_strlen($body) > 200;
                    ?>
                    <div class="story-card">
                        <?php if ($stitle): ?><div class="story-card-title"><?php echo esc_html($stitle); ?></div><?php endif; ?>
                        <div class="story-card-text <?php echo $long ? 'collapsed' : ''; ?>" id="sc-<?php echo $s->ID; ?>"><?php echo esc_html($body); ?></div>
                        <?php if ($long): ?><button class="story-card-expand" onclick="mgExpandStory('sc-<?php echo $s->ID; ?>', this)">Read more</button><?php endif; ?>
                        <div class="story-card-meta">
                            <span class="story-card-author">— <?php echo esc_html($s->post_title); ?></span>
                            <span class="story-card-date"><?php echo get_the_date('M j', $s); ?></span>
                        </div>
                    </div>
                    <?php endforeach; else: ?>
                    <div class="story-empty" id="story-empty"><p>No stories yet — be the first to leave one!</p></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ╔══════════════════════════════════════════════════════════╗
     ║  EMT FUND                                                ║
     ╚══════════════════════════════════════════════════════════╝ -->
<section class="emt-fund-section" id="emt-fund">
    <div class="container">
        <div class="emt-fund-inner">
            <span class="section-label">Give the Gift of a Future</span>
            <h2 class="section-title" style="margin-bottom:16px;">Support Maple's<br>EMT Future Fund</h2>
            <p>Maple's journey doesn't stop at graduation. Continuing EMT education, certifications, and equipment all take resources — and every bit of support helps them get there.</p>
            <p>Send a gift directly via Venmo. Every dollar is a vote of confidence in an incredible next chapter.</p>
            <a href="<?php echo esc_url($venmo_url); ?>" target="_blank" rel="noopener noreferrer" class="venmo-btn">
                <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M444.2 64c16.5 27.5 24 55.8 24 91.4 0 113.8-97.2 261.5-176.2 365.6H109.3L64 96.9l152.1-14.3 23.3 184.9c21.6-36.2 48.4-93.3 48.4-132.1 0-21.3-3.6-35.8-9.3-47.9L444.2 64z"/></svg>
                Donate via Venmo
            </a>
        </div>
    </div>
</section>

<div class="toast-container" id="toast-container"></div>

<?php
get_footer();

function mg_eucalyptus_svg(): string {
    return '<svg viewBox="0 0 200 500" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M100 480 C90 400 60 350 40 280 C20 210 30 140 100 80" stroke="#7a9e87" stroke-width="3" fill="none"/>
        <ellipse cx="55" cy="160" rx="28" ry="18" fill="#7a9e87" opacity=".7" transform="rotate(-30 55 160)"/>
        <ellipse cx="42" cy="220" rx="32" ry="20" fill="#b2cdb9" opacity=".6" transform="rotate(20 42 220)"/>
        <ellipse cx="60" cy="290" rx="30" ry="19" fill="#7a9e87" opacity=".65" transform="rotate(-15 60 290)"/>
        <ellipse cx="45" cy="350" rx="26" ry="16" fill="#a8c4a2" opacity=".7" transform="rotate(25 45 350)"/>
        <ellipse cx="80" cy="120" rx="24" ry="15" fill="#b2cdb9" opacity=".6" transform="rotate(-40 80 120)"/>
        <ellipse cx="70" cy="400" rx="22" ry="14" fill="#7a9e87" opacity=".6" transform="rotate(10 70 400)"/>
    </svg>';
}
?>
