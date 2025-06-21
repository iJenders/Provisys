<?php
include_once 'core/Model.php';

class WastesModel extends Model
{
    protected string $table = 'desperdicio';
    protected array $attributes = [
        'id' => 'id_desperdicio',
        'date' => 'fecha',
        'reason' => 'motivo',
        'quantity' => 'cantidad_producto',
        'price' => 'precio_producto',
        'iva' => 'iva_producto',
        'product_id' => 'id_producto',
        'storage_id' => 'id_almacen'
    ];
    protected array $searchableAttributes = [
        'id',
        'reason',
        'quantity'
    ];
    protected array $guarded = ['id'];
    protected string $primaryKey = 'id';

    // Métodos

    public function getAll($deleted = 0, $from = '', $to = '')
    {
        $sql = "SELECT desperdicio.*, producto.nombre AS nombre_producto, almacen.nombre AS nombre_almacen
                FROM desperdicio
                    INNER JOIN producto ON desperdicio.id_producto = producto.id_producto
                    INNER JOIN almacen ON desperdicio.id_almacen = almacen.id_almacen";

        $wheres = [];
        $params = [];

        // Filtros
        $wheres[] = "desperdicio.eliminado = ?"; // Eliminados
        $params[] = $deleted;

        if ($from != '') {
            $wheres[] = "desperdicio.fecha >= ?";
            $params[] = $from;
        }

        if ($to != '') {
            $wheres[] = "desperdicio.fecha <= ?";
            $params[] = $to;
        }

        // Añadir wheres  y params a la consulta
        if (!empty($wheres)) {
            $sql .= " WHERE " . implode(" AND ", $wheres);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $result = $stmt->get_result();

        $wastes = [];
        while ($row = $result->fetch_assoc()) {
            $waste = [
                'id' => $row['id_desperdicio'],
                'date' => $row['fecha'],
                'reason' => $row['motivo'],
                'quantity' => $row['cantidad_producto'],
                'price' => $row['precio_producto'],
                'iva' => $row['iva_producto'],
                'product_id' => $row['id_producto'],
                'storage_id' => $row['id_almacen'],
                'product_name' => $row['nombre_producto'],
                'storage_name' => $row['nombre_almacen']
            ];
            $wastes[] = $waste;
        }

        return $wastes;
    }

    public function createWaste($product_id, $quantity, $date, $reason)
    {
        // Desactivar auto commit
        $this->db->autocommit(false);
        $this->db->begin_transaction();

        // Descontar productos del stock
        try {
            $sql1 = "UPDATE productos_en_almacen SET stock_disponible = stock_disponible - ? WHERE id_producto = ?";
            $stmt1 = $this->db->prepare($sql1);
            $stmt1->execute([$quantity, $product_id]);
        } catch (Exception $e) {
            $this->db->rollback();
            $this->db->autocommit(true);
            throw $e;
        }

        // Obtener el precio del producto
        $price = null;
        $iva = null;
        $storage_id = null;
        try {
            $sql3 = "SELECT precio, iva, id_almacen FROM producto INNER JOIN iva ON producto.id_iva = iva.id_iva INNER JOIN productos_en_almacen ON producto.id_producto = productos_en_almacen.id_producto WHERE producto.id_producto = ?";
            $stmt3 = $this->db->prepare($sql3);
            $stmt3->execute([$product_id]);

            $result = $stmt3->get_result();

            if ($result->num_rows == 0) {
                $this->db->rollback();
                $this->db->autocommit(true);
                throw new Exception("Producto no encontrado. Probablemente no tiene almacén configurado.");
            }

            $product = $result->fetch_assoc();

            $price = $product['precio'];
            $iva = $product['iva'];
            $storage_id = $product['id_almacen'];
        } catch (Exception $e) {
            $this->db->rollback();
            $this->db->autocommit(true);
            throw $e;
        }

        // Insertar desperdicio
        try {
            $sql2 = "INSERT INTO desperdicio (fecha, motivo, cantidad_producto, precio_producto, iva_producto, id_producto, id_almacen) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute([$date, $reason, $quantity, $price, $iva, $product_id, $storage_id]);
        } catch (Exception $e) {
            $this->db->rollback();
            $this->db->autocommit(true);
            throw $e;
        }

        $this->db->commit();
        $this->db->autocommit(true);

        return true;
    }

    public function deleteWaste($id)
    {
        $this->db->autocommit(false);

        // Obtener información del desperdicio antes de eliminarlo
        try {
            $sql = "SELECT cantidad_producto, id_producto, id_almacen FROM desperdicio WHERE id_desperdicio = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);

            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                $this->db->rollback();
                $this->db->autocommit(true);
                throw new Exception("Desperdicio no encontrado.");
            }

            $waste = $result->fetch_assoc();
        } catch (Exception $e) {
            $this->db->rollback();
            $this->db->autocommit(true);
            throw $e;
        }

        // Actualizar el stock
        try {
            $sql2 = "UPDATE productos_en_almacen SET stock_disponible = stock_disponible + ? WHERE id_producto = ? AND id_almacen = ?";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute([$waste['cantidad_producto'], $waste['id_producto'], $waste['id_almacen']]);
        } catch (Exception $e) {
            $this->db->rollback();
            $this->db->autocommit(true);
            throw $e;
        }

        // Eliminar el desperdicio
        try {
            $sql3 = "UPDATE desperdicio SET eliminado = 1 WHERE id_desperdicio = ?";
            $stmt3 = $this->db->prepare($sql3);
            $stmt3->execute([$id]);
        } catch (Exception $e) {
            $this->db->rollback();
            $this->db->autocommit(true);
            throw $e;
        }

        $this->db->commit();
        $this->db->autocommit(true);

        return true;

    }

    public function isDeleted($id)
    {
        $sql = "SELECT eliminado FROM desperdicio WHERE id_desperdicio = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['eliminado'];
    }
}
