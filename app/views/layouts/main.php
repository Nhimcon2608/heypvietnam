<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($data['title']) ? $data['title'] . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo URL_ROOT; ?>/public/img/logoHEYP.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo URL_ROOT; ?>/public/img/logoHEYP.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo URL_ROOT; ?>/public/img/logoHEYP.png">
    <link rel="shortcut icon" href="<?php echo URL_ROOT; ?>/public/img/logoHEYP.png">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="<?php echo URL_ROOT; ?>/public/manifest.json">
    
    <!-- Theme Colors -->
    <meta name="theme-color" content="#5A6B00">
    <meta name="msapplication-TileColor" content="#5A6B00">
    <meta name="msapplication-TileImage" href="<?php echo URL_ROOT; ?>/public/img/logoHEYP.png">
    
    <!-- Cache Control -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <!-- Bootstrap CSS with version timestamp for cache busting -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome with version timestamp -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css?v=<?php echo time(); ?>">
    <!-- Custom CSS with version timestamp -->
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/public/css/style.css?v=<?php echo time(); ?>">
    <!-- CSS Fix Fallback -->
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/public/css/fix.css?v=<?php echo time(); ?>">
    <!-- Fonts with version timestamp -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <!-- Fallback Bootstrap from CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css?v=<?php echo time(); ?>">
</head>
<body>
    <!-- Header -->
    <?php require_once 'app/views/partials/header.php'; ?>
    
    <!-- Main Content -->
    <main>
        <?php echo $content; ?>
    </main>
    
    <!-- Footer -->
    <?php require_once 'app/views/partials/footer.php'; ?>
    
    <!-- JavaScript Bundle with Popper with timestamp -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js?v=<?php echo time(); ?>"></script>
    <!-- Fallback Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js?v=<?php echo time(); ?>"></script>
    <script src="<?php echo URL_ROOT; ?>/public/js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html> 