<?php

use Illuminate\Http\JsonResponse;
/**
 * Get the response in case of success
 *
 * @param string $message
 * @return JsonResponse
 */
function response_ok($message = 'ok')
{
    $message = is_array($message) ? $message : ['message' => $message];

    return response()->json($message);
}

/**
 * Get the response in case of fail
 *
 * @param int $code
 * @param mixed $message
 * @return JsonResponse
 */
function response_fail($message = 'not_allowed', $code = 403)
{
    $message = is_array($message) ? $message : ['message' => $message];

    return response()->json($message, $code);
}

if (!function_exists('format_to_string')) {
    function format_to_string($data, $field, $trim = ', ') {
        $string = '';
        foreach ($data as $item) {
            $string .= $item->{$field} . ', ';
        }
        return rtrim($string, $trim);
    }
}
