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

    // Setters

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
                $user['correo_electrónico'],
                $user['telefono'],
                $user['telefono_secundario'],
                $user['direccion'],
                $user['id_rol']
            );
        } else {
            return null; // No se encontró el usuario
        }
    }
}