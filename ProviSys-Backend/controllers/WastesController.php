<?php
include_once 'models/WastesModel.php';

class WastesController
{
    public static function getAll()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $filters = $data['filters'];

        // Validar filtro deleted
        if ($filters['deleted'] != 0 && $filters['deleted'] != 1) {
            Responses::json(['errors' => ['deleted' => 'deleted debe ser 0 o 1']], 400);
        }

        // Validar filtro from y to
        $fromValidator = new Validator($filters['from'], 'from');
        $fromValidation = $fromValidator->date();

        $toValidator = new Validator($filters['to'], 'to');
        $toValidation = $toValidator->date();

        $errs = array_merge($fromValidation->getErrors(), $toValidation->getErrors());
        if ($errs) {
            Responses::json(['errors' => $errs], 400);
        }

        $model = new WastesModel();
        $wastes = $model->getAll($filters['deleted'], $filters['from'], $filters['to']);
        $count = $model->corePoweredCount([], '');

        Responses::json(
            [
                'wastes' => $wastes,
                'count' => $count
            ],
            200
        );
    }

    public static function createWaste()
    {
        // Obtener datos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Verificar que los campos requeridos estén presentes
        Validator::ensureFields($data, ['product_id', 'quantity', 'date', 'reason']);

        // Validar date
        $dateValidator = new Validator($data['date'], 'date');
        $dateValidation = $dateValidator->date();
        if ($dateValidation->getErrors()) {
            Responses::json(['errors' => $dateValidation->getErrors()], 400);
        }

        // Validar product_id
        $productIdValidator = new Validator($data['product_id'], 'product_id');
        $productIdValidation = $productIdValidator->numeric()->minValue(1);
        if ($productIdValidation->getErrors()) {
            Responses::json(['errors' => $productIdValidation->getErrors()], 400);
        }

        // Validar quantity
        $quantityValidator = new Validator($data['quantity'], 'quantity');
        $quantityValidation = $quantityValidator->numeric()->minValue(1);
        if ($quantityValidation->getErrors()) {
            Responses::json(['errors' => $quantityValidation->getErrors()], 400);
        }

        // Validar reason
        $reasonValidator = new Validator($data['reason'], 'reason');
        $reasonValidation = $reasonValidator->alphaNumericWithSecureSpecialChars()->maxLength(255);
        if ($reasonValidation->getErrors()) {
            Responses::json(['errors' => $reasonValidation->getErrors()], 400);
        }

        $model = new WastesModel();
        try {
            $model->createWaste($data['product_id'], $data['quantity'], $data['date'], $data['reason']);
        } catch (Exception $e) {
            Responses::json(['errors' => $e->getMessage()], 500);
        }
    }

    public static function deleteWaste()
    {
        // Obtener el ID del desperdicio a eliminar
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['id'])) {
            Responses::json(['errors' => ['El ID del desperdicio es requerido']], 400);
        }

        $id = $data['id'];
        $validator = new Validator($id, 'id');
        $validation = $validator->numeric()->minValue(1);
        if ($validation->getErrors()) {
            Responses::json(['errors' => $validation->getErrors()], 400);
        }

        $model = new WastesModel();

        // Verificar si el desperdicio ya existe
        if (!$model->exists($id)) {
            Responses::json(['errors' => ['El desperdicio no existe']], 404);
        }

        // Verificar si el desperdicio ya fue eliminado
        if ($model->isDeleted($id)) {
            Responses::json(['errors' => ['El desperdicio ya fue eliminado']], 400);
        }

        try {
            $model->deleteWaste($id);
        } catch (Exception $e) {
            Responses::json(['errors' => $e->getMessage()], 500);
        }

        Responses::json(['message' => 'Desperdicio eliminado correctamente'], 200);
    }
}
