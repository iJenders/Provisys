<?php

include_once 'models/PurchasesModel.php';
include_once 'models/ProductsModel.php';
include_once 'models/ProvidersModel.php';
include_once 'models/StoragesModel.php';

class PurchasesController
{
    public static function getAll()
    {
        // Obtener los datos de la consulta
        $data = json_decode(file_get_contents('php://input'), true);
        $filters = $data['filters'] ?? [];
        $offset = $data['offset'] ?? 0;
        $search = $data['search'] ?? '';

        // Validar offset si se envió
        if (isset($data['offset'])) {
            $offsetValidator = new Validator($offset);
            $offsetValidation = $offsetValidator->required()->numeric()->minValue(1);

            if (count($offsetValidation->getErrors()) > 0) {
                Responses::json(["errors" => $offsetValidation->getErrors()], 400);
            }
        }

        // Validar search
        if (isset($data['search'])) {
            $search = $data['search'];
            $searchValidator = new Validator($search, 'search');
            if (!empty($search)) {
                $searchValidator->maxLength(255)->alphaNumericWithSecureSpecialChars();
            }
            $errors = $searchValidator->getErrors();
            if (!empty($errors)) {
                Responses::json(['errors' => $errors], 400);
            }
        }

        $model = new PurchasesModel();
        try {
            Responses::json(
                $model->getAll($filters, $search, $offset)
            );
        } catch (Exception $e) {
            Responses::json($e->getMessage(), 500);
        }
    }

    public static function getPurchaseDetails()
    {
        // Obtener datos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar id
        if (!isset($data['id'])) {
            Responses::json(['errors' => ['id' => 'El campo id es requerido']], 400);
        }
        $id = $data['id'];
        $idValidator = new Validator($id, 'id');
        $idValidation = $idValidator->required()->numeric()->minValue(1);

        if ($idValidation->getErrors()) {
            Responses::json(['errors' => $idValidation->getErrors()], 400);
        }

        $model = new PurchasesModel();
        if (!$model->exists($id)) {
            Responses::json(['errors' => ['id' => 'La compra no existe']], 400);
        }

        // Obtener los detalles de la compra
        $purchaseDetails = $model->getPurchaseDetails($id);
        if (!$purchaseDetails) {
            Responses::json(['errors' => ['id' => 'La compra no existe']], 400);
        }

        Responses::json(
            ['purchase' => $purchaseDetails],
            200
        );
    }

    public static function createPurchase()
    {
        // Obtener datos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Verificar que los campos requeridos estén presentes
        Validator::ensureFields($data, ['date', 'providerId', 'products']);

        // Validar date
        $dateValidator = new Validator($data['date'], 'date');
        $dateValidation = $dateValidator->required()->date();

        if ($dateValidation->getErrors()) {
            Responses::json(['errors' => $dateValidation->getErrors()], 400);
        }

        // Validar providerId
        $providerValidator = new Validator($data['providerId'], 'providerId');
        $providerValidation = $providerValidator->required()->CiOrRif();

        if ($providerValidation->getErrors()) {
            Responses::json(['errors' => $providerValidation->getErrors()], 400);
        }

        // Validar products array
        if (!is_array($data['products']) || empty($data['products'])) {
            Responses::json(['errors' => ['products' => 'Debe proporcionar al menos un producto']], 400);
        }

        // Validar cada producto en el array
        foreach ($data['products'] as $index => $product) {
            Validator::ensureFields($product, ['id', 'quantity', 'unitPrice', 'iva']);

            // Validar id del producto
            $productIdValidator = new Validator($product['id'], "products[$index].id");
            $productIdValidation = $productIdValidator->required()->numeric()->minValue(1);

            if ($productIdValidation->getErrors()) {
                Responses::json(['errors' => $productIdValidation->getErrors()], 400);
            }

            // Validar cantidad
            $quantityValidator = new Validator($product['quantity'], "products[$index].quantity");
            $quantityValidation = $quantityValidator->required()->numeric()->minValue(1);

            if ($quantityValidation->getErrors()) {
                Responses::json(['errors' => $quantityValidation->getErrors()], 400);
            }

            // Validar precio unitario
            $priceValidator = new Validator($product['unitPrice'], "products[$index].unitPrice");
            $priceValidation = $priceValidator->required()->numeric()->minValue(0.01);

            if ($priceValidation->getErrors()) {
                Responses::json(['errors' => $priceValidation->getErrors()], 400);
            }

            // Validar IVA
            $ivaValidator = new Validator($product['iva'], "products[$index].iva");
            $ivaValidation = $ivaValidator->required()->numeric()->minValue(0)->maxValue(100);

            if ($ivaValidation->getErrors()) {
                Responses::json(['errors' => $ivaValidation->getErrors()], 400);
            }

            // Validar warehouseId
            $warehouseIdValidator = new Validator($product['warehouseId'], "products[$index].warehouseId");
            $warehouseIdValidation = $warehouseIdValidator->required()->numeric()->minValue(1);
            if ($warehouseIdValidation->getErrors()) {
                Responses::json(['errors' => $warehouseIdValidation->getErrors()], 400);
            }

            // Validar que el producto exista
            $productModel = new ProductsModel();
            $productExists = $productModel->exists($product['id']);
            if (!$productExists) {
                Responses::json(['errors' => ['products' => 'El producto no existe']], 400);
            }

            // Validar que el proveedor exista
            $providerModel = new ProvidersModel();
            $providerExists = $providerModel->exists($data['providerId']);
            if (!$providerExists) {
                Responses::json(['errors' => ['providerId' => 'El proveedor no existe']], 400);
            }

            // Validar que el almacen exista
            $warehouseModel = new StoragesModel();
            $warehouseExists = $warehouseModel->exists($product['warehouseId']);
            if (!$warehouseExists) {
                Responses::json(['errors' => ['warehouseId' => 'El almacen no existe']], 400);
            }
        }

        // Crear el modelo y guardar los datos
        $model = new PurchasesModel();

        try {
            // Intentar crear la compra
            $result = $model->create($data);

            if ($result) {
                Responses::json(['message' => 'Compra creada exitosamente'], 201);
            } else {
                Responses::json(['errors' => ['No se pudo crear la compra']], 500);
            }
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }
    }
    public static function deletePurchase()
    {
        // Obtener datos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar id
        if (!isset($data['id'])) {
            Responses::json(['errors' => ['id' => 'El campo id es requerido']], 400);
        }

        $id = $data['id'];
        $idValidator = new Validator($id, 'id');
        $idValidation = $idValidator->required()->numeric()->minValue(1);
        if ($idValidation->getErrors()) {
            Responses::json(['errors' => $idValidation->getErrors()], 400);
        }

        $model = new PurchasesModel();

        if (!$model->exists($id)) {
            Responses::json(['errors' => ['id' => 'La compra no existe']], 400);
        }

        try {
            if ($model->delete($id)) {
                Responses::json(['message' => 'Compra eliminada exitosamente'], 200);
            } else {
                Responses::json(['errors' => ['No se pudo eliminar la compra']], 500);
            }
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }
    }
    public static function addPayment()
    {
        // Obtener datos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar campos requeridos
        Validator::ensureFields($data, ['purchaseId', 'amount', 'date', 'methodId', 'reference']);

        // Validar purchaseId
        $purchaseIdValidator = new Validator($data['purchaseId'], 'purchaseId');
        $purchaseIdValidation = $purchaseIdValidator->required()->numeric()->minValue(1);
        if ($purchaseIdValidation->getErrors()) {
            Responses::json(['errors' => $purchaseIdValidation->getErrors()], 400);
        }

        // Validar amount
        $amountValidator = new Validator($data['amount'], 'amount');
        $amountValidation = $amountValidator->required()->numeric()->minValue(0.01);
        if ($amountValidation->getErrors()) {
            Responses::json(['errors' => $amountValidation->getErrors()], 400);
        }

        // Validar date
        $dateValidator = new Validator($data['date'], 'date');
        $dateValidation = $dateValidator->required()->date();
        if ($dateValidation->getErrors()) {
            Responses::json(['errors' => $dateValidation->getErrors()], 400);
        }

        // Validar reference
        $referenceValidator = new Validator($data['reference'], 'reference');
        $referenceValidation = $referenceValidator->required()->alphaNumericWithSecureSpecialChars()->maxLength(255);
        if ($referenceValidation->getErrors()) {
            Responses::json(['errors' => $referenceValidation->getErrors()], 400);
        }

        // Validar methodId
        $methodIdValidator = new Validator($data['methodId'], 'methodId');
        $methodIdValidation = $methodIdValidator->required()->numeric()->minValue(1);
        if ($methodIdValidation->getErrors()) {
            Responses::json(['errors' => $methodIdValidation->getErrors()], 400);
        }

        $model = new PurchasesModel();

        try {
            $model->registerPayment(
                $data['purchaseId'],
                $data['amount'],
                $data['date'],
                $data['reference'],
                $data['methodId']
            );
            Responses::json(['message' => 'Pago registrado exitosamente'], 201);
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }

    }
}