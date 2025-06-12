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
        // Desactivar el commit automático
        $this->db->autocommit(false);

        // Comenzar la transacción
        $this->db->begin_transaction();

        $transactionSuccessful = true; // Esto se utilizará para lanzar un error más adelante si algo sale mal

        try {
            $sql1 = "SELECT * FROM cuota WHERE id_pago = ?";

            // Preparar y ejecutar la consulta
            $stmt1 = $this->db->prepare($sql1);

            $stmt1->execute([$id]);

            // Obtener resultados
            $results1 = $stmt1->get_result();

            $confirmedPayment = 0.00;
            // Recorrer todas las cuotas, verificando si todas están verificadas
            while ($row = $results1->fetch_assoc()) {
                if ($row['verificado'] != '1') {
                    return false;
                } else {
                    $confirmedPayment += $row['monto'];
                }
            }

            // Comprobar que el monto total de las cuotas cubra el monto total del pago
            $sql2 = "SELECT * FROM pago WHERE id_pago = ?";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute([$id]);
            $results2 = $stmt2->get_result();
            $paymentMount = $results2->fetch_assoc()['monto_total'];

            if ($confirmedPayment != $paymentMount) {
                return false;
            }

            // Correcto, confirmar la transacción
            $this->db->commit();
        } catch (Exception $e) {
            // Error, revertir la transacción
            $this->db->rollback();
            $transactionSuccessful = false;
        }

        // Reactivar el commit automático
        $this->db->autocommit(true);

        // Lanzar un error si la transacción no se confirmó correctamente
        if (!$transactionSuccessful) {
            throw new Exception("Error en la transacción");
        }

        return true;
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
