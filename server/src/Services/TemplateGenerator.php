<?php

namespace App\Services;

class TemplateGenerator
{
    public static function generateCSV(): string
    {
        $template = [
            ['item_name', 'image_name', 'price', 'description', 'assets'],
            ['Sample Product 1', 'product1.jpg', '19.99', 'Description for product 1', '{"color":"blue","size":"medium"}'],
            ['Sample Product 2', 'product2.jpg', '29.99', 'Description for product 2', '{"color":"red","size":"large"}'],
            ['Sample Product 3', 'product3.jpg', '39.99', 'Description for product 3', '{"color":"green","size":"small"}'],
        ];

        $output = fopen('php://temp', 'r+');
        
        foreach ($template as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    public static function downloadCSV(): void
    {
        $csv = self::generateCSV();
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="product_template.csv"');
        header('Content-Length: ' . strlen($csv));
        
        echo $csv;
    }

    public static function generateJSONSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'item_name' => [
                    'type' => 'string',
                    'description' => 'The name of the product',
                    'example' => 'Sample Product 1'
                ],
                'image_name' => [
                    'type' => 'string',
                    'description' => 'Filename of the product image',
                    'example' => 'product1.jpg'
                ],
                'price' => [
                    'type' => 'number',
                    'description' => 'Product price',
                    'example' => 19.99
                ],
                'description' => [
                    'type' => 'string',
                    'description' => 'Product description',
                    'example' => 'Description for product 1'
                ],
                'assets' => [
                    'type' => 'object',
                    'description' => 'Additional product attributes as JSON',
                    'example' => [
                        'color' => 'blue',
                        'size' => 'medium',
                        'weight' => '500g'
                    ]
                ]
            ],
            'required' => ['item_name']
        ];
    }
}

