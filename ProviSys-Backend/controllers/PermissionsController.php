<?php

include_once 'models/PermissionsModel.php';

class PermissionsController
{
    public static function getPermissionsTree()
    {
        try {
            $permissions = PermissionsModel::getAllAsTree();
            echo json_encode(['response' => $permissions]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['errors' => $e->getMessage()]);
        }
    }
}
