<?php
require_once __DIR__ . '/includes/admin_guard.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../vendor/autoload.php';

$db = getDB();

// Handle CSV export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="ats-reports-' . date('Y-m-d') . '.csv"');

    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID', 'User', 'File', 'Role', 'Score', 'Keywords', 'Skills', 'Sections', 'Date']);

    $stmt = $db->query('SELECT r.*, u.full_name FROM resume_reports r LEFT JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC');
    while ($row = $stmt->fetch()) {
        fputcsv($out, [
            $row['id'], $row['full_name'] ?? 'Guest', $row['original_filename'],
            $row['job_role'] ?? '', $row['total_score'], $row['score_keyword'],
            $row['score_skills'], $row['score_sections'], $row['created_at'],
        ]);
    }
    fclose($out);
    exit;
}

$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 20;

$reportGen = new ReportGenerator($db);
$result = $reportGen->getAll($page, $perPage);

$pageTitle = 'All Reports';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-semibold text-gray-900">All Reports</h1>
        <div class="flex gap-4 items-center">
            <a href="/admin/reports.php?export=csv" class="border border-gray-200 text-gray-600 px-3.5 py-1.5 rounded-lg hover:bg-gray-50 transition text-sm font-medium">Export CSV</a>
            <a href="/admin/" class="text-sm text-gray-400 hover:text-gray-600 transition">&larr; Dashboard</a>
        </div>
    </div>

    <div class="border border-gray-100 rounded-xl overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50/50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">ID</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">User</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">File</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">Role</th>
                    <th class="text-center px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">Score</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">Date</th>
                    <th class="text-center px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">View</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($result['reports'] as $r): ?>
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-3 text-xs text-gray-400">#<?= $r['id'] ?></td>
                    <td class="px-6 py-3 text-sm text-gray-500"><?= e($r['user_name'] ?? 'Guest') ?></td>
                    <td class="px-6 py-3 text-sm font-medium text-gray-900"><?= e($r['original_filename']) ?></td>
                    <td class="px-6 py-3 text-sm text-gray-500"><?= e($r['job_role'] ?? '-') ?></td>
                    <td class="px-6 py-3 text-center">
                        <?php
                        $sc = (float)$r['total_score'];
                        $scColor = $sc >= 70 ? 'bg-green-50 text-green-700' : ($sc >= 40 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700');
                        ?>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium <?= $scColor ?>"><?= $r['total_score'] ?></span>
                    </td>
                    <td class="px-6 py-3 text-xs text-gray-400"><?= date('M j, Y', strtotime($r['created_at'])) ?></td>
                    <td class="px-6 py-3 text-center">
                        <a href="/result.php?id=<?= $r['id'] ?>" class="text-indigo-600 hover:text-indigo-700 text-sm">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($result['reports'])): ?>
                <tr><td colspan="7" class="px-6 py-8 text-center text-gray-300 text-sm">No reports yet</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($result['total_pages'] > 1): ?>
    <div class="flex justify-center gap-2 mt-6">
        <?php for ($i = 1; $i <= $result['total_pages']; $i++): ?>
            <a href="/admin/reports.php?page=<?= $i ?>"
               class="px-3 py-1 rounded-lg border text-sm <?= $i === $page ? 'bg-gray-900 text-white border-gray-900' : 'border-gray-200 text-gray-500 hover:bg-gray-50' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

    <p class="text-xs text-gray-300 mt-4">Total: <?= $result['total'] ?> reports</p>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
