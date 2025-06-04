<?php

class DBConnection
{
    private static $instance = null;

    private $connection;

    private function __construct()
    {
        include_once 'env.php';

        global $ENV;

        if (!isset($ENV['DB_HOST']) || !isset($ENV['DB_USER']) || !isset($ENV['DB_PASSWORD']) || !isset($ENV['DB_NAME']) || !isset($ENV['DB_PORT'])) {
            // Si faltan variables de entorno, devolver un error 500
            Responses::json(['errors' => ['Error al conectar a la base de datos: ' . 'Faltan variables de entorno']], 500);
        }

        try {
            $this->connection = new mysqli(
                $ENV['DB_HOST'],
                $ENV['DB_USER'],
                $ENV['DB_PASSWORD'],
                $ENV['DB_NAME'],
                $ENV['DB_PORT']
            );
        } catch (Exception $e) {
            // Si hay un error al crear la conexión, devolver un error 500
            Responses::json(['errors' => ['Error al conectar a la base de datos: ' . $e->getMessage()]], 500);
        }

        if ($this->connection->connect_error) {
            Responses::json(['errors' => ['Error al conectar a la base de datos: ' . $this->connection->connect_error]], 500);
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new DBConnection();
        }

        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}

/*
Ejemplo de uso:

$db = DBConnection::getInstance();
$connection = $db->getConnection();

// Ahora puedes usar $connection para realizar consultas a la base de datos.

*/