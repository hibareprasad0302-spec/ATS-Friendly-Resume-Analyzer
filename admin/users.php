<?php
require_once __DIR__ . '/includes/admin_guard.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../vendor/autoload.php';

$db = getDB();

// Handle AJAX actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';
    $userModel = new User($db);

    if ($action === 'toggle_role') {
        $id = (int) ($_POST['id'] ?? 0);
        $newRole = $_POST['role'] ?? '';
        if ($id === (int) currentUserId()) {
            jsonResponse(['error' => 'Cannot change your own role'], 403);
        }
        $userModel->updateRole($id, $newRole);
        jsonResponse(['success' => true]);
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id === (int) currentUserId()) {
            jsonResponse(['error' => 'Cannot delete your own account'], 403);
        }
        $userModel->delete($id);
        jsonResponse(['success' => true]);
    }

    jsonResponse(['error' => 'Invalid action'], 400);
}

$userModel = new User($db);
$page = max(1, (int) ($_GET['page'] ?? 1));
$result = $userModel->getAll($page);

$pageTitle = 'Manage Users';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-semibold text-gray-900">Manage Users</h1>
        <a href="/admin/" class="text-sm text-gray-400 hover:text-gray-600 transition">&larr; Dashboard</a>
    </div>

    <div class="border border-gray-100 rounded-xl overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50/50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">Name</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">Email</th>
                    <th class="text-center px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">Role</th>
                    <th class="text-center px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">Reports</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">Joined</th>
                    <th class="text-center px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($result['users'] as $u): ?>
                <tr class="hover:bg-gray-50/50" id="user-row-<?= $u['id'] ?>">
                    <td class="px-6 py-3 text-sm font-medium text-gray-900"><?= e($u['full_name']) ?></td>
                    <td class="px-6 py-3 text-sm text-gray-500"><?= e($u['email']) ?></td>
                    <td class="px-6 py-3 text-center">
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium
                            <?= $u['role'] === 'admin' ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-50 text-gray-500' ?>">
                            <?= e($u['role']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-3 text-center text-sm text-gray-400"><?= $u['report_count'] ?></td>
                    <td class="px-6 py-3 text-xs text-gray-400"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                    <td class="px-6 py-3 text-center space-x-2">
                        <?php if ((int)$u['id'] !== (int)currentUserId()): ?>
                            <button onclick="toggleRole(<?= $u['id'] ?>, '<?= $u['role'] === 'admin' ? 'user' : 'admin' ?>')"
                                class="text-xs text-indigo-600 hover:text-indigo-700 transition">
                                Make <?= $u['role'] === 'admin' ? 'User' : 'Admin' ?>
                            </button>
                            <button onclick="deleteUser(<?= $u['id'] ?>, '<?= e($u['full_name']) ?>')"
                                class="text-xs text-gray-400 hover:text-red-500 transition">Delete</button>
                        <?php else: ?>
                            <span class="text-xs text-gray-300">You</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <p class="text-xs text-gray-300 mt-4">Total: <?= $result['total'] ?> users</p>
</div>

<script>
async function toggleRole(id, newRole) {
    if (!confirm('Change this user to ' + newRole + '?')) return;
    const formData = new FormData();
    formData.append('action', 'toggle_role');
    formData.append('id', id);
    formData.append('role', newRole);
    const res = await fetch('/admin/users.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) location.reload();
    else alert(data.error);
}

async function deleteUser(id, name) {
    if (!confirm('Delete user "' + name + '"? This cannot be undone.')) return;
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);
    const res = await fetch('/admin/users.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) document.getElementById('user-row-' + id)?.remove();
    else alert(data.error);
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
