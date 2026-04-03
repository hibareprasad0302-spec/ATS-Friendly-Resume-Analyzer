<?php
require_once __DIR__ . '/includes/admin_guard.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../vendor/autoload.php';

$db = getDB();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = strtolower(trim($_POST['skill_name'] ?? ''));
        $category = trim($_POST['category'] ?? 'general');
        $aliasesRaw = trim($_POST['aliases'] ?? '');
        $aliases = $aliasesRaw ? json_encode(array_map('trim', explode(',', $aliasesRaw))) : null;

        if (empty($name)) {
            jsonResponse(['error' => 'Skill name is required'], 422);
        }

        try {
            $stmt = $db->prepare('INSERT INTO skills_master (skill_name, category, aliases) VALUES (?, ?, ?)');
            $stmt->execute([$name, $category, $aliases]);
            jsonResponse(['success' => true, 'id' => $db->lastInsertId()]);
        } catch (PDOException $e) {
            jsonResponse(['error' => 'Skill already exists or invalid data'], 409);
        }
    }

    if ($action === 'update') {
        $id = (int) ($_POST['id'] ?? 0);
        $name = strtolower(trim($_POST['skill_name'] ?? ''));
        $category = trim($_POST['category'] ?? 'general');
        $aliasesRaw = trim($_POST['aliases'] ?? '');
        $aliases = $aliasesRaw ? json_encode(array_map('trim', explode(',', $aliasesRaw))) : null;

        $stmt = $db->prepare('UPDATE skills_master SET skill_name = ?, category = ?, aliases = ? WHERE id = ?');
        $stmt->execute([$name, $category, $aliases, $id]);
        jsonResponse(['success' => true]);
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM skills_master WHERE id = ?');
        $stmt->execute([$id]);
        jsonResponse(['success' => true]);
    }

    jsonResponse(['error' => 'Invalid action'], 400);
}

// Fetch skills
$search = trim($_GET['search'] ?? '');
$catFilter = trim($_GET['category'] ?? '');

$sql = 'SELECT * FROM skills_master WHERE 1=1';
$params = [];

if ($search) {
    $sql .= ' AND skill_name LIKE ?';
    $params[] = '%' . $search . '%';
}
if ($catFilter) {
    $sql .= ' AND category = ?';
    $params[] = $catFilter;
}
$sql .= ' ORDER BY category, skill_name';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$skills = $stmt->fetchAll();

$categories = $db->query('SELECT DISTINCT category FROM skills_master ORDER BY category')->fetchAll(PDO::FETCH_COLUMN);

$pageTitle = 'Manage Skills';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-semibold text-gray-900">Manage Skills</h1>
        <a href="/admin/" class="text-sm text-gray-400 hover:text-gray-600 transition">&larr; Back to Dashboard</a>
    </div>

    <!-- Add Skill Form -->
    <div class="border border-gray-100 rounded-xl p-6 mb-8">
        <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-4">Add New Skill</h2>
        <form id="add-skill-form" class="grid md:grid-cols-4 gap-4">
            <input type="text" name="skill_name" placeholder="Skill name" required
                class="px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-600 outline-none text-sm">
            <select name="category" class="px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-600 outline-none text-sm">
                <option value="programming">Programming</option>
                <option value="framework">Framework</option>
                <option value="database">Database</option>
                <option value="tool">Tool</option>
                <option value="cloud">Cloud</option>
                <option value="data">Data</option>
                <option value="api">API</option>
                <option value="soft_skill">Soft Skill</option>
                <option value="design">Design</option>
                <option value="security">Security</option>
                <option value="general">General</option>
            </select>
            <input type="text" name="aliases" placeholder="Aliases (comma-separated)"
                class="px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-600 outline-none text-sm">
            <button type="submit" class="bg-gray-900 text-white px-4 py-2.5 rounded-lg hover:bg-gray-800 transition text-sm font-medium">Add Skill</button>
        </form>
        <p id="add-status" class="text-sm mt-2 hidden"></p>
    </div>

    <!-- Filters -->
    <div class="flex gap-4 mb-6">
        <input type="text" id="search-input" placeholder="Search skills..." value="<?= e($search) ?>"
            class="px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-600 outline-none flex-grow text-sm">
        <select id="category-filter" class="px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-600 outline-none text-sm">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= e($cat) ?>" <?= $catFilter === $cat ? 'selected' : '' ?>><?= e(ucfirst($cat)) ?></option>
            <?php endforeach; ?>
        </select>
        <button onclick="applyFilters()" class="border border-gray-200 text-gray-600 px-4 py-2.5 rounded-lg hover:bg-gray-50 transition text-sm">Filter</button>
    </div>

    <!-- Skills Table -->
    <div class="border border-gray-100 rounded-xl overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50/50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">Skill</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">Category</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">Aliases</th>
                    <th class="text-center px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($skills as $skill): ?>
                <tr class="hover:bg-gray-50/50" id="skill-row-<?= $skill['id'] ?>">
                    <td class="px-6 py-3 text-sm font-medium text-gray-900"><?= e($skill['skill_name']) ?></td>
                    <td class="px-6 py-3 text-sm">
                        <span class="badge badge-blue"><?= e($skill['category']) ?></span>
                    </td>
                    <td class="px-6 py-3 text-xs text-gray-400">
                        <?php
                        $aliases = $skill['aliases'] ? json_decode($skill['aliases'], true) : [];
                        echo e(implode(', ', $aliases ?: ['-']));
                        ?>
                    </td>
                    <td class="px-6 py-3 text-center">
                        <button onclick="deleteSkill(<?= $skill['id'] ?>, '<?= e($skill['skill_name']) ?>')"
                            class="text-xs text-gray-400 hover:text-red-500 transition">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($skills)): ?>
                <tr><td colspan="4" class="px-6 py-8 text-center text-gray-300 text-sm">No skills found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <p class="text-xs text-gray-300 mt-4">Total: <?= count($skills) ?> skills</p>
</div>

<script>
document.getElementById('add-skill-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const status = document.getElementById('add-status');
    const formData = new FormData(form);
    formData.append('action', 'add');

    try {
        const res = await fetch('/admin/skills.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
            status.textContent = 'Skill added successfully!';
            status.className = 'text-sm mt-2 text-green-600';
            status.classList.remove('hidden');
            form.reset();
            setTimeout(() => location.reload(), 1000);
        } else {
            status.textContent = data.error;
            status.className = 'text-sm mt-2 text-red-600';
            status.classList.remove('hidden');
        }
    } catch (err) {
        status.textContent = 'Error adding skill.';
        status.className = 'text-sm mt-2 text-red-600';
        status.classList.remove('hidden');
    }
});

async function deleteSkill(id, name) {
    if (!confirm('Delete skill "' + name + '"?')) return;
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);

    const res = await fetch('/admin/skills.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) {
        document.getElementById('skill-row-' + id)?.remove();
    }
}

function applyFilters() {
    const search = document.getElementById('search-input').value;
    const category = document.getElementById('category-filter').value;
    const params = new URLSearchParams();
    if (search) params.set('search', search);
    if (category) params.set('category', category);
    window.location.href = '/admin/skills.php?' + params.toString();
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
