<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ceci est un titre</title>
    <?php
        if ((bool)$app->config()->get('app.debug.show_debugbar')) {
            echo $app->debugbar()->getJavascriptRenderer()->renderHead();
        }
    ?>
</head>
<body>
