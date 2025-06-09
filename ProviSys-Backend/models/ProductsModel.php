<?php
include_once 'core/Model.php';
include_once 'models/CategoriesModel.php';
include_once 'models/ManufacturersModel.php';
include_once 'models/IVAsModel.php';

class ProductsModel extends Model
{
    protected string $table = 'producto';
    protected array $attributes = [
        'id' => 'id_producto',
        'name' => 'nombre',
        'description' => 'descripcion_producto',
        'actualPrice' => 'precio',
        'actualIva' => 'id_iva',
        'categoria' => 'id_categoria',
        'fabricante' => 'id_fabricante',
        'deleted' => 'eliminado'
    ];
    protected array $searchableAttributes = [
        'id',
        'name',
        'description',
        'categoria',
        'fabricante'
    ];
    protected array $guarded = [];
    protected string $primaryKey = 'id';

    // Método para obtener todos los productos
    public function getAllProducts($filters, $search, $offset)
    {
        // Obtener los productos crudos
        $products = $this->corePoweredGetAll($filters, $search, $offset);

        // Instanciar modelos
        $ivasModel = new IVAsModel();
        $manufacturersModel = new ManufacturersModel();

        // Hidratar los productos con los datos de IVA, Categoría y Fabricante
        foreach ($products as $productKey => $product) {
            $products[$productKey]['actualIva'] = $ivasModel->corePoweredGetById($product['actualIva']);
            $products[$productKey]['categoria'] = CategoriesModel::getById($product['categoria']);
            $products[$productKey]['fabricante'] = $manufacturersModel->corePoweredGetById($product['fabricante']);
        }

        return $products;
    }
}