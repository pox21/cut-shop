<?php

    namespace Database\Seeders;

    // use Illuminate\Database\Console\Seeds\WithoutModelEvents;
    use Database\Factories\BrandFactory;
    use Database\Factories\CategoryFactory;
    use Database\Factories\OptionFactory;
    use Database\Factories\OptionValueFactory;
    use Database\Factories\PropertyFactory;
    use Domain\Product\Models\Product;
    use Illuminate\Database\Seeder;

    class DatabaseSeeder extends Seeder
    {
        public function run(): void
        {
            BrandFactory::new()->count(20)->create();

            $properties = PropertyFactory::new()->count(10)->create();

            OptionFactory::new()->count(2)->create();

            $optionValues = OptionValueFactory::new()->count(10)->create();

            CategoryFactory::new()->count(10)
                ->has(
                    Product::factory(10)
                        ->hasAttached($optionValues)
                        ->hasAttached($properties, function () {
                            return ['value' => ucfirst(fake()->word())];
                        })
                )->create();
        }
    }
