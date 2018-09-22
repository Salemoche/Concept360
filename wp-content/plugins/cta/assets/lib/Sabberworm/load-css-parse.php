<?php

spl_autoload_register(function($class)
{
    $file = WP_CTA_PATH.'assets/lib/'.strtr($class, '\\', '/').'.php';

    if (file_exists($file)) {
        require $file;
        return true;
    }
});