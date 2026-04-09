<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Inventory\Models\Product;
use App\Modules\Inventory\Models\Category;
use Faker\Factory as Faker;

class FakeProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('pt_BR');

        // Garantir que exista pelo menos uma categoria geral para vincular os produtos
        $category = Category::firstOrCreate(
            ['name' => 'Categoria Geral (Fake)']
        );

        // Termos comerciais para simular produtos de estoque/PDV
        $productTypes = ['Teclado', 'Mouse', 'Monitor', 'Cabo', 'Fonte', 'Roteador', 'Headset', 'Memória RAM', 'SSD', 'Placa Mãe'];
        $brands = ['Logitech', 'Razer', 'Dell', 'Corsair', 'Kingston', 'Duex', 'Intel', 'AMD', 'Gigabyte', 'Asus'];

        for ($i = 0; $i < 50; $i++) {
            $type = $faker->randomElement($productTypes);
            $brand = $faker->randomElement($brands);
            $name = $type . ' ' . $brand . ' ' . $faker->word;
            
            // Gerar SKU unico baseado no loop para facilitar o UpdateOrCreate
            $sku = 'FAKE-PRD-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            $barcode = $faker->ean13;

            // Preço Custo entre 10 e 500 reais em centavos
            $costCents = $faker->numberBetween(1000, 50000);
            
            // Margem entre 20% e 80% sobre o custo para venda final
            $margin = $faker->randomFloat(2, 1.2, 1.8);
            $saleCents = (int) ($costCents * $margin);
            
            // Preço clube entre custo e venda
            $clubCents = (int) ($costCents * 1.1);

            Product::updateOrCreate(
                ['sku' => $sku], // Condição de exclusividade (Evitar duplicidade)
                [
                    'name' => mb_strtoupper($name),
                    'barcode' => $barcode,
                    'description' => $faker->sentence(10),
                    'category_id' => $category->id,
                    'price_cents_cost' => $costCents,
                    'price_cents_sale' => $saleCents,
                    'price_cents_club' => $clubCents,
                    'status' => 1
                ]
            );
        }

        $this->command->info('50 Produtos Fake foram criados ou atualizados com sucesso usando SKU Dinâmicos (FAKE-PRD-0001 a 0050)!');
    }
}
