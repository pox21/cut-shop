<?php

    namespace App\Http\Controllers;

    use Domain\Catalog\Models\Category;
    use Domain\Product\Models\Product;
    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Contracts\View\Factory;
    use Illuminate\Contracts\View\View;
    use Illuminate\Database\Eloquent\Builder;

    class CatalogController extends Controller
    {
        public function __invoke(?Category $category): Factory|View|Application
        {
            $categories = Category::query()
                ->select(['id', 'title', 'slug'])
                ->has('products')
                ->get();

            $products = Product::query()
                ->select(['id', 'title', 'slug', 'price', 'thumbnail', 'json_properties'])
                ->when(request('s'), function (Builder $query) {
                    $query->whereFullText(['title', 'text'], request('s'));
                })
                ->when($category->exists, function (Builder $query) use ($category) {
                    $query->whereRelation(
                        'categories',
                        'categories.id',
                        '=',
                        $category->id
                    );
                })
                ->filtered()
                ->sorted()
                ->paginate(6);

            return view('catalog.index', [
                'products' => $products,
                'categories' => $categories,
                'category' => $category
            ]);
        }
    }
