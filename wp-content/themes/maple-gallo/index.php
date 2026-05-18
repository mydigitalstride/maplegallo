<?php
defined('ABSPATH') || exit;
get_header();

$party_date  = get_option('maple_party_date',  '2026-06-15T18:00:00');
$party_venue = get_option('maple_party_venue', 'The Old Oak Farm');
$about_text  = get_option('maple_about_text',  "Maple Gallo is officially a graduate! After years of hard work, late nights studying, and an unstoppable drive to serve her community, Maple has earned her degree and is on her way to becoming a certified EMT.\n\nJoin us as we celebrate this incredible milestone at a rustic farm gathering filled with good food, great music, and even better company.");

$party_datetime = new DateTime($party_date);
$formatted_date = $party_datetime->format('F j, Y');
$formatted_time = $party_datetime->format('g:i A');
?>

<!-- ╔══════════════════════════════════════════════════════════╗
     ║  HERO                                                    ║
     ╚══════════════════════════════════════════════════════════╝ -->
<section class="hero" id="home">
    <?php get_template_part('template-parts/string-lights', null, ['top' => true]); ?>

    <!-- Decorative eucalyptus branches (inline SVG) -->
    <div class="hero-eucalyptus-left" aria-hidden="true">
        <?php echo mg_eucalyptus_svg(); ?>
    </div>
    <div class="hero-eucalyptus-right" aria-hidden="true">
        <?php echo mg_eucalyptus_svg(); ?>
    </div>

    <div class="hero-content">
        <span class="hero-eyebrow">You're Invited to Celebrate</span>
        <h1 class="hero-title">
            Congratulations
            <span class="hero-title-name">Maple Gallo</span>
        </h1>
        <p class="hero-subtitle">Class of 2026 &nbsp;·&nbsp; EMT Graduate</p>

        <div class="hero-details">
            <div class="hero-detail">
                <span class="hero-detail-icon">📅</span>
                <span class="hero-detail-label">Date</span>
                <span class="hero-detail-value"><?php echo esc_html($formatted_date); ?></span>
            </div>
            <div class="hero-detail">
                <span class="hero-detail-icon">🕕</span>
                <span class="hero-detail-label">Time</span>
                <span class="hero-detail-value"><?php echo esc_html($formatted_time); ?></span>
            </div>
            <div class="hero-detail">
                <span class="hero-detail-icon">🌾</span>
                <span class="hero-detail-label">Venue</span>
                <span class="hero-detail-value"><?php echo esc_html($party_venue); ?></span>
            </div>
        </div>

        <div class="hero-actions">
            <a href="#gallery" class="btn btn-primary">See the Gallery</a>
            <a href="#donate" class="btn btn-outline">Support the EMT Fund</a>
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
        <div class="countdown-unit">
            <span class="countdown-number" id="cd-days">--</span>
            <span class="countdown-label">Days</span>
        </div>
        <div class="countdown-unit">
            <span class="countdown-number" id="cd-hours">--</span>
            <span class="countdown-label">Hours</span>
        </div>
        <div class="countdown-unit">
            <span class="countdown-number" id="cd-minutes">--</span>
            <span class="countdown-label">Minutes</span>
        </div>
        <div class="countdown-unit">
            <span class="countdown-number" id="cd-seconds">--</span>
            <span class="countdown-label">Seconds</span>
        </div>
    </div>
</section>

<!-- ╔══════════════════════════════════════════════════════════╗
     ║  ABOUT / STORY                                           ║
     ╚══════════════════════════════════════════════════════════╝ -->
<section class="about-section section-pad" id="about">
    <div class="container">
        <div class="about-grid">
            <div class="about-img-wrap">
                <?php
                $hero_img = get_template_directory_uri() . '/images/maple-hero.jpg';
                // Falls back to a placeholder if no image uploaded
                ?>
                <img src="<?php echo esc_url($hero_img); ?>"
                     alt="Maple Gallo"
                     onerror="this.src='https://placehold.co/480x600/7a9e87/fff?text=Maple+🌿'">
                <div class="about-img-badge">
                    <span class="badge-year">2026</span>
                    Graduate
                </div>
            </div>

            <div class="about-text">
                <span class="section-label">Her Story</span>
                <h2 class="section-title">From Student<br>to EMT Hero</h2>
                <?php
                $paragraphs = explode("\n\n", $about_text);
                foreach ($paragraphs as $p):
                    if (trim($p)):
                ?>
                <p><?php echo esc_html(trim($p)); ?></p>
                <?php endif; endforeach; ?>

                <div class="about-stats">
                    <div class="about-stat">
                        <span class="about-stat-number">4+</span>
                        <span class="about-stat-label">Years of Study</span>
                    </div>
                    <div class="about-stat">
                        <span class="about-stat-number">EMT</span>
                        <span class="about-stat-label">Certified</span>
                    </div>
                    <div class="about-stat">
                        <span class="about-stat-number">∞</span>
                        <span class="about-stat-label">Lives to Impact</span>
                    </div>
                    <div class="about-stat">
                        <span class="about-stat-number">🌿</span>
                        <span class="about-stat-label">Farm Party!</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ╔══════════════════════════════════════════════════════════╗
     ║  SCHEDULE / TIMELINE                                     ║
     ╚══════════════════════════════════════════════════════════╝ -->
<section class="schedule-section section-pad" id="schedule">
    <div class="container text-center">
        <span class="section-label">Plan Your Evening</span>
        <h2 class="section-title">Party Schedule</h2>
        <p class="section-subtitle">An evening under the lights at the farm — good food, good people, and great memories.</p>

        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-time">6:00 PM</div>
                <div class="timeline-title">Guests Arrive</div>
                <div class="timeline-desc">Welcome drinks &amp; mingle under the string lights</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-time">6:30 PM</div>
                <div class="timeline-title">Dinner Is Served</div>
                <div class="timeline-desc">Farm-to-table feast at the wooden tables</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-time">7:30 PM</div>
                <div class="timeline-title">Toasts &amp; Speeches</div>
                <div class="timeline-desc">Celebrating Maple's incredible journey</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-time">8:00 PM</div>
                <div class="timeline-title">Maple Gallo Trivia!</div>
                <div class="timeline-desc">Test your knowledge — compete for the top spot on the leaderboard</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-time">8:30 PM</div>
                <div class="timeline-title">Dancing &amp; Music</div>
                <div class="timeline-desc">Live music and dancing under the stars</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-time">9:30 PM</div>
                <div class="timeline-title">Cake &amp; Desserts</div>
                <div class="timeline-desc">Sweet endings to a perfect night</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-time">10:00 PM</div>
                <div class="timeline-title">Photo Booth Open</div>
                <div class="timeline-desc">Capture the memories &amp; upload your favorites</div>
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
            <!-- Populated via JS/AJAX; seed photos shown as placeholders -->
            <?php
            $seed_photos = [
                ['label'=>'Childhood',    'cat'=>'childhood', 'color'=>'b2cdb9'],
                ['label'=>'School Days',  'cat'=>'school',    'color'=>'c9956c'],
                ['label'=>'Study Nights', 'cat'=>'school',    'color'=>'7a9e87'],
                ['label'=>'EMT Training', 'cat'=>'emt',       'color'=>'4e7260'],
                ['label'=>'Best Friends', 'cat'=>'friends',   'color'=>'8b5e3c'],
                ['label'=>'Family Fun',   'cat'=>'friends',   'color'=>'a8c4a2'],
            ];
            foreach ($seed_photos as $sp):
            ?>
            <div class="gallery-item" data-cat="<?php echo esc_attr($sp['cat']); ?>" data-full="https://placehold.co/900x700/<?php echo $sp['color']; ?>/fff?text=<?php echo urlencode($sp['label']); ?>" data-caption="<?php echo esc_attr($sp['label']); ?>">
                <img src="https://placehold.co/400x400/<?php echo $sp['color']; ?>/fff?text=<?php echo urlencode($sp['label']); ?>"
                     alt="<?php echo esc_attr($sp['label']); ?>"
                     loading="lazy">
                <div class="gallery-overlay">
                    <span class="gallery-overlay-text"><?php echo esc_html($sp['label']); ?></span>
                </div>
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
     ║  UPLOAD PHOTOS                                           ║
     ╚══════════════════════════════════════════════════════════╝ -->
<section class="upload-section section-pad" id="upload">
    <div class="container">
        <div class="text-center">
            <span class="section-label">Share Your Memories</span>
            <h2 class="section-title">Upload Your Favorite<br>Photo of Maple</h2>
            <p class="section-subtitle">Have a special photo of Maple? Share it with everyone! All uploads are reviewed before appearing in the gallery.</p>
        </div>

        <div class="upload-box" id="upload-drop-zone">
            <span class="upload-icon">📸</span>
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
                        <span>📁</span>
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
                    Upload Photo ✨
                </button>
            </form>
        </div>

        <?php if (is_user_logged_in() && current_user_can('manage_options')): ?>
        <div class="pending-uploads-section">
            <h3 style="text-align:center;margin-bottom:8px;">Pending Uploads (Admin Review)</h3>
            <p style="text-align:center;color:var(--soft-gray);font-size:.9rem;margin-bottom:24px;">Approve or reject guest photo submissions below.</p>
            <div class="pending-grid" id="pending-grid">
                <?php
                $pending = get_posts([
                    'post_type'      => 'mg_photo',
                    'post_status'    => 'pending',
                    'posts_per_page' => 30,
                ]);
                foreach ($pending as $p):
                    $att_id = get_post_meta($p->ID, '_mg_attachment_id', true);
                    $thumb  = wp_get_attachment_image_url($att_id, 'medium');
                ?>
                <div class="pending-card" id="pending-<?php echo $p->ID; ?>">
                    <img src="<?php echo esc_url($thumb ?: 'https://placehold.co/200x200/ccc/fff?text=Photo'); ?>"
                         alt="<?php echo esc_attr($p->post_title); ?>">
                    <div class="pending-card-meta">
                        <div class="pending-card-name"><?php echo esc_html($p->post_title); ?></div>
                        <div class="pending-card-caption"><?php echo esc_html(get_post_meta($p->ID, '_mg_caption', true)); ?></div>
                        <span class="pending-badge pending">Pending Review</span>
                        <div style="margin-top:10px;display:flex;gap:8px;">
                            <button class="btn btn-primary" style="padding:6px 16px;font-size:.8rem;"
                                    onclick="mgApprovePhoto(<?php echo $p->ID; ?>)">Approve</button>
                            <button class="btn" style="padding:6px 16px;font-size:.8rem;background:#e74c3c;color:#fff;border-color:#e74c3c;"
                                    onclick="mgRejectPhoto(<?php echo $p->ID; ?>)">Reject</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($pending)): ?>
                <p style="color:var(--soft-gray);grid-column:1/-1;text-align:center;font-style:italic;">No pending uploads — you're all caught up! 🎉</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ╔══════════════════════════════════════════════════════════╗
     ║  TRIVIA QUIZ                                             ║
     ╚══════════════════════════════════════════════════════════╝ -->
<section class="quiz-section section-pad" id="quiz">
    <div class="container">
        <div class="text-center">
            <span class="section-label">How Well Do You Know Her?</span>
            <h2 class="section-title">Maple Gallo Trivia</h2>
            <p class="section-subtitle">Test your knowledge and compete for the top spot on the leaderboard!</p>
        </div>

        <div class="quiz-card" id="quiz-card">

            <!-- Start Screen -->
            <div class="quiz-start" id="quiz-start">
                <h3>Ready to play?</h3>
                <p>Answer <?php echo count(mg_get_quiz_questions()); ?> questions about Maple and see how you stack up!</p>
                <input type="text" class="quiz-name-input" id="quiz-player-name" placeholder="Enter your name to start…" maxlength="40">
                <button class="btn btn-gold" id="quiz-start-btn" onclick="mgStartQuiz()">Start the Quiz!</button>
            </div>

            <!-- Quiz Questions (hidden until started) -->
            <div id="quiz-game" style="display:none;">
                <div class="quiz-progress-wrap">
                    <div class="quiz-progress-bar">
                        <div class="quiz-progress-fill" id="quiz-progress-fill" style="width:0%"></div>
                    </div>
                    <span class="quiz-progress-text" id="quiz-progress-text">Question 1 of <?php echo count(mg_get_quiz_questions()); ?></span>
                </div>

                <div class="quiz-question-number" id="quiz-q-number">Question 1</div>
                <div class="quiz-question-text" id="quiz-q-text"></div>
                <div class="quiz-options" id="quiz-options"></div>
                <div class="quiz-feedback" id="quiz-feedback"></div>

                <div class="quiz-nav">
                    <span id="quiz-score-display" style="color:var(--gold);font-weight:700;">Score: 0</span>
                    <button class="btn btn-gold" id="quiz-next-btn" onclick="mgNextQuestion()" style="display:none;">Next Question →</button>
                </div>
            </div>

            <!-- Results (hidden until finished) -->
            <div class="quiz-results" id="quiz-results" style="display:none;">
                <div class="quiz-score-circle">
                    <span class="quiz-score-number" id="result-score">0</span>
                    <span class="quiz-score-label" id="result-label">/ <?php echo count(mg_get_quiz_questions()); ?></span>
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
                <span>#</span>
                <span>Name</span>
                <span style="text-align:center;">Score</span>
                <span style="text-align:center;">Date</span>
            </div>
            <div id="leaderboard-body">
                <div class="leaderboard-empty">
                    <p>No scores yet — be the first to play! 🏆</p>
                </div>
            </div>
        </div>

        <div class="text-center" style="margin-top:24px;">
            <button class="btn btn-outline" onclick="mgLoadLeaderboard()">Refresh Leaderboard</button>
        </div>
    </div>
</section>

<!-- ╔══════════════════════════════════════════════════════════╗
     ║  DONATION SECTION                                        ║
     ╚══════════════════════════════════════════════════════════╝ -->
<section class="donate-section section-pad" id="donate">
    <div class="container">
        <div class="text-center">
            <span class="section-label">Give the Gift of a Future</span>
            <h2 class="section-title">Support Maple's<br>EMT Future Fund</h2>
            <p class="section-subtitle">Help Maple pursue her dream of serving her community as an EMT.</p>
        </div>

        <div class="donate-grid">
            <div class="donate-story">
                <h3>Why the EMT Fund?</h3>
                <p>Maple's journey to becoming an EMT doesn't stop at graduation. There are certifications, equipment, continuing education, and so much more ahead of her.</p>
                <p>Your generous contribution goes directly toward supporting Maple's path as she steps into a career dedicated to saving lives and making a difference every single day.</p>
                <p>Every dollar — big or small — is a vote of confidence in her incredible future.</p>

                <div class="donate-goal">
                    <div class="donate-goal-label">Community Goal</div>
                    <div class="donate-goal-amounts">
                        <span class="donate-goal-raised" id="goal-raised">$0 raised</span>
                        <span id="goal-target">of $<?php echo number_format((float) get_option('maple_party_goal', 2000), 0); ?></span>
                    </div>
                    <div class="donate-goal-bar">
                        <div class="donate-goal-fill" id="goal-fill" style="width:0%"></div>
                    </div>
                </div>
            </div>

            <div class="donate-form-card">
                <h3>Make a Pledge</h3>
                <p>Choose an amount or enter your own. We'll follow up to arrange payment.</p>

                <div id="donate-form-wrap">
                    <div class="donate-amounts">
                        <button class="donate-amount-btn" data-amount="25" onclick="mgSelectAmount(this, 25)">$25</button>
                        <button class="donate-amount-btn active" data-amount="50" onclick="mgSelectAmount(this, 50)">$50</button>
                        <button class="donate-amount-btn" data-amount="100" onclick="mgSelectAmount(this, 100)">$100</button>
                        <button class="donate-amount-btn" data-amount="250" onclick="mgSelectAmount(this, 250)">$250</button>
                        <button class="donate-amount-btn" data-amount="500" onclick="mgSelectAmount(this, 500)">$500</button>
                        <button class="donate-amount-btn" data-amount="0" onclick="mgSelectAmount(this, 0)">Other</button>
                    </div>

                    <form class="donate-form" id="donation-form">
                        <div class="donate-custom form-group">
                            <label for="donate-custom-input">Custom Amount</label>
                            <span class="donate-custom-symbol">$</span>
                            <input type="number" id="donate-custom-input" name="amount"
                                   value="50" min="1" step="1" placeholder="50">
                        </div>

                        <div class="form-group">
                            <label for="donor-name">Your Name <span style="color:#c0392b">*</span></label>
                            <input type="text" id="donor-name" name="donor_name" placeholder="e.g. Aunt Linda" required>
                        </div>

                        <div class="form-group">
                            <label for="donor-email">Email (for receipt)</label>
                            <input type="email" id="donor-email" name="donor_email" placeholder="you@example.com">
                        </div>

                        <div class="form-group">
                            <label for="donor-message">Message for Maple</label>
                            <textarea id="donor-message" name="message" rows="3"
                                      placeholder="Share some words of encouragement…"></textarea>
                        </div>

                        <button type="submit" class="btn btn-gold" style="width:100%;">
                            Pledge Donation 💚
                        </button>

                        <div class="donate-secure-note">
                            🔒 Your information is kept private and secure
                        </div>
                    </form>
                </div>

                <div class="donate-confirm" id="donate-confirm">
                    <div class="donate-confirm-icon">🌿</div>
                    <h3>Thank You!</h3>
                    <p id="donate-confirm-msg">Your pledge has been recorded. We'll be in touch!</p>
                    <button class="btn btn-outline" style="margin-top:20px;" onclick="mgResetDonation()">Make Another Pledge</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ╔══════════════════════════════════════════════════════════╗
     ║  STRING LIGHTS FOOTER SEPARATOR                          ║
     ╚══════════════════════════════════════════════════════════╝ -->
<?php get_template_part('template-parts/string-lights', null, ['top' => false]); ?>

<!-- Toast container -->
<div class="toast-container" id="toast-container"></div>

<?php
get_footer();

// ── Helpers (PHP, only used server-side) ─────────────────────
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

function mg_get_quiz_questions(): array {
    return [
        [
            'q' => 'What career is Maple pursuing after graduation?',
            'opts' => ['EMT / Emergency Medical Technician', 'Nurse Practitioner', 'Firefighter', 'Paramedic'],
            'correct' => 0,
            'fact' => 'Maple is passionate about emergency medicine and becoming a certified EMT to serve her community!',
        ],
        [
            'q' => 'What is Maple\'s favorite season?',
            'opts' => ['Summer', 'Autumn', 'Spring', 'Winter'],
            'correct' => 1,
            'fact' => 'Maple loves the golden colors and crisp air of autumn — fitting for a farm party!',
        ],
        [
            'q' => 'If Maple could travel anywhere in the world, where would she go?',
            'opts' => ['Iceland', 'Italy', 'Japan', 'New Zealand'],
            'correct' => 2,
            'fact' => 'Japan has always been at the top of Maple\'s travel bucket list!',
        ],
        [
            'q' => 'What is Maple\'s go-to comfort food?',
            'opts' => ['Tacos', 'Mac and Cheese', 'Pizza', 'Ramen'],
            'correct' => 3,
            'fact' => 'Maple never says no to a big bowl of ramen on a cold evening!',
        ],
        [
            'q' => 'Which best describes Maple\'s personality?',
            'opts' => ['Calm & Introspective', 'Bold & Adventurous', 'Warm & Empathetic', 'Witty & Sarcastic'],
            'correct' => 2,
            'fact' => 'Maple\'s warmth and empathy are exactly what makes her such a perfect fit for a career in emergency medicine.',
        ],
        [
            'q' => 'What is Maple\'s hidden talent?',
            'opts' => ['Playing the guitar', 'Speed reading', 'Baking sourdough bread', 'Painting watercolors'],
            'correct' => 2,
            'fact' => 'Maple can bake an amazing loaf of sourdough — she started during the pandemic and never stopped!',
        ],
        [
            'q' => 'What\'s Maple\'s favorite way to decompress?',
            'opts' => ['Hiking outdoors', 'Watching movies', 'Reading a good book', 'Listening to podcasts'],
            'correct' => 0,
            'fact' => 'Maple loves getting out in nature — trail walks clear her head like nothing else.',
        ],
        [
            'q' => 'Which quote best resonates with Maple?',
            'opts' => [
                '"Be the change you wish to see in the world."',
                '"In the middle of difficulty lies opportunity."',
                '"The purpose of life is to contribute in some way to making things better."',
                '"You miss 100% of the shots you don\'t take."',
            ],
            'correct' => 2,
            'fact' => 'Maple lives by this mindset — she is driven by purpose and a desire to make life better for those around her.',
        ],
    ];
}
?>
