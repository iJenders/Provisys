
<?php
include_once 'models/PaymentsModel.php';

class PaymentsController
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

        $model = new PaymentsModel();

        try {
            $payments = $model->corePoweredGetAll($filters, $search, ($offset - 1) * 10);
            $count = $model->corePoweredCount($filters, $search);
        } catch (Exception $e) {
            Responses::json(['errors' => $e->getMessage()], 500);
        }
        Responses::json(['payments' => $payments, 'count' => $count], 200);
    }
}
