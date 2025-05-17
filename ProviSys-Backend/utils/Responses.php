<?php
class Responses{
    public static function json($data, $status = 200) {
        header('Content-Type: application/json', true, $status);
        echo json_encode(["response" => $data]);
        exit;
    }

    public static function redirect($url, $status = 302) {
        header('Location: ' . $url, true, $status);
        exit;
    }
}

?>