document.addEventListener('DOMContentLoaded', async () => {
    const reportId = new URLSearchParams(window.location.search).get('id');
    if (!reportId) {
        window.location.href = '/analyzer.php';
        return;
    }

    try {
        const response = await fetch('/api/report.php?id=' + reportId);
        const data = await response.json();

        if (!response.ok || !data.success) {
            showError(data.error || 'Could not load results.');
            return;
        }

        renderResults(data.report);

    } catch (err) {
        showError('Failed to load results. Please try again.');
    }
});

// Category color palette
const CAT_COLORS = {
    Keywords:    { bg: '#6366f1', light: '#eef2ff' },
    Skills:      { bg: '#8b5cf6', light: '#f5f3ff' },
    Sections:    { bg: '#06b6d4', light: '#ecfeff' },
    Projects:    { bg: '#f59e0b', light: '#fffbeb' },
    Experience:  { bg: '#10b981', light: '#ecfdf5' },
    Education:   { bg: '#ec4899', light: '#fdf2f8' },
    Formatting:  { bg: '#64748b', light: '#f8fafc' },
};

function showError(message) {
    document.getElementById('loading-state').classList.add('hidden');
    document.getElementById('error-state').classList.remove('hidden');
    document.getElementById('error-message').textContent = message;
}

function renderResults(report) {
    document.getElementById('loading-state').classList.add('hidden');
    document.getElementById('results-content').classList.remove('hidden');

    // Score gauge
    const totalScore = parseFloat(report.total_score);
    const scoreRing = document.getElementById('score-ring');
    const circumference = 2 * Math.PI * 90;
    const offset = circumference - (totalScore / 100) * circumference;

    const color = totalScore >= 70 ? '#16a34a' : (totalScore >= 40 ? '#ea580c' : '#dc2626');
    scoreRing.style.stroke = color;
    setTimeout(() => { scoreRing.style.strokeDashoffset = offset; }, 100);

    animateValue('score-value', 0, totalScore, 1500);

    // Score label
    const labelEl = document.getElementById('score-label');
    if (totalScore >= 70) {
        labelEl.textContent = 'Strong ATS Match - Your resume is well-optimized!';
        labelEl.style.color = '#16a34a';
    } else if (totalScore >= 40) {
        labelEl.textContent = 'Moderate Match - Some improvements needed.';
        labelEl.style.color = '#ea580c';
    } else {
        labelEl.textContent = 'Needs Improvement - Significant changes recommended.';
        labelEl.style.color = '#dc2626';
    }

    // Badges
    document.getElementById('file-badge').textContent = report.original_filename;
    if (report.job_role) {
        const roleBadge = document.getElementById('role-badge');
        roleBadge.textContent = report.job_role;
        roleBadge.classList.remove('hidden');
    }

    // Radar chart
    renderRadarChart(report);

    // Category bars
    renderCategoryBars(report);

    // Keywords & Skills with tabs + ratio
    renderKeywordSkillSection(report);

    // Section checklist
    renderSectionChecklist(report.detected_sections, report.missing_sections);

    // Suggestions
    renderSuggestions(report.suggestions);

    // Download button
    document.getElementById('download-btn').href = '/api/download_report.php?id=' + report.id;

    // Init tabs
    initTabs();

    // Init scroll-reveal
    initReveal();
}

// ── Radar Chart ──
function renderRadarChart(report) {
    const categories = getCategories(report);
    const colors = categories.map(c => CAT_COLORS[c.label].bg);

    const ctx = document.getElementById('radarChart').getContext('2d');
    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: categories.map(c => c.label),
            datasets: [{
                label: 'Your Score (%)',
                data: categories.map(c => (parseFloat(c.score) / c.max) * 100),
                backgroundColor: 'rgba(99, 102, 241, 0.06)',
                borderColor: 'rgba(99, 102, 241, 0.4)',
                pointBackgroundColor: colors,
                pointBorderColor: colors,
                pointRadius: 5,
                pointHoverRadius: 8,
                borderWidth: 1.5,
            }],
        },
        options: {
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100,
                    ticks: { stepSize: 20, display: false },
                    grid: { color: '#f3f4f6' },
                    angleLines: { color: '#f3f4f6' },
                    pointLabels: { font: { size: 11, family: 'Inter' }, color: '#9ca3af' },
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title: (items) => items[0].label,
                        label: (item) => {
                            const cat = categories[item.dataIndex];
                            const pct = Math.round((parseFloat(cat.score) / cat.max) * 100);
                            return `${pct}%  (${parseFloat(cat.score).toFixed(1)} / ${cat.max})`;
                        }
                    },
                    backgroundColor: '#1f2937',
                    titleFont: { size: 12, weight: '600', family: 'Inter' },
                    bodyFont: { size: 11, family: 'Inter' },
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: false,
                }
            },
        }
    });
}

// ── Category Bars ──
function renderCategoryBars(report) {
    const container = document.getElementById('category-bars');
    const categories = getCategories(report);

    container.innerHTML = categories.map((cat, i) => {
        const pct = Math.round((parseFloat(cat.score) / cat.max) * 100);
        const color = CAT_COLORS[cat.label].bg;
        return `
            <div class="bar-wrap relative" style="animation-delay:${i * 80}ms">
                <div class="flex justify-between text-xs mb-1.5">
                    <span class="font-medium text-gray-700 flex items-center gap-1.5">
                        <span class="inline-block w-2 h-2 rounded-full" style="background:${color}"></span>
                        ${cat.label}
                    </span>
                    <span class="text-gray-400">${parseFloat(cat.score).toFixed(1)} / ${cat.max}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2 relative">
                    <div class="h-2 rounded-full transition-all duration-1000 ease-out" style="width:0%;background:${color}" data-target-width="${pct}%"></div>
                    <div class="bar-tooltip">${pct}%</div>
                </div>
            </div>`;
    }).join('');

    // Stagger bar fill animation
    setTimeout(() => {
        container.querySelectorAll('[data-target-width]').forEach((bar, i) => {
            setTimeout(() => {
                bar.style.width = bar.dataset.targetWidth;
            }, i * 100);
        });
    }, 200);
}

// ── Keywords & Skills with Ratio ──
function renderKeywordSkillSection(report) {
    const matched_kw = report.matched_keywords || [];
    const missing_kw = report.missing_keywords || [];
    const matched_sk = report.matched_skills || [];
    const missing_sk = report.missing_skills || [];

    // Ratio bars
    renderRatioBar('keywords-ratio', matched_kw.length, matched_kw.length + missing_kw.length, '#6366f1');
    renderRatioBar('skills-ratio', matched_sk.length, matched_sk.length + missing_sk.length, '#8b5cf6');

    // Tab counts
    document.getElementById('kw-matched-count').textContent = `(${matched_kw.length})`;
    document.getElementById('kw-missing-count').textContent = `(${missing_kw.length})`;
    document.getElementById('sk-matched-count').textContent = `(${matched_sk.length})`;
    document.getElementById('sk-missing-count').textContent = `(${missing_sk.length})`;

    // Badges
    renderBadges('matched-keywords', matched_kw, 'green');
    renderBadges('missing-keywords', missing_kw, 'red');
    renderBadges('matched-skills', matched_sk, 'green');
    renderBadges('missing-skills', missing_sk, 'red');
}

function renderRatioBar(containerId, matched, total, color) {
    const container = document.getElementById(containerId);
    if (total === 0) {
        container.innerHTML = '<span class="text-xs text-gray-300">No data</span>';
        return;
    }
    const pct = Math.round((matched / total) * 100);
    container.innerHTML = `
        <div class="flex items-center gap-3">
            <div class="flex-grow bg-gray-100 rounded-full h-1.5">
                <div class="h-1.5 rounded-full transition-all duration-1000" style="width:${pct}%;background:${color}"></div>
            </div>
            <span class="text-xs text-gray-500 whitespace-nowrap font-medium">${matched} of ${total}</span>
        </div>`;
}

function renderBadges(containerId, items, color) {
    const container = document.getElementById(containerId);
    if (!items || items.length === 0) {
        container.innerHTML = '<span class="text-xs text-gray-300">None</span>';
        return;
    }
    container.innerHTML = items.map(item =>
        `<span class="badge badge-${color}">${escapeHtml(item)}</span>`
    ).join('');
}

// ── Sections Checklist ──
function renderSectionChecklist(detected, missing) {
    const container = document.getElementById('sections-checklist');
    let html = '';

    (detected || []).forEach(sec => {
        html += `<div class="flex items-center gap-2 p-2 rounded-lg bg-gray-50">
            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-xs text-gray-600 capitalize">${escapeHtml(sec)}</span>
        </div>`;
    });

    (missing || []).forEach(sec => {
        html += `<div class="flex items-center gap-2 p-2 rounded-lg bg-gray-50">
            <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span class="text-xs text-gray-400 capitalize">${escapeHtml(sec)} (missing)</span>
        </div>`;
    });

    container.innerHTML = html;
}

// ── Expandable Suggestions ──
function renderSuggestions(suggestions) {
    const container = document.getElementById('suggestions-list');
    if (!suggestions || suggestions.length === 0) {
        container.innerHTML = '<p class="text-xs text-gray-300">No suggestions - your resume looks great!</p>';
        return;
    }

    container.innerHTML = suggestions.map((sug) => {
        const priorityClass = `priority-${sug.priority}`;
        const priorityLabel = sug.priority.charAt(0).toUpperCase() + sug.priority.slice(1);
        return `
            <div class="suggestion-card ${priorityClass} rounded-lg p-4" onclick="this.classList.toggle('expanded')">
                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold uppercase mt-0.5 flex-shrink-0">${priorityLabel}</span>
                    <p class="text-sm text-gray-600 flex-grow truncate">${escapeHtml(sug.message)}</p>
                    <svg class="suggestion-chevron w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
                <div class="suggestion-body mt-3">
                    <p class="text-sm text-gray-500 leading-relaxed">${escapeHtml(sug.message)}</p>
                </div>
            </div>`;
    }).join('');
}

// ── Tabs ──
function initTabs() {
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const tabId = btn.dataset.tab;
            const parent = btn.closest('.border');

            // Toggle active button
            parent.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // Toggle panels
            parent.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
        });
    });
}

// ── Scroll Reveal ──
function initReveal() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                setTimeout(() => entry.target.classList.add('revealed'), i * 100);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
}

// ── Helpers ──
function getCategories(report) {
    return [
        { label: 'Keywords',    score: report.score_keyword,    max: 30 },
        { label: 'Skills',      score: report.score_skills,     max: 20 },
        { label: 'Sections',    score: report.score_sections,   max: 15 },
        { label: 'Projects',    score: report.score_projects,   max: 10 },
        { label: 'Experience',  score: report.score_experience, max: 10 },
        { label: 'Education',   score: report.score_education,  max: 5 },
        { label: 'Formatting',  score: report.score_formatting, max: 10 },
    ];
}

function animateValue(elementId, start, end, duration) {
    const el = document.getElementById(elementId);
    const range = end - start;
    const startTime = performance.now();

    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        el.textContent = Math.round(start + range * eased);
        if (progress < 1) requestAnimationFrame(update);
    }
    requestAnimationFrame(update);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
