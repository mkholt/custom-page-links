<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title><?php echo dk\mholt\CustomPageLinks\HttpStatus::getStatus(dk\mholt\CustomPageLInks\HttpStatus::HttpNotFound); ?></title>
</head>
<body>
    <h1><?php echo dk\mholt\CustomPageLinks\HttpStatus::getStatus(dk\mholt\CustomPageLInks\HttpStatus::HttpNotFound); ?></h1>
    <p>
        The link with the ID "<?php echo $link ?>" was not found on the post with id "<?php echo $post ?>".
    </p>
</body>
</html>