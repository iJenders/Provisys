<?php
include_once 'core/Model.php';

class StoragesModel extends Model
{
    protected string $table = 'almacen';
    protected array $attributes = [
        'id' => 'id_almacen',
        'name' => 'nombre',
        'description' => 'descripcion_almacen',
        'vehicle' => 'es_vehiculo',
        'deleted' => 'eliminado'
    ];
    protected array $searchableAttributes = [
        'name',
        'description'
    ];
    protected array $guarded = ['id'];
    protected string $primaryKey = 'id';
}