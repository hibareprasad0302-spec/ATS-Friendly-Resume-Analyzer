<?php require_once __DIR__ . '/includes/auth_guard.php'; ?>
<?php $pageTitle = 'Analyze Resume'; $pageScript = 'analyzer.js'; ?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-xl md:text-2xl font-semibold text-gray-900 mb-1">Resume Analyzer</h1>
    <p class="text-xs md:text-sm text-gray-500 mb-6 md:mb-8">Upload your resume and paste the job description to get your ATS compatibility score.</p>

    <!-- Progress Indicator -->
    <div id="progress-section" class="hidden mb-6">
        <div class="flex items-center justify-between mb-2">
            <span id="progress-text" class="text-xs font-medium text-gray-600">Uploading...</span>
            <span id="progress-percent" class="text-xs text-gray-400">0%</span>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-1.5">
            <div id="progress-bar" class="bg-indigo-600 h-1.5 rounded-full progress-bar" style="width: 0%"></div>
        </div>
    </div>

    <form id="analyzer-form" class="space-y-6 md:space-y-0 md:grid md:grid-cols-2 md:gap-8">
        <!-- Left Column: File Upload -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Resume File</label>
            <div id="dropzone" class="dropzone p-6 md:p-8 text-center min-h-[180px] md:min-h-[250px] flex flex-col items-center justify-center">
                <svg class="w-8 h-8 md:w-10 md:h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <p class="text-xs md:text-sm text-gray-500 mb-1">Drag & drop your resume here</p>
                <p class="text-gray-400 text-xs mb-3">or click to browse</p>
                <div class="flex gap-2 justify-center">
                    <span class="badge badge-blue">PDF</span>
                    <span class="badge badge-blue">DOCX</span>
                </div>
                <input type="file" id="resume-file" name="resume" accept=".pdf,.docx" class="hidden">
                <p class="text-xs text-gray-300 mt-3">Max file size: 10 MB</p>
            </div>
            <div id="file-info" class="hidden mt-3 p-3 border border-gray-200 rounded-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 min-w-0">
                        <svg class="w-4 h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span id="file-name" class="text-sm text-gray-700 font-medium truncate"></span>
                    </div>
                    <button type="button" id="remove-file" class="text-xs text-gray-400 hover:text-gray-600 flex-shrink-0 ml-2">Remove</button>
                </div>
            </div>
        </div>

        <!-- Right Column: Job Description -->
        <div class="flex flex-col">
            <label for="job-role" class="block text-sm font-medium text-gray-700 mb-2">Job Role (Optional)</label>
            <input type="text" id="job-role" name="job_role" placeholder="e.g., Full Stack Developer"
                class="w-full px-3 md:px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-600 focus:border-transparent outline-none mb-4 text-sm">

            <label for="job-description" class="block text-sm font-medium text-gray-700 mb-2">Job Description</label>
            <textarea id="job-description" name="job_description" rows="8"
                placeholder="Paste the complete job description here..."
                class="w-full px-3 md:px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-600 focus:border-transparent outline-none resize-y flex-grow text-sm md:rows-10"
                minlength="50"></textarea>
            <p id="jd-counter" class="text-xs text-gray-300 mt-1 text-right">0 characters (min 50)</p>
        </div>

        <!-- Submit Button -->
        <div class="md:col-span-2">
            <button type="submit" id="submit-btn"
                class="w-full bg-gray-900 text-white py-3 rounded-full text-sm font-medium hover:bg-gray-800 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                disabled>
                <span id="btn-text">Analyze Resume</span>
                <span id="btn-spinner" class="spinner hidden"></span>
            </button>
            <p id="form-error" class="text-sm text-danger mt-2 hidden"></p>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
