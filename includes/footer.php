    </main>
    <footer class="bg-gray-50 border-t border-gray-100 text-gray-400 py-8 text-center text-xs">
        <p>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. All rights reserved.</p>
    </footer>
    <?php if (isset($pageScript)): ?>
        <script src="/assets/js/<?= e($pageScript) ?>"></script>
    <?php endif; ?>
</body>
</html>
