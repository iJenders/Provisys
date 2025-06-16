<?php
include_once 'core/Model.php';

class PaymentMethodsModel extends Model
{
    protected string $table = 'metodo_de_pago';
    protected array $attributes = [
        'id' => 'id_metodo',
        'name' => 'nombre_metodo',
        'description' => 'descripcion',
        'deleted' => 'eliminado'
    ];
    protected array $searchableAttributes = [
        'name',
        'description'
    ];
    protected array $guarded = ['id'];
    protected string $primaryKey = 'id';
}
