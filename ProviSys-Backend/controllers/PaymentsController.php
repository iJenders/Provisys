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
            $payments = $model->getAll($filters, $search, ($offset - 1) * 10);
            $count = $model->corePoweredCount($filters, $search);
        } catch (Exception $e) {
            Responses::json(['errors' => $e->getMessage()], 500);
        }
        Responses::json(['payments' => $payments, 'count' => $count], 200);
    }
    public static function getPaymentDetails()
    {
        // Obtener campos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar ID
        $idValidator = new Validator($data['id'], 'id');
        $idValidation = $idValidator->numeric()->minValue(1);
        if ($idValidation->getErrors()) {
            Responses::json(['errors' => $idValidation->getErrors()], 400);
        }

        $id = $data['id'];
        $model = new PaymentsModel();

        if ($model->exists($id)) {
            try {
                $payment = $model->getPaymentDetails($id);
            } catch (Exception $e) {
                Responses::json(['errors' => $e->getMessage()], 500);
            }

            Responses::json(['payment' => $payment], 200);
        } else {
            Responses::json(['errors' => ['payment' => 'Payment not found']], 404);
        }
    }

    public static function verifyInstallment()
    {
        // Obtener campos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar ID

        $idValidator = new Validator($data['id'], 'id');
        $idValidation = $idValidator->numeric()->minValue(1);
        if ($idValidation->getErrors()) {
            Responses::json(['errors' => $idValidation->getErrors()], 400);
        }

        $model = new PaymentsModel();
        try {
            $model->verifyInstallment($data['id']);
        } catch (Exception $e) {
            Responses::json(['errors' => $e->getMessage()], 500);
        }

        Responses::json(['message' => '¡Cuota verificada exitosamente!'], 200);
    }

    public static function deleteInstallment()
    {
        // Obtener campos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar ID

        $idValidator = new Validator($data['id'], 'id');
        $idValidation = $idValidator->numeric()->minValue(1);
        if ($idValidation->getErrors()) {
            Responses::json(['errors' => $idValidation->getErrors()], 400);
        }

        $model = new PaymentsModel();
        try {
            $model->deleteInstallment($data['id']);
        } catch (Exception $e) {
            Responses::json(['errors' => $e->getMessage()], 500);
        }

        Responses::json(['message' => '¡Cuota eliminada exitosamente!'], 200);
    }

    public static function createInstallment()
    {
        // Obtener campos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar ID de pago
        $paymentIdValidator = new Validator($data['payment_id'], 'payment_id');
        $paymentIdValidation = $paymentIdValidator->required()->numeric()->minValue(1);
        if ($paymentIdValidation->getErrors()) {
            Responses::json(['errors' => $paymentIdValidation->getErrors()], 400);
        }

        // Validar monto
        $amountValidator = new Validator($data['amount'], 'amount');
        $amountValidation = $amountValidator->required()->numeric()->minValue(0);
        if ($amountValidation->getErrors()) {
            Responses::json(['errors' => $amountValidation->getErrors()], 400);
        }

        // Validar fecha
        $dateValidator = new Validator($data['date'], 'date');
        $dateValidation = $dateValidator->required()->date();
        if ($dateValidation->getErrors()) {
            Responses::json(['errors' => $dateValidation->getErrors()], 400);
        }

        // Validar numero de referencia
        $referenceValidator = new Validator($data['reference'], 'reference');
        $referenceValidation = $referenceValidator->required()->alphaNumericWithSecureSpecialChars()->minLength(1)->maxLength(255);
        if ($referenceValidation->getErrors()) {
            Responses::json(['errors' => $referenceValidation->getErrors()], 400);
        }

        // Validar metodo de pago
        $paymentMethodValidator = new Validator($data['payment_method'], 'payment_method');
        $paymentMethodValidation = $paymentMethodValidator->required()->numeric()->minValue(1);
        if ($paymentMethodValidation->getErrors()) {
            Responses::json(['errors' => $paymentMethodValidation->getErrors()], 400);
        }

        $model = new PaymentsModel();

        $paymentId = $data['payment_id'];
        $amount = $data['amount'];
        $date = $data['date'];
        $reference = $data['reference'];
        $paymentMethod = $data['payment_method'];

        try {
            $model->createInstallment(
                $paymentId,
                $amount,
                $date,
                $reference,
                $paymentMethod
            );
        } catch (Exception $e) {
            Responses::json(['errors' => $e->getMessage()], 500);
        }

        Responses::json(['message' => '¡Cuota creada exitosamente!'], 201);

    }

    public static function getPaymentHistory()
    {
        $model = new PaymentsModel();
        try {
            $payments = $model->getPaymentHistory();
            Responses::json(['payments' => $payments], 200);
        } catch (Exception $e) {
            Responses::json(['errors' => $e->getMessage()], 500);
        }
    }
}
