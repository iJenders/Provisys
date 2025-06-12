
<?php
include_once 'core/Model.php';

class ProvidersModel extends Model
{
    protected string $table = 'proveedor';
    protected array $attributes = [
        'id' => 'id_proveedor',
        'name' => 'nombre',
        'phone' => 'telefono',
        'secondaryPhone' => 'telefono_secundario',
        'email' => 'correo',
        'address' => 'direccion',
        'deleted' => 'eliminado'
    ];
    protected array $searchableAttributes = [
        'name',
        'phone',
        'secondaryPhone',
        'email',
        'address'
    ];
    protected array $guarded = [];
    protected string $primaryKey = 'id';
}
