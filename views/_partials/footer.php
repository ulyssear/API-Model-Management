<?php
    if ((bool) $app->config()->get('app.debug.show_debugbar')) {
        echo $app->debugbar()->getJavascriptRenderer()->render();
    }
?>

</body>
</html>