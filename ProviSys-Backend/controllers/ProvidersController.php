<?php
include_once 'models/ProvidersModel.php';

class ProvidersController
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

        $model = new ProvidersModel();

        try {
            $providers = $model->corePoweredGetAll($filters, $search, ($offset - 1) * 10);
            $count = $model->corePoweredCount($filters, $search);
        } catch (Exception $e) {
            Responses::json(['errors' => $e->getMessage()], 500);
        }
        Responses::json(['providers' => $providers, 'count' => $count], 200);
    }

    public static function createProvider()
    {
        // Obtener datos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Verificar que los campos requeridos estén presentes
        Validator::ensureFields($data, ['id', 'name', 'email', 'phone', 'address']);

        // Validar id
        $idValidator = new Validator($data['id'], 'id');
        $idValidation = $idValidator->required()->CiOrRif();
        if ($idValidation->getErrors()) {
            Responses::json(['errors' => $idValidation->getErrors()], 400);
        }

        // Validar name
        $nameValidator = new Validator($data['name'], 'name');
        $nameValidation = $nameValidator->required()
            ->minLength(3)
            ->maxLength(100)
            ->alphaNumericWithSecureSpecialChars();

        if ($nameValidation->getErrors()) {
            Responses::json(['errors' => $nameValidation->getErrors()], 400);
        }

        // Validar email
        $emailValidator = new Validator($data['email'], 'email');
        $emailValidation = $emailValidator->required()
            ->email()
            ->maxLength(100);

        if ($emailValidation->getErrors()) {
            Responses::json(['errors' => $emailValidation->getErrors()], 400);
        }

        // Validar phone
        $phoneValidator = new Validator($data['phone'], 'phone');
        $phoneValidation = $phoneValidator->required()
            ->phone()
            ->maxLength(20);

        if ($phoneValidation->getErrors()) {
            Responses::json(['errors' => $phoneValidation->getErrors()], 400);
        }

        // Validar address
        $addressValidator = new Validator($data['address'], 'address');
        $addressValidation = $addressValidator->required()
            ->maxLength(255)
            ->alphaNumericWithSecureSpecialChars();

        if ($addressValidation->getErrors()) {
            Responses::json(['errors' => $addressValidation->getErrors()], 400);
        }

        // Preparar los datos para la inserción
        $providerData = [
            'id' => $data['id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'disabled' => 0
        ];

        // Crear el modelo y guardar los datos
        $model = new ProvidersModel();

        try {
            // Usar el método corePoweredCreate para insertar el nuevo registro
            $newProviderId = $model->corePoweredCreate($providerData);

            // Obtener el registro recién creado para devolverlo en la respuesta
            $newProvider = $model->corePoweredGetById($newProviderId);

            Responses::json(['message' => 'Proveedor creado exitosamente', 'provider' => $newProvider], 201);
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }
    }

    public static function deleteProvider()
    {
        // Obtener datos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Verificar que el ID esté presente
        Validator::ensureFields($data, ['id']);

        // Validar ID
        $idValidator = new Validator($data['id'], 'id');
        $idValidation = $idValidator->required()->CiOrRif();

        if ($idValidation->getErrors()) {
            Responses::json(['errors' => $idValidation->getErrors()], 400);
        }

        $model = new ProvidersModel();

        try {
            // Verificar si el proveedor existe
            $provider = $model->exists($data['id']);
            if (!$provider) {
                Responses::json(['errors' => ['El proveedor no existe']], 404);
            }

            // Realizar el borrado lógico
            $model->corePoweredDelete($data['id']);

            Responses::json(['message' => 'Proveedor eliminado exitosamente'], 200);
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }
    }

    public static function updateProvider()
    {
        // Obtener datos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Verificar que los campos requeridos estén presentes
        Validator::ensureFields($data, ['id', 'name', 'email', 'phone', 'address']);

        // Validar ID
        $idValidator = new Validator($data['id'], 'id');
        $idValidation = $idValidator->required()->CiOrRif();

        if ($idValidation->getErrors()) {
            Responses::json(['errors' => $idValidation->getErrors()], 400);
        }

        // Validar name
        $nameValidator = new Validator($data['name'], 'name');
        $nameValidation = $nameValidator->required()
            ->minLength(3)
            ->maxLength(100)
            ->alphaNumericWithSecureSpecialChars();

        if ($nameValidation->getErrors()) {
            Responses::json(['errors' => $nameValidation->getErrors()], 400);
        }

        // Validar email
        $emailValidator = new Validator($data['email'], 'email');
        $emailValidation = $emailValidator->required()
            ->email()
            ->maxLength(100);

        if ($emailValidation->getErrors()) {
            Responses::json(['errors' => $emailValidation->getErrors()], 400);
        }

        // Validar phone
        $phoneValidator = new Validator($data['phone'], 'phone');
        $phoneValidation = $phoneValidator->required()
            ->phone()
            ->maxLength(20);

        if ($phoneValidation->getErrors()) {
            Responses::json(['errors' => $phoneValidation->getErrors()], 400);
        }

        // Validar address
        $addressValidator = new Validator($data['address'], 'address');
        $addressValidation = $addressValidator->required()
            ->maxLength(255)
            ->alphaNumericWithSecureSpecialChars();

        if ($addressValidation->getErrors()) {
            Responses::json(['errors' => $addressValidation->getErrors()], 400);
        }

        $model = new ProvidersModel();

        try {
            // Verificar si el proveedor existe
            $provider = $model->exists($data['id']);
            if (!$provider) {
                Responses::json(['errors' => ['El proveedor no existe']], 404);
            }

            // Preparar los datos para la actualización
            $providerData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address']
            ];

            // Realizar la actualización
            $model->corePoweredUpdate($data['id'], $providerData);

            // Obtener el registro actualizado
            $updatedProvider = $model->corePoweredGetById($data['id']);

            Responses::json(['message' => 'Proveedor actualizado exitosamente', 'provider' => $updatedProvider], 200);
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }
    }
}
