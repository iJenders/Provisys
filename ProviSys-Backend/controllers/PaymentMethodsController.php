<?php
include_once 'models/PaymentMethodsModel.php';

class PaymentMethodsController
{
    public static function getAll()
    {
        // Obtener campos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        $filters = $data['filters'] ?? [];
        $search = $data['search'] ?? '';
        $offset = $data['offset'] ?? 1;

        // Validar los filtros, si se dan
        foreach ($filters as $key => $value) {
            $filterValidator = new Validator($value, $key);
            $filterValidation = $filterValidator->alphaNumericWithSecureSpecialChars()->maxLength(255);
            if ($filterValidation->getErrors()) {
                Responses::json(['errors' => $filterValidation->getErrors()], 400);
            }
        }

        // Validar búsqueda si se da
        if ($search != null || $search !== '') {
            $searchValidator = new Validator($search, 'search');
            $searchValidation = $searchValidator->alphaNumericWithSecureSpecialChars()->maxLength(255);
            if ($searchValidation->getErrors()) {
                Responses::json(['errors' => $searchValidation->getErrors()], 400);
            }
        }

        // Validar Offset si se da
        if ($offset != null || $offset !== '') {
            $offsetValidator = new Validator($offset, 'offset');
            $offsetValidation = $offsetValidator->numeric()->minValue(1);
            if ($offsetValidation->getErrors()) {
                Responses::json(['errors' => $offsetValidation->getErrors()], 400);
            }
        }

        $model = new PaymentMethodsModel();

        try {
            $paymentMethods = $model->corePoweredGetAll($filters, $search, ($offset - 1) * 10);
            $count = $model->corePoweredCount($filters, $search);
        } catch (Exception $e) {
            Responses::json(['errors' => $e->getMessage()], 500);
        }
        Responses::json(['paymentMethods' => $paymentMethods, 'count' => $count], 200);
    }

    public static function createPaymentMethod()
    {
        // Obtener datos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Verificar que los campos requeridos estén presentes
        Validator::ensureFields($data, ['name', 'description']);

        // Validar name
        $nameValidator = new Validator($data['name'], 'name');
        $nameValidation = $nameValidator->required()
            ->minLength(3)
            ->maxLength(45)
            ->alphaNumericWithSecureSpecialChars();

        if ($nameValidation->getErrors()) {
            Responses::json(['errors' => $nameValidation->getErrors()], 400);
        }

        // Validar description
        $descriptionValidator = new Validator($data['description'], 'description');
        $descriptionValidation = $descriptionValidator->required()
            ->maxLength(255)
            ->alphaNumericWithSecureSpecialChars();

        if ($descriptionValidation->getErrors()) {
            Responses::json(['errors' => $descriptionValidation->getErrors()], 400);
        }

        // Preparar los datos para la inserción
        $paymentMethodData = [
            'name' => $data['name'],
            'description' => $data['description'],
            'disabled' => 0
        ];

        // Crear el modelo y guardar los datos
        $model = new PaymentMethodsModel();

        try {
            // Usar el método corePoweredCreate para insertar el nuevo registro
            $newPaymentMethodId = $model->corePoweredCreate($paymentMethodData);

            // Obtener el registro recién creado para devolverlo en la respuesta
            $newPaymentMethod = $model->corePoweredGetById($newPaymentMethodId);

            Responses::json(['message' => 'Método de pago creado exitosamente', 'paymentMethod' => $newPaymentMethod], 201);
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }
    }

    public static function deletePaymentMethod()
    {
        // Obtener datos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Verificar que el ID esté presente
        Validator::ensureFields($data, ['id']);

        // Validar ID
        $idValidator = new Validator($data['id'], 'id');
        $idValidation = $idValidator->required()->numeric()->minValue(1);

        if ($idValidation->getErrors()) {
            Responses::json(['errors' => $idValidation->getErrors()], 400);
        }

        $model = new PaymentMethodsModel();

        try {
            // Verificar si el método de pago existe
            $paymentMethod = $model->exists($data['id']);
            if (!$paymentMethod) {
                Responses::json(['errors' => ['El método de pago no existe']], 404);
            }

            // Realizar el borrado lógico
            $model->corePoweredDelete($data['id']);

            Responses::json(['message' => 'Método de pago eliminado exitosamente'], 200);
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }
    }

    public static function updatePaymentMethod()
    {
        // Obtener datos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Verificar que los campos requeridos estén presentes
        Validator::ensureFields($data, ['id', 'name', 'description']);

        // Validar ID
        $idValidator = new Validator($data['id'], 'id');
        $idValidation = $idValidator->required()->numeric()->minValue(1);

        if ($idValidation->getErrors()) {
            Responses::json(['errors' => $idValidation->getErrors()], 400);
        }

        // Validar name
        $nameValidator = new Validator($data['name'], 'name');
        $nameValidation = $nameValidator->required()
            ->minLength(3)
            ->alphaNumericWithSecureSpecialChars();

        if ($nameValidation->getErrors()) {
            Responses::json(['errors' => $nameValidation->getErrors()], 400);
        }

        // Validar description
        $descriptionValidator = new Validator($data['description'], 'description');
        $descriptionValidation = $descriptionValidator->required()
            ->maxLength(255)
            ->alphaNumericWithSecureSpecialChars();

        if ($descriptionValidation->getErrors()) {
            Responses::json(['errors' => $descriptionValidation->getErrors()], 400);
        }

        $model = new PaymentMethodsModel();

        try {
            // Verificar si el método de pago existe
            $paymentMethod = $model->exists($data['id']);
            if (!$paymentMethod) {
                Responses::json(['errors' => ['El método de pago no existe']], 404);
            }

            // Preparar los datos para la actualización
            $paymentMethodData = [
                'name' => $data['name'],
                'description' => $data['description']
            ];

            // Realizar la actualización
            $model->corePoweredUpdate($data['id'], $paymentMethodData);

            // Obtener el registro actualizado
            $updatedPaymentMethod = $model->corePoweredGetById($data['id']);

            Responses::json(['message' => 'Método de pago actualizado exitosamente', 'paymentMethod' => $updatedPaymentMethod], 200);
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }
    }
}