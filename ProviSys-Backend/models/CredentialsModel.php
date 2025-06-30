<?php

class CredentialsModel
{
    private $db;
    private $username;
    private $password;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;

        $this->db = DBConnection::getInstance()->getConnection();
    }

    // Getters y Setters
    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    // Métodos de instancia

    public function verifyPassword($password)
    {
        // Verifica si la contraseña proporcionada coincide con la almacenada
        return password_verify($password, $this->password);
    }

    public function updatePassword($newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $sql = "UPDATE credencial SET password = ? WHERE nombre_usuario = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$hashedPassword, $this->username]);
    }

    // Métodos estáticos
    public static function getCredential($username)
    {
        $db = DBConnection::getInstance()->getConnection();
        $query = "SELECT * FROM credencial WHERE nombre_usuario = '$username'";

        $result = $db->query($query);

        if ($result->num_rows > 0) {
            $credential = $result->fetch_assoc();
            $credential = new CredentialsModel(
                $credential['nombre_usuario'],
                $credential['password']
            );
            return $credential;
        } else {
            return null; // No se encontró el usuario
        }
    }
}