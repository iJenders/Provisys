<?php
include_once 'models/CategoriesModel.php';

class categoriesController
{
    public static function createCategory()
    {
        // Obtener los datos del cuerpo de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Validación de los datos de entrada
        Validator::ensureFields($data, [
            'name',
            'description'
        ]);

        $name = $data['name'];
        $description = $data['description'];

        $nameValidator = new Validator($name, 'name');
        $descriptionValidator = new Validator($description, 'description');

        $nameValidator->required()->minLength(3)->maxLength(45)->alphaNumericWithSpaces();
        $descriptionValidator->required()->minLength(3)->maxLength(255)->alphaNumericWithSecureSpecialChars();

        $errors = array_merge($nameValidator->getErrors(), $descriptionValidator->getErrors());

        if (!empty($errors)) {
            Responses::json(['errors' => $errors], 400);
        }

        // Crear la categoría en la base de datos
        try {
            CategoriesModel::create($name, $description);
        } catch (Exception $e) {
            Responses::json(['errors' => 'Error al crear la categoría.'], 500);
        }

        // Mensaje de éxito
        Responses::json(['message' => 'Categoría creada exitosamente'], 201);
    }

    public static function getCategories()
    {
        // Validación de los datos de entrada
        $data = json_decode(file_get_contents('php://input'), true);

        $search = null;
        if (isset($data['search'])) {
            $search = $data['search'];
            $searchValidator = new Validator($search, 'search');
            $searchValidator->required()->maxLength(255)->alphaNumericWithSecureSpecialChars();

            $errors = $searchValidator->getErrors();
            if (!empty($errors)) {
                Responses::json(['errors' => $errors], 400);
            }
        }

        // Obtener todas las categorías de la base de datos
        try {
            if ($search) {
                $categories = CategoriesModel::search($search);
            } else {
                $categories = CategoriesModel::getAll();
            }
        } catch (Exception $e) {
            Responses::json(['errors' => 'Error al obtener las categorías.'], 500);
        }

        $categoriesArray = [];
        foreach ($categories as $category) {
            $categoriesArray[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'description' => $category->getDescription(),
            ];
        }

        // Devolver las categorías en formato JSON
        Responses::json($categoriesArray, 200);
    }

    public static function editCategory()
    {
        // Obtener los datos del cuerpo de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Validación de los datos de entrada
        Validator::ensureFields($data, [
            'id',
            'name',
            'description'
        ]);

        $id = $data['id'];
        $name = $data['name'];
        $description = $data['description'];

        $idValidator = new Validator($id, 'id');
        $nameValidator = new Validator($name, 'name');
        $descriptionValidator = new Validator($description, 'description');

        $idValidator->required()->numeric();
        $nameValidator->required()->minLength(3)->maxLength(45)->alphaNumericWithSpaces();
        $descriptionValidator->required()->minLength(3)->maxLength(255)->alphaNumericWithSecureSpecialChars();

        $errors = array_merge($idValidator->getErrors(), $nameValidator->getErrors(), $descriptionValidator->getErrors());

        if (!empty($errors)) {
            Responses::json(['errors' => $errors], 400);
        }

        try {
            if (!CategoriesModel::categoryExists($id)) {
                Responses::json(['errors' => ['La categoría no existe.']], 404);
            }
        } catch (Exception $e) {
            Responses::json(['errors' => ['Error al verificar la existencia de la categoría.']], 500);
        }

        // Actualizar la categoría en la base de datos
        try {
            CategoriesModel::edit($id, $name, $description);
        } catch (Exception $e) {
            Responses::json(['errors' => ['Error al editar la categoría.']], 500);
        }

        // Mensaje de éxito
        Responses::json(['message' => 'Categoría editada exitosamente'], 200);
    }

    public static function deleteCategory()
    {
        // Obtener los datos del cuerpo de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Validación de los datos de entrada
        Validator::ensureFields($data, [
            'id'
        ]);

        $id = $data['id'];
        $idValidator = new Validator($id, 'id');
        $idValidator->required()->numeric();

        $errors = $idValidator->getErrors();

        if (!empty($errors)) {
            Responses::json(['errors' => $errors], 400);
        }

        try {
            if (!CategoriesModel::categoryExists($id)) {
                Responses::json(['errors' => ['La categoría no existe.']], 404);
            }
        } catch (Exception $e) {
            Responses::json(['errors' => ['Error al verificar la existencia de la categoría.']], 500);
        }

        // Eliminar la categoría de la base de datos
        try {
            CategoriesModel::delete($id);
        } catch (Exception $e) {
            Responses::json(['errors' => ['Error al eliminar la categoría.']], 500);
        }

        // Mensaje de éxito
        Responses::json(['message' => 'Categoría eliminada exitosamente'], 200);

    }
}