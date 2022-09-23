<?php
if (!function_exists('jsonResponse')) {
	function jsonResponse($data)
	{
		$CI = &get_instance();
		$CI->jsonResponse($data);
	}
}
