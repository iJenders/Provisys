<?php
include_once 'core/Model.php';
include_once 'models/ProvidersModel.php';
include_once 'models/ProductsModel.php';
include_once 'models/PaymentsModel.php';

class PurchasesModel extends Model
{
    protected string $table = 'compra';
    protected array $attributes = [
        'id' => 'id_compra',
        'date' => 'fecha_compra',
        'provider_id' => 'id_proveedor',
    ];
    protected array $searchableAttributes = [];
    protected array $guarded = ['id'];
    protected string $primaryKey = 'id';


    // Métodos
    public function getAll($filters, $search, $offset)
    {
        // Armar consulta
        $sql = "SELECT * FROM vista_obtener_compras";
        $sql2 = "SELECT COUNT(*) as count FROM vista_obtener_compras";

        $whereClauses = [];
        $params = [];

        if (isset($filters['date']['from'])) {
            $whereClauses[] = "fecha_compra >= ?";
            $params[] = $filters['date']['from'];
        }

        if (isset($filters['date']['to'])) {
            $whereClauses[] = "fecha_compra <= ?";
            $params[] = $filters['date']['to'];
        }

        if (isset($filters['provider'])) {
            $whereClauses[] = "nombre = ?";
            $params[] = $filters['provider'];
        }

        if (isset($filters['value']['from'])) {
            $whereClauses[] = "total >= ?";
            $params[] = $filters['value']['from'];
        }

        if (isset($filters['value']['to'])) {
            $whereClauses[] = "total <= ?";
            $params[] = $filters['value']['to'];
        }

        if (!empty($whereClauses)) {
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
            $sql2 .= " WHERE " . implode(" AND ", $whereClauses);
        }

        $sql .= " ORDER BY fecha_compra DESC";

        $sql .= " LIMIT 10 OFFSET ?";
        $params[] = $offset;

        // Preparar consulta
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        // Obtener resultados
        $results = $stmt->get_result();

        $purchases = [];
        while ($row = $results->fetch_assoc()) {
            $purchases[] = $row;
        }

        // Obtener el conteo de resultados totales
        array_pop($params); // Eliminar el último parámetro, que es el offset (no es necesario para el conteo)

        $stmt2 = $this->db->prepare($sql2);
        $stmt2->execute($params);
        $result2 = $stmt2->get_result();
        $row2 = $result2->fetch_assoc();
        $totalCount = $row2['count'];


        return ['purchases' => $purchases, 'count' => $totalCount];
    }

    public function getPurchaseDetails($purchaseId)
    {

        // Obtener detalles de la compra
        $sql1 = "SELECT * FROM vista_obtener_compras WHERE id_compra = ?";
        $stmt1 = $this->db->prepare($sql1);
        $stmt1->execute([$purchaseId]);
        $result = $stmt1->get_result()->fetch_assoc();

        $purchase = [];

        $purchase['id'] = $result['id_compra'];
        $purchase['date'] = $result['fecha_compra'];

        // Obtener el proveedor de la compra
        $sql2 = "SELECT * FROM vista_obtener_proveedor_compra WHERE id_compra = ?";
        $stmt2 = $this->db->prepare($sql2);
        $stmt2->execute([$purchaseId]);
        $result2 = $stmt2->get_result()->fetch_assoc();

        $provider = [];
        $provider['id'] = $result2['id_proveedor'];
        $provider['name'] = $result2['nombre'];
        $provider['phone'] = $result2['telefono'];
        $provider['secondaryPhone'] = $result2['telefono_secundario'];
        $provider['email'] = $result2['correo'];
        $provider['address'] = $result2['direccion'];
        $provider['deleted'] = $result2['eliminado'];

        $purchase['provider'] = $provider;

        // Obtener los productos de la compra
        $sql3 = "SELECT * FROM vista_obtener_productos_compra WHERE id_compra = ?";
        $stmt3 = $this->db->prepare($sql3);
        $stmt3->execute([$purchaseId]);
        $results = $stmt3->get_result();

        $products = [];
        while ($row = $results->fetch_assoc()) {
            $product = $row;
            $products[] = $product;
        }
        $purchase['products'] = $products;

        // Por último, obtener los detalles del pago

        $sql4 = "SELECT * FROM vista_obtener_pagos_compra WHERE purchaseId = ?";
        $stmt4 = $this->db->prepare($sql4);
        $stmt4->execute([$purchaseId]);
        $result4 = $stmt4->get_result();

        $payments = [];
        while ($row = $result4->fetch_assoc()) {
            $payment = $row;
            $payments[] = $payment;
        }
        $purchase['payments'] = $payments;

        return $purchase;
    }

    public function create(array $data)
    {
        try {
            // Iniciar transacción
            $this->db->autocommit(false);
            $this->db->begin_transaction();

            // Añadir compra
            $sql2 = "INSERT INTO compra (fecha_compra, id_proveedor) VALUES (?, ?)";
            $args2 = [$data['date'], $data['providerId']];
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute($args2);

            // Verificar exito
            if ($stmt2->affected_rows > 0) {
                // Añadir productos de la compra
                $sql1 = "INSERT INTO detalles_compra (id_compra, id_producto, cantidad_producto, precio_de_compra, iva_de_compra, id_almacen) VALUES ";
                $args1 = [];

                $compraID = $this->db->insert_id;

                foreach ($data['products'] as $key => $product) {
                    $sql1 .= "(?, ?, ?, ?, ?, ?),";
                    $args1[] = $compraID;
                    $args1[] = $product['id'];
                    $args1[] = $product['quantity'];
                    $args1[] = $product['unitPrice'];
                    $args1[] = $product['iva'];
                    $args1[] = $product['warehouseId'];
                }
                $sql1 = substr($sql1, 0, -1);

                // Preparar consulta
                $stmt1 = $this->db->prepare($sql1);
                $stmt1->execute($args1);

                // Verificar exito
                if ($stmt1->affected_rows > 0) {
                    // Añadir pago pendiente
                    $totalAmount = 0;
                    foreach ($data['products'] as $product) {
                        $totalAmount += $product['quantity'] * $product['unitPrice'] + ($product['quantity'] * $product['unitPrice'] * $product['iva'] / 100);
                    }
                    $this->createPurchasePayment($compraID, $data['date'], $totalAmount);

                    // Añadir los productos de la compra a la tabla de productos
                    foreach ($data['products'] as $product) {
                        $sql3 = "UPDATE productos_en_almacen SET stock_disponible = stock_disponible + ? WHERE id_producto = ? AND id_almacen = ?";
                        $args3 = [];
                        $args3[] = $product['quantity'];
                        $args3[] = $product['id'];
                        $args3[] = $product['warehouseId'];

                        // Preparar consulta
                        $stmt3 = $this->db->prepare($sql3);
                        $stmt3->execute($args3);

                        // Validar exito
                        if ($stmt3->affected_rows <= 0) {
                            // Revertir transacción
                            $this->db->rollback();
                            throw new Exception('Error al añadir productos a la tabla de productos');
                        }
                    }

                    // Confirmar transacción
                    $this->db->commit();
                    $this->db->autocommit(true);
                    return true;
                } else {
                    // Revertir transacción
                    $this->db->rollback();
                    throw new Exception('Error al añadir productos a la tabla de productos');
                }
            }
        } catch (Exception $e) {
            // Revertir transacción
            $this->db->rollback();
            throw $e;
        }

        // Reactivar autocommit
        $this->db->autocommit(true);
    }

    public function getPurchaseProducts($purchaseId)
    {
        $sql = "SELECT * FROM detalles_compra WHERE id_compra = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$purchaseId]);

        // Recorrer cada producto, hidratandolo con su información
        $products = [];
        $productsModel = new ProductsModel();
        $results = $stmt->get_result();
        while ($row = $results->fetch_assoc()) {
            $product = $productsModel->corePoweredGetById($row['id_producto']);
            $product['quantity'] = $row['cantidad_producto'];
            $product['price'] = $row['precio_de_compra'];
            $product['iva'] = $row['iva_de_compra'];
            $products[] = $product;
        }

        return $products;
    }

    public function createPurchasePayment($purchaseId, $date, $amount)
    {
        $sql = "INSERT INTO pago (id_compra, fecha_pago, monto_total) VALUES (?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$purchaseId, $date, $amount]);

        return $this->db->insert_id;
    }

    public function registerPayment($purchaseId, $amount, $date, $reference, $methodId)
    {
        $sql = "CALL `rutina_agregar_pago_compra`(?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([$purchaseId, $amount, $date, $reference, $methodId]);
    }

    public function delete($purchaseId)
    {
        // Contador del número de filas afectadas
        $affectedRows = 0;

        // Desactivar auto commit
        $this->db->autocommit(false);

        // Iniciar transacción
        $this->db->begin_transaction();

        // Obtener los productos de la compra
        $sql1 = "SELECT * FROM detalles_compra WHERE id_compra = ?";
        $stmt1 = $this->db->prepare($sql1);

        try {
            $stmt1->execute([$purchaseId]);
        } catch (Exception $e) {
            // Revertir transacción
            $this->db->rollback();
            $this->db->autocommit(true);
            throw $e;
        }
        $results1 = $stmt1->get_result();

        // Actualizar el stock de los productos
        while ($row = $results1->fetch_assoc()) {
            $sql2 = "UPDATE productos_en_almacen SET stock_disponible = stock_disponible - ? WHERE id_producto = ? AND id_almacen = ?";
            $stmt2 = $this->db->prepare($sql2);

            try {
                $stmt2->execute([$row['cantidad_producto'], $row['id_producto'], $row['id_almacen']]);
            } catch (Exception $e) {
                // Revertir transacción
                $this->db->rollback();
                $this->db->autocommit(true);
                throw $e;
            }
        }
        $affectedRows += $this->db->affected_rows;

        // Eliminar la compra
        $sql3 = "DELETE FROM compra WHERE id_compra = ?";
        $stmt3 = $this->db->prepare($sql3);

        try {
            $stmt3->execute([$purchaseId]);
        } catch (Exception $e) {
            // Revertir transacción
            $this->db->rollback();
            $this->db->autocommit(true);
            throw $e;
        }
        $affectedRows += $this->db->affected_rows;

        // Confirmar transacción
        $this->db->commit();
        $this->db->autocommit(true);

        return true;
    }
}