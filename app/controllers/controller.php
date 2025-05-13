<?php
function RenderView(string $views, array $data = [], string $layout) {
        ob_start();
        extract($data);
        require_once "../app/views/$views.html.php";
        $content = ob_get_clean();
        require_once "../app/views/layout/$layout.php";
    };
    