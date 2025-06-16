<?php
include_once 'utils/DBConnection.php';

class Model
{
    // Atributos
    protected $db;
    protected string $table;
    protected array $attributes = []; // Array asociativo: "atributo" => "campo_en_bd"
    protected array $guarded = []; // Array con los atributos dentro de $attributes no llenables
    protected array $searchableAttributes = []; // Array con los atributos dentro de $attributes que son buscables
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

        // Verificar que los $searchableAttributes estén en $attributes
        if ($this->searchableAttributes !== null) {
            foreach ($this->searchableAttributes as $attribute) {
                if (!isset($this->attributes[$attribute])) {
                    throw new Exception("El atributo $attribute no está definido en el modelo " . get_class($this));
                }
            }
        }

        // Verificar que $primaryKey realmente pertenezca a $attributes
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

    public function corePoweredGetAll($filters, $search, $offset, $range = [])
    {
        if (empty($this->table) || empty($this->attributes)) {
            throw new \Exception("La tabla y los atributos deben ser definidos en la clase hija.");
        }

        $sql = "SELECT * FROM " . $this->table;

        $whereClauses = [];
        $args = [];

        // Filtros (col = value)
        foreach ($filters as $modelProperty => $value) {
            // Buscar la columna de la DB correspondiente al nombre de la propiedad del modelo
            $dbColumn = $this->attributes[$modelProperty] ?? false;

            if ($dbColumn !== false) {
                $whereClauses[] = "$dbColumn = ?";
                $args[] = $value;
            }
        }

        // Búsqueda (col LIKE %value%)
        if ($search !== null && !empty($search)) {
            $searchPattern = "%$search%";
            $searchSqlParts = [];

            // Iterar sobre los atributos buscables y construir las cláusulas LIKE
            foreach ($this->searchableAttributes as $modelProperty) {
                $dbColumn = $this->attributes[$modelProperty] ?? false;

                if ($dbColumn !== false) {
                    $searchSqlParts[] = "$dbColumn LIKE ?";
                    $args[] = $searchPattern;
                }
            }
            if (!empty($searchSqlParts)) {
                $whereClauses[] = "(" . implode(' OR ', $searchSqlParts) . ")";
            }
        }

        // Range (col >= value1 AND col <= value2)
        if ($range !== null && !empty($range)) {
            foreach ($range as $modelProperty => $rangeValue) {
                // Buscar la columna de la DB correspondiente al nombre de la propiedad del modelo
                $dbColumn = $this->attributes[$modelProperty] ?? false;

                if ($dbColumn !== false) {
                    if (isset($rangeValue['min'])) {
                        $whereClauses[] = "$dbColumn >= ?";
                        $args[] = $rangeValue['min'];
                    }
                    if (isset($rangeValue['max'])) {
                        $whereClauses[] = "$dbColumn <= ?";
                        $args[] = $rangeValue['max'];
                    }
                }
                // Verificar si el rango es válido
                if (isset($rangeValue['min']) && isset($rangeValue['max'])) {
                    if ($rangeValue['min'] > $rangeValue['max']) {
                        throw new \Exception("El rango de valores no es válido.");
                    }
                }
            }
        }

        // Combinar cláusulas WHERE
        if (!empty($whereClauses)) {
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }

        // Aplicar LIMIT y OFFSET para paginación
        $sql .= " LIMIT 10 OFFSET ?";
        $args[] = ($offset < 0) ? 0 : $offset;

        // Preparar y ejecutar la consulta
        $stmt = $this->db->prepare($sql);

        // Ejecutar la consulta
        $stmt->execute($args);

        // 6. Obtener y formatear los resultados
        $results = $stmt->get_result();
        $items = [];
        while ($row = $results->fetch_assoc()) {
            $item = [];
            foreach ($this->attributes as $modelProperty => $dbColumn) {
                if (isset($row[$dbColumn])) {
                    $item[$modelProperty] = $row[$dbColumn];
                }
            }
            $items[] = $item;
        }

        return $items;
    }

    public function corePoweredCount($filters, $search)
    {
        if (empty($this->table) || empty($this->attributes)) {
            throw new \Exception("La tabla y los atributos deben ser definidos en la clase hija.");
        }

        $sql = "SELECT COUNT(*) FROM " . $this->table;

        $whereClauses = [];
        $args = [];

        // Recorrer los filtros y convertirlos a nombres de columnas de DB
        foreach ($filters as $modelProperty => $value) {
            // Buscar la columna de la DB correspondiente al nombre de la propiedad del modelo
            $dbColumn = $this->attributes[$modelProperty] ?? false;

            if ($dbColumn !== false) {
                $whereClauses[] = "$dbColumn = ?";
                $args[] = $value;
            }
        }

        // Búsqueda (LIKE)
        if ($search !== null && !empty($search)) {
            $searchPattern = "%$search%";
            $searchSqlParts = [];

            // Iterar sobre los atributos buscables y construir las cláusulas LIKE
            foreach ($this->searchableAttributes as $modelProperty) {
                $dbColumn = $this->attributes[$modelProperty] ?? false;

                if ($dbColumn !== false) {
                    $searchSqlParts[] = "$dbColumn LIKE ?";
                    $args[] = $searchPattern;
                }
            }
            if (!empty($searchSqlParts)) {
                $whereClauses[] = "(" . implode(' OR ', $searchSqlParts) . ")";
            }
        }

        // Combinar cláusulas WHERE
        if (!empty($whereClauses)) {
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }

        // Preparar y ejecutar la consulta
        $stmt = $this->db->prepare($sql);

        // Ejecutar la consulta
        $stmt->execute($args);

        // 6. Obtener y formatear el resultado
        $results = $stmt->get_result();
        $count = $results->fetch_row()[0];

        return $count;
    }

    public function corePoweredGetById($id)
    {
        if (empty($this->table) || empty($this->attributes)) {
            throw new \Exception("La tabla y los atributos deben ser definidos en la clase hija.");
        }

        $sql = "SELECT * FROM " . $this->table . " WHERE " . $this->attributes[$this->primaryKey] . " = ?";

        // Preparar y ejecutar la consulta
        $stmt = $this->db->prepare($sql);

        $stmt->execute([$id]);

        // Obtener y formatear el resultado

        $results = $stmt->get_result();
        $row = $results->fetch_assoc();
        $item = [];
        foreach ($this->attributes as $modelProperty => $dbColumn) {
            if (isset($row[$dbColumn])) {
                $item[$modelProperty] = $row[$dbColumn];
            }
        }

        return $item;
    }

    public function exists($id)
    {
        if (empty($this->table) || empty($this->attributes)) {
            throw new \Exception("La tabla y los atributos deben ser definidos en la clase hija.");
        }

        $sql = "SELECT * FROM " . $this->table . " WHERE " . $this->attributes[$this->primaryKey] . " = ?";

        // Preparar y ejecutar la consulta
        $stmt = $this->db->prepare($sql);

        $stmt->execute([$id]);

        // Obtener y formatear el resultado

        $results = $stmt->get_result();
        $row = $results->fetch_assoc();

        return $row !== null;
    }

    public function corePoweredCreate($data)
    {
        if (empty($this->table) || empty($this->attributes)) {
            throw new \Exception("La tabla y los atributos deben ser definidos en la clase hija.");
        }

        // Obtener los atributos llenables
        $fillableAttributes = $this->getFillableAttributes();

        // Preparar las columnas y valores para la consulta SQL
        $dbColumns = [];
        $placeholders = [];
        $values = [];

        foreach ($data as $modelProperty => $value) {
            // Verificar si el atributo es llenable
            if (in_array($modelProperty, $fillableAttributes)) {
                // Obtener el nombre de la columna en la base de datos
                $dbColumn = $this->attributes[$modelProperty] ?? null;

                if ($dbColumn !== null) {
                    $dbColumns[] = $dbColumn;
                    $placeholders[] = "?";
                    $values[] = $value;
                }
            }
        }

        // Si no hay columnas para insertar, lanzar una excepción
        if (empty($dbColumns)) {
            throw new \Exception("No hay datos válidos para insertar.");
        }

        // Construir la consulta SQL
        $sql = "INSERT INTO " . $this->table . " (" . implode(", ", $dbColumns) . ") VALUES (" . implode(", ", $placeholders) . ")";

        // Preparar y ejecutar la consulta
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new \Exception("Error al preparar la consulta: " . $this->db->error);
        }

        // Ejecutar la consulta
        $result = $stmt->execute($values);

        if (!$result) {
            throw new \Exception("Error al ejecutar la consulta: " . $stmt->error);
        }

        // Obtener el ID del registro insertado
        $insertedId = $this->db->insert_id;

        return $insertedId;
    }

    public function corePoweredDelete($id, $forced = false)
    {
        if (empty($this->table) || empty($this->attributes)) {
            throw new \Exception("La tabla y los atributos deben ser definidos en la clase hija.");
        }

        if (!$forced) {
            if (!isset($this->attributes['deleted']) || empty($this->attributes['deleted'])) {
                throw new \Exception("El campo 'deleted' no está definido en la tabla. Este campo es necesario para la eliminación lógica. Probablemente estés utilizando una tabla sin eliminación lógica. Por favor, asegúrate de que la tabla tenga el campo 'deleted'.");
            }

            $sql = "UPDATE " . $this->table . " SET " . $this->attributes['deleted'] . " = NOT " . $this->attributes['deleted'] . " WHERE " . ($this->attributes[$this->primaryKey]) . " = ?";

            // Preparar y ejecutar la consulta
            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                throw new \Exception("Error al preparar la consulta: " . $this->db->error);
            }

            // Ejecutar la consulta
            $result = $stmt->execute([$id]);

            if (!$result) {
                throw new \Exception("Error al ejecutar la consulta: " . $stmt->error);
            }

            // Retornar el número de filas afectadas
            return $stmt->affected_rows;
        } else {
            $sql = "DELETE FROM " . $this->table . " WHERE " . $this->attributes[$this->primaryKey] . " = ?";

            // Preparar y ejecutar la consulta
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new \Exception("Error al preparar la consulta: " . $this->db->error);
            }
            // Ejecutar la consulta
            $result = $stmt->execute([$id]);
            if (!$result) {
                throw new \Exception("Error al ejecutar la consulta: " . $stmt->error);
            }
            // Retornar el número de filas afectadas
            return $stmt->affected_rows;
        }
    }

    public function corePoweredUpdate($id, $data)
    {
        if (empty($this->table) || empty($this->attributes)) {
            throw new \Exception("La tabla y los atributos deben ser definidos en la clase hija.");
        }

        // Obtener los atributos llenables
        $fillableAttributes = $this->getFillableAttributes();

        // Preparar las columnas y valores para la consulta SQL
        $updateParts = [];
        $values = [];

        foreach ($data as $modelProperty => $value) {
            // Verificar si el atributo es llenable
            if (in_array($modelProperty, $fillableAttributes)) {
                // Obtener el nombre de la columna en la base de datos
                $dbColumn = $this->attributes[$modelProperty] ?? null;

                if ($dbColumn !== null) {
                    $updateParts[] = "$dbColumn = ?";
                    $values[] = $value;
                }
            }
        }

        // Si no hay columnas para actualizar, lanzar una excepción
        if (empty($updateParts)) {
            throw new \Exception("No hay datos válidos para actualizar.");
        }

        // Construir la consulta SQL
        $sql = "UPDATE " . $this->table . " SET " . implode(", ", $updateParts) . " WHERE " . $this->attributes[$this->primaryKey] . " = ?";

        // Agregar el ID al final del array de valores
        $values[] = $id;

        // Preparar y ejecutar la consulta
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new \Exception("Error al preparar la consulta: " . $this->db->error);
        }

        // Ejecutar la consulta
        $result = $stmt->execute($values);

        if (!$result) {
            throw new \Exception("Error al ejecutar la consulta: " . $stmt->error);
        }

        // Retornar el número de filas afectadas
        return $stmt->affected_rows;
    }

}
