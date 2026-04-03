<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    exit('Invalid report ID');
}

try {
    $db = getDB();
    $reportGen = new ReportGenerator($db);

    if (isLoggedIn()) {
        $report = $reportGen->getById($id, currentUserId());
        if (!$report && isAdmin()) {
            $report = $reportGen->getById($id);
        }
    } else {
        $lastReportId = $_SESSION['last_report_id'] ?? null;
        $report = ($lastReportId === $id) ? $reportGen->getById($id) : null;
    }

    if (!$report) {
        http_response_code(404);
        exit('Report not found');
    }

    $scoreColor = $report['total_score'] >= 70 ? '#16a34a' : ($report['total_score'] >= 40 ? '#ea580c' : '#dc2626');
    $scoreLabel = $report['total_score'] >= 70 ? 'Strong Match' : ($report['total_score'] >= 40 ? 'Moderate Match' : 'Needs Improvement');

    $categories = [
        ['Keywords', 'score_keyword', 30, '#6366f1'],
        ['Skills', 'score_skills', 20, '#8b5cf6'],
        ['Sections', 'score_sections', 15, '#06b6d4'],
        ['Projects', 'score_projects', 10, '#f59e0b'],
        ['Experience', 'score_experience', 10, '#10b981'],
        ['Education', 'score_education', 5, '#ec4899'],
        ['Formatting', 'score_formatting', 10, '#64748b'],
    ];

    // Build HTML
    ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; line-height: 1.5; padding: 40px; font-size: 12px; }
        h1 { font-size: 22px; margin-bottom: 4px; color: #111827; }
        h2 { font-size: 14px; margin: 20px 0 10px; padding-bottom: 6px; border-bottom: 2px solid #e5e7eb; color: #374151; }
        .meta { color: #6b7280; font-size: 11px; margin-bottom: 16px; }
        .score-box { text-align: center; padding: 24px; margin: 16px 0; border-radius: 12px; background: #f9fafb; border: 1px solid #f3f4f6; }
        .score-value { font-size: 56px; font-weight: 800; }
        .score-label { font-size: 14px; margin-top: 4px; }
        .score-sub { font-size: 11px; color: #9ca3af; margin-top: 4px; }
        .category { padding: 6px 0; border-bottom: 1px solid #f3f4f6; }
        .cat-row { display: table; width: 100%; }
        .cat-name { display: table-cell; width: 100px; font-weight: 600; font-size: 11px; vertical-align: middle; }
        .cat-bar-wrap { display: table-cell; vertical-align: middle; padding: 0 12px; }
        .cat-bar-bg { background: #f3f4f6; height: 8px; border-radius: 4px; width: 100%; }
        .cat-bar { height: 8px; border-radius: 4px; }
        .cat-score { display: table-cell; width: 70px; text-align: right; font-size: 11px; color: #6b7280; vertical-align: middle; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; margin: 2px; }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .section-check { padding: 3px 0; font-size: 11px; }
        .check { color: #16a34a; font-weight: bold; }
        .cross { color: #dc2626; font-weight: bold; }
        .suggestion { padding: 10px; margin: 6px 0; border-radius: 6px; border-left: 4px solid; font-size: 11px; }
        .suggestion-high { background: #fef2f2; border-color: #dc2626; }
        .suggestion-medium { background: #fffbeb; border-color: #d97706; }
        .suggestion-low { background: #f0fdf4; border-color: #16a34a; }
        .suggestion strong { text-transform: uppercase; font-size: 9px; letter-spacing: 0.5px; }
        .two-col { width: 100%; }
        .two-col td { width: 50%; vertical-align: top; padding-right: 10px; }
        .footer { margin-top: 30px; text-align: center; color: #9ca3af; font-size: 10px; border-top: 1px solid #f3f4f6; padding-top: 12px; }
    </style>
</head>
<body>
    <h1>ATS Resume Analysis Report</h1>
    <p class="meta">
        File: <?= htmlspecialchars($report['original_filename']) ?> |
        Date: <?= date('F j, Y', strtotime($report['created_at'])) ?>
        <?= $report['job_role'] ? ' | Role: ' . htmlspecialchars($report['job_role']) : '' ?>
    </p>

    <div class="score-box">
        <div class="score-value" style="color: <?= $scoreColor ?>"><?= $report['total_score'] ?></div>
        <div class="score-label" style="color: <?= $scoreColor ?>"><?= $scoreLabel ?></div>
        <p class="score-sub">out of 100</p>
    </div>

    <h2>Category Scores</h2>
    <?php foreach ($categories as [$label, $field, $max, $color]):
        $pct = $max > 0 ? round(($report[$field] / $max) * 100) : 0;
    ?>
    <div class="category">
        <div class="cat-row">
            <span class="cat-name"><?= $label ?></span>
            <span class="cat-bar-wrap">
                <div class="cat-bar-bg">
                    <div class="cat-bar" style="width:<?= $pct ?>%;background:<?= $color ?>"></div>
                </div>
            </span>
            <span class="cat-score"><?= $report[$field] ?> / <?= $max ?></span>
        </div>
    </div>
    <?php endforeach; ?>

    <table class="two-col">
        <tr>
            <td>
                <h2>Matched Keywords</h2>
                <?php foreach ($report['matched_keywords'] ?? [] as $kw): ?>
                    <span class="badge badge-green"><?= htmlspecialchars($kw) ?></span>
                <?php endforeach; ?>
                <?php if (empty($report['matched_keywords'])): ?><p class="meta">None</p><?php endif; ?>
            </td>
            <td>
                <h2>Missing Keywords</h2>
                <?php foreach ($report['missing_keywords'] ?? [] as $kw): ?>
                    <span class="badge badge-red"><?= htmlspecialchars($kw) ?></span>
                <?php endforeach; ?>
                <?php if (empty($report['missing_keywords'])): ?><p class="meta">None</p><?php endif; ?>
            </td>
        </tr>
    </table>

    <table class="two-col">
        <tr>
            <td>
                <h2>Matched Skills</h2>
                <?php foreach ($report['matched_skills'] ?? [] as $s): ?>
                    <span class="badge badge-green"><?= htmlspecialchars($s) ?></span>
                <?php endforeach; ?>
                <?php if (empty($report['matched_skills'])): ?><p class="meta">None</p><?php endif; ?>
            </td>
            <td>
                <h2>Missing Skills</h2>
                <?php foreach ($report['missing_skills'] ?? [] as $s): ?>
                    <span class="badge badge-red"><?= htmlspecialchars($s) ?></span>
                <?php endforeach; ?>
                <?php if (empty($report['missing_skills'])): ?><p class="meta">None</p><?php endif; ?>
            </td>
        </tr>
    </table>

    <h2>Resume Sections</h2>
    <?php foreach ($report['detected_sections'] ?? [] as $sec): ?>
        <div class="section-check"><span class="check">&#10003;</span> <?= ucfirst(htmlspecialchars($sec)) ?></div>
    <?php endforeach; ?>
    <?php foreach ($report['missing_sections'] ?? [] as $sec): ?>
        <div class="section-check"><span class="cross">&#10007;</span> <?= ucfirst(htmlspecialchars($sec)) ?> (missing)</div>
    <?php endforeach; ?>

    <h2>Improvement Suggestions</h2>
    <?php foreach ($report['suggestions'] ?? [] as $sug): ?>
        <div class="suggestion suggestion-<?= htmlspecialchars($sug['priority']) ?>">
            <strong><?= htmlspecialchars($sug['priority']) ?></strong> &mdash;
            <?= htmlspecialchars($sug['message']) ?>
        </div>
    <?php endforeach; ?>

    <div class="footer">Generated by <?= htmlspecialchars(APP_NAME) ?></div>
</body>
</html>
<?php
    $html = ob_get_clean();

    // Generate PDF
    $options = new Options();
    $options->set('isRemoteEnabled', false);
    $options->set('defaultFont', 'DejaVu Sans');

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $filename = 'ATS-Report-' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $report['original_filename']) . '.pdf';
    $dompdf->stream($filename, ['Attachment' => true]);

} catch (Exception $e) {
    error_log('Download report error: ' . $e->getMessage());
    http_response_code(500);
    echo 'Error generating report.';
}
