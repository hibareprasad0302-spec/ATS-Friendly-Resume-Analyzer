<?php $pageTitle = 'ATS Resume Analyzer - Score Your Resume'; ?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<style>html, body { overflow-x: hidden; }</style>

<!-- All decorative blobs in one layer -->
<div class="pointer-events-none fixed inset-0 z-0" aria-hidden="true">
    <div class="hero-blob hero-blob-1"></div>
    <div class="hero-blob hero-blob-2"></div>
    <div class="hero-blob" style="width:350px;height:350px;background:radial-gradient(circle,#818cf8 0%,transparent 70%);top:700px;right:-80px;animation:blobDrift2 12s ease-in-out infinite;opacity:0.45"></div>
    <div class="hero-blob" style="width:300px;height:300px;background:radial-gradient(circle,#a78bfa 0%,transparent 70%);top:1200px;left:-80px;animation:blobDrift1 14s ease-in-out infinite;opacity:0.4"></div>
    <div class="hero-blob" style="width:250px;height:250px;background:radial-gradient(circle,#22d3ee 0%,transparent 70%);top:1400px;right:-40px;animation:blobDrift2 11s ease-in-out infinite;opacity:0.35"></div>
    <div class="hero-blob" style="width:400px;height:400px;background:radial-gradient(circle,#818cf8 0%,transparent 70%);top:1900px;left:50%;margin-left:-200px;animation:blobDrift1 10s ease-in-out infinite;opacity:0.4"></div>
</div>

<!-- Hero Section -->
<section class="relative z-10 py-16 md:py-28 px-2">
    <div class="max-w-5xl mx-auto flex flex-col md:flex-row items-center gap-10 md:gap-16">
        <!-- Left: Text -->
        <div class="flex-1 text-center md:text-left">
            <h1 class="text-3xl md:text-5xl font-semibold text-gray-900 mb-4 md:mb-6 leading-tight">
                Score Your Resume<br>
                Against <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-violet-500">Any Job Description</span>
            </h1>
            <p class="text-sm md:text-base text-gray-500 max-w-lg mb-8 md:mb-10">
                Upload your resume and paste a job description to get an instant ATS compatibility score
                with detailed analysis and actionable improvement suggestions.
            </p>
            <div class="flex flex-col sm:flex-row items-center gap-3 justify-center md:justify-start mb-6">
                <a href="/analyzer.php" class="inline-block bg-gray-900 text-white px-7 py-3 rounded-full text-sm font-medium hover:bg-gray-800 hover:shadow-lg hover:shadow-gray-900/20 transition-all">
                    Analyze My Resume
                </a>
                <a href="#how-it-works" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 font-medium transition">
                    See how it works
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </a>
            </div>
            <div class="flex flex-wrap gap-3 justify-center md:justify-start">
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-400">
                    <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    PDF & DOCX
                </span>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-400">
                    <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    7 Scoring Categories
                </span>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-400">
                    <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Free to Use
                </span>
            </div>
        </div>

        <!-- Right: Illustration -->
        <div class="flex-shrink-0 hero-float">
            <svg width="320" height="360" viewBox="0 0 320 360" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-56 md:w-80 drop-shadow-2xl">
                <ellipse cx="160" cy="345" rx="100" ry="10" fill="#e5e7eb" opacity="0.4"/>
                <rect x="50" y="20" width="180" height="240" rx="16" fill="white" stroke="#e5e7eb" stroke-width="1.5"/>
                <rect x="50" y="20" width="180" height="50" rx="16" fill="#f9fafb"/>
                <rect x="50" y="54" width="180" height="16" fill="#f9fafb"/>
                <circle cx="90" cy="50" r="14" fill="#eef2ff"/>
                <circle cx="90" cy="46" r="5" fill="#a5b4fc"/>
                <path d="M 79 58 Q 90 52 101 58" fill="#a5b4fc"/>
                <rect x="112" y="40" width="80" height="8" rx="4" fill="#e5e7eb"/>
                <rect x="112" y="54" width="50" height="6" rx="3" fill="#f3f4f6"/>
                <rect x="74" y="90" width="100" height="6" rx="3" fill="#6366f1" opacity="0.2"/>
                <rect x="74" y="104" width="130" height="4" rx="2" fill="#f3f4f6"/>
                <rect x="74" y="114" width="120" height="4" rx="2" fill="#f3f4f6"/>
                <rect x="74" y="124" width="110" height="4" rx="2" fill="#f3f4f6"/>
                <rect x="74" y="148" width="80" height="6" rx="3" fill="#8b5cf6" opacity="0.2"/>
                <rect x="74" y="162" width="130" height="4" rx="2" fill="#f3f4f6"/>
                <rect x="74" y="172" width="125" height="4" rx="2" fill="#f3f4f6"/>
                <rect x="74" y="182" width="115" height="4" rx="2" fill="#f3f4f6"/>
                <rect x="74" y="206" width="60" height="6" rx="3" fill="#06b6d4" opacity="0.2"/>
                <rect x="74" y="220" width="50" height="16" rx="8" fill="#eef2ff"/>
                <rect x="130" y="220" width="40" height="16" rx="8" fill="#f5f3ff"/>
                <rect x="176" y="220" width="45" height="16" rx="8" fill="#ecfdf5"/>
                <circle cx="230" cy="230" r="50" fill="white" stroke="#f3f4f6" stroke-width="1"/>
                <circle cx="230" cy="230" r="50" fill="white" filter="drop-shadow(0 4px 12px rgba(0,0,0,0.08))"/>
                <circle cx="230" cy="230" r="38" fill="none" stroke="#f3f4f6" stroke-width="6"/>
                <circle cx="230" cy="230" r="38" fill="none" stroke="url(#gaugeGrad)" stroke-width="6"
                    stroke-linecap="round" class="score-arc-anim"
                    transform="rotate(-90, 230, 230)"/>
                <text x="230" y="225" text-anchor="middle" font-family="Inter, sans-serif" font-size="22" font-weight="700" fill="#1f2937">
                    <tspan id="hero-score">0</tspan>
                </text>
                <text x="230" y="244" text-anchor="middle" font-family="Inter, sans-serif" font-size="10" fill="#9ca3af">/100</text>
                <g opacity="0.9">
                    <circle cx="270" cy="160" r="14" fill="#ecfdf5"/>
                    <path d="M 264 160 L 268 164 L 276 156" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </g>
                <g opacity="0.7">
                    <circle cx="55" cy="200" r="12" fill="#eef2ff"/>
                    <path d="M 50 200 L 53 203 L 60 196" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </g>
                <defs>
                    <linearGradient id="gaugeGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#6366f1"/>
                        <stop offset="100%" stop-color="#8b5cf6"/>
                    </linearGradient>
                </defs>
            </svg>
        </div>
    </div>
</section>

<!-- Stats Strip -->
<section class="relative z-10 py-8 md:py-10 border-y border-gray-100 bg-white/60 backdrop-blur-sm reveal">
    <div class="max-w-4xl mx-auto grid grid-cols-3 gap-4 text-center">
        <div>
            <p class="text-xl md:text-2xl font-bold text-gray-900">150+</p>
            <p class="text-xs text-gray-400 mt-0.5">Skills Tracked</p>
        </div>
        <div>
            <p class="text-xl md:text-2xl font-bold text-gray-900">7</p>
            <p class="text-xs text-gray-400 mt-0.5">Scoring Categories</p>
        </div>
        <div>
            <p class="text-xl md:text-2xl font-bold text-gray-900">PDF</p>
            <p class="text-xs text-gray-400 mt-0.5">Report Download</p>
        </div>
    </div>
</section>

<!-- How It Works -->
<section id="how-it-works" class="relative z-10 py-16 md:py-24">
    <div class="text-center mb-14">
        <h2 class="text-xl md:text-2xl font-semibold text-gray-900 mb-2 reveal">How It Works</h2>
        <p class="text-sm text-gray-400 reveal reveal-delay-1">Three simple steps to optimize your resume</p>
    </div>
    <div class="relative max-w-3xl mx-auto">
        <div class="steps-line"></div>
        <div class="grid md:grid-cols-3 gap-10 md:gap-8">
            <div class="text-center relative z-10 reveal reveal-delay-1">
                <div class="w-16 h-16 rounded-2xl bg-indigo-50 flex items-center justify-center mx-auto mb-4 ring-4 ring-white">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                </div>
                <span class="inline-block text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-full mb-2">Step 1</span>
                <h3 class="text-sm font-semibold text-gray-900 mb-1">Upload Resume</h3>
                <p class="text-gray-400 text-xs leading-relaxed">Upload your resume in PDF or DOCX format for analysis.</p>
            </div>
            <div class="text-center relative z-10 reveal reveal-delay-2">
                <div class="w-16 h-16 rounded-2xl bg-violet-50 flex items-center justify-center mx-auto mb-4 ring-4 ring-white">
                    <svg class="w-7 h-7 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="inline-block text-xs font-bold text-violet-600 bg-violet-50 px-2.5 py-0.5 rounded-full mb-2">Step 2</span>
                <h3 class="text-sm font-semibold text-gray-900 mb-1">Paste Job Description</h3>
                <p class="text-gray-400 text-xs leading-relaxed">Paste the job description you're targeting.</p>
            </div>
            <div class="text-center relative z-10 reveal reveal-delay-3">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 flex items-center justify-center mx-auto mb-4 ring-4 ring-white">
                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <span class="inline-block text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full mb-2">Step 3</span>
                <h3 class="text-sm font-semibold text-gray-900 mb-1">Get Your ATS Score</h3>
                <p class="text-gray-400 text-xs leading-relaxed">Receive a detailed score with improvement tips.</p>
            </div>
        </div>
    </div>
</section>

<!-- Features Grid -->
<section class="relative z-10 py-16 md:py-24">
    <h2 class="text-xl md:text-2xl font-semibold text-center text-gray-900 mb-3 reveal">What You Get</h2>
    <p class="text-sm text-gray-400 text-center mb-12 reveal reveal-delay-1">Everything you need to optimize your resume for ATS systems</p>
    <div class="grid md:grid-cols-3 gap-5 max-w-4xl mx-auto px-2">
        <?php
        $features = [
            ['Keyword Match', 'See exactly which job keywords your resume contains and which are missing.', 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z', '#6366f1', '#eef2ff'],
            ['Skill Analysis', 'Compare your skills against job requirements using our database of 150+ skills.', 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z', '#8b5cf6', '#f5f3ff'],
            ['Section Detection', 'Verify your resume has all the sections ATS systems look for.', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', '#06b6d4', '#ecfeff'],
            ['Scoring Breakdown', 'Get a detailed 100-point score across 7 categories.', 'M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z', '#f59e0b', '#fffbeb'],
            ['Improvement Tips', 'Receive prioritized, actionable suggestions to boost your score.', 'M13 10V3L4 14h7v7l9-11h-7z', '#10b981', '#ecfdf5'],
            ['Download Report', 'Download a complete PDF analysis report for your records.', 'M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', '#ec4899', '#fdf2f8'],
        ];
        foreach ($features as $i => [$title, $desc, $icon, $color, $bg]): ?>
        <div class="feature-card bg-white/80 backdrop-blur-sm border border-gray-100 rounded-xl p-5 reveal reveal-delay-<?= ($i % 3) + 1 ?>" style="--accent-color: <?= $color ?>">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background: <?= $bg ?>">
                <svg class="w-5 h-5" style="color: <?= $color ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?= $icon ?>"/>
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-900 mb-1"><?= e($title) ?></h3>
            <p class="text-gray-400 text-xs leading-relaxed"><?= e($desc) ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- CTA -->
<section class="relative z-10 py-16 md:py-24 text-center reveal">
    <h2 class="text-xl md:text-2xl font-semibold text-gray-900 mb-3">Ready to Optimize Your Resume?</h2>
    <p class="text-sm text-gray-400 mb-8 max-w-md mx-auto">Get your ATS compatibility score in under a minute.</p>
    <a href="/analyzer.php" class="inline-block bg-gray-900 text-white px-8 py-3 rounded-full text-sm font-medium hover:bg-gray-800 hover:shadow-lg hover:shadow-gray-900/20 transition-all">
        Get Started Free
    </a>
</section>

<!-- Scroll reveal + Hero score counter -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

    const scoreEl = document.getElementById('hero-score');
    if (scoreEl) {
        const target = 92;
        const duration = 2000;
        const start = performance.now();
        function update(now) {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            scoreEl.textContent = Math.round(target * eased);
            if (progress < 1) requestAnimationFrame(update);
        }
        setTimeout(() => requestAnimationFrame(update), 500);
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
