<?php
include_once 'models/ProductsModel.php';
include_once 'models/CategoriesModel.php';
include_once 'models/ManufacturersModel.php';
include_once 'models/IVAsModel.php';
include_once 'models/StoragesModel.php';

class ProductsController
{
    public static function getAll()
    {
        // Obtener campos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        $filters = $data['filters'] ?? [];
        $search = $data['search'] ?? '';
        $offset = $data['offset'] ?? 1;
        $range = $data['range'] ?? [];

        // Validar los campos, si están

        // Validar rango
        if (!is_array($range)) {
            Responses::json(['errors' => ['El rango debe ser un array con la forma: \n ["key": ["min":0, "max":2]]']], 400);
        }
        foreach ($range as $key => $value) {
            if (!isset($value['min']) && !isset($value['max'])) {
                Responses::json(['errors' => ['El rango debe ser un array con la forma: \n ["key": ["min":0, "max":2]]']], 400);
            }
            if (isset($value['min']) && isset($value['max'])) {
                if ($value['min'] > $value['max']) {
                    Responses::json(['errors' => ['En el rango de valores, el    mínimo debe ser menor al máximo']], 400);
                }
            }
        }

        // Instanciar modelo
        $productsModel = new ProductsModel();

        // Obtener productos
        try {
            $products = $productsModel->getAllProducts($filters, $search, ($offset - 1) * 10, $range);
            $count = $productsModel->corePoweredCount($filters, $search);

            Responses::json(['products' => $products, 'count' => $count], 200);
        } catch (Exception $e) {
            Responses::json(['errors' => ['No se pudo obtener la lista de productos']], 500);
        }
    }

    public static function getShopProducts()
    {
        // Obtener campos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        $page = $data['page'] ?? 1;
        $filters = $data['filters'] ?? [];
        $search = $data['search'] ?? '';

        // Validar página
        $pageValidator = new Validator($page, 'page');
        $pageValidation = $pageValidator->required()->numeric()->minValue(1);
        if ($pageValidation->getErrors()) {
            Responses::json(['errors' => $pageValidation->getErrors()], 400);
        }

        $model = new ProductsModel();

        try {
            $products = $model->getShopProducts(offset: ($page - 1) * 10);
            Responses::json($products, 200);
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
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

            // Intentar subir la imágen del producto
            try {
                // Obtener imágen
                if (!isset($_FILES['image'])) {
                    // Continuar sin imágen
                    throw new Exception('No se recibió ninguna imágen');
                }

                $image = $_FILES['image']; // Cargar la imágen en una variable

                if ($image['error'] !== 0) {
                    Responses::json(['errors' => ['No se pudo subir la imágen']], 400);
                }

                if ($image['type'] !== 'image/jpeg') {
                    Responses::json(['errors' => ['La imágen debe ser de tipo JPG']], 400);
                }

                if ($image['size'] > 2097152) {
                    Responses::json(['errors' => ['La imágen debe tener un tamaño máximo de 2MB']], 400);
                }

                global $ENV;
                $imagesDirectory = $ENV['UPLOAD_FOLDER'];
                if (!is_dir($imagesDirectory)) {
                    mkdir($imagesDirectory);
                }

                $image['name'] = "product-" . $data['id'] . ".jpg";

                move_uploaded_file($image['tmp_name'], $imagesDirectory . $image['name']);
            } catch (Exception $e) {
                // No se pudo subir la imágen, continuar sin imágen
            }

            Responses::json(['message' => 'Producto creado exitosamente', 'id' => $data['id']], 201);
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }
    }

    public static function getImage()
    {
        $id = $_GET['id'] ?? null;

        global $ENV;
        $imagesDirectory = $ENV['UPLOAD_FOLDER'];

        // Crear directorio si aún no existe
        if (!is_dir($imagesDirectory)) {
            mkdir($imagesDirectory);
        }

        $imagePath = $imagesDirectory . "product-$id.jpg";

        if (file_exists($imagePath)) {
            header('Content-Type: image/jpeg');
            readfile($imagePath);
            exit;
        } else {
            // Sino, se devuelve el placeholder de imágen. Ubicado en assets/product-placeholder.jpg
            header('Content-Type: image/jpeg');
            readfile('assets/product-placeholder.jpg');
            exit;
        }
    }

    public static function updateProduct()
    {
        // Obtener datos de la solicitud
        if (!isset($_POST['product'])) {
            Responses::json(['errors' => ['No se recibió ningún producto']], 400);
        }
        $data = json_decode($_POST['product'], true);

        // Verificar que los campos requeridos estén presentes
        Validator::ensureFields($data, ['id']);

        // Validar id
        $idValidator = new Validator($data['id'], 'id');
        $idValidation = $idValidator->required()->numeric()->minValue(1);
        if ($idValidation->getErrors()) {
            Responses::json(['errors' => $idValidation->getErrors()], 400);
        }

        // Validar name (Si se envía)
        if (isset($data['name'])) {
            $nameValidator = new Validator($data['name'], 'name');
            $nameValidation = $nameValidator->required()
                ->minLength(3)
                ->maxLength(45)
                ->alphaNumericWithSecureSpecialChars();

            if ($nameValidation->getErrors()) {
                Responses::json(['errors' => $nameValidation->getErrors()], 400);
            }
        }

        // Validar description (Si se envía)
        if (isset($data['description'])) {
            $descriptionValidator = new Validator($data['description'], 'description');
            $descriptionValidation = $descriptionValidator->required()
                ->maxLength(255)
                ->alphaNumericWithSecureSpecialChars();

            if ($descriptionValidation->getErrors()) {
                Responses::json(['errors' => $descriptionValidation->getErrors()], 400);
            }
        }

        // Validar precio (Si se envía)
        if (isset($data['actualPrice'])) {
            $priceValidator = new Validator($data['actualPrice'], 'actualPrice');
            $priceValidation = $priceValidator->required()->numeric()->minValue(0)->maxValue(10000000000);

            if ($priceValidation->getErrors()) {
                Responses::json(['errors' => $priceValidation->getErrors()], 400);
            }
        }

        // Validar IVA (Si se envía)
        if (isset($data['actualIva'])) {
            $ivaValidator = new Validator($data['actualIva'], 'actualIva');
            $ivaValidation = $ivaValidator->required()->numeric()->minValue(1);

            if ($ivaValidation->getErrors()) {
                Responses::json(['errors' => $ivaValidation->getErrors()], 400);
            }
        }

        // Validar categoria (Si se envía)
        if (isset($data['categoria'])) {
            $categoriaValidator = new Validator($data['categoria'], 'categoria');
            $categoriaValidation = $categoriaValidator->required()->numeric()->minValue(1);

            if ($categoriaValidation->getErrors()) {
                Responses::json(['errors' => $categoriaValidation->getErrors()], 400);
            }
        }

        // Validar fabricante (Si se envía)
        if (isset($data['fabricante'])) {
            $fabricanteValidator = new Validator($data['fabricante'], 'fabricante');
            $fabricanteValidation = $fabricanteValidator->required()->CiOrRif();

            if ($fabricanteValidation->getErrors()) {
                Responses::json(['errors' => $fabricanteValidation->getErrors()], 400);
            }
        }

        // Validar que existan los fabricantes, IVAs y categorías (Si se envían)
        if (isset($data['fabricante'])) {
            $fabricanteModel = new ManufacturersModel();
            if (!$fabricanteModel->exists($data['fabricante'])) {
                Responses::json(['errors' => ['El fabricante no existe']], 400);
            }
        }

        if (isset($data['actualIva'])) {
            $ivaModel = new IVAsModel();
            if (!$ivaModel->exists($data['actualIva'])) {
                Responses::json(['errors' => ['El IVA no existe']], 400);
            }
        }

        if (isset($data['categoria'])) {
            if (!CategoriesModel::categoryExists($data['categoria'])) {
                Responses::json(['errors' => ['La categoría no existe']], 400);
            }
        }

        // Validar que el producto con el ID exista
        $model = new ProductsModel();
        if (!$model->exists($data['id'])) {
            Responses::json(['errors' => ['El producto especificado no existe']], 400);
        }

        // Preparar los datos para la inserción
        $productData = [];
        if (isset($data['name'])) {
            $productData['name'] = $data['name'];
        }
        if (isset($data['description'])) {
            $productData['description'] = $data['description'];
        }
        if (isset($data['actualPrice'])) {
            $productData['actualPrice'] = $data['actualPrice'];
        }
        if (isset($data['actualIva'])) {
            $productData['actualIva'] = $data['actualIva'];
        }
        if (isset($data['categoria'])) {
            $productData['categoria'] = $data['categoria'];
        }
        if (isset($data['fabricante'])) {
            $productData['fabricante'] = $data['fabricante'];
        }

        if ($productData == []) {
            Responses::json(['errors' => ['No se recibieron datos para actualizar']], 400);
        }

        try {
            $newProductId = $model->corePoweredUpdate($data['id'], $productData);

            // Intentar subir la imágen del producto
            try {
                // Obtener imágen
                if (!isset($_FILES['image'])) {
                    // Continuar sin imágen
                    throw new Exception('No se recibió ninguna imágen');
                }

                $image = $_FILES['image']; // Cargar la imágen en una variable

                if ($image['error'] !== 0) {
                    Responses::json(['errors' => ['No se pudo subir la imágen']], 400);
                }

                if ($image['type'] !== 'image/jpeg') {
                    Responses::json(['errors' => ['La imágen debe ser de tipo JPG']], 400);
                }

                if ($image['size'] > 2097152) {
                    Responses::json(['errors' => ['La imágen debe tener un tamaño máximo de 2MB']], 400);
                }

                global $ENV;
                $imagesDirectory = $ENV['UPLOAD_FOLDER'];
                if (!is_dir($imagesDirectory)) {
                    mkdir($imagesDirectory);
                }

                $image['name'] = "product-" . $data['id'] . ".jpg";

                move_uploaded_file($image['tmp_name'], $imagesDirectory . $image['name']);
            } catch (Exception $e) {
                // No se pudo subir la imágen, continuar sin imágen
            }

            Responses::json(['message' => 'Producto editado exitosamente', 'id' => $data['id']], 201);
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

    public static function getCompatibleStorages()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        Validator::ensureFields($data, ['id']);

        // Validar id
        $idValidator = new Validator($data['id'], 'id');
        $idValidation = $idValidator->required()->numeric()->minValue(1);
        if ($idValidation->getErrors()) {
            Responses::json(['errors' => $idValidation->getErrors()], 400);
        }

        $model = new ProductsModel();

        // Validar si el producto existe
        try {
            if (!$model->exists($data['id'])) {
                Responses::json(['errors' => ['El producto especificado no existe']], 400);
            }
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }

        // Obtener los almacenes compatibles
        try {
            $compatibleStorages = $model->getCompatibleStorages($data['id']);
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }

        // Devolver los almacenes compatibles
        Responses::json(['storages' => $compatibleStorages], 200);
    }

    public static function setCompatibleStorages()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        Validator::ensureFields($data, ['id']);

        // Validar id
        $idValidator = new Validator($data['id'], 'id');
        $idValidation = $idValidator->required()->numeric()->minValue(1);
        if ($idValidation->getErrors()) {
            Responses::json(['errors' => $idValidation->getErrors()], 400);
        }

        // Validar compatibleStorages
        if (!isset($data['compatibleStorages'])) {
            Responses::json(['errors' => ['compatibleStorages es requerido']], 400);
        }
        if (!is_array($data['compatibleStorages'])) {
            Responses::json(['errors' => ['compatibleStorages debe ser un array']], 400);
        }
        if (count($data['compatibleStorages']) == 0) {
            Responses::json(['errors' => ['compatibleStorages debe tener al menos un elemento']], 400);
        }
        foreach ($data['compatibleStorages'] as $compatibleStorage) {
            $compatibleStorageValidator = new Validator($compatibleStorage, 'compatibleStorage');
            $compatibleStorageValidation = $compatibleStorageValidator->required()->numeric()->minValue(1);
            if ($compatibleStorageValidation->getErrors()) {
                Responses::json(['errors' => $compatibleStorageValidation->getErrors()], 400);
            }
        }

        $model = new ProductsModel();

        // Validar si el producto existe
        try {
            if (!$model->exists($data['id'])) {
                Responses::json(['errors' => ['El producto especificado no existe']], 400);
            }
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }

        // Validar si los almacenes especificados existen
        $storageModel = new StoragesModel();
        try {
            foreach ($data['compatibleStorages'] as $compatibleStorage) {
                $storage = $storageModel->corePoweredGetById($compatibleStorage);
                if (!$storage) {
                    Responses::json(['errors' => ['El almacén especificado no existe']], 400);
                }
                if ($storage['deleted'] == 1) {
                    Responses::json(['errors' => ['El almacén especificado está eliminado']], 400);
                }
            }
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }

        // Validar que no se estén agregando almacenes que ya son compatibles
        try {
            $compatibleStorages = $model->getCompatibleStorages($data['id']);
            foreach ($compatibleStorages as $compatibleStorage) {
                if (in_array($compatibleStorage['id'], $data['compatibleStorages'])) {
                    Responses::json(['errors' => ['El almacén especificado ya es compatible']], 400);
                }
            }
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }

        // Actualizar los almacenes compatibles
        try {
            $model->setStorageCompatibility($data['id'], $data['compatibleStorages']);
            Responses::json(['message' => 'Almacenes compatibles actualizados exitosamente'], 200);
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }
    }

    public static function disableCompatibleStorage()
    {
        // Obtener datos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar campos
        Validator::ensureFields($data, ['id', 'storageId']);

        // Validar id
        $idValidator = new Validator($data['id'], 'id');
        $idValidation = $idValidator->required()->numeric()->minValue(1);
        if ($idValidation->getErrors()) {
            Responses::json(['errors' => $idValidation->getErrors()], 400);
        }

        // Validar storageId
        $storageIdValidator = new Validator($data['storageId'], 'storageId');
        $storageIdValidation = $storageIdValidator->required()->numeric()->minValue(1);
        if ($storageIdValidation->getErrors()) {
            Responses::json(['errors' => $storageIdValidation->getErrors()], 400);
        }

        // Validar si el producto existe
        $model = new ProductsModel();
        try {
            if (!$model->exists($data['id'])) {
                Responses::json(['errors' => ['El producto especificado no existe']], 400);
            }
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }

        // Validar si el almacén especificado existe
        $storageModel = new StoragesModel();
        try {
            $storage = $storageModel->corePoweredGetById($data['storageId']);
            if (!$storage) {
                Responses::json(['errors' => ['El almacén especificado no existe']], 400);
            }
            if ($storage['deleted'] == 1) {
                Responses::json(['errors' => ['El almacén especificado está eliminado']], 400);
            }
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }

        // Validar si el almacén especificado es compatible y no tiene existencia
        try {
            $compatibleStorages = $model->getCompatibleStorages($data['id']);
            if (
                !in_array($data['storageId'], array_map(
                    function ($compatibleStorage) {
                        return $compatibleStorage['id'];
                    },
                    $compatibleStorages
                ))
            ) {
                Responses::json(['errors' => ['El almacén especificado no es compatible']], 400);
            }
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()], 500]);
        }

        // Validar que el almacén especificado no tenga existencias del producto
        try {
            $model = new ProductsModel();
            if ($model->hasExistences($data['id'], $data['storageId'])) {
                Responses::json(['errors' => ['El almacén especificado tiene existencias del producto. Pruebe vaciar el almacén e intente de nuevo']], 400);
            }
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }

        // Deshabilitar el almacén compatible
        try {
            $model->disableCompatibleStorage($data['id'], $data['storageId']);
            Responses::json(['message' => 'Almacén compatible deshabilitado exitosamente'], 200);
        } catch (Exception $e) {
            Responses::json(['errors' => [$e->getMessage()]], 500);
        }
    }
}