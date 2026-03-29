<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle : 'Cafe 404' ?></title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
</head>

<body class="w-full bg-background min-h-screen">

    <!-- Main Content Injection -->
    <?= isset($content) ? $content : '' ?>

    <!-- Contextual Scripts -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>