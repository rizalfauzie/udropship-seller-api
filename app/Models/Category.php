<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * \App\Models\Category
 *
 * @property int $entity_id Entity ID
 * @property int $attribute_set_id Attribute Set ID
 * @property int $parent_id Parent Category ID
 * @property \Illuminate\Support\Carbon $created_at Creation Time
 * @property \Illuminate\Support\Carbon $updated_at Update Time
 * @property string $path Tree Path
 * @property int $position Position
 * @property int $level Tree Level
 * @property int $children_count Child Count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product[] $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereAttributeSetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereChildrenCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Category extends Model
{
    /**
     * @var string
     */
    protected $table = 'catalog_category_entity';

    /**
     * @var string
     */
    protected $primaryKey = 'entity_id';

    /**
     * {@inheritDoc}
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            return false;
        });
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'catalog_category_product',
            'category_id',
            'product_id'
        );
    }
}
