<?php
include_once 'core/Model.php';

class ReportsModel extends Model
{
    public function __construct()
    {
        $this->db = new DBConnection()->getConnection();
    }

    public function actualInventoryValue()
    {
        $sql = "SELECT * FROM vista_reporte_estado_inventario";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function providersRanking($from = null, $to = null)
    {
        $where = [];
        $args = [];

        if ($from !== null) {
            $where[] = "c.fecha_compra >= ?";
            $args[] = $from;
        }
        if ($to !== null) {
            $where[] = "c.fecha_compra <= ?";
            $args[] = $to;
        }


        $sql = "SELECT
                    p.id_proveedor,
                    p.nombre,
                    SUM(cantidad_producto) AS volumen
                FROM detalles_compra dc
                    INNER JOIN compra c ON dc.id_compra = c.id_compra
                    INNER JOIN proveedor p ON c.id_proveedor = p.id_proveedor ";

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " GROUP BY p.id_proveedor";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($args);
        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function ingressesEgresses($from = null, $to = null)
    {
        $where = [];
        $args = [];

        if ($from !== null) {
            $where[] = "cuota.fecha_cuota >= ?";
            $args[] = $from;
        }
        if ($to !== null) {
            $where[] = "cuota.fecha_cuota <= ?";
            $args[] = $to;
        }


        $sql = "SELECT cuota.*,
                    mp.nombre_metodo,
                    pago.id_pedido,
                    pago.id_compra
                FROM cuota
                    INNER JOIN metodo_de_pago mp ON cuota.id_metodo = mp.id_metodo
                    INNER JOIN pago ON cuota.id_pago = pago.id_pago";

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($args);
        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function waitingOrders()
    {
        $sql = "SELECT * from vista_obtener_pedidos_clientes WHERE estado = 0 ORDER BY fecha_pedido ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        $rows = [];

        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function inventoryEntry($from = null, $to = null)
    {
        $where = [];
        $args = [];

        if ($from !== null) {
            $where[] = "c.fecha_compra >= ?";
            $args[] = $from;
        }
        if ($to !== null) {
            $where[] = "c.fecha_compra <= ?";
            $args[] = $to;
        }

        $sql = "SELECT
		pr.nombre AS nombre_producto,
                    dc.id_producto,
                    dc.cantidad_producto,
                    dc.id_almacen,
                    c.id_compra,
                    c.fecha_compra,
                    p.nombre
                FROM	producto pr
					 		INNER JOIN detalles_compra dc ON pr.id_producto = dc.id_producto
                    INNER JOIN compra c ON dc.id_compra = c.id_compra
                    INNER JOIN proveedor p ON c.id_proveedor = p.id_proveedor";

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY c.fecha_compra DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($args);
        $result = $stmt->get_result();
        $rows = [];

        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function inventoryExit($from = null, $to = null)
    {
        $where = ["pe.estado != 3"];
        $args = [];

        if ($from !== null) {
            $where[] = "pe.fecha_pedido >= ?";
            $args[] = $from;
        }
        if ($to !== null) {
            $where[] = "pe.fecha_pedido <= ?";
            $args[] = $to;
        }

        $sql = "SELECT
                    p.id_producto,
                    p.nombre,
                    dp.cantidad_producto,
                    pe.id_pedido,
                    pe.fecha_pedido,
                    pe.nombre_usuario,
                    pe.estado
                FROM producto p
                    INNER JOIN detalles_pedido dp ON p.id_producto = dp.id_producto
                    INNER JOIN pedido pe ON dp.id_pedido = pe.id_pedido";

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY pe.fecha_pedido DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($args);
        $result = $stmt->get_result();
        $rows = [];

        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }
}