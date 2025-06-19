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

    public function __construct($username, $registerDate, $names, $lastNames, $email, $phone, $secondaryPhone, $address, $roleId)
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

        // Obtener la conexión a la base de datos
        $this->db = DBConnection::getInstance()->getConnection();
    }

    // Getters
    public function getUsername()
    {
        return $this->username;
    }
    public function getRegisterDate()
    {
        return $this->registerDate;
    }
    public function getNames()
    {
        return $this->names;
    }
    public function getLastNames()
    {
        return $this->lastNames;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function getPhone()
    {
        return $this->phone;
    }
    public function getSecondaryPhone()
    {
        return $this->secondaryPhone;
    }
    public function getAddress()
    {
        return $this->address;
    }
    public function getRoleId()
    {
        return $this->roleId;
    }

    public function getCredentials()
    {
        // Obtener las credenciales del usuario
        include_once 'models/CredentialsModel.php';
        return CredentialsModel::getCredential($this->username);
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
            'roleId' => $this->roleId
        ];
    }

    // Métodos estáticos
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
                $user['id_rol']
            );
        } else {
            return null; // No se encontró el usuario
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

    public static function createUser($username, $password, $names, $lastNames, $email, $phone, $address, $roleId = 2, $secondaryPhone = null)
    {
        $db = DBConnection::getInstance()->getConnection();

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $registerDate = date('Y-m-d H:i:s');

        $db->autocommit(false);

        $sql1 = "INSERT INTO credencial (nombre_usuario, password) VALUES (?, ?)";
        $sql2 = "INSERT INTO usuario (nombre_usuario, fecha_registro, nombres, apellidos, correo, telefono, direccion, id_rol";
        $sqlValues = ") VALUES (?, ?, ?, ?, ?, ?, ?, ?";

        $params = [$username, $registerDate, $names, $lastNames, $email, $phone, $address, $roleId];
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
}