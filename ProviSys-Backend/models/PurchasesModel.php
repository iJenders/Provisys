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
        // Obtener las compras en crudo
        $purchases = $this->corePoweredGetAll($filters, $search, $offset);

        // Hidratar las compras con detalles de los proveedores y productos
        $hydratedPurchases = $purchases;
        $providersModel = new ProvidersModel();
        $paymentsModel = new PaymentsModel();
        foreach ($hydratedPurchases as $key => $purchase) {
            // Proveedor
            $purchase['provider'] = $providersModel->corePoweredGetById($purchase['provider_id']);
            unset($purchase['provider_id']);

            // Productos
            $purchase['products'] = $this->getPurchaseProducts($purchase['id']);

            // Pago pendiente
            $purchase['pendingPayment'] = $paymentsModel->corePoweredGetAll(['purchaseId' => $purchase['id']], [], 0);
            if (count($purchase['pendingPayment']) <= 0) {
                $totalMount = 0;
                foreach ($purchase['products'] as $product) {
                    $totalMount += $product['quantity'] * $product['price'] + ($product['quantity'] * $product['price'] * $product['iva'] / 100);
                }
                $insertId = $this->createPurchasePayment($purchase['id'], $purchase['date'], $totalMount);
                $purchase['pendingPayment'] = $insertId;
            } else {
                $purchase['pendingPayment'] = $paymentsModel->corePoweredGetAll(['purchaseId' => $purchase['id']], [], 0)[0]['id'];
            }

            $purchase['pendingPayment'] = $paymentsModel->isVerified($purchase['pendingPayment']);

            $hydratedPurchases[$key] = $purchase;
        }

        return $hydratedPurchases;
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
                $sql1 = "INSERT INTO detalles_compra (id_compra, id_producto, cantidad_producto, precio_de_compra, iva_de_compra) VALUES ";
                $args1 = [];

                $compraID = $this->db->insert_id;

                foreach ($data['products'] as $key => $product) {
                    $sql1 .= "(?, ?, ?, ?, ?),";
                    $args1[] = $compraID;
                    $args1[] = $product['id'];
                    $args1[] = $product['quantity'];
                    $args1[] = $product['unitPrice'];
                    $args1[] = $product['iva'];
                }
                $sql1 = substr($sql1, 0, -1);

                // Preparar consulta
                $stmt1 = $this->db->prepare($sql1);
                $stmt1->execute($args1);

                // Verificar exito
                if ($stmt1->affected_rows > 0) {
                    $totalAmount = 0;
                    foreach ($data['products'] as $product) {
                        $totalAmount += $product['quantity'] * $product['unitPrice'] + ($product['quantity'] * $product['unitPrice'] * $product['iva'] / 100);
                    }
                    $this->createPurchasePayment($compraID, $data['date'], $totalAmount);

                    // Confirmar transacción
                    $this->db->commit();
                    $this->db->autocommit(true);
                    return true;
                } else {
                    // Revertir transacción
                    $this->db->rollback();
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
}