<?php

    namespace Domain\Product\Models;

    use App\Jobs\ProductJsonProperties;
    use Domain\Catalog\Facades\Sorter;
    use Domain\Catalog\Models\Brand;
    use Domain\Catalog\Models\Category;
    use Illuminate\Contracts\Database\Query\Builder;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\BelongsToMany;
    use Illuminate\Pipeline\Pipeline;
    use Support\Casts\PriceCast;
    use Support\Traits\Models\HasSlug;
    use Support\Traits\Models\HasThumbnail;

    use function app;
    use function filters;
    use function now;

    class Product extends Model
    {
        use HasFactory;
        use HasSlug;
        use HasThumbnail;

        protected $fillable = [
            'title',
            'slug',
            'brand_id',
            'price',
            'thumbnail',
            'on_home_page',
            'sorting',
            'text',
            'json_properties'
        ];

        protected $casts = [
            'price' => PriceCast::class,
            'json_properties' => 'array'
        ];

        protected static function boot()
        {
            parent::boot();

            static::created(function (Product $product) {
                ProductJsonProperties::dispatch($product)
                    ->delay(now()->addSecond(10));
            });
        }

        protected function thumbnailDir(): string
        {
            return 'products';
        }

        public function scopeFiltered(Builder $query)
        {
            return app(Pipeline::class)
                ->send($query)
                ->through(filters())
                ->thenReturn();
        }

        public function scopeSorted(Builder $query)
        {
            return Sorter::run($query);
//            return sorter()->run($query);
        }

        public function scopeHomePage(Builder $query)
        {
            $query->where('on_home_page', true)
                ->orderBy('sorting')
                ->limit(6);
        }

        public function brand(): BelongsTo
        {
            return $this->belongsTo(Brand::class);
        }

        public function categories(): BelongsToMany
        {
            return $this->belongsToMany(Category::class);
        }

        public function properties(): BelongsToMany
        {
            return $this->belongsToMany(Property::class)
                ->withPivot('value');
        }

        public function optionValues(): BelongsToMany
        {
            return $this->belongsToMany(OptionValue::class);
        }


    }
