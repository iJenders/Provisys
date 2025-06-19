<?php
include_once 'core/Model.php';
include_once 'models/PaymentMethodsModel.php';
include_once 'models/UsersModel.php';

class OrdersModel extends Model
{
    protected string $table = 'pedido';
    protected array $attributes = [
        'id' => 'id_pedido',
        'date' => 'fecha_pedido',
        'status' => 'estado',
        'username' => 'nombre_usuario',
    ];
    protected array $searchableAttributes = [
        'id',
        'date',
        'status',
        'username'
    ];
    protected array $guarded = ['id'];
    protected string $primaryKey = 'id';

    // Métodos
    public function getAll($filters, $search, $offset)
    {
        // Armar consulta
        $sql1 = "SELECT * FROM vista_obtener_pedidos_clientes";
        $args = [];

        if (isset($filters['status'])) {
            $sql1 .= " WHERE estado = ?";
            $args[] = $filters['status'];
        }


        // Ejecutar consulta
        $stmt1 = $this->db->prepare($sql1);

        $stmt1->execute($args);

        $result1 = $stmt1->get_result();

        $clientOrders = [];
        while ($row = $result1->fetch_assoc()) {
            $order = [
                "id" => $row['id_pedido'],
                "date" => $row['fecha_pedido'],
                "status" => $row['estado'],
                "username" => $row['nombre_usuario'],
                "totalProducts" => $row['total_productos'],
                "payment" => [
                    "id" => $row['id_pago'],
                    "value" => floatval($row['valor']),
                    "paid" => floatval($row['total_pagado']),
                    "verified" => $row['verificados'],
                    "paying" => $row['por_pagar'],
                ]
            ];

            $clientOrders[] = $order;
        }

        return $clientOrders;
    }

    public function getUserOrders($filters, $search, $offset)
    {
        global $USER;

        $sql1 = "SELECT * FROM vista_obtener_pedidos_clientes WHERE nombre_usuario = ? AND estado != 3";
        $stmt1 = $this->db->prepare($sql1);

        $stmt1->execute([$USER]);

        $result1 = $stmt1->get_result();

        $clientOrders = [];
        while ($row = $result1->fetch_assoc()) {
            $order = [
                "id" => $row['id_pedido'],
                "date" => $row['fecha_pedido'],
                "status" => $row['estado'],
                "username" => $row['nombre_usuario'],
                "totalProducts" => $row['total_productos'],
                "payment" => [
                    "id" => $row['id_pago'],
                    "value" => floatval($row['valor']),
                    "paid" => floatval($row['total_pagado']),
                    "verified" => $row['verificados'],
                    "paying" => $row['por_pagar'],
                ]
            ];

            $clientOrders[] = $order;
        }

        return $clientOrders;
    }

    public function getProducts($orderId)
    {
        $sql = "SELECT * FROM vista_obtener_productos_pedido WHERE id_pedido = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->execute([$orderId]);
        $result = $stmt->get_result();

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $product = [
                "id" => $row['id_producto'],
                "name" => $row['nombre_producto'],
                "description" => $row['descripcion_producto'],
                "category" => $row['nombre_categoria'],
                "manufacturer" => $row['nombre_fabricante'],
                "quantity" => $row['cantidad_producto'],
                "price" => $row['precio_de_venta'],
                "tax" => $row['iva_de_venta']
            ];
            $products[] = $product;
        }

        return $products;
    }

    public function getPayments($orderId)
    {
        $sql = "SELECT * FROM cuota INNER JOIN metodo_de_pago ON cuota.id_metodo = metodo_de_pago.id_metodo INNER JOIN pago ON pago.id_pago=cuota.id_pago WHERE id_pedido = ? AND cuota.eliminado = 0 ORDER BY id_cuota DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        $results = $stmt->get_result();

        $payments = [];
        while ($row = $results->fetch_assoc()) {
            $payment = [
                'id' => $row['id_cuota'],
                'date' => $row['fecha_cuota'],
                'amount' => $row['monto'],
                'reference' => $row['nro_referencia'],
                'verified' => $row['verificado'],
                'method' => $row['nombre_metodo']
            ];
            $payments[] = $payment;
        }

        return $payments;
    }

    public function cancelOrder($orderId)
    {
        // Desactivar autocommit
        $this->db->autocommit(false);
        $this->db->begin_transaction();

        try {
            // Primero, obtener los productos del pedido
            $sql1 = "SELECT * FROM detalles_pedido WHERE id_pedido = ?";
            $stmt1 = $this->db->prepare($sql1);
            $stmt1->execute([$orderId]);
            $result1 = $stmt1->get_result();

            $products = [];
            while ($row = $result1->fetch_assoc()) {
                $product = [
                    "id" => $row['id_producto'],
                    "quantity" => $row['cantidad_producto']
                ];
                $products[] = $product;
            }

            // Luego, obtener el pedido como tal
            $sql2 = "SELECT * FROM pedido WHERE id_pedido = ?";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute([$orderId]);
            $result2 = $stmt2->get_result();
            $order = $result2->fetch_assoc();
            $orderStatus = intval($order['estado']);

            switch ($orderStatus) {
                case 0: // Si el estado es 0 (Pendiente), se restaura el stock reservado en cada producto.
                    foreach ($products as $product) {
                        $sql3 = "UPDATE productos_en_almacen SET stock_reservado = stock_reservado - ? WHERE id_producto = ?";
                        $stmt3 = $this->db->prepare($sql3);
                        $stmt3->execute([$product['quantity'], $product['id']]);
                    }

                    $sql4 = "UPDATE pedido SET estado = 3 WHERE id_pedido = ?";
                    $stmt4 = $this->db->prepare($sql4);
                    $stmt4->execute([$orderId]);

                    $this->db->commit();
                    break;
                case 1: // Si el estado es 1 (Facturado) Se hace lo mismo.
                    foreach ($products as $product) {
                        $sql3 = "UPDATE productos_en_almacen SET stock_reservado = stock_reservado - ? WHERE id_producto = ?";
                        $stmt3 = $this->db->prepare($sql3);
                        $stmt3->execute([$product['quantity'], $product['id']]);
                    }

                    $sql4 = "UPDATE pedido SET estado = 3 WHERE id_pedido = ?";
                    $stmt4 = $this->db->prepare($sql4);
                    $stmt4->execute([$orderId]);

                    $this->db->commit();
                    break;
                case 2: // Si el estado es 2 (Entregado), se restaura el stock disponible en cada producto.
                    foreach ($products as $product) {
                        $sql3 = "UPDATE productos_en_almacen SET stock_disponible = stock_disponible + ? WHERE id_producto = ?";
                        $stmt3 = $this->db->prepare($sql3);
                        $stmt3->execute([$product['quantity'], $product['id']]);
                    }

                    $sql4 = "UPDATE pedido SET estado = 3 WHERE id_pedido = ?";
                    $stmt4 = $this->db->prepare($sql4);
                    $stmt4->execute([$orderId]);

                    $this->db->commit();
                    break;
                default:
                    $this->db->rollback();
                    break;
            }

            $this->db->autocommit(true);
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            $this->db->autocommit(true);
            throw $e;
        }
    }

    public function createOrder($products)
    {
        // Desactivar autocommit
        $this->db->autocommit(false);
        $this->db->begin_transaction();

        // Obtener el ID del usuario
        global $USER;

        try {
            // Crear el pedido
            $sql1 = "INSERT INTO pedido (nombre_usuario, fecha_pedido, estado) VALUES (?, NOW(), 0)";
            $stmt1 = $this->db->prepare($sql1);
            $stmt1->execute([$USER]);
            $orderId = $this->db->insert_id;

            // Reservar stock a cada producto
            foreach ($products as $product) {
                $sql2 = "UPDATE productos_en_almacen SET stock_reservado = stock_reservado + ? WHERE id_producto = ?";
                $stmt2 = $this->db->prepare($sql2);
                $stmt2->execute([$product['quantity'], $product['id']]);
            }

            // Obtener los precios y iva de cada producto
            $productsWithPrices = [];
            foreach ($products as $product) {
                $sql3 = "SELECT producto.precio, iva.iva FROM producto INNER JOIN iva ON producto.id_iva = iva.id_iva WHERE producto.id_producto = ?";
                $stmt3 = $this->db->prepare($sql3);
                $stmt3->execute([$product['id']]);
                $result3 = $stmt3->get_result();
                $row3 = $result3->fetch_assoc();
                $product['price'] = $row3['precio'];
                $product['iva'] = $row3['iva'];
                $productsWithPrices[] = $product;
            }

            // Insertar los detalles del pedido
            foreach ($productsWithPrices as $product) {
                $sql4 = "INSERT INTO detalles_pedido (id_pedido, id_producto, cantidad_producto, precio_de_venta, iva_de_venta) VALUES (?, ?, ?, ?, ?)";
                $stmt4 = $this->db->prepare($sql4);
                $stmt4->execute([$orderId, $product['id'], $product['quantity'], $product['price'], $product['iva']]);
            }

            // Obtener el precio total de la orden
            $total = 0;
            foreach ($productsWithPrices as $product) {
                $total += floatval($product['price']) * floatval($product['quantity']) * (1 + floatval($product['iva']) / 100);
            }

            // Crear el pago relacionado con el pedido
            $sql5 = "INSERT INTO pago (id_pedido, fecha_pago, monto_total) VALUES (?, NOW(), ?)";
            $stmt5 = $this->db->prepare($sql5);
            $stmt5->execute([$orderId, $total]);

            // Confirmar la transacción
            $this->db->commit();
            $this->db->autocommit(true);

            return $orderId;
        } catch (Exception $e) {
            $this->db->rollback();
            $this->db->autocommit(true);
            throw $e;
        }
    }

    public function registerOrderPayment($orderId, $paymentAmount, $paymentMethodId, $paymentDate, $paymentReference)
    {
        // Obtener el pago relacionado con el pedido
        $sql1 = "SELECT id_pago FROM pago WHERE id_pedido = ?";
        $stmt1 = $this->db->prepare($sql1);
        $stmt1->execute([$orderId]);
        $result1 = $stmt1->get_result();
        $row1 = $result1->fetch_assoc();
        $paymentId = $row1['id_pago'];

        // Registrar el pago
        $sql2 = "INSERT INTO cuota (fecha_cuota, monto, nro_referencia, verificado, id_metodo, id_pago) VALUES (?, ?, ?, 0, ?, ?)";
        $stmt2 = $this->db->prepare($sql2);
        $stmt2->execute([$paymentDate, $paymentAmount, $paymentReference, $paymentMethodId, $paymentId]);

        return true;
    }

    public function mountExcededPayment($orderId, $paymentAmount)
    {
        $sql = "SELECT * FROM vista_obtener_pagos WHERE id_pedido = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $montoTotal = floatval($row['monto_total']);
            $montoPago = floatval($row['total_pagado']);

            $montoPendiente = $montoTotal - $montoPago;
            if ($montoPendiente - $paymentAmount < 0) {
                return true;
            }
        }
    }

    public function billOrder($orderId)
    {
        $sql = "UPDATE pedido SET estado = 1 WHERE id_pedido = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        return true;
    }

    public function deliverOrder($orderId)
    {
        // Desacttivar autocommit
        $this->db->autocommit(false);
        $this->db->begin_transaction();

        try {
            // Obtener los productos del pedido
            $sql1 = "SELECT * FROM detalles_pedido WHERE id_pedido = ?";
            $stmt1 = $this->db->prepare($sql1);
            $stmt1->execute([$orderId]);
            $result1 = $stmt1->get_result();

            $orderProducts = [];
            while ($row1 = $result1->fetch_assoc()) {
                $orderProducts[] = $row1;
            }

            // Actualizar el stock de los productos
            foreach ($orderProducts as $orderProduct) {
                $sql2 = "UPDATE productos_en_almacen SET stock_reservado = stock_reservado - ? WHERE id_producto = ?";
                $stmt2 = $this->db->prepare($sql2);
                $stmt2->execute([$orderProduct['cantidad_producto'], $orderProduct['id_producto']]);

                $sql3 = "UPDATE productos_en_almacen SET stock_disponible = stock_disponible - ? WHERE id_producto = ?";
                $stmt3 = $this->db->prepare($sql3);
                $stmt3->execute([$orderProduct['cantidad_producto'], $orderProduct['id_producto']]);
            }

            // Actualizar el estado del pedido
            $sql4 = "UPDATE pedido SET estado = 2 WHERE id_pedido = ?";
            $stmt4 = $this->db->prepare($sql4);
            $stmt4->execute([$orderId]);

            $this->db->commit();
            $this->db->autocommit(true);
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            $this->db->autocommit(true);
            throw $e;
        }
    }
}