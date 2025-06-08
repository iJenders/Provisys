<?php
include_once 'core/Model.php';

class IVAsModel extends Model
{
    protected string $table = 'iva';
    protected array $attributes = [
        'id' => 'id_iva',
        'name' => 'nombre_iva',
        'value' => 'iva',
        'deleted' => 'eliminado'
    ];
    protected array $searchableAttributes = [
        'name',
        'value'
    ];
    protected array $guarded = [];
    protected string $primaryKey = 'id';
}