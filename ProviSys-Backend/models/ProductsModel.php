<?php
include_once 'core/Model.php';
include_once 'models/CategoriesModel.php';
include_once 'models/ManufacturersModel.php';
include_once 'models/IVAsModel.php';
include_once 'models/StoragesModel.php';

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
    public function getAllProducts($filters, $search, $offset, $range)
    {
        // Obtener los productos crudos
        $products = $this->corePoweredGetAll($filters, $search, $offset, $range);

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

    // Método para añadir compatibilidad con almacenes
    public function setStorageCompatibility($productId, array $storageIds)
    {
        $sql = "INSERT INTO productos_en_almacen (id_producto, id_almacen) VALUES";

        $args = [];

        if (empty($storageIds)) {
            return true;
        }
        foreach ($storageIds as $storageId) {
            $sql .= "(?, ?),";

            $args[] = $productId;
            $args[] = $storageId;
        }
        $sql = substr($sql, 0, -1);

        $stmt = $this->db->prepare($sql);

        $stmt->execute($args);

        return true;
    }

    public function getCompatibleStorages($productId)
    {
        $sql = "SELECT * FROM productos_en_almacen WHERE id_producto = ?";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([$productId]);

        $result = $stmt->get_result();

        $storages = [];

        while ($row = $result->fetch_assoc()) {
            // No solo obtenemos el id del almacen, sino también se hidrata con los datos del almacen

            $storageModel = new StoragesModel();

            $storage = $storageModel->corePoweredGetById($row['id_almacen']);
            $storage['stock'] = intval($row['stock_disponible']);
            $storage['reservedStock'] = intval($row['stock_reservado']);
            $storage['active'] = $row['eliminado'] !== 1;

            $storages[] = $storage;
        }

        return $storages;
    }

    public function disableCompatibleStorage($productId, $storageId)
    {
        $sql = "UPDATE productos_en_almacen SET eliminado = NOT eliminado WHERE id_producto = ? AND id_almacen = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId, $storageId]);
        return true;
    }

    public function hasExistences($productId, $storageId = null)
    {
        $sql = "SELECT * FROM productos_en_almacen WHERE id_producto = ? AND eliminado = 0 AND stock_disponible > 0";

        $args = [$productId];

        if ($storageId) {
            $sql .= " AND id_almacen = ?";
            $args[] = $storageId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($args);
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }
}