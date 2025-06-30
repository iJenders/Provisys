<?php

class PermissionsModel
{
    public static function getAllAsTree()
    {
        $db = DBConnection::getInstance()->getConnection();
        $query = "SELECT id_permiso as id, nombre as name, id_permiso_padre FROM permiso ORDER BY nombre ASC";
        $result = $db->query($query);
        $permissions = [];
        while ($row = $result->fetch_assoc()) {
            $permissions[] = $row;
        }

        return self::buildTree($permissions);
    }

    private static function buildTree(array &$elements, $parentId = null)
    {
        $branch = [];

        foreach ($elements as $element) {
            if ($element['id_permiso_padre'] == $parentId) {
                $children = self::buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }
}
