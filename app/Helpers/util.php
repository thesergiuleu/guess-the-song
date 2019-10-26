<?php

/**
 * Get the response in case of success
 *
 * @param string $message
 * @return \Illuminate\Http\JsonResponse
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
 * @return \Illuminate\Http\JsonResponse
 */
function response_fail($message = 'not_allowed', $code = 403)
{
    $message = is_array($message) ? $message : ['message' => $message];

    return response()->json($message, $code);
}
