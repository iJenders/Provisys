<?php
include_once 'models/IVAsModel.php';

class IVAsController
{
    public static function getAll()
    {
        // Obtener campos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        $filters = $data['filters'];
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

        $model = new IVAsModel();

        try {
            $IVAs = $model->corePoweredGetAll($filters, $search, ($offset - 1) * 10);
            $count = $model->corePoweredCount($filters, $search);
        } catch (Exception $e) {
            Responses::json(['errors' => $e->getMessage()], 500);
        }
        Responses::json(['IVAs' => $IVAs, 'count' => $count], 200);
    }

    public static function createIVA()
    {
        // Obtener datos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Verificar que los campos requeridos estén presentes
        Validator::ensureFields($data, ['name', 'value']);

        // Validar name
        $nombreValidator = new Validator($data['name'], 'name');
        $nombreValidation = $nombreValidator->required()
            ->minLength(3)
            ->maxLength(45)
            ->alphaNumericWithSecureSpecialChars();

        if ($nombreValidation->getErrors()) {
            Responses::json(['errors' => $nombreValidation->getErrors()], 400);
        }

        // Validar iva (porcentaje de impuesto)
        $ivaValidator = new Validator($data['value'], 'value');
        $ivaValidation = $ivaValidator->required()->numeric();

        if ($ivaValidation->getErrors()) {
            Responses::json(['errors' => $ivaValidation->getErrors()], 400);
        }

        // Validar que el valor de iva sea un número decimal válido
        if (!is_numeric($data['value'])) {
            Responses::json(['errors' => ['El campo de iva debe ser un valor numérico']], 400);
        }

        // Convertir a float para validaciones adicionales
        $ivaValue = floatval($data['value']);

        // Validar rango y formato del valor de IVA
        if ($ivaValue < 0 || $ivaValue > 100) {
            Responses::json(['errors' => ['El campo de iva debe estar entre 0 y 100']], 400);
        }

        // Formatear el valor de IVA a decimal(12,2)
        $formattedIva = number_format($ivaValue, 2, '.', '');

        // Preparar los datos para la inserción
        $ivaData = [
            'name' => $data['name'],
            'value' => $formattedIva,
            'deleted' => 0 // Por defecto, no está eliminado
        ];

        // Crear el modelo y guardar los datos
        $model = new IVAsModel();

        try {
            // Usar el método corePoweredCreate para insertar el nuevo registro
            $newIvaId = $model->corePoweredCreate($ivaData);

            // Obtener el registro recién creado para devolverlo en la respuesta
            $newIva = $model->corePoweredGetById([$newIvaId]);

            Responses::json(['message' => 'IVA creado exitosamente', 'iva' => $newIva], 201);
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }
    }

    public static function deleteIVA()
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

        $model = new IVAsModel();

        try {
            // Verificar si el IVA existe
            $iva = $model->exists($data['id']);
            if (!$iva) {
                Responses::json(['errors' => ['El IVA no existe']], 404);
            }

            // Realizar el borrado lógico
            $model->corePoweredDelete($data['id']);

            Responses::json(['message' => 'IVA eliminado exitosamente'], 200);
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }
    }

    public static function updateIVA()
    {
        // Obtener datos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Verificar que los campos requeridos estén presentes
        Validator::ensureFields($data, ['id', 'name', 'value']);

        // Validar ID
        $idValidator = new Validator($data['id'], 'id');
        $idValidation = $idValidator->required()->numeric()->minValue(1);

        if ($idValidation->getErrors()) {
            Responses::json(['errors' => $idValidation->getErrors()], 400);
        }

        // Validar name
        $nombreValidator = new Validator($data['name'], 'name');
        $nombreValidation = $nombreValidator->required()
            ->minLength(3)
            ->maxLength(45)
            ->alphaNumericWithSecureSpecialChars();

        if ($nombreValidation->getErrors()) {
            Responses::json(['errors' => $nombreValidation->getErrors()], 400);
        }

        // Validar iva (porcentaje de impuesto)
        $ivaValidator = new Validator($data['value'], 'value');
        $ivaValidation = $ivaValidator->required()->numeric();

        if ($ivaValidation->getErrors()) {
            Responses::json(['errors' => $ivaValidation->getErrors()], 400);
        }

        // Validar que el valor de iva sea un número decimal válido
        if (!is_numeric($data['value'])) {
            Responses::json(['errors' => ['El campo de iva debe ser un valor numérico']], 400);
        }

        // Convertir a float para validaciones adicionales
        $ivaValue = floatval($data['value']);

        // Validar rango y formato del valor de IVA
        if ($ivaValue < 0 || $ivaValue > 100) {
            Responses::json(['errors' => ['El campo de iva debe estar entre 0 y 100']], 400);
        }

        // Formatear el valor de IVA a decimal(12,2)
        $formattedIva = number_format($ivaValue, 2, '.', '');

        $model = new IVAsModel();

        try {
            // Verificar si el IVA existe
            $iva = $model->exists($data['id']);
            if (!$iva) {
                Responses::json(['errors' => ['El IVA no existe']], 404);
            }

            // Preparar los datos para la actualización
            $ivaData = [
                'name' => $data['name'],
                'value' => $formattedIva
            ];

            // Realizar la actualización
            $model->corePoweredUpdate($data['id'], $ivaData);

            // Obtener el registro actualizado para devolverlo en la respuesta
            $updatedIva = $model->corePoweredGetById($data['id']);

            Responses::json(['message' => 'IVA actualizado exitosamente', 'iva' => $updatedIva], 200);
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }
    }

}
