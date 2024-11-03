<?php

namespace app\Services;

use App\Models\Product;
use Exception;

class ProductService
{
    /**
     *
     * @param array
     * @throws Exception
     */
    public function importProduct(array $data)
    {
        if (empty($data['sku']) || empty($data['name'])) {
            throw new Exception('Invalid product data');
        }
        $data['price'] = isset($data['price']) ? $data['price'] : 0.00;

        
        Product::updateOrCreate(
            ['sku' => $data['sku']],
            [
                'name' => $data['name'],
                'status' => $data['status'] ?? 'active',
                'variations' => $data['variations'] ?? '',
                'price' => $data['price'],
                'currency' => $data['currency'] ?? 'USD',
            ]
        );
    }

    /**
     * 
     *
     * @param array 
     */
    public function removeOldProducts(array $skus)
    {
        Product::whereNotIn('sku', $skus)->update(['status' => 'deleted']);
    }
}
