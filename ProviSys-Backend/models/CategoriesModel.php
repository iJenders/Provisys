<?php

class CategoriesModel
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

        $query = "INSERT INTO categoria_producto (nombre, descripcion) VALUES ('$name', '$description')";

        $result = $db->query($query);

        if ($result) {
            return true;
        } else {
            throw new Exception("Error al crear la categoría: " . $db->error);
        }
    }

    public static function getAll($page, $onlyDisabled, $search = '')
    {
        $db = DBConnection::getInstance()->getConnection();

        $query =
            "SELECT * FROM categoria_producto" .
            " WHERE (id_categoria LIKE '%$search%' OR nombre LIKE '%$search%' OR descripcion LIKE '%$search%') " .
            ($onlyDisabled ? " AND eliminado = 1" : "") .
            " LIMIT 10 OFFSET " . ($page - 1) * 10;

        $result = $db->query($query);

        if ($result) {
            $categories = [];

            while ($row = $result->fetch_assoc()) {
                $category = new CategoriesModel(
                    $row['nombre'],
                    $row['descripcion'],
                    $row['eliminado'] == 1 ? true : false
                );
                $category->setId($row['id_categoria']);
                $categories[] = $category;
            }

            return $categories;
        } else {
            throw new Exception("Error al obtener las categorías: " . $db->error);
        }
    }

    public static function getCount($search, $onlyDisabled)
    {
        $db = DBConnection::getInstance()->getConnection();

        $query =
            "SELECT COUNT(*) as total FROM categoria_producto" .
            " WHERE (id_categoria LIKE '%$search%' OR nombre LIKE '%$search%' OR descripcion LIKE '%$search%')" .
            ($onlyDisabled ? " AND eliminado = 1" : "");

        $result = $db->query($query);

        $row = $result->fetch_assoc();
        return $row['total'];
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

    public static function edit($id, $name, $description, $disabled = null)
    {
        $db = DBConnection::getInstance()->getConnection();

        if ($disabled !== null) {
            $query = "UPDATE categoria_producto SET nombre = '$name', descripcion = '$description', eliminado = $disabled WHERE id_categoria = $id";
        } else {
            $query = "UPDATE categoria_producto SET nombre = '$name', descripcion = '$description' WHERE id_categoria = $id";
        }

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

        $query = "UPDATE categoria_producto SET eliminado = NOT eliminado WHERE id_categoria = $id";
        $result = $db->query($query);
        if ($result) {
            return true;
        } else {
            throw new Exception("Error al eliminar la categoría: " . $db->error);
        }
    }
}