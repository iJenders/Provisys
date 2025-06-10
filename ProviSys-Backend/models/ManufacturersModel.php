<?php
include_once 'core/Model.php';

class ManufacturersModel extends Model
{
    protected string $table = 'fabricante';
    protected array $attributes = [
        'id' => 'id_fabricante',
        'name' => 'nombre',
        'phone' => 'telefono',
        'secondaryPhone' => 'telefono_secundario',
        'email' => 'correo',
        'address' => 'direccion',
        'deleted' => 'eliminado'
    ];
    protected array $searchableAttributes = [
        'name',
        'phone',
        'secondaryPhone',
        'email',
        'address'
    ];
    protected array $guarded = [];
    protected string $primaryKey = 'id';

    // Acciones del método

    public function getAll($filters, $search, $offset)
    {
        // Armar la consulta
        $sql = "SELECT * FROM fabricante";

        // Si hay filtros, añadirlos
        $validFilters = [];
        if (count($filters) > 0) {
            // Primero se verifica que los filtros sean válidos
            $validFilters = array_intersect(array_keys($this->attributes), array_keys($filters));

            if (count($validFilters) > 0) {
                $sql .= " WHERE (";
                foreach ($validFilters as $filter) {
                    $sql .= $this->attributes[$filter] . " = ? AND ";
                }
                $sql = substr($sql, 0, -5); // Eliminar el string ' AND '
                $sql .= ")";
            }
        }

        // Añadir la búsqueda a todos los campos de la tabla
        if ($search != '') {
            // Si no se aplicaron filtros, añadir WHERE
            if (count($validFilters) == 0) {
                $sql .= " WHERE ";
            } else {
                $sql .= " AND ";
            }

            $sql .= '(id_fabricante LIKE ? OR nombre LIKE ? OR telefono LIKE ? OR telefono_secundario LIKE ? OR correo LIKE ? OR direccion LIKE ?)';
        }

        // Añadir el maximo de registros a mostrar y el offset
        $sql .= " LIMIT 10 OFFSET " . ($offset - 1) * 10;


        // Preparar la consulta
        $stmt = $this->db->prepare($sql);

        // Ejecutar la consulta
        $args = [];
        foreach ($validFilters as $filter) { // Filtros
            $args[] = $filters[$filter];
        }
        if ($search != '') { // Búsqueda
            $args = array_merge($args, array_fill(0, count($this->attributes) - 1, '%' . $search . '%'));
        }

        $stmt->execute($args);

        // Obtener los resultados
        $result = $stmt->get_result();
        $manufacturers = [];
        while ($row = $result->fetch_assoc()) {
            $manufacturers[] = [
                'id' => $row['id_fabricante'],
                'name' => $row['nombre'],
                'phone' => $row['telefono'],
                'secondaryPhone' => $row['telefono_secundario'],
                'email' => $row['correo'],
                'address' => $row['direccion'],
                'deleted' => $row['eliminado']
            ];
        }

        return $manufacturers;
    }

    public function count($filters, $search)
    {
        // Armar la consulta
        $sql = "SELECT COUNT(*) FROM fabricante";

        // Si hay filtros, añadirlos
        $validFilters = [];
        if (count($filters) > 0) {
            // Primero se verifica que los filtros sean válidos
            $validFilters = array_intersect(array_keys($this->attributes), array_keys($filters));

            if (count($validFilters) > 0) {
                $sql .= " WHERE (";
                foreach ($validFilters as $filter) {
                    $sql .= $this->attributes[$filter] . " = ? AND ";
                }
                $sql = substr($sql, 0, -5); // Eliminar el string ' AND '
                $sql .= ")";
            }
        }

        // Añadir la búsqueda a todos los campos de la tabla
        if ($search != '') {
            // Si no se aplicaron filtros, añadir WHERE
            if (count($validFilters) == 0) {
                $sql .= " WHERE ";
            } else {
                $sql .= " AND ";
            }

            $sql .= '(id_fabricante LIKE ? OR nombre LIKE ? OR telefono LIKE ? OR telefono_secundario LIKE ? OR correo LIKE ? OR direccion LIKE ?)';
        }

        // Preparar la consulta
        $stmt = $this->db->prepare($sql);

        // Ejecutar la consulta
        $args = [];
        foreach ($validFilters as $filter) { // Filtros
            $args[] = $filters[$filter];
        }
        if ($search != '') { // Búsqueda
            $args = array_merge($args, array_fill(0, count($this->attributes) - 1, '%' . $search . '%'));
        }

        $stmt->execute($args);

        // Obtener los resultados
        $result = $stmt->get_result();
        $manufacturersCount = $result->fetch_row()[0];
        return $manufacturersCount;
    }

    public function exists($id)
    {
        $sql = "SELECT * FROM fabricante WHERE id_fabricante = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function create($data)
    {
        // Armar la consulta
        $sql = "INSERT INTO fabricante (";

        foreach ($data as $key => $value) {
            $sql .= $this->attributes[$key] . ', ';
        }
        $sql = substr($sql, 0, -2); // Eliminar el string ', '
        $sql .= ") VALUES (";

        foreach ($data as $key) {
            $sql .= '?, ';
        }
        $sql = substr($sql, 0, -2); // Eliminar el string ', '
        $sql .= ")";

        // Preparar la consulta
        $stmt = $this->db->prepare($sql);

        if ($stmt === false) {
            throw new Exception("Error al preparar la consulta: " . $this->db->error);
        }

        // Ejecutar la consulta
        $dataValues = array_values($data);
        $success = $stmt->execute($dataValues);

        if ($success === false) {
            throw new Exception("Error al ejecutar la consulta: " . $this->db->error);
        }

        return true;
    }

    public function update($id, $data)
    {
        // Armar la consulta
        $sql = "UPDATE fabricante SET ";

        foreach ($data as $key => $value) {
            if (isset($this->attributes[$key])) {
                $sql .= $this->attributes[$key] . " = ?, ";
            }
        }
        $sql = substr($sql, 0, -2); // Eliminar el string ', '

        $sql .= " WHERE id_fabricante = ?";

        // Preparar la consulta
        $stmt = $this->db->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Error al preparar la consulta: " . $this->db->error);
        }

        // Ejecutar la consulta
        $dataValues = array_values($data);
        $dataValues[] = $id;

        $success = $stmt->execute($dataValues);

        if ($success === false) {
            throw new Exception("Error al ejecutar la consulta: " . $this->db->error);
        }
    }

    public function delete($id)
    {
        $sql = "UPDATE fabricante SET eliminado = NOT eliminado WHERE id_fabricante = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->affected_rows > 0;
    }
}