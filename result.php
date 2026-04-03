<?php $pageTitle = 'Analysis Results'; $pageScript = 'result.js'; ?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="max-w-5xl mx-auto">
    <!-- Loading State -->
    <div id="loading-state" class="text-center py-20">
        <div class="spinner mx-auto mb-4" style="width:40px;height:40px"></div>
        <p class="text-sm text-gray-400">Loading your analysis results...</p>
    </div>

    <!-- Error State -->
    <div id="error-state" class="hidden text-center py-20">
        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
        </svg>
        <p class="text-sm text-gray-500 mb-4" id="error-message">Could not load results.</p>
        <a href="/analyzer.php" class="text-sm text-indigo-600 hover:text-indigo-700">Try Another Analysis</a>
    </div>

    <!-- Results Content -->
    <div id="results-content" class="hidden">
        <!-- Header with Score -->
        <div class="flex flex-col md:flex-row items-center text-center md:text-left gap-6 md:gap-8 mb-10 md:mb-12">
            <div class="flex-shrink-0">
                <div class="relative w-28 h-28 md:w-36 md:h-36">
                    <svg class="w-28 h-28 md:w-36 md:h-36 transform -rotate-90" viewBox="0 0 200 200">
                        <circle cx="100" cy="100" r="90" fill="none" stroke="#f3f4f6" stroke-width="8"/>
                        <circle id="score-ring" cx="100" cy="100" r="90" fill="none" stroke="#4f46e5" stroke-width="8"
                            stroke-dasharray="565.48" stroke-dashoffset="565.48" stroke-linecap="round" class="score-circle"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span id="score-value" class="text-3xl md:text-4xl font-semibold text-gray-900">0</span>
                        <span class="text-gray-400 text-xs">/100</span>
                    </div>
                </div>
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 mb-2">ATS Compatibility Score</h1>
                <p id="score-label" class="text-xs md:text-sm text-gray-500 mb-3 md:mb-4"></p>
                <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                    <span id="file-badge" class="badge badge-blue"></span>
                    <span id="role-badge" class="badge badge-yellow hidden"></span>
                </div>
            </div>
        </div>

        <!-- Category Breakdown -->
        <div class="grid md:grid-cols-2 gap-8 mb-12 reveal">
            <!-- Radar Chart -->
            <div class="border border-gray-100 rounded-xl p-4 md:p-6">
                <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-4">Score Breakdown</h2>
                <canvas id="radarChart" width="400" height="400"></canvas>
            </div>

            <!-- Category Bars -->
            <div class="border border-gray-100 rounded-xl p-4 md:p-6">
                <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-4">Category Scores</h2>
                <div id="category-bars" class="space-y-4"></div>
            </div>
        </div>

        <!-- Keywords & Skills -->
        <div class="grid md:grid-cols-2 gap-8 mb-12 reveal">
            <!-- Keywords -->
            <div class="border border-gray-100 rounded-xl p-4 md:p-6">
                <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">Keywords</h2>
                <div id="keywords-ratio" class="mb-4"></div>
                <div class="flex gap-1 border-b border-gray-100 mb-4">
                    <button class="tab-btn active" data-tab="kw-matched">Matched <span id="kw-matched-count"></span></button>
                    <button class="tab-btn" data-tab="kw-missing">Missing <span id="kw-missing-count"></span></button>
                </div>
                <div id="kw-matched" class="tab-panel active">
                    <div id="matched-keywords" class="flex flex-wrap gap-1"></div>
                </div>
                <div id="kw-missing" class="tab-panel">
                    <div id="missing-keywords" class="flex flex-wrap gap-1"></div>
                </div>
            </div>

            <!-- Skills -->
            <div class="border border-gray-100 rounded-xl p-4 md:p-6">
                <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">Skills</h2>
                <div id="skills-ratio" class="mb-4"></div>
                <div class="flex gap-1 border-b border-gray-100 mb-4">
                    <button class="tab-btn active" data-tab="sk-matched">Matched <span id="sk-matched-count"></span></button>
                    <button class="tab-btn" data-tab="sk-missing">Missing <span id="sk-missing-count"></span></button>
                </div>
                <div id="sk-matched" class="tab-panel active">
                    <div id="matched-skills" class="flex flex-wrap gap-1"></div>
                </div>
                <div id="sk-missing" class="tab-panel">
                    <div id="missing-skills" class="flex flex-wrap gap-1"></div>
                </div>
            </div>
        </div>

        <!-- Sections Detected -->
        <div class="border border-gray-100 rounded-xl p-6 mb-12 reveal">
            <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-4">Resume Sections</h2>
            <div id="sections-checklist" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 md:gap-3"></div>
        </div>

        <!-- Suggestions -->
        <div class="border border-gray-100 rounded-xl p-6 mb-12 reveal">
            <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-4">Improvement Suggestions</h2>
            <div id="suggestions-list" class="space-y-2"></div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-4 justify-center no-print">
            <a id="download-btn" href="#" class="bg-gray-900 text-white px-6 py-2.5 rounded-full text-sm font-medium hover:bg-gray-800 transition">
                Download Report
            </a>
            <a href="/analyzer.php" class="border border-gray-200 text-gray-600 px-6 py-2.5 rounded-full text-sm font-medium hover:bg-gray-50 transition">
                Analyze Another Resume
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
