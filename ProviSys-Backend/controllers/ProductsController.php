<?php
include_once 'models/ProductsModel.php';
include_once 'models/CategoriesModel.php';
include_once 'models/ManufacturersModel.php';
include_once 'models/IVAsModel.php';

class ProductsController
{
    public static function getAll()
    {
        // Obtener campos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        $filters = $data['filters'] ?? [];
        $search = $data['search'] ?? '';
        $offset = $data['offset'] ?? 1;

        // Validar los campos, si están


        // Instanciar modelo
        $productsModel = new ProductsModel();

        // Obtener productos
        try {
            $products = $productsModel->getAllProducts($filters, $search, ($offset - 1) * 10);
            $count = $productsModel->corePoweredCount($filters, $search);

            Responses::json(['products' => $products, 'count' => $count], 200);
        } catch (Exception $e) {
            Responses::json(['errors' => ['No se pudo obtener la lista de productos']], 500);
        }
    }

    public static function createProduct()
    {
        // Obtener datos de la solicitud
        if (!isset($_POST['product'])) {
            Responses::json(['errors' => ['No se recibió ningún producto']], 400);
        }
        $data = json_decode($_POST['product'], true);

        // Verificar que los campos requeridos estén presentes
        Validator::ensureFields($data, ['id', 'name', 'description', 'actualPrice', 'actualIva', 'categoria', 'fabricante']);

        // Validar id
        $idValidator = new Validator($data['id'], 'id');
        $idValidation = $idValidator->required()->numeric()->minValue(1);
        if ($idValidation->getErrors()) {
            Responses::json(['errors' => $idValidation->getErrors()], 400);
        }

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

        // Validar precio
        $priceValidator = new Validator($data['actualPrice'], 'actualPrice');
        $priceValidation = $priceValidator->required()->numeric()->minValue(0)->maxValue(10000000000);

        if ($priceValidation->getErrors()) {
            Responses::json(['errors' => $priceValidation->getErrors()], 400);
        }

        // Validar IVA
        $ivaValidator = new Validator($data['actualIva'], 'actualIva');
        $ivaValidation = $ivaValidator->required()->numeric()->minValue(1);

        if ($ivaValidation->getErrors()) {
            Responses::json(['errors' => $ivaValidation->getErrors()], 400);
        }

        // Validar categoria
        $categoriaValidator = new Validator($data['categoria'], 'categoria');
        $categoriaValidation = $categoriaValidator->required()->numeric()->minValue(1);

        if ($categoriaValidation->getErrors()) {
            Responses::json(['errors' => $categoriaValidation->getErrors()], 400);
        }

        // Validar fabricante
        $fabricanteValidator = new Validator($data['fabricante'], 'fabricante');
        $fabricanteValidation = $fabricanteValidator->required()->CiOrRif();

        if ($fabricanteValidation->getErrors()) {
            Responses::json(['errors' => $fabricanteValidation->getErrors()], 400);
        }

        // Validar que existan los fabricantes, IVAs y categorías
        $fabricanteModel = new ManufacturersModel();
        $ivaModel = new IVAsModel();
        if (!$fabricanteModel->exists($data['fabricante'])) {
            Responses::json(['errors' => ['El fabricante no existe']], 400);
        }
        if (!$ivaModel->exists($data['actualIva'])) {
            Responses::json(['errors' => ['El IVA no existe']], 400);
        }
        if (!CategoriesModel::categoryExists($data['categoria'])) {
            Responses::json(['errors' => ['La categoría no existe']], 400);
        }

        // Validar que no exista un producto con el mismo id
        $model = new ProductsModel();
        if ($model->exists($data['id'])) {
            Responses::json(['errors' => ['El producto ya existe']], 400);
        }

        // Preparar los datos para la inserción
        $productData = [
            'id' => $data['id'],
            'name' => $data['name'],
            'description' => $data['description'],
            'actualPrice' => $data['actualPrice'],
            'actualIva' => $data['actualIva'],
            'categoria' => $data['categoria'],
            'fabricante' => $data['fabricante'],
            'eliminado' => 0
        ];

        try {
            $newProductId = $model->corePoweredCreate($productData);

            // Obtener el registro recién creado para devolverlo en la respuesta
            $newProduct = $model->corePoweredGetById($newProductId);

            Responses::json(['message' => 'Producto creado exitosamente', 'product' => $newProduct], 201);
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }
    }

    public static function deleteProduct()
    {
        // Obtener campos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        Validator::ensureFields($data, ['id']);

        // Validar id
        $idValidator = new Validator($data['id'], 'id');
        $idValidation = $idValidator->required()->numeric()->minValue(1);
        if ($idValidation->getErrors()) {
            Responses::json(['errors' => $idValidation->getErrors()], 400);
        }

        // Validar que exista el producto
        $model = new ProductsModel();
        if (!$model->exists($data['id'])) {
            Responses::json(['errors' => ['El producto no existe']], 400);
        }

        try {
            $model->corePoweredDelete($data['id']);
            Responses::json(['message' => 'Producto eliminado exitosamente'], 200);
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }
    }
}