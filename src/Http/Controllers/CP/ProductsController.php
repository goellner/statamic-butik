<?php

namespace Jonassiewertsen\StatamicButik\Http\Controllers\CP;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Jonassiewertsen\StatamicButik\Blueprints\ProductBlueprint;
use Jonassiewertsen\StatamicButik\Http\Controllers\CpController;
use Jonassiewertsen\StatamicButik\Http\Models\Category;
use Jonassiewertsen\StatamicButik\Http\Models\Product;
use Statamic\CP\Column;

class ProductsController extends CpController
{
    public function index()
    {
        $this->authorize('index', Product::class);

        $products = Product::all()->map(function ($product) {
            return [
                'available'       => $product->available,
                'title'           => $product->title,
                'slug'            => $product->slug,
                'stock'           => $product->stock,
                'stock_unlimited' => $product->stock_unlimited,
                'images'          => $product->images[0] ?? null,
                'description'     => $product->description,
                'price'           => $product->price,
                'cp_route'        => config('statamic.cp.route', 'cp'),
                'edit_url'        => $product->editUrl,
                'deleteable'      => auth()->user()->can('delete', $product),
            ];
        })->values();

        return view('butik::cp.products.index', [
            'products' => $products,
            'columns'  => [
                Column::make('available')->label(''),
                Column::make('title')->label(__('butik::product.title')),
                Column::make('base_price')->label(__('butik::product.base_price')),
                Column::make('stock_unlimited')->label(__('butik::product.stock_unlimited')),
                Column::make('stock')->label(__('butik::product.stock')),
                Column::make('price')->label(__('butik::product.price')),
                Column::make('slug')->label(__('butik::product.slug')),
            ],
        ]);
    }

    public function create()
    {
        $this->authorize('create', Product::class);

        $blueprint = new ProductBlueprint();
        $fields    = $blueprint()->fields()->preProcess();

        return view('butik::cp.products.create', [
            'blueprint' => $blueprint()->toPublishArray(),
            'values'    => $fields->values(),
            'meta'      => $fields->meta(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('store', Product::class);

        $blueprint = new ProductBlueprint();
        $fields    = $blueprint()->fields()->addValues($request->all());
        $fields->validate();
        $values = $fields->process()->values();
        Product::create($values->toArray());
    }

    public function edit(Product $product)
    {
        $this->authorize('edit', $product);

        $values    = $product->toArray();
        $blueprint = new ProductBlueprint();
        $fields    = $blueprint()->fields()->addValues($values)->preProcess();

        $categories = Category::orderBy('name')->get()->map(function ($category) use ($product) {
            return [
                'name'        => $category->name,
                'slug'        => $category->slug,
                'is_attached' => $category->isProductAttached($product),
            ];
        });

        return view('butik::cp.products.edit', [
            'blueprint'  => $blueprint()->toPublishArray(),
            'values'     => $fields->values(),
            'meta'       => $fields->meta(),
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $blueprint = new ProductBlueprint();
        $fields    = $blueprint()->fields()->addValues($request->all());
        $fields->validate();
        $values = $fields->process()->values();
        $product->update($values->toArray());

        // Clear the product cache, to automatically update anyones shopping cart
        Cache::forget("product:{$product->slug}");
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        $product->delete();
    }
}
