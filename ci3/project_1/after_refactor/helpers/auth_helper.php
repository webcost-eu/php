<?php
if (!function_exists('roleIs')) {
	function roleIs($role)
	{
		$CI = &get_instance();
		if (method_exists($CI, 'roleIs')) {
			return $CI->roleIs($role);
		}

        return false;
	}
}

if (!function_exists('getRoles')) {
	function getRoles()
	{
		$CI = &get_instance();
		return $CI->getRoles();
	}
}

if (!function_exists('canEdit')) {
    function canEdit($controller, $method = 'tab_details')
    {
        $CI = &get_instance();
        if (method_exists($CI, 'canEdit')) {
            return $CI->canEdit($controller, $method);
        }

        return true;
    }
}

if (!function_exists('canView' . 'tab_details')) {
    function canView($controller, $method)
    {
        $CI = &get_instance();
        if (method_exists($CI, 'canView')) {
            return $CI->canView($controller, $method);
        }

        return true;
    }
}

if (!function_exists('hasAccess')) {
    function hasAccess($controller, $method = 'tab_details')
    {
        $CI = &get_instance();
        if (method_exists($CI, 'hasAccess')) {
            return $CI->canView($controller, $method);
        }

        return true;
    }
}
