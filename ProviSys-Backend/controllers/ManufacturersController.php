<?php
include_once 'models/ManufacturersModel.php';
include_once 'utils/Validator.php';

class ManufacturersController
{
    public static function getManufacturers()
    {
        // Obtener los datos de la consulta
        $data = json_decode(file_get_contents('php://input'), true);
        $filters = $data['filters'] ?? [];
        $offset = $data['offset'] ?? 1;
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

        $manufacturersModel = new ManufacturersModel();

        try {
            $manufacturers = $manufacturersModel->getAll($filters, $search, $offset);
            $manufacturersCount = $manufacturersModel->count($filters, $search);

            Responses::json([
                'manufacturers' => $manufacturers,
                'count' => $manufacturersCount
            ]);
        } catch (Exception $e) {
            Responses::json($e->getMessage(), 500);
        }
    }
    public static function createManufacturer()
    {
        // Obtener campos de la consulta
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar campos
        Validator::ensureFields($data, [
            "id",
            "name",
            "phone",
            "email",
            "address"
        ]);

        $id = $data['id'];
        $name = $data['name'];
        $phone = $data['phone'];
        $secondaryPhone = null;
        $email = $data['email'];
        $address = $data['address'];

        $idValidator = new Validator($id, 'id');
        $nameValidator = new Validator($name, 'name');
        $phoneValidator = new Validator($phone, 'phone');
        $emailValidator = new Validator($email, 'email');
        $addressValidator = new Validator($address, 'address');

        $idValidation = $idValidator->required()->CiOrRif();
        $nameValidation = $nameValidator->required()->alphaNumericWithSecureSpecialChars()->minLength(3)->maxLength(128);
        $phoneValidation = $phoneValidator->required()->phone();
        $emailValidation = $emailValidator->required()->email();
        $addressValidation = $addressValidator->required()->alphaNumericWithSecureSpecialChars()->minLength(3)->maxLength(255);

        $errors = array_merge($idValidation->getErrors(), $nameValidation->getErrors(), $phoneValidation->getErrors(), $emailValidation->getErrors(), $addressValidation->getErrors());

        // Verificar si se especifica un telefono secundario
        if (isset($data['secondaryPhone']) && !empty($data['secondaryPhone'])) {
            $secondaryPhone = $data['secondaryPhone'];
            $secondaryPhoneValidator = new Validator($secondaryPhone, 'secondaryPhone');
            $secondaryPhoneValidation = $secondaryPhoneValidator->phone();
            $errors = array_merge($errors, $secondaryPhoneValidation->getErrors());
        }

        // Verificar si hay errores
        if (!empty($errors)) {
            Responses::json([
                'errors' => $errors
            ], 400);
        }

        // Instanciar el modelo
        $manufacturerModel = new ManufacturersModel();

        // Verificar si existe un fabricante con el mismo id
        if ($manufacturerModel->exists($id)) {
            Responses::json([
                'errors' => [
                    'Ya existe un fabricante con el mismo id'
                ]
            ], 400);
        }

        // Ejecutar la consulta desde el modelo

        try {
            $manufacturerModel->create($data);
        } catch (Exception $e) {
            Responses::json([
                'errors' => [
                    $e->getMessage()
                ]
            ], 500);
        }

        // Mensaje de éxito
        Responses::json([
            'message' => 'Fabricante creado exitosamente'
        ], 201);
    }
    public static function updateManufacturer()
    {
        // Obtener campos de la consulta
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar campos
        Validator::ensureFields($data, [
            "id"
        ]);
        $id = $data['id'];
        $idValidator = new Validator($id, 'id');
        $idValidation = $idValidator->required()->CiOrRif();
        $errors = $idValidation->getErrors();

        $fields = [];

        if (isset($data['name'])) {
            $name = $data['name'];
            $fields['name'] = $name;
            $nameValidator = new Validator($name, 'name');
            $nameValidation = $nameValidator->required()->alphaNumericWithSecureSpecialChars()->minLength(3)->maxLength(128);
            $errors = array_merge($errors, $nameValidation->getErrors());
        }

        if (isset($data['phone'])) {
            $phone = $data['phone'];
            $fields['phone'] = $phone;
            $phoneValidator = new Validator($phone, 'phone');
            $phoneValidation = $phoneValidator->required()->phone();
            $errors = array_merge($errors, $phoneValidation->getErrors());
        }

        if (isset($data['secondaryPhone']) && !empty($data['secondaryPhone'])) {
            $secondaryPhone = $data['secondaryPhone'];
            $fields['secondaryPhone'] = $secondaryPhone;
            $secondaryPhoneValidator = new Validator($secondaryPhone, 'secondaryPhone');
            $secondaryPhoneValidation = $secondaryPhoneValidator->phone();
            $errors = array_merge($errors, $secondaryPhoneValidation->getErrors());
        }

        if (isset($data['email'])) {
            $email = $data['email'];
            $fields['email'] = $email;
            $emailValidator = new Validator($email, 'email');
            $emailValidation = $emailValidator->required()->email();
            $errors = array_merge($errors, $emailValidation->getErrors());
        }

        if (isset($data['address'])) {
            $address = $data['address'];
            $fields['address'] = $address;
            $addressValidator = new Validator($address, 'address');
            $addressValidation = $addressValidator->required()->alphaNumericWithSecureSpecialChars()->minLength(3)->maxLength(255);
            $errors = array_merge($errors, $addressValidation->getErrors());
        }

        // Verificar si hay errores
        if (!empty($errors)) {
            Responses::json([
                'errors' => $errors
            ], 400);
        }

        // Instanciar el modelo
        $manufacturerModel = new ManufacturersModel();

        // Verificar si existe un fabricante con el mismo id
        if (!$manufacturerModel->exists($id)) {
            Responses::json([
                'errors' => [
                    'No existe un fabricante con el id proporcionado'
                ]
            ], 400);
        }

        // Ejecutar la consulta desde el modelo
        try {
            $manufacturerModel->update($id, $fields);
        } catch (Exception $e) {
            Responses::json([
                'errors' => [
                    $e->getMessage()
                ]
            ], 500);
        }
    }
    public static function deleteManufacturer()
    {
        // Obtener campos de la consulta
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar campos
        Validator::ensureFields($data, [
            "id"
        ]);

        $id = $data['id'];
        $idValidator = new Validator($id, 'id');
        $idValidation = $idValidator->required()->CiOrRif();

        // Verificar si hay errores
        if (!empty($idValidation->getErrors())) {
            Responses::json([
                'errors' => $idValidation->getErrors()
            ], 400);
        }

        // Instanciar el modelo
        $manufacturerModel = new ManufacturersModel();

        // Verificar si existe un fabricante con el mismo id
        if (!$manufacturerModel->exists($id)) {
            Responses::json([
                'errors' => [
                    'No existe un fabricante con el id especificado'
                ]
            ], 400);
        }

        // Ejecutar la consulta desde el modelo
        try {
            $manufacturerModel->delete($id);
            Responses::json([
                'message' => 'Fabricante eliminado exitosamente'
            ], 200);
        } catch (Exception $e) {
            Responses::json(
                ['errors' => [$e->getMessage()]],
                500
            );
        }
    }
}