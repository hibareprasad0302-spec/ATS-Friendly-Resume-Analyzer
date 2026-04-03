<?php
require_once __DIR__ . '/includes/admin_guard.php';
require_once __DIR__ . '/../includes/db.php';

$db = getDB();

// Fetch stats
$totalUsers = (int) $db->query('SELECT COUNT(*) FROM users')->fetchColumn();
$totalAnalyses = (int) $db->query('SELECT COUNT(*) FROM resume_reports')->fetchColumn();
$avgScore = (float) $db->query('SELECT COALESCE(AVG(total_score), 0) FROM resume_reports')->fetchColumn();
$todayCount = (int) $db->query("SELECT COUNT(*) FROM resume_reports WHERE DATE(created_at) = CURDATE()")->fetchColumn();

// Recent reports
$recentReports = $db->query(
    'SELECT r.id, r.original_filename, r.job_role, r.total_score, r.created_at, u.full_name as user_name
     FROM resume_reports r LEFT JOIN users u ON r.user_id = u.id
     ORDER BY r.created_at DESC LIMIT 10'
)->fetchAll();

// Score distribution
$distribution = $db->query(
    "SELECT
        SUM(total_score BETWEEN 0 AND 20) as range_0_20,
        SUM(total_score BETWEEN 21 AND 40) as range_21_40,
        SUM(total_score BETWEEN 41 AND 60) as range_41_60,
        SUM(total_score BETWEEN 61 AND 80) as range_61_80,
        SUM(total_score BETWEEN 81 AND 100) as range_81_100
     FROM resume_reports"
)->fetch();

$pageTitle = 'Admin Dashboard';
$pageScript = 'admin.js';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-6xl mx-auto">
    <h1 class="text-2xl font-semibold text-gray-900 mb-8">Admin Dashboard</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
        <div class="py-4 border-b-2 border-indigo-600">
            <p class="text-3xl font-semibold text-gray-900"><?= $totalUsers ?></p>
            <p class="text-xs text-gray-400 mt-1">Total Users</p>
        </div>
        <div class="py-4 border-b-2 border-gray-200">
            <p class="text-3xl font-semibold text-gray-900"><?= $totalAnalyses ?></p>
            <p class="text-xs text-gray-400 mt-1">Total Analyses</p>
        </div>
        <div class="py-4 border-b-2 border-gray-200">
            <p class="text-3xl font-semibold text-gray-900"><?= round($avgScore, 1) ?></p>
            <p class="text-xs text-gray-400 mt-1">Average Score</p>
        </div>
        <div class="py-4 border-b-2 border-gray-200">
            <p class="text-3xl font-semibold text-gray-900"><?= $todayCount ?></p>
            <p class="text-xs text-gray-400 mt-1">Today's Analyses</p>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-8 mb-8">
        <!-- Score Distribution Chart -->
        <div class="border border-gray-100 rounded-xl p-6">
            <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-4">Score Distribution</h2>
            <canvas id="distributionChart" height="200"></canvas>
        </div>

        <!-- Quick Links -->
        <div class="border border-gray-100 rounded-xl p-6">
            <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-4">Quick Links</h2>
            <div class="space-y-3">
                <a href="/admin/skills.php" class="block p-4 rounded-lg border border-gray-100 hover:border-gray-200 hover:bg-gray-50/50 transition">
                    <p class="text-sm font-medium text-gray-900">Manage Skills</p>
                    <p class="text-xs text-gray-400 mt-0.5">Add, edit, or remove skills from the master list</p>
                </a>
                <a href="/admin/reports.php" class="block p-4 rounded-lg border border-gray-100 hover:border-gray-200 hover:bg-gray-50/50 transition">
                    <p class="text-sm font-medium text-gray-900">View All Reports</p>
                    <p class="text-xs text-gray-400 mt-0.5">Browse and filter all analysis reports</p>
                </a>
                <a href="/admin/users.php" class="block p-4 rounded-lg border border-gray-100 hover:border-gray-200 hover:bg-gray-50/50 transition">
                    <p class="text-sm font-medium text-gray-900">Manage Users</p>
                    <p class="text-xs text-gray-400 mt-0.5">View users, change roles, delete accounts</p>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Analyses -->
    <div class="border border-gray-100 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Recent Analyses</h2>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50/50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">User</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">File</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">Role</th>
                    <th class="text-center px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">Score</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($recentReports as $r): ?>
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-4 text-sm text-gray-500"><?= e($r['user_name'] ?? 'Guest') ?></td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                        <a href="/result.php?id=<?= $r['id'] ?>" class="text-indigo-600 hover:text-indigo-700"><?= e($r['original_filename']) ?></a>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500"><?= e($r['job_role'] ?? '-') ?></td>
                    <td class="px-6 py-4 text-center">
                        <?php
                        $sc = (float)$r['total_score'];
                        $scColor = $sc >= 70 ? 'bg-green-50 text-green-700' : ($sc >= 40 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700');
                        ?>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium <?= $scColor ?>"><?= $r['total_score'] ?></span>
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-400"><?= date('M j, Y', strtotime($r['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($recentReports)): ?>
                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-300 text-sm">No analyses yet</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Score distribution chart
const distData = <?= json_encode([
    (int)($distribution['range_0_20'] ?? 0),
    (int)($distribution['range_21_40'] ?? 0),
    (int)($distribution['range_41_60'] ?? 0),
    (int)($distribution['range_61_80'] ?? 0),
    (int)($distribution['range_81_100'] ?? 0),
]) ?>;

new Chart(document.getElementById('distributionChart'), {
    type: 'bar',
    data: {
        labels: ['0-20', '21-40', '41-60', '61-80', '81-100'],
        datasets: [{
            label: 'Reports',
            data: distData,
            backgroundColor: ['#fecaca', '#fed7aa', '#fde68a', '#bbf7d0', '#86efac'],
            borderColor: ['#f87171', '#fb923c', '#fbbf24', '#4ade80', '#22c55e'],
            borderWidth: 1,
            borderRadius: 4,
        }]
    },
    options: {
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f9fafb' } },
            x: { grid: { display: false } },
        },
        plugins: { legend: { display: false } },
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
