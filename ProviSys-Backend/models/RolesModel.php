<?php

class RolesModel
{
    private $db;
    private int $id;
    private string $name;
    private string $description;
    private bool $disabled;

    public function __construct($name, $description, $disabled)
    {
        $this->name = $name;
        $this->description = $description;
        $this->disabled = $disabled;

        $this->db = DBConnection::getInstance()->getConnection();
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getDisabled()
    {
        return $this->disabled;
    }

    // Setters

    public function setId($id)
    {
        $this->id = $id;
    }

    // Métodos
    public static function create($name, $description)
    {
        $db = DBConnection::getInstance()->getConnection();

        $query = "INSERT INTO rol (nombre, descripcion) VALUES ('$name', '$description')";

        $result = $db->query($query);

        if ($result) {
            return true;
        } else {
            throw new Exception("Error al crear el rol: " . $db->error);
        }
    }

    public static function getAll($page, $onlyDisabled, $search = '')
    {
        $db = DBConnection::getInstance()->getConnection();

        $query =
            "SELECT * FROM rol" .
            " WHERE (id_rol LIKE '%$search%' OR nombre LIKE '%$search%' OR descripcion LIKE '%$search%') " .
            ($onlyDisabled ? " AND eliminado = 1" : "") .
            " LIMIT 10 OFFSET " . ($page - 1) * 10;

        $result = $db->query($query);

        if ($result) {
            $roles = [];

            while ($row = $result->fetch_assoc()) {
                $role = new RolesModel(
                    $row['nombre'],
                    $row['descripcion'],
                    $row['eliminado'] == 1 ? true : false
                );
                $role->setId($row['id_rol']);
                $roles[] = $role;
            }

            return $roles;
        } else {
            throw new Exception("Error al obtener los roles: " . $db->error);
        }
    }

    public static function getById($id)
    {
        $db = DBConnection::getInstance()->getConnection();

        $query = "SELECT * FROM rol WHERE id_rol = $id";

        $result = $db->query($query);

        if ($result) {
            $row = $result->fetch_assoc();
            $role = [
                'id' => intval($row['id_rol']),
                'name' => $row['nombre'],
                'description' => $row['descripcion'],
                'disabled' => $row['eliminado'] == 1 ? true : false
            ];

            return $role;
        } else {
            throw new Exception("Error al obtener el rol: " . $db->error);
        }
    }

    public static function getCount($search, $onlyDisabled)
    {
        $db = DBConnection::getInstance()->getConnection();

        $query =
            "SELECT COUNT(*) as total FROM rol" .
            " WHERE (id_rol LIKE '%$search%' OR nombre LIKE '%$search%' OR descripcion LIKE '%$search%')" .
            ($onlyDisabled ? " AND eliminado = 1" : "");

        $result = $db->query($query);

        $row = $result->fetch_assoc();
        return intval($row['total']);
    }

    public static function roleExists($id)
    {
        $db = DBConnection::getInstance()->getConnection();

        $query = "SELECT * FROM rol WHERE id_rol = $id";
        $result = $db->query($query);

        if ($result) {
            return $result->num_rows > 0;
        } else {
            throw new Exception("Error al verificar la existencia del rol: " . $db->error);
        }
    }

    public static function edit($id, $name, $description, $disabled = null)
    {
        $db = DBConnection::getInstance()->getConnection();

        if ($disabled !== null) {
            $query = "UPDATE rol SET nombre = '$name', descripcion = '$description', eliminado = $disabled WHERE id_rol = $id";
        } else {
            $query = "UPDATE rol SET nombre = '$name', descripcion = '$description' WHERE id_rol = $id";
        }

        $result = $db->query($query);

        if ($result) {
            return true;
        } else {
            throw new Exception("Error al editar el rol: " . $db->error);
        }
    }

    public static function delete($id)
    {
        $db = DBConnection::getInstance()->getConnection();

        $query = "UPDATE rol SET eliminado = NOT eliminado WHERE id_rol = $id";
        $result = $db->query($query);
        if ($result) {
            return true;
        } else {
            throw new Exception("Error al eliminar el rol: " . $db->error);
        }
    }

    public static function getAllActive()
    {
        $db = DBConnection::getInstance()->getConnection();

        $query = "SELECT * FROM rol WHERE eliminado = 0";

        $result = $db->query($query);

        if ($result) {
            $roles = [];

            while ($row = $result->fetch_assoc()) {
                $role = new RolesModel(
                    $row['nombre'],
                    $row['descripcion'],
                    $row['eliminado'] == 1 ? true : false
                );
                $role->setId($row['id_rol']);
                $roles[] = $role;
            }

            return $roles;
        } else {
            throw new Exception("Error al obtener los roles: " . $db->error);
        }
    }

    public static function getPermissions($roleId)
    {
        $db = DBConnection::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id_permiso FROM permisos_de_rol WHERE id_rol = ?");
        $stmt->bind_param('i', $roleId);
        $stmt->execute();
        $result = $stmt->get_result();
        $permissions = [];
        while ($row = $result->fetch_assoc()) {
            $permissions[] = $row['id_permiso'];
        }
        return $permissions;
    }

    public static function updatePermissions($roleId, $permissions)
    {
        $db = DBConnection::getInstance()->getConnection();
        $db->begin_transaction();

        try {
            // Eliminar permisos actuales
            $stmt = $db->prepare("DELETE FROM permisos_de_rol WHERE id_rol = ?");
            $stmt->bind_param('i', $roleId);
            $stmt->execute();
            $stmt->close();

            // Insertar nuevos permisos
            if (!empty($permissions)) {
                $stmt = $db->prepare("INSERT INTO permisos_de_rol (id_rol, id_permiso) VALUES (?, ?)");
                foreach ($permissions as $permissionId) {
                    $pId = intval($permissionId);
                    $stmt->bind_param('ii', $roleId, $pId);
                    $stmt->execute();
                }
                $stmt->close();
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }
}
