<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - <?php echo $data['title']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/public/css/style.css?v=<?php echo time(); ?>">
    <!-- CSS fix fallback -->
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/public/css/fix.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php require APPROOT . '/views/partials/header.php'; ?>
    
    <main>
        <?php require $view; ?>
    </main>
    
    <?php require APPROOT . '/views/partials/footer.php'; ?>
    
    <script src="<?php echo URL_ROOT; ?>/public/js/main.js"></script>
</body>
</html> 