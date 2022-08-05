<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * \App\Models\TierPrice
 *
 * @property int $value_id Value ID
 * @property int $entity_id Entity ID
 * @property int $all_groups Is Applicable To All Customer Groups
 * @property int $customer_group_id Customer Group ID
 * @property int $qty QTY
 * @property int $value Value
 * @property int $website_id Website ID
 * @property string|null $percentage_value Percentage value
 * @property-read \App\Models\Product $product
 * @method static Builder|TierPrice newModelQuery()
 * @method static Builder|TierPrice newQuery()
 * @method static Builder|TierPrice query()
 * @method static Builder|TierPrice whereAllGroups($value)
 * @method static Builder|TierPrice whereCustomerGroupId($value)
 * @method static Builder|TierPrice whereEntityId($value)
 * @method static Builder|TierPrice wherePercentageValue($value)
 * @method static Builder|TierPrice whereQty($value)
 * @method static Builder|TierPrice whereValue($value)
 * @method static Builder|TierPrice whereValueId($value)
 * @method static Builder|TierPrice whereWebsiteId($value)
 * @mixin \Eloquent
 */
class TierPrice extends Model
{
    /**
     * @var string
     */
    protected $table = 'catalog_product_entity_tier_price';

    /**
     * @var string
     */
    protected $primaryKey = 'value_id';

    /**
     * @var bool|array
     */
    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $casts = [
        'qty' => 'int',
        'value' => 'int',
    ];

    /**
     * {@inheritDoc}
     */
    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('entity_id');
        });

        static::addGlobalScope('website', function (Builder $builder) {
            $builder->where('website_id', 0);
        });

        static::addGlobalScope('defaultGroup', function (Builder $builder) {
            $builder->where('all_groups', 1);
        });
    }

    /**
     * Get mitra paernt associated with this client.
     *
     * @return void
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'entity_id', 'entity_id');
    }
}
