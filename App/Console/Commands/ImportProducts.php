<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProductService;
use League\Csv\Reader;

class ImportProducts extends Command
{
    protected $signature = 'import:products';
    protected $description = 'Import products from a CSV file';

    private $productService;

    /**
     * 
     *
     * @param ProductService $productService
     */
    public function __construct(ProductService $productService)
    {
        parent::__construct();
        $this->productService = $productService;
    }

    /**
     * 
     */
    public function handle()
    {
        $csvPath = storage_path('app/public/products.csv');

        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(0); 
        $skus = [];

        foreach ($csv as $record) {
            try {
                $this->productService->importProduct($record);
                $skus[] = $record['sku'];
            } catch (\Exception $e) {
                $this->error("Error importing product SKU: {$record['sku']} - {$e->getMessage()}");
            }
        }

        $this->productService->removeOldProducts($skus);

        $this->info('Products imported successfully, and outdated products removed.');
    }
}
