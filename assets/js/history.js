document.addEventListener('DOMContentLoaded', () => {
    let currentPage = 1;
    loadHistory(currentPage);
});

async function loadHistory(page) {
    const loading = document.getElementById('history-loading');
    const empty = document.getElementById('history-empty');
    const table = document.getElementById('history-table');
    const body = document.getElementById('history-body');

    loading.classList.remove('hidden');
    empty.classList.add('hidden');
    table.classList.add('hidden');

    try {
        const res = await fetch('/api/history.php?page=' + page + '&per_page=10');
        const data = await res.json();

        loading.classList.add('hidden');

        if (!data.success || !data.reports || data.reports.length === 0) {
            empty.classList.remove('hidden');
            return;
        }

        body.innerHTML = data.reports.map(report => {
            const date = new Date(report.created_at).toLocaleDateString('en-US', {
                year: 'numeric', month: 'short', day: 'numeric'
            });
            const scoreColor = parseFloat(report.total_score) >= 70 ? 'bg-green-100 text-green-800' :
                               parseFloat(report.total_score) >= 40 ? 'bg-yellow-100 text-yellow-800' :
                               'bg-red-100 text-red-800';

            return `<tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm text-gray-600">${escapeHtml(date)}</td>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">${escapeHtml(report.original_filename)}</td>
                <td class="px-6 py-4 text-sm text-gray-600">${escapeHtml(report.job_role || '-')}</td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold ${scoreColor}">
                        ${report.total_score}/100
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="inline-flex items-center gap-2">
                        <a href="/result.php?id=${report.id}" class="text-primary hover:underline text-sm font-medium">View</a>
                        <a href="/api/download_report.php?id=${report.id}" class="text-gray-400 hover:text-gray-600 transition" title="Download PDF">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 15V3"/><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/></svg>
                        </a>
                    </div>
                </td>
            </tr>`;
        }).join('');

        table.classList.remove('hidden');

        // Pagination
        renderPagination(data.page, data.total_pages);

    } catch (err) {
        loading.classList.add('hidden');
        empty.classList.remove('hidden');
    }
}

function renderPagination(current, total) {
    const container = document.getElementById('pagination');
    if (total <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = '';

    if (current > 1) {
        html += `<button onclick="loadHistory(${current - 1})" class="px-3 py-1 rounded border text-sm hover:bg-gray-100">Prev</button>`;
    }

    for (let i = 1; i <= total; i++) {
        const active = i === current ? 'bg-primary text-white' : 'hover:bg-gray-100';
        html += `<button onclick="loadHistory(${i})" class="px-3 py-1 rounded border text-sm ${active}">${i}</button>`;
    }

    if (current < total) {
        html += `<button onclick="loadHistory(${current + 1})" class="px-3 py-1 rounded border text-sm hover:bg-gray-100">Next</button>`;
    }

    container.innerHTML = html;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
}
