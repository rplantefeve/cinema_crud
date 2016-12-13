<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?= $title ?></title>
        <link type="text/css" href="<?= $request->getBasePath() . '/css/cinema.css' ?>" rel="stylesheet"/>
    </head>
    <body>
        <div id="content">
            <?= $content ?>
        </div>
    </body>
</html>
