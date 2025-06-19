<?php
include_once 'models/OrdersModel.php';

class OrdersController
{
    public static function getAll()
    {
        $filters = [];
        $search = '';
        $offset = 0;

        // Obtener campos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['filters'])) {
            $filters = $data['filters'];
        }

        $model = new OrdersModel();

        $orders = $model->getAll($filters, $search, $offset);

        Responses::json(
            [
                'orders' => $orders
            ],
            200
        );
    }

    public static function getUserOrders()
    {
        $filters = [];
        $search = '';
        $offset = 0;

        $model = new OrdersModel();

        $userOrders = $model->getUserOrders($filters, $search, $offset);

        Responses::json(
            [
                'orders' => $userOrders
            ],
            200
        );
    }

    public static function getProducts()
    {
        // Obtener campos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['orderId'])) {
            Responses::json(
                ['error' => 'Falta el ID del pedido'],
                400
            );
        }

        $orderId = $data['orderId'];
        $orderIdValidator = new Validator($data['orderId'], 'orderId');
        $orderIdValidator->required()->numeric()->minValue(1);
        if (!empty($orderIdValidator->getErrors())) {
            Responses::json(
                ['error' => $orderIdValidator->getErrors()],
                400
            );
        }

        // Consulta a la base de datos

        $model = new OrdersModel();

        try {
            $products = $model->getProducts($orderId);
            $payments = $model->getPayments($orderId);

            Responses::json(
                [
                    'products' => $products,
                    'payments' => $payments
                ],
                200
            );
        } catch (Exception $e) {
            Responses::json(
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    public static function createOrder()
    {
        // Obtener campos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar que esté el campo 'products', un array asociativo con los productos y sus cantidades
        if (!isset($data['products'])) {
            Responses::json(
                ['error' => 'Falta el campo "products" en la solicitud'],
                400
            );
        }

        $products = $data['products'];

        // Validar que los productos sean un array asociativo con los productos y sus cantidades
        foreach ($products as $product) {
            if (!isset($product['id']) || !isset($product['quantity'])) {
                Responses::json(
                    ['error' => 'El campo "products" debe ser un array asociativo con los productos y sus cantidades'],
                    400
                );
            }

            // Validar que el producto sea un array asociativo con los productos y sus cantidades
            $id = $product['id'];
            $quantity = $product['quantity'];

            $idValidator = new Validator($id, 'id');
            $quantityValidator = new Validator($quantity, 'quantity');

            $idValidator->required()->numeric()->minValue(1);
            $quantityValidator->required()->numeric()->minValue(1);

            $errors = array_merge($idValidator->getErrors(), $quantityValidator->getErrors());

            if (!empty($errors)) {
                Responses::json(
                    ['error' => $errors],
                    400
                );
            }

            // Validar que el producto exista
            $productModel = new ProductsModel();
            $product = $productModel->corePoweredGetById($id);
            if (!$product) {
                Responses::json(
                    ['error' => 'El producto no existe'],
                    400
                );
            }

            // Validar que haya suficiente stock del producto
            if (!$productModel->enoughStock($id, $quantity)) {
                Responses::json(
                    ['error' => 'No hay suficiente stock del producto', 'product' => $id, 'quantity' => $quantity],
                    400
                );
            }
        }

        $model = new OrdersModel();
        try {
            $orderId = $model->createOrder($products);
        } catch (Exception $e) {
            Responses::json(
                ['error' => $e->getMessage()],
                500
            );
        }

        Responses::json(
            [
                'orderId' => $orderId
            ],
            200
        );
    }

    public static function cancelOrder()
    {
        // Obtener campos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['orderId'])) {
            Responses::json(
                ['error' => 'Falta el ID del pedido'],
                400
            );
        }

        $orderId = $data['orderId'];
        $orderIdValidator = new Validator($data['orderId'], 'orderId');
        $orderIdValidator->required()->numeric()->minValue(1);
        if (!empty($orderIdValidator->getErrors())) {
            Responses::json(
                ['error' => $orderIdValidator->getErrors()],
                400
            );
        }

        $model = new OrdersModel();

        // Verificar si la orden existe
        $order = $model->corePoweredGetById($orderId);
        if (!$order) {
            Responses::json(
                ['error' => 'La orden no existe'],
                404
            );
        }

        // Verificar si la orden ya está cancelada
        if ($order['status'] == '3') {
            Responses::json(
                ['error' => 'La orden ya está cancelada'],
                400
            );
        }

        // Cancelar la orden
        $canceled = $model->cancelOrder($orderId);

        if ($canceled) {
            Responses::json(
                ['message' => 'La orden ha sido cancelada'],
                200
            );
        } else {
            Responses::json(
                ['error' => 'No se pudo cancelar la orden'],
                500
            );
        }
    }

    public static function registerPayment()
    {
        // Obtener campos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['orderId'])) {
            Responses::json(
                ['error' => 'Falta el ID del pedido'],
                400
            );
        }
        if (!isset($data['paymentMethod'])) {
            Responses::json(
                ['error' => 'Falta el método de pago'],
                400
            );
        }
        if (!isset($data['paymentAmount'])) {
            Responses::json(
                ['error' => 'Falta el monto del pago'],
                400
            );
        }
        if (!isset($data['paymentDate'])) {
            Responses::json(
                ['error' => 'Falta la fecha del pago'],
                400
            );
        }
        if (!isset($data['paymentReference'])) {
            Responses::json(
                ['error' => 'Falta la referencia del pago'],
                400
            );
        }

        $orderId = $data['orderId'];
        $orderIdValidator = new Validator($data['orderId'], 'orderId');
        $orderIdValidator->required()->numeric()->minValue(1);
        if (!empty($orderIdValidator->getErrors())) {
            Responses::json(
                ['errors' => [$orderIdValidator->getErrors()]],
                400
            );
        }

        $paymentMethod = $data['paymentMethod'];
        $paymentMethodValidator = new Validator($data['paymentMethod'], 'paymentMethod');
        $paymentMethodValidator->required()->numeric()->minValue(1);
        if (!empty($paymentMethodValidator->getErrors())) {
            Responses::json(
                ['errors' => [$paymentMethodValidator->getErrors()]],
                400
            );
        }

        $paymentAmount = $data['paymentAmount'];
        $paymentAmountValidator = new Validator($data['paymentAmount'], 'paymentAmount');
        $paymentAmountValidator->required()->numeric()->minValue(1);
        if (!empty($paymentAmountValidator->getErrors())) {
            Responses::json(
                ['errors' => [$paymentAmountValidator->getErrors()]],
                400
            );
        }

        $paymentDate = $data['paymentDate'];
        $paymentDateValidator = new Validator($data['paymentDate'], 'paymentDate');
        $paymentDateValidator->required()->date();
        if (!empty($paymentDateValidator->getErrors())) {
            Responses::json(
                ['errors' => [$paymentDateValidator->getErrors()]],
                400
            );
        }

        $paymentReference = $data['paymentReference'];
        $paymentReferenceValidator = new Validator($data['paymentReference'], 'paymentReference');
        $paymentReferenceValidator->required()->alphaNumericWithSecureSpecialChars();
        if (!empty($paymentReferenceValidator->getErrors())) {
            Responses::json(
                ['errors' => [$paymentReferenceValidator->getErrors()]],
                400
            );
        }

        // Validar que el monto del pago sea igual al total de la orden
        $model = new OrdersModel();
        if ($model->mountExcededPayment($orderId, $paymentAmount)) {
            Responses::json(
                ["errors" => ['El monto del pago excede el total de la orden']],
                400
            );
        }

        // Si todos los validadores pasan, registrar el pago
        try {
            $result = $model->registerOrderPayment(
                $orderId,
                $paymentAmount,
                $paymentMethod,
                $paymentDate,
                $paymentReference
            );
        } catch (Exception $e) {
            Responses::json(
                ['errors' => [$e->getMessage()]],
                500
            );
        }

        if ($result) {
            Responses::json(
                ['message' => 'Pago registrado con éxito'],
                200
            );
        } else {
            Responses::json(
                ['error' => 'No se pudo registrar el pago'],
                500
            );
        }
    }

    public static function billOrder()
    {
        // Obtener campos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['orderId'])) {
            Responses::json(
                ['error' => 'Falta el ID del pedido'],
                400
            );
        }

        $orderId = $data['orderId'];
        $orderIdValidator = new Validator($data['orderId'], 'orderId');
        $orderIdValidator->required()->numeric()->minValue(1);
        if (!empty($orderIdValidator->getErrors())) {
            Responses::json(
                ['errors' => [$orderIdValidator->getErrors()]],
                400
            );
        }

        // Validar si el pedido existe
        $model = new OrdersModel();
        $order = $model->corePoweredGetById($orderId);
        if (!$order) {
            Responses::json(
                ['error' => 'El pedido no existe'],
                404
            );
        }

        // Validar que el pedido esté estrictamente en espera (estado 0)
        if ($order['status'] != 0) {
            Responses::json(
                ['error' => 'El pedido no está en estado de espera'],
                400
            );
        }

        try {
            $result = $model->billOrder($orderId);
        } catch (Exception $e) {
            Responses::json(
                ['errors' => [$e->getMessage()]],
                500
            );
        }

        if ($result) {
            Responses::json(
                ['message' => 'Pedido facturado con éxito'],
                200
            );
        } else {
            Responses::json(
                ['error' => 'No se pudo facturar el pedido'],
                500
            );
        }

    }

    public static function deliverOrder()
    {
        // Obtener campos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['orderId'])) {
            Responses::json(
                ['error' => 'Falta el ID del pedido'],
                400
            );
        }

        $orderId = $data['orderId'];
        $orderIdValidator = new Validator($data['orderId'], 'orderId');
        $orderIdValidator->required()->numeric()->minValue(1);
        if (!empty($orderIdValidator->getErrors())) {
            Responses::json(
                ['errors' => [$orderIdValidator->getErrors()]],
                400
            );
        }

        // Validar si el pedido existe
        $model = new OrdersModel();
        $order = $model->corePoweredGetById($orderId);
        if (!$order) {
            Responses::json(
                ['error' => 'El pedido no existe'],
                404
            );
        }

        // Validar que el pedido esté estrictamente facturado (estado 1)
        if ($order['status'] != 1) {
            Responses::json(
                ['error' => 'El pedido no está facturado'],
                400
            );
        }

        try {
            $result = $model->deliverOrder($orderId);
        } catch (Exception $e) {
            Responses::json(
                ['errors' => [$e->getMessage()]],
                500
            );
        }

        if ($result) {
            Responses::json(
                ['message' => 'Pedido entregado con éxito'],
                200
            );
        } else {
            Responses::json(
                ['error' => 'No se pudo entregar el pedido'],
                500
            );
        }

    }
}