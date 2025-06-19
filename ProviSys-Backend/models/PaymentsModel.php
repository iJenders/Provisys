<?php
include_once 'core/Model.php';
include_once 'models/PaymentMethodsModel.php';

class PaymentsModel extends Model
{
    protected string $table = 'pago';
    protected array $attributes = [
        'id' => 'id_pago',
        'date' => 'fecha_pago',
        'amount' => 'monto_total',
        'orderId' => 'id_pedido',
        'purchaseId' => 'id_compra',
    ];
    protected array $searchableAttributes = [
        'date',
        'amount',
        'orderId',
        'purchaseId'
    ];
    protected array $guarded = ['id'];
    protected string $primaryKey = 'id';

    // Métodos

    public function getAll($filters, $search, $offset)
    {
        $sql = "SELECT * FROM vista_obtener_pagos";

        $where = [];
        $params = [];

        if (!empty($search)) {
            $searchWhere = [];
            foreach ($this->searchableAttributes as $attribute) {
                $searchWhere[] = "{$this->attributes[$attribute]} LIKE ?";
                $params[] = "%$search%";
            }
            $where[] = "(" . implode(" OR ", $searchWhere) . ")";
        }

        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                if (isset($this->attributes[$key]) && $value !== '') {
                    $where[] = "{$this->attributes[$key]} = ?";
                    $params[] = $value;
                }
            }
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " LIMIT ?, 10";
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $results = $stmt->get_result();

        $payments = [];
        while ($row = $results->fetch_assoc()) {
            $payment = [
                'id' => $row['id_pago'],
                'date' => $row['fecha_pago'],
                'amount' => $row['monto_total'],
                'orderId' => $row['id_pedido'],
                'purchaseId' => $row['id_compra'],
                'totalPaid' => $row['total_pagado'],
                'verified' => $row['verificados'],
            ];
            $payments[] = $payment;
        }

        return $payments;
    }

    public function getPaymentDetails($id)
    {
        $sql = "SELECT * FROM vista_obtener_pagos WHERE id_pago = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $results = $stmt->get_result();
        $row = $results->fetch_assoc();

        $payment = [
            'id' => $row['id_pago'],
            'date' => $row['fecha_pago'],
            'orderId' => $row['id_pedido'],
            'orderCustomer' => '',
            'purchaseId' => $row['id_compra'],
            'totalAmount' => $row['monto_total'],
        ];

        // Obtener el nombre del usuario que hizo el pedido, si no es null
        if ($payment['orderId'] != null) {
            $sql2 = "SELECT usuario.nombre_usuario FROM usuario INNER JOIN pedido ON usuario.nombre_usuario = pedido.nombre_usuario WHERE pedido.id_pedido = ?";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute([$payment['orderId']]);

            $results2 = $stmt2->get_result();
            $row2 = $results2->fetch_assoc();
            $payment['orderCustomer'] = $row2['nombre_usuario'];
        }

        // Obtener las cuotas del pago
        $sql = "SELECT * FROM cuota INNER JOIN metodo_de_pago ON cuota.id_metodo = metodo_de_pago.id_metodo WHERE id_pago = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        $results = $stmt->get_result();
        $payment['installments'] = [];

        while ($row = $results->fetch_assoc()) {
            $installment = [
                'id' => $row['id_cuota'],
                'date' => $row['fecha_cuota'],
                'amount' => $row['monto'],
                'reference' => $row['nro_referencia'],
                'verified' => $row['verificado'],
                'paymentMethod' => $row['nombre_metodo'],
                'paymentMethodId' => $row['id_metodo'],
            ];
            $payment['installments'][] = $installment;
        }

        // Retornar el pago con los detalles
        return $payment;
    }

    public function verifyInstallment($id)
    {
        $sql = "UPDATE cuota SET verificado = NOT verificado WHERE id_cuota = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->affected_rows > 0;
    }

    public function deleteInstallment($id)
    {
        $sql = "DELETE FROM cuota WHERE id_cuota = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->affected_rows > 0;
    }

    public function createInstallment($paymentId, $amount, $date, $reference, $paymentMethodId)
    {
        $sql = "INSERT INTO cuota (id_pago, monto, fecha_cuota, nro_referencia, id_metodo) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$paymentId, $amount, $date, $reference, $paymentMethodId]);
        return $stmt->affected_rows > 0;
    }

    public function getPaymentHistory()
    {
        $sql = "SELECT * FROM cuota INNER JOIN metodo_de_pago ON cuota.id_metodo = metodo_de_pago.id_metodo INNER JOIN pago ON pago.id_pago=cuota.id_pago ORDER BY id_cuota DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $results = $stmt->get_result();

        $payments = [];
        while ($row = $results->fetch_assoc()) {
            $payment = [
                'id' => $row['id_cuota'],
                'date' => $row['fecha_cuota'],
                'amount' => $row['monto'],
                'reference' => $row['nro_referencia'],
                'verified' => $row['verificado'],
                'deleted' => $row['eliminado'],
                'paymentId' => $row['id_pago'],
                'paymentMethodId' => $row['id_metodo'],
                'paymentMethod' => $row['nombre_metodo'],
                'payment' => [
                    'id' => $row['id_pago'],
                    'date' => $row['fecha_pago'],
                    'amount' => $row['monto_total'],
                    'orderId' => $row['id_pedido'],
                    'purchaseId' => $row['id_compra'],
                ]
            ];
            $payments[] = $payment;
        }

        return $payments;
    }
}
