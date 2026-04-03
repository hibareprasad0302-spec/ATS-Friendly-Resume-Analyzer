document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('analyzer-form');
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('resume-file');
    const fileInfo = document.getElementById('file-info');
    const fileName = document.getElementById('file-name');
    const removeFileBtn = document.getElementById('remove-file');
    const jdTextarea = document.getElementById('job-description');
    const jdCounter = document.getElementById('jd-counter');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = document.getElementById('btn-text');
    const btnSpinner = document.getElementById('btn-spinner');
    const formError = document.getElementById('form-error');
    const progressSection = document.getElementById('progress-section');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    const progressPercent = document.getElementById('progress-percent');

    let selectedFile = null;

    // Drag and drop
    dropzone.addEventListener('click', () => fileInput.click());

    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('drag-over');
    });

    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('drag-over');
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('drag-over');
        const files = e.dataTransfer.files;
        if (files.length > 0) handleFileSelect(files[0]);
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) handleFileSelect(fileInput.files[0]);
    });

    function handleFileSelect(file) {
        const ext = file.name.split('.').pop().toLowerCase();
        if (!['pdf', 'docx'].includes(ext)) {
            showError('Please upload a PDF or DOCX file.');
            return;
        }
        if (file.size > 10 * 1024 * 1024) {
            showError('File size exceeds 10 MB limit.');
            return;
        }
        selectedFile = file;
        fileName.textContent = file.name + ' (' + formatSize(file.size) + ')';
        fileInfo.classList.remove('hidden');
        dropzone.classList.add('has-file');
        hideError();
        updateSubmitState();
    }

    removeFileBtn.addEventListener('click', () => {
        selectedFile = null;
        fileInput.value = '';
        fileInfo.classList.add('hidden');
        dropzone.classList.remove('has-file');
        updateSubmitState();
    });

    // JD character counter
    jdTextarea.addEventListener('input', () => {
        const len = jdTextarea.value.length;
        jdCounter.textContent = len + ' characters (min 50)';
        jdCounter.classList.toggle('text-red-500', len > 0 && len < 50);
        jdCounter.classList.toggle('text-green-500', len >= 50);
        updateSubmitState();
    });

    function updateSubmitState() {
        const jdValid = jdTextarea.value.trim().length >= 50;
        submitBtn.disabled = !selectedFile || !jdValid;
    }

    // Form submit
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!selectedFile || jdTextarea.value.trim().length < 50) return;

        setLoading(true);
        hideError();

        try {
            // Step 1: Upload file
            updateProgress('Uploading resume...', 20);
            const uploadForm = new FormData();
            uploadForm.append('resume', selectedFile);

            const uploadRes = await fetch('/api/upload.php', { method: 'POST', body: uploadForm });
            const uploadData = await uploadRes.json();

            if (!uploadRes.ok || !uploadData.success) {
                throw new Error(uploadData.details?.[0] || uploadData.error || 'Upload failed');
            }

            // Step 2: Analyze
            updateProgress('Analyzing resume...', 50);
            const analyzeForm = new FormData();
            analyzeForm.append('job_description', jdTextarea.value.trim());
            analyzeForm.append('job_role', document.getElementById('job-role').value.trim());

            const analyzeRes = await fetch('/api/analyze.php', { method: 'POST', body: analyzeForm });
            const analyzeData = await analyzeRes.json();

            if (!analyzeRes.ok || !analyzeData.success) {
                throw new Error(analyzeData.error || 'Analysis failed');
            }

            // Step 3: Redirect to results
            updateProgress('Loading results...', 90);
            window.location.href = '/result.php?id=' + analyzeData.report_id;

        } catch (err) {
            showError(err.message);
            setLoading(false);
        }
    });

    function setLoading(loading) {
        submitBtn.disabled = loading;
        btnText.textContent = loading ? 'Analyzing...' : 'Analyze Resume';
        btnSpinner.classList.toggle('hidden', !loading);
        progressSection.classList.toggle('hidden', !loading);
        if (!loading) {
            progressBar.style.width = '0%';
        }
    }

    function updateProgress(text, percent) {
        progressText.textContent = text;
        progressPercent.textContent = percent + '%';
        progressBar.style.width = percent + '%';
    }

    function showError(msg) {
        formError.textContent = msg;
        formError.classList.remove('hidden');
    }

    function hideError() {
        formError.classList.add('hidden');
    }

    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }
});
