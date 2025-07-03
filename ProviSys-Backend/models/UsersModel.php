<?php
include_once 'utils/DBConnection.php';

class UsersModel
{
    private $db;
    private $username;
    private $registerDate;
    private $names;
    private $lastNames;
    private $email;
    private $phone;
    private $secondaryPhone;
    private $address;
    private $roleId;
    private $verified;

    public function __construct($username, $registerDate, $names, $lastNames, $email, $phone, $secondaryPhone, $address, $roleId, $verified)
    {
        $this->username = $username;
        $this->registerDate = $registerDate;
        $this->names = $names;
        $this->lastNames = $lastNames;
        $this->email = $email;
        $this->phone = $phone;
        $this->secondaryPhone = $secondaryPhone;
        $this->address = $address;
        $this->roleId = $roleId;
        $this->verified = $verified;

        // Obtener la conexión a la base de datos
        $this->db = DBConnection::getInstance()->getConnection();
    }

    // Getters
    public function getUsername()
    {
        return $this->username ?? '';
    }
    public function getRegisterDate()
    {
        return $this->registerDate;
    }
    public function getNames()
    {
        return $this->names ?? '';
    }
    public function getLastNames()
    {
        return $this->lastNames ?? '';
    }
    public function getEmail()
    {
        return $this->email ?? '';
    }
    public function getPhone()
    {
        return $this->phone ?? '';
    }
    public function getSecondaryPhone()
    {
        return $this->secondaryPhone ?? '';
    }
    public function getAddress()
    {
        return $this->address ?? '';
    }
    public function getRoleId()
    {
        return $this->roleId ?? 0;
    }
    public function getVerified()
    {
        return $this->verified ?? 0;
    }
    public function getCredentials()
    {
        // Obtener las credenciales del usuario
        include_once 'models/CredentialsModel.php';
        return CredentialsModel::getCredential($this->username);
    }

    // Setters
    public function setNames($names)
    {
        $this->names = $names;
    }

    public function setLastNames($lastNames)
    {
        $this->lastNames = $lastNames;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function setSecondaryPhone($secondaryPhone)
    {
        $this->secondaryPhone = $secondaryPhone;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;
    }

    public function setVerified($verified)
    {
        $this->verified = $verified;
    }

    public function toArray()
    {
        return [
            'username' => $this->username,
            'registerDate' => $this->registerDate,
            'names' => $this->names,
            'lastNames' => $this->lastNames,
            'email' => $this->email,
            'phone' => $this->phone,
            'secondaryPhone' => $this->secondaryPhone,
            'address' => $this->address,
            'roleId' => $this->roleId,
            'verified' => $this->verified
        ];
    }

    // Métodos estáticos
    public static function getAllEmployees($offset = 0, $search = '', $limit = 10)
    {
        $db = DBConnection::getInstance()->getConnection();
        
        $whereClause = "WHERE u.id_rol <> 2";
        if ($search !== '') {
            $whereClause .= " AND (u.nombres LIKE '%$search%' OR u.apellidos LIKE '%$search%' OR u.correo LIKE '%$search%' OR u.telefono LIKE '%$search%' OR u.telefono_secundario LIKE '%$search%' OR u.direccion LIKE '%$search%')";
        }

        // Data query
        $query = "SELECT u.*, r.nombre as role_name FROM usuario u JOIN rol r ON u.id_rol = r.id_rol " . $whereClause . " LIMIT $limit OFFSET $offset";
        $result = $db->query($query);

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $user = new UsersModel(
                $row['nombre_usuario'],
                $row['fecha_registro'],
                $row['nombres'],
                $row['apellidos'],
                $row['correo'],
                $row['telefono'],
                $row['telefono_secundario'],
                $row['direccion'],
                $row['id_rol'],
                intval($row['verificado'])
            );
            $userArray = $user->toArray();
            $userArray['role'] = ['name' => $row['role_name']];
            $userArray['status'] = boolval($userArray['verified']);
            $users[] = $userArray;
        }

        // Count query
        $countQuery = "SELECT COUNT(*) as count FROM usuario u " . $whereClause;
        $result = $db->query($countQuery);
        $count = $result->fetch_assoc()['count'];

        return ['users' => $users, 'count' => intval($count)];
    }

    public static function getAllClients($offset = 0, $search = '')
    {
        $db = DBConnection::getInstance()->getConnection();
        $query = "SELECT * FROM usuario WHERE id_rol = 2";
        if ($search !== '') {
            $query .= " AND (nombres LIKE '%$search%' OR apellidos LIKE '%$search%' OR correo LIKE '%$search%' OR telefono LIKE '%$search%' OR telefono_secundario LIKE '%$search%' OR direccion LIKE '%$search%')";
        }
        $result = $db->query($query);

        $users = [];

        while ($row = $result->fetch_assoc()) {
            $user = new UsersModel(
                $row['nombre_usuario'],
                $row['fecha_registro'],
                $row['nombres'],
                $row['apellidos'],
                $row['correo'],
                $row['telefono'],
                $row['telefono_secundario'],
                $row['direccion'],
                $row['id_rol'],
                intval($row['verificado'])
            );
            $users[] = $user->toArray();
        }

        // Count
        $query = "SELECT COUNT(*) as count FROM usuario WHERE id_rol = 2";
        $result = $db->query($query);
        $count = $result->fetch_assoc()['count'];

        return ['users' => $users, 'count' => intval($count)];
    }

    public static function getUser($username)
    {
        $db = DBConnection::getInstance()->getConnection();

        $query = "SELECT * FROM usuario WHERE nombre_usuario = '$username'";

        $result = $db->query($query);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            return new UsersModel(
                $user['nombre_usuario'],
                $user['fecha_registro'],
                $user['nombres'],
                $user['apellidos'],
                $user['correo'],
                $user['telefono'],
                $user['telefono_secundario'],
                $user['direccion'],
                $user['id_rol'],
                $user['verificado']
            );
        } else {
            return null; // No se encontró el usuario
        }
    }

    public static function getUserPermissions($username)
    {
        $db = DBConnection::getInstance()->getConnection();

        $query =
                "SELECT p.nombre
                    FROM permiso p
                        JOIN permisos_de_rol pr ON p.id_permiso = pr.id_permiso
                        JOIN usuario u ON pr.id_rol = u.id_rol
                    WHERE u.nombre_usuario = '$username'";

        $result = $db->query($query);

        if ($result->num_rows > 0) {
            $permissions = [];
            while ($row = $result->fetch_assoc()) {
                $permissions[] = $row['nombre'];
            }
            return $permissions;
        } else {
            return [];
        }
    }

    public static function userExists($username)
    {
        $db = DBConnection::getInstance()->getConnection();

        $query = "SELECT * FROM usuario WHERE nombre_usuario = '$username'";

        $result = $db->query($query);

        return $result->num_rows > 0; // Devuelve true si el usuario existe, false en caso contrario
    }

    public static function emailExists($email)
    {
        $db = DBConnection::getInstance()->getConnection();

        $query = "SELECT * FROM usuario WHERE correo = '$email'";

        $result = $db->query($query);

        return $result->num_rows > 0; // Devuelve true si el correo electrónico ya está en uso, false en caso contrario
    }

    public static function createUser($username, $password, $names, $lastNames, $email, $phone, $address, $roleId = 2, $secondaryPhone = null, $verified = 0)
    {
        $db = DBConnection::getInstance()->getConnection();

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $registerDate = date('Y-m-d H:i:s');

        $db->autocommit(false);

        $sql1 = "INSERT INTO credencial (nombre_usuario, password) VALUES (?, ?)";
        $sql2 = "INSERT INTO usuario (nombre_usuario, fecha_registro, nombres, apellidos, correo, telefono, direccion, id_rol, verificado";
        $sqlValues = ") VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?";

        $params = [$username, $registerDate, $names, $lastNames, $email, $phone, $address, $roleId, $verified];
        if ($secondaryPhone !== null) {
            $sql2 .= ", telefono_secundario";

            $sqlValues .= ", ?";
            $params[] = $secondaryPhone;
        }
        $sqlValues .= ")";

        $sql2 .= $sqlValues;

        try {
            $stmt1 = $db->prepare($sql1);
            $stmt1->execute([$username, $hashedPassword]);

            $stmt2 = $db->prepare($sql2);
            $stmt2->execute($params);

            $db->commit();
            $db->autocommit(true);

            return UsersModel::getUser($username);

        } catch (Exception $e) {
            $db->rollback();
            $db->autocommit(true);
            throw $e;
        }
    }

    public static function deleteUser($username)
    {
        $db = DBConnection::getInstance()->getConnection();

        $db->autocommit(false);

        try {
            // Primero, eliminar de la tabla usuario
            $sql1 = "DELETE FROM usuario WHERE nombre_usuario = ?";
            $stmt1 = $db->prepare($sql1);
            $stmt1->execute([$username]);

            // Luego, eliminar de la tabla credencial
            $sql2 = "DELETE FROM credencial WHERE nombre_usuario = ?";
            $stmt2 = $db->prepare($sql2);
            $stmt2->execute([$username]);

            if ($stmt1->affected_rows > 0 && $stmt2->affected_rows > 0) {
                $db->commit();
                $db->autocommit(true);
                return true;
            } else {
                $db->rollback();
                $db->autocommit(true);
                return false;
            }
        } catch (Exception $e) {
            $db->rollback();
            $db->autocommit(true);
            throw $e;
        }
    }

    public function update()
    {
        $db = DBConnection::getInstance()->getConnection();

        $sql = "UPDATE usuario SET nombres = ?, apellidos = ?, correo = ?, telefono = ?, telefono_secundario = ?, direccion = ?, id_rol = ?, verificado = ? WHERE nombre_usuario = ?";
        $params = [];

        $params[] = $this->getNames();
        $params[] = $this->getLastNames();
        $params[] = $this->getEmail();
        $params[] = $this->getPhone();
        $params[] = $this->getSecondaryPhone();
        $params[] = $this->getAddress();
        $params[] = $this->getRoleId();
        $params[] = $this->getVerified();
        $params[] = $this->getUsername();

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    }
}