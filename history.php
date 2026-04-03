<?php require_once __DIR__ . '/includes/auth_guard.php'; ?>
<?php $pageTitle = 'Analysis History'; $pageScript = 'history.js'; ?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6 md:mb-8">
        <h1 class="text-xl md:text-2xl font-semibold text-gray-900">Analysis History</h1>
        <a href="/analyzer.php" class="bg-gray-900 text-white px-3 py-1.5 rounded-lg text-xs md:text-sm font-medium hover:bg-gray-800 transition">
            New Analysis
        </a>
    </div>

    <!-- Loading -->
    <div id="history-loading" class="text-center py-12">
        <div class="spinner mx-auto mb-4"></div>
        <p class="text-sm text-gray-400">Loading history...</p>
    </div>

    <!-- Empty State -->
    <div id="history-empty" class="hidden text-center py-16">
        <svg class="w-12 h-12 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-sm text-gray-400 mb-4">No analyses yet.</p>
        <a href="/analyzer.php" class="text-sm text-indigo-600 hover:text-indigo-700">Analyze your first resume</a>
    </div>

    <!-- History Table -->
    <div id="history-table" class="hidden">
        <div class="border border-gray-100 rounded-xl overflow-x-auto">
            <table class="w-full min-w-[540px]">
                <thead class="bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">Date</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">File</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">Job Role</th>
                        <th class="text-center px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">Score</th>
                        <th class="text-center px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">Action</th>
                    </tr>
                </thead>
                <tbody id="history-body" class="divide-y divide-gray-50"></tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div id="pagination" class="flex justify-center items-center gap-2 mt-6"></div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
