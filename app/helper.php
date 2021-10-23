<?php
use Phalcon\Http\Response;

function jsonResponse(bool $success, array $data) {
    $response = new Response();
    $array = array(
        'success' => $success,
        'data' => $data
    );
    return $response
        ->setJsonContent($array)
        ->send();
}

function jsonMessage(bool $success, string $message) {
    $response = new Response();
    $array = array(
        'success' => $success,
        'message' => $message
    );
    return $response
        ->setJsonContent($array)
        ->send();
}
