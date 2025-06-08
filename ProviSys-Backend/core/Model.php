<?php
include_once 'utils/DBConnection.php';

class Model
{
    // Atributos
    protected $db;
    protected array $attributes = []; // Array asociativo: "atributo" => "campo_en_bd"
    protected array $guarded = []; // Array con los atributos dentro de $attributes no llenables
    protected string $primaryKey = ''; // String con el índice  de la clave primaria

    // Constructor
    public function __construct()
    {
        $this->db = new DBConnection()->getConnection();

        // Verificar que los atributos de $guarded estén en $attributes
        foreach ($this->guarded as $guarded) {
            if (!isset($this->attributes[$guarded])) {
                throw new Exception("El atributo $guarded no está definido en el modelo " . get_class($this));
            }
        }

        // Verificar que $primaryKey realmente perteneezca a $attributes
        if ($this->primaryKey !== null && !isset($this->attributes[$this->primaryKey])) {
            throw new Exception("El atributo $this->primaryKey no está definido en el modelo " . get_class($this));
        }
    }

    // Getter de BD,
    public function getDB()
    {
        return $this->db;
    }

    // Métodos mágicos para acceder a los atributos como propiedades
    public function __get($key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function __isset($key): bool
    {
        return isset($this->attributes[$key]);
    }

    // Métodos

    public function getFillableAttributes()
    {
        return array_diff(array_keys($this->attributes), $this->guarded);
    }
}