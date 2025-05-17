<?php

class ProductsController
{
    public static function create()
    {
        Responses::json(['message' => 'Product created successfully']);
    }

    public static function update()
    {
        Responses::json(['message' => 'Product updated successfully']);
    }

    public static function delete()
    {
        Responses::json(['message' => 'Product deleted successfully']);
    }
}