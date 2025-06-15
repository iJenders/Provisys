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
        'noteId' => 'id_nota',
        'orderId' => 'id_pedido',
        'purchaseId' => 'id_compra',
    ];
    protected array $searchableAttributes = [
        'date',
        'amount',
        'noteId',
        'orderId',
        'purchaseId'
    ];
    protected array $guarded = ['id'];
    protected string $primaryKey = 'id';

    // Métodos

    public function pendingMount($id)
    {
        // Desactivar el auto commit
        $this->db->autocommit(false);

        $this->db->begin_transaction();

        try {
            $sql = "SELECT * FROM pago WHERE id_pago = ?";

            // Preparar y ejecutar la consulta
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);

            // Obtener resultados
            $results = $stmt->get_result();
            $paymentMount = $results->fetch_assoc()['monto_total'];

            // Obtener las cuotas
            $sql = "SELECT * FROM cuota WHERE id_pago = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);

            $results = $stmt->get_result();
            $confirmedPayment = 0.00;
            while ($row = $results->fetch_assoc()) {
                if ($row['verificado'] == '1') {
                    $confirmedPayment += $row['monto'];
                }
            }

            // Calcular el monto pendiente
            $pendingPayment = $paymentMount - $confirmedPayment;
            $this->db->commit();

            // Reactivar el auto commit
            $this->db->autocommit(true);

            return $pendingPayment;
        } catch (Exception $e) {
            $this->db->rollback();
            // Reactivar el auto commit
            $this->db->autocommit(true);
        }
    }

    public function isVerified($id)
    {
        $sql = "SELECT * FROM vista_total_pagado WHERE id_pago = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $results = $stmt->get_result();

        $returnVal = [];

        $row = $results->fetch_assoc();
        $returnVal['monto_pagado'] = $row['monto_pagado'] == null ? 0.00 : floatval($row['monto_pagado']);
        $returnVal['monto_total'] = floatval($row['monto_total']);


        return $returnVal;
    }

    public function getFees($id)
    {
        $sql = "SELECT * FROM cuota WHERE id_pago = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $results = $stmt->get_result();

        $fees = [];

        $model = new PaymentMethodsModel();
        while ($row = $results->fetch_assoc()) {
            $fees[] = [
                'id' => $row['id_cuota'],
                'date' => $row['fecha_cuota'],
                'amount' => $row['monto'],
                'verified' => $row['verificado'],
                'paymentId' => $row['id_pago'],
                'methodId' => $model->corePoweredGetById($row['id_metodo']),
            ];
        }

        return $fees;
    }
}
