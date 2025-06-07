<?php

class CategoriesModel
{
    private $db;
    private $id;
    private $name;
    private $description;

    public function __construct($name, $description)
    {
        $this->name = $name;
        $this->description = $description;

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

    // Setters

    public function setId($id)
    {
        $this->id = $id;
    }

    // Métodos
    public static function create($name, $description)
    {
        $db = DBConnection::getInstance()->getConnection();

        $query = "INSERT INTO categoria_producto (nombre, descripcion) VALUES ('$name', '$description')";

        $result = $db->query($query);

        if ($result) {
            return true;
        } else {
            throw new Exception("Error al crear la categoría: " . $db->error);
        }
    }

    public static function getAll()
    {
        $db = DBConnection::getInstance()->getConnection();

        $query = "SELECT * FROM categoria_producto WHERE eliminado = 0";
        $result = $db->query($query);

        if ($result) {
            $categories = [];

            while ($row = $result->fetch_assoc()) {
                $category = new CategoriesModel($row['nombre'], $row['descripcion']);
                $category->setId($row['id_categoria']);
                $categories[] = $category;
            }

            return $categories;
        } else {
            throw new Exception("Error al obtener las categorías: " . $db->error);
        }
    }

    public static function search($search)
    {
        $db = DBConnection::getInstance()->getConnection();

        $query = "SELECT * FROM categoria_producto WHERE eliminado = 0 AND (id_categoria LIKE '%$search%' OR nombre LIKE '%$search%' OR descripcion LIKE '%$search%')";
        $result = $db->query($query);

        if ($result) {
            $categories = [];

            while ($row = $result->fetch_assoc()) {
                $category = new CategoriesModel($row['nombre'], $row['descripcion']);
                $category->setId($row['id_categoria']);
                $categories[] = $category;
            }

            return $categories;
        } else {
            throw new Exception("Error al obtener las categorías: " . $db->error);
        }
    }

    public static function categoryExists($id)
    {
        $db = DBConnection::getInstance()->getConnection();

        $query = "SELECT * FROM categoria_producto WHERE id_categoria = $id";
        $result = $db->query($query);

        if ($result) {
            return $result->num_rows > 0;
        } else {
            throw new Exception("Error al verificar la existencia de la categoría: " . $db->error);
        }
    }

    public static function edit($id, $name, $description)
    {
        $db = DBConnection::getInstance()->getConnection();

        $query = "UPDATE categoria_producto SET nombre = '$name', descripcion = '$description' WHERE id_categoria = $id";

        $result = $db->query($query);

        if ($result) {
            return true;
        } else {
            throw new Exception("Error al editar la categoría: " . $db->error);
        }
    }

    public static function delete($id)
    {
        $db = DBConnection::getInstance()->getConnection();

        $query = "UPDATE categoria_producto SET eliminado = 1 WHERE id_categoria = $id";
        $result = $db->query($query);
        if ($result) {
            return true;
        } else {
            throw new Exception("Error al eliminar la categoría: " . $db->error);
        }
    }
}