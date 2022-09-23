<?php

function load_app_controllers()
{
    spl_autoload_register('my_own_controllers');
}

function my_own_controllers($class)
{
    if (preg_match('/\_(trait|job|enum)$/', $class, $matches)) {
        $path = $matches[1] . 's/';
        if (is_readable(APPPATH . $path . $class . '.php')) {
            require_once(APPPATH . $path . $class . '.php');
        }
    } elseif (strpos($class, 'CI_') !== 0 || strpos($class, '_core') !== false) {
        if (is_readable(APPPATH . 'core/' . $class . '.php')) {
            require_once(APPPATH . 'core/' . $class . '.php');
        }
    }
}
