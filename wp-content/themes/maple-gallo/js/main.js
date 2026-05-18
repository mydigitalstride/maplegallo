/* ============================================================
   Maple Gallo Graduation — main.js
   ============================================================ */
'use strict';

/* ── Helpers ────────────────────────────────────────────────── */
function mgToast(message, type = 'default') {
    const container = document.getElementById('toast-container');
    if (!container) return;
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `<span>${type === 'success' ? '✅' : type === 'error' ? '❌' : '🌿'}</span><span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}

function mgPost(action, data) {
    const form = new FormData();
    form.append('action', action);
    form.append('nonce', mapleGallo.nonce);
    Object.entries(data).forEach(([k, v]) => form.append(k, v));
    return fetch(mapleGallo.ajaxUrl, { method: 'POST', body: form })
        .then(r => r.json());
}

/* ── Header scroll effect ───────────────────────────────────── */
(function () {
    const header = document.getElementById('site-header');
    if (!header) return;
    window.addEventListener('scroll', () => {
        header.classList.toggle('scrolled', window.scrollY > 40);
    }, { passive: true });
})();

/* ── Mobile menu ────────────────────────────────────────────── */
(function () {
    const toggle = document.getElementById('menu-toggle');
    const nav    = document.getElementById('site-nav');
    if (!toggle || !nav) return;
    toggle.addEventListener('click', () => {
        const open = nav.classList.toggle('open');
        toggle.setAttribute('aria-expanded', open);
    });
    nav.querySelectorAll('a').forEach(a => {
        a.addEventListener('click', () => {
            nav.classList.remove('open');
            toggle.setAttribute('aria-expanded', false);
        });
    });
})();

/* ── Countdown Timer ────────────────────────────────────────── */
(function () {
    const target = new Date(mapleGallo.partyDate).getTime();
    const els = {
        days:    document.getElementById('cd-days'),
        hours:   document.getElementById('cd-hours'),
        minutes: document.getElementById('cd-minutes'),
        seconds: document.getElementById('cd-seconds'),
    };
    if (!els.days) return;

    function pad(n) { return String(Math.max(0, n)).padStart(2, '0'); }

    function tick() {
        const diff = target - Date.now();
        if (diff <= 0) {
            Object.values(els).forEach(el => el && (el.textContent = '00'));
            return;
        }
        const d = Math.floor(diff / 86400000);
        const h = Math.floor((diff % 86400000) / 3600000);
        const m = Math.floor((diff % 3600000)  / 60000);
        const s = Math.floor((diff % 60000)    / 1000);
        els.days.textContent    = pad(d);
        els.hours.textContent   = pad(h);
        els.minutes.textContent = pad(m);
        els.seconds.textContent = pad(s);
    }
    tick();
    setInterval(tick, 1000);
})();

/* ── Gallery ────────────────────────────────────────────────── */
(function () {
    let allItems = [];
    let currentLightboxIndex = 0;

    function buildGalleryItems() {
        allItems = Array.from(document.querySelectorAll('.gallery-item'));
    }

    // Filter buttons
    const filterBtns = document.querySelectorAll('.gallery-filter');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const cat = btn.dataset.cat;
            allItems.forEach(item => {
                const show = cat === 'all' || item.dataset.cat === cat;
                item.style.display = show ? '' : 'none';
            });
        });
    });

    // Open lightbox
    document.getElementById('gallery-grid')?.addEventListener('click', e => {
        const item = e.target.closest('.gallery-item');
        if (!item) return;
        buildGalleryItems();
        currentLightboxIndex = allItems.findIndex(i => i === item);
        showLightbox(currentLightboxIndex);
    });

    function showLightbox(index) {
        const visible = allItems.filter(i => i.style.display !== 'none');
        if (!visible.length) return;
        index = (index + visible.length) % visible.length;
        currentLightboxIndex = index;
        const item    = visible[index];
        const full    = item.dataset.full || item.querySelector('img')?.src;
        const caption = item.dataset.caption || item.querySelector('.gallery-overlay-text')?.textContent || '';
        document.getElementById('lightbox-img').src          = full;
        document.getElementById('lightbox-caption').textContent = caption;
        document.getElementById('lightbox').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        document.getElementById('lightbox').classList.remove('open');
        document.body.style.overflow = '';
    }

    document.getElementById('lightbox-close')?.addEventListener('click', closeLightbox);
    document.getElementById('lightbox')?.addEventListener('click', e => {
        if (e.target === e.currentTarget) closeLightbox();
    });

    document.getElementById('lightbox-prev')?.addEventListener('click', () => {
        showLightbox(currentLightboxIndex - 1);
    });
    document.getElementById('lightbox-next')?.addEventListener('click', () => {
        showLightbox(currentLightboxIndex + 1);
    });

    document.addEventListener('keydown', e => {
        if (!document.getElementById('lightbox')?.classList.contains('open')) return;
        if (e.key === 'Escape')      closeLightbox();
        if (e.key === 'ArrowLeft')   showLightbox(currentLightboxIndex - 1);
        if (e.key === 'ArrowRight')  showLightbox(currentLightboxIndex + 1);
    });

    // Load approved photos from server and append them
    function loadServerPhotos() {
        mgPost('mg_get_photos', { category: 'all' }).then(res => {
            if (!res.success || !res.data.photos.length) return;
            const grid = document.getElementById('gallery-grid');
            res.data.photos.forEach(p => {
                if (document.querySelector(`[data-photo-id="${p.id}"]`)) return;
                const div = document.createElement('div');
                div.className = 'gallery-item';
                div.dataset.cat = p.cat || 'party';
                div.dataset.full = p.full;
                div.dataset.caption = p.caption || p.name;
                div.dataset.photoId = p.id;
                div.innerHTML = `
                    <img src="${p.thumb}" alt="${p.name}" loading="lazy">
                    <div class="gallery-overlay">
                        <span class="gallery-overlay-text">${p.caption || p.name}</span>
                    </div>`;
                grid.appendChild(div);
            });
            buildGalleryItems();
        }).catch(() => {});
    }

    buildGalleryItems();
    loadServerPhotos();

    // Load More button (placeholder — in a real WP install this would paginate)
    document.getElementById('load-more-photos')?.addEventListener('click', function () {
        this.textContent = 'All photos loaded!';
        this.disabled = true;
    });
})();

/* ── Photo Upload ────────────────────────────────────────────── */
(function () {
    const form     = document.getElementById('photo-upload-form');
    const dropZone = document.getElementById('upload-drop-zone');
    const fileIn   = document.getElementById('photo-file');
    const preview  = document.getElementById('upload-preview');
    const progress = document.getElementById('upload-progress');
    const bar      = document.getElementById('upload-progress-bar');
    const msg      = document.getElementById('upload-message');
    const fileLabel = document.getElementById('file-label-text');

    if (!form) return;

    // Drag and drop
    ['dragenter','dragover'].forEach(ev =>
        dropZone.addEventListener(ev, e => {
            e.preventDefault();
            dropZone.classList.add('drag-over');
        })
    );
    ['dragleave','drop'].forEach(ev =>
        dropZone.addEventListener(ev, e => {
            e.preventDefault();
            dropZone.classList.remove('drag-over');
        })
    );
    dropZone.addEventListener('drop', e => {
        const files = e.dataTransfer?.files;
        if (files?.length) {
            fileIn.files = files;
            handleFileChange(files[0]);
        }
    });

    fileIn.addEventListener('change', () => {
        if (fileIn.files[0]) handleFileChange(fileIn.files[0]);
    });

    function handleFileChange(file) {
        fileLabel.textContent = file.name;
        preview.innerHTML = '';
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'upload-preview-thumb';
            img.alt = file.name;
            preview.appendChild(img);
        };
        reader.readAsDataURL(file);
    }

    form.addEventListener('submit', async e => {
        e.preventDefault();
        msg.textContent = '';
        msg.className = 'upload-message';

        const name = document.getElementById('uploader-name').value.trim();
        if (!name) { showMsg('Please enter your name.', 'error'); return; }
        if (!fileIn.files[0]) { showMsg('Please choose a photo.', 'error'); return; }

        const submitBtn = document.getElementById('upload-submit-btn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Uploading…';

        progress.style.display = 'block';
        bar.style.width = '30%';

        const formData = new FormData();
        formData.append('action', 'mg_upload_photo');
        formData.append('nonce', mapleGallo.nonce);
        formData.append('uploader_name', name);
        formData.append('caption', document.getElementById('photo-caption').value.trim());
        formData.append('photo', fileIn.files[0]);

        try {
            bar.style.width = '60%';
            const res = await fetch(mapleGallo.ajaxUrl, { method: 'POST', body: formData });
            bar.style.width = '100%';
            const data = await res.json();

            if (data.success) {
                showMsg(data.data.message, 'success');
                mgToast('Photo uploaded! It will appear after review. 📸', 'success');
                form.reset();
                preview.innerHTML = '';
                fileLabel.textContent = 'Choose a photo…';
            } else {
                showMsg(data.data?.message || 'Upload failed. Please try again.', 'error');
            }
        } catch (err) {
            showMsg('Network error. Please try again.', 'error');
        }

        submitBtn.disabled = false;
        submitBtn.textContent = 'Upload Photo ✨';
        setTimeout(() => { bar.style.width = '0%'; progress.style.display = 'none'; }, 1000);
    });

    function showMsg(text, type) {
        msg.textContent = text;
        msg.className   = `upload-message ${type}`;
    }
})();

// Admin photo moderation (global so inline onclick handlers can call them)
function mgApprovePhoto(id) {
    mgPost('mg_approve_photo', { photo_id: id }).then(res => {
        if (res.success) {
            document.getElementById(`pending-${id}`)?.remove();
            mgToast('Photo approved and added to gallery! ✅', 'success');
        }
    });
}

function mgRejectPhoto(id) {
    if (!confirm('Permanently delete this photo?')) return;
    mgPost('mg_reject_photo', { photo_id: id }).then(res => {
        if (res.success) {
            document.getElementById(`pending-${id}`)?.remove();
            mgToast('Photo removed.', 'default');
        }
    });
}

/* ── Quiz ────────────────────────────────────────────────────── */
const QUIZ_QUESTIONS = [
    {
        q: 'What career is Maple pursuing after graduation?',
        opts: ['EMT / Emergency Medical Technician','Nurse Practitioner','Firefighter','Paramedic'],
        correct: 0,
        fact: 'Maple is passionate about emergency medicine and becoming a certified EMT to serve her community!',
    },
    {
        q: "What is Maple's favorite season?",
        opts: ['Summer','Autumn','Spring','Winter'],
        correct: 1,
        fact: 'Maple loves the golden colors and crisp air of autumn — fitting for a farm party!',
    },
    {
        q: 'If Maple could travel anywhere, where would she go?',
        opts: ['Iceland','Italy','Japan','New Zealand'],
        correct: 2,
        fact: "Japan has always been at the top of Maple's travel bucket list!",
    },
    {
        q: "What is Maple's go-to comfort food?",
        opts: ['Tacos','Mac and Cheese','Pizza','Ramen'],
        correct: 3,
        fact: "Maple never says no to a big bowl of ramen on a cold evening!",
    },
    {
        q: "Which best describes Maple's personality?",
        opts: ['Calm & Introspective','Bold & Adventurous','Warm & Empathetic','Witty & Sarcastic'],
        correct: 2,
        fact: "Maple's warmth and empathy are exactly what makes her such a perfect fit for a career in emergency medicine.",
    },
    {
        q: "What is Maple's hidden talent?",
        opts: ['Playing the guitar','Speed reading','Baking sourdough bread','Painting watercolors'],
        correct: 2,
        fact: 'Maple can bake an amazing loaf of sourdough — she started during the pandemic and never stopped!',
    },
    {
        q: "What's Maple's favorite way to decompress?",
        opts: ['Hiking outdoors','Watching movies','Reading a good book','Listening to podcasts'],
        correct: 0,
        fact: "Maple loves getting out in nature — trail walks clear her head like nothing else.",
    },
    {
        q: 'Which quote best resonates with Maple?',
        opts: [
            '"Be the change you wish to see in the world."',
            '"In the middle of difficulty lies opportunity."',
            '"The purpose of life is to contribute in some way to making things better."',
            '"You miss 100% of the shots you don\'t take."',
        ],
        correct: 2,
        fact: 'Maple lives by this mindset — she is driven by purpose and a desire to make life better for those around her.',
    },
];

let quizState = {
    playerName:   '',
    currentQ:     0,
    score:        0,
    answered:     false,
    startTime:    0,
    elapsedSecs:  0,
};

function mgStartQuiz() {
    const nameIn = document.getElementById('quiz-player-name');
    const name = nameIn?.value.trim();
    if (!name) {
        nameIn?.focus();
        mgToast('Please enter your name to play! 🌿', 'default');
        return;
    }
    quizState = { playerName: name, currentQ: 0, score: 0, answered: false, startTime: Date.now(), elapsedSecs: 0 };
    document.getElementById('quiz-start').style.display = 'none';
    document.getElementById('quiz-game').style.display  = 'block';
    mgRenderQuestion();
}

function mgRenderQuestion() {
    const { currentQ, score } = quizState;
    const total = QUIZ_QUESTIONS.length;
    const q = QUIZ_QUESTIONS[currentQ];

    document.getElementById('quiz-q-number').textContent = `Question ${currentQ + 1} of ${total}`;
    document.getElementById('quiz-q-text').textContent   = q.q;
    document.getElementById('quiz-progress-text').textContent = `Question ${currentQ + 1} of ${total}`;
    document.getElementById('quiz-progress-fill').style.width = `${(currentQ / total) * 100}%`;
    document.getElementById('quiz-score-display').textContent = `Score: ${score}`;

    const optWrap = document.getElementById('quiz-options');
    const letters = ['A','B','C','D'];
    optWrap.innerHTML = q.opts.map((opt, i) => `
        <button class="quiz-option" onclick="mgSelectAnswer(${i})" data-index="${i}">
            <span class="quiz-option-letter">${letters[i]}</span>
            <span>${opt}</span>
        </button>
    `).join('');

    const fb = document.getElementById('quiz-feedback');
    fb.className = 'quiz-feedback';
    fb.textContent = '';

    document.getElementById('quiz-next-btn').style.display = 'none';
    quizState.answered = false;
}

function mgSelectAnswer(selectedIndex) {
    if (quizState.answered) return;
    quizState.answered = true;

    const q = QUIZ_QUESTIONS[quizState.currentQ];
    const correct = q.correct === selectedIndex;

    if (correct) quizState.score++;

    const opts = document.querySelectorAll('.quiz-option');
    opts.forEach((btn, i) => {
        btn.disabled = true;
        if (i === q.correct)    btn.classList.add('correct');
        if (i === selectedIndex && !correct) btn.classList.add('wrong');
    });

    const fb = document.getElementById('quiz-feedback');
    fb.className = `quiz-feedback show ${correct ? 'correct-fb' : 'wrong-fb'}`;
    fb.innerHTML = correct
        ? `<strong>Correct! 🎉</strong> ${q.fact}`
        : `<strong>Not quite!</strong> The correct answer is <em>${q.opts[q.correct]}</em>. ${q.fact}`;

    document.getElementById('quiz-score-display').textContent = `Score: ${quizState.score}`;
    document.getElementById('quiz-next-btn').style.display = 'inline-block';
    document.getElementById('quiz-next-btn').textContent =
        quizState.currentQ + 1 < QUIZ_QUESTIONS.length ? 'Next Question →' : 'See Results!';
}

function mgNextQuestion() {
    quizState.currentQ++;
    if (quizState.currentQ >= QUIZ_QUESTIONS.length) {
        mgShowResults();
    } else {
        mgRenderQuestion();
    }
}

function mgShowResults() {
    quizState.elapsedSecs = Math.floor((Date.now() - quizState.startTime) / 1000);
    const { score, playerName, elapsedSecs } = quizState;
    const total = QUIZ_QUESTIONS.length;
    const pct = Math.round((score / total) * 100);

    document.getElementById('quiz-game').style.display    = 'none';
    document.getElementById('quiz-results').style.display = 'block';
    document.getElementById('result-score').textContent = score;
    document.getElementById('result-label').textContent = `/ ${total}`;

    const msgs = [
        { min:100, msg:"Perfect Score! 🏆", sub:"You know Maple better than anyone!" },
        { min:75,  msg:"Maple Expert! 🌿",  sub:"You've been paying close attention!" },
        { min:50,  msg:"Pretty Good! 🎉",   sub:"You know Maple pretty well — well done!" },
        { min:25,  msg:"Good Try! 😊",      sub:"Maybe spend more time with Maple and play again!" },
        { min:0,   msg:"Thanks for Playing!",sub:"Now you know Maple a little better! 🌿" },
    ];
    const result = msgs.find(m => pct >= m.min);
    document.getElementById('result-msg').textContent = result.msg;
    document.getElementById('result-sub').textContent = `${playerName}, you scored ${score} out of ${total} (${pct}%) in ${elapsedSecs}s. ${result.sub}`;

    // Submit to leaderboard
    mgPost('mg_submit_score', {
        name:    playerName,
        score:   score,
        total:   total,
        seconds: elapsedSecs,
    }).then(() => mgLoadLeaderboard()).catch(() => {});

    // Animate progress fill to final state
    document.getElementById('quiz-progress-fill').style.width = '100%';
}

function mgRestartQuiz() {
    document.getElementById('quiz-results').style.display = 'none';
    document.getElementById('quiz-start').style.display   = 'block';
    document.getElementById('quiz-player-name').value     = quizState.playerName;
}

/* ── Leaderboard ─────────────────────────────────────────────── */
function mgLoadLeaderboard() {
    const body = document.getElementById('leaderboard-body');
    if (!body) return;

    mgPost('mg_get_leaderboard', {}).then(res => {
        if (!res.success || !res.data.scores.length) {
            body.innerHTML = '<div class="leaderboard-empty"><p>No scores yet — be the first to play! 🏆</p></div>';
            return;
        }
        const medals = ['🥇','🥈','🥉'];
        body.innerHTML = res.data.scores.map((s, i) => `
            <div class="leaderboard-row ${i < 3 ? 'top-'+(i+1) : ''}">
                <span class="lb-rank">${i < 3 ? `<span class="lb-medal">${medals[i]}</span>` : i + 1}</span>
                <span class="lb-name">${s.name}</span>
                <span class="lb-score">${s.score}/${s.total}</span>
                <span class="lb-time">${s.date}</span>
            </div>
        `).join('');
    }).catch(() => {});
}

document.addEventListener('DOMContentLoaded', mgLoadLeaderboard);

/* ── Donation Form ───────────────────────────────────────────── */
(function () {
    const form = document.getElementById('donation-form');
    if (!form) return;

    // Animate goal bar on load
    setTimeout(() => {
        mgPost('mg_get_leaderboard', {}).catch(() => {}); // keep connection warm
        loadGoalProgress();
    }, 500);

    function loadGoalProgress() {
        // We'll show a static visual — real total comes from PHP on load
        const fillEl   = document.getElementById('goal-fill');
        const raisedEl = document.getElementById('goal-raised');
        const goalEl   = document.getElementById('goal-target');
        if (!fillEl) return;

        // Animate whatever pct is currently set
        const currentWidth = fillEl.style.width || '0%';
        const pct = parseFloat(currentWidth);
        if (pct > 0) fillEl.style.width = pct + '%';
    }

    form.addEventListener('submit', async e => {
        e.preventDefault();
        const submitBtn = form.querySelector('[type=submit]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Processing…';

        const data = {
            donor_name:  document.getElementById('donor-name').value.trim(),
            donor_email: document.getElementById('donor-email').value.trim(),
            amount:      document.getElementById('donate-custom-input').value,
            message:     document.getElementById('donor-message').value.trim(),
        };

        try {
            const res = await mgPost('mg_donate', data);
            if (res.success) {
                document.getElementById('donate-form-wrap').style.display = 'none';
                const confirm = document.getElementById('donate-confirm');
                confirm.classList.add('show');
                document.getElementById('donate-confirm-msg').textContent = res.data.message;
                mgToast('Thank you for your pledge! 💚', 'success');

                // Update progress bar
                if (res.data.total_raised !== undefined) {
                    updateGoalBar(res.data.total_raised);
                }
            } else {
                mgToast(res.data?.message || 'Something went wrong. Please try again.', 'error');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Pledge Donation 💚';
            }
        } catch (err) {
            mgToast('Network error. Please try again.', 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Pledge Donation 💚';
        }
    });

    function updateGoalBar(raised) {
        const goalText = document.getElementById('goal-target')?.textContent || '';
        const goalMatch = goalText.match(/\$([\d,]+)/);
        const goal = goalMatch ? parseFloat(goalMatch[1].replace(',','')) : 2000;
        const pct  = Math.min(100, Math.round((raised / goal) * 100));

        const fillEl   = document.getElementById('goal-fill');
        const raisedEl = document.getElementById('goal-raised');
        if (fillEl)   fillEl.style.width = pct + '%';
        if (raisedEl) raisedEl.textContent = `$${raised.toLocaleString('en-US', {minimumFractionDigits:2,maximumFractionDigits:2})} raised`;
    }
})();

function mgSelectAmount(btn, amount) {
    document.querySelectorAll('.donate-amount-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const input = document.getElementById('donate-custom-input');
    if (input) {
        if (amount > 0) {
            input.value = amount;
            input.closest('.form-group').style.display = 'none';
        } else {
            input.value = '';
            input.closest('.form-group').style.display = '';
            input.focus();
        }
    }
}

// Init: hide custom amount row since $50 is pre-selected
document.addEventListener('DOMContentLoaded', () => {
    const customRow = document.getElementById('donate-custom-input')?.closest('.form-group');
    if (customRow) customRow.style.display = 'none';
});

function mgResetDonation() {
    document.getElementById('donate-form-wrap').style.display = '';
    document.getElementById('donate-confirm').classList.remove('show');
    document.getElementById('donation-form').reset();
    // Re-select $50 default
    const defaultBtn = document.querySelector('[data-amount="50"]');
    if (defaultBtn) mgSelectAmount(defaultBtn, 50);
}
