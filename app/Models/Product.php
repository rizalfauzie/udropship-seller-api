<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

/**
 * \App\Models\Product
 *
 * @property int $entity_id Entity ID
 * @property int $attribute_set_id Attribute Set ID
 * @property int $type_id Type ID
 * @property string|null $sku SKU
 * @property \Illuminate\Support\Carbon $created_at Creation Time
 * @property \Illuminate\Support\Carbon $updated_at Update Time
 * @property-read mixed $category_ids
 * @property-read mixed $id
 * @property-read mixed $is_in_stock
 * @property-read mixed $name
 * @property-read mixed $price
 * @property-read mixed $qty
 * @property-read mixed $tier_price
 * @property-read mixed $vendor_id
 * @property-read mixed $weight
 * @property-read \App\Models\Stock|null $stock
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TierPrice[] $tiers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Category[] $categories
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereAttributeSetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Product extends Model
{
    /**
     * @var string
     */
    protected $table = 'catalog_product_entity';

    /**
     * @var string
     */
    protected $primaryKey = 'entity_id';

    /**
     * @var string[]
     */
    protected $guarded = ['*'];

    /**
     * @var string[]
     */
    protected $hidden = [
        'entity_id',
        'has_options',
        'required_options',
        'attribute_set_id',
        'vendor_id',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'name',
        'price',
        'qty',
        'is_in_stock',
        'weight',
        'category_ids',
        'tier_price'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'type_id' => 'int',
        'price' => 'int',
        'weight' => 'float',
        'category_ids' => 'array',
        'tier_price' => 'array',
    ];

    /**
     * @var \stdClass
     */
    protected $stockItem;

    /**
     * @var string[]
     */
    protected $_excludes = [
        'udropship_vendor',
        'price_type',
        'price_view',
        'weight_type',
        'attribute_id',
        'vendor_region',
        'weltpixel_hover_image',
        'google_product_category',
        'quantity_and_stock_status',
        'is_simplified_bundle',
        'zeals_tracking',
        'swatch_image',
        'sku_type',
        'merchant_center_category',
        'content_constructor_content',
        'layout_update_xml_backup',
        'cms_pages_ids'
    ];

    /**
     * @var array
     */
    public static $eavs = [];

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

    /**
     * @param string $sku
     * @return Product
     */
    public static function bySku($sku)
    {
        return Product::whereSku($sku)->first();
    }

    public function stock(): HasOne
    {
        return $this->hasOne(
            Stock::class,
            'product_id',
            'entity_id'
        );
    }

    public function tiers(): HasMany
    {
        return $this->hasMany(
            TierPrice::class,
            'entity_id',
            'entity_id'
        );
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'catalog_category_product',
            'product_id',
            'category_id'
        );
    }

    public function getIdAttribute()
    {
        if (empty($this->attributes['id'])) {
            $this->attributes['id'] = (int) $this->entity_id;
        }
        return $this->attributes['id'];
    }

    public function getNameAttribute()
    {
        if (empty($this->attributes['name'])) {
            $this->attributes['name'] = $this->_getAttrValue('name');
        }
        return $this->attributes['name'];
    }

    public function getPriceAttribute()
    {
        if (empty($this->attributes['price'])) {
            $this->attributes['price'] = (int) $this->_getAttrValue('price');
        }
        return $this->attributes['price'];
    }

    public function getTypeIdAttribute()
    {
        return (int) $this->attributes['attribute_set_id'];
    }

    public function getWeightAttribute()
    {
        if (empty($this->attributes['weight'])) {
            $this->attributes['weight'] = (float) $this->_getAttrValue('weight');
        }
        return $this->attributes['weight'];
    }

    public function getCategoryIdsAttribute()
    {
        if (empty($this->attributes['category_ids'])) {
            $this->attributes['category_ids'] = $this->categories->pluck('entity_id')->all();
        }
        return $this->attributes['category_ids'];
    }

    public function getQtyAttribute()
    {
        if (empty($this->attributes['qty'])) {
            $stockItem = $this->stock;
            $this->attributes['qty'] = $stockItem ? (int)$stockItem->qty : 0;
        }
        return $this->attributes['qty'];
    }

    public function getIsInStockAttribute()
    {
        if (empty($this->attributes['is_in_stock'])) {
            $stockItem = $this->stock;
            $this->attributes['is_in_stock'] = $stockItem ? (int)$stockItem->is_in_stock : 0;
        }
        return $this->attributes['is_in_stock'];
    }

    public function getVendorIdAttribute()
    {
        if (empty($this->attributes['vendor_id'])) {
            $this->attributes['vendor_id'] = (int) $this->_getAttrValue('udropship_vendor');
        }
        return $this->attributes['vendor_id'];
    }

    public function getTierPriceAttribute()
    {
        if (empty($this->attributes['tier_price'])) {
            $this->attributes['tier_price'] = $this->tiers()->get(['qty', 'value'])->all();
        }
        return $this->attributes['tier_price'];
    }

    private function _getAttrValue($code)
    {
        $attr = $this->_getAttrEav($code);
        if ($attr === null || $attr->backend_type == 'static') {
            return;
        }
        $table = 'catalog_product_entity_'.$attr->backend_type;
        $value = DB::table($table)
            ->where('attribute_id', $attr->attribute_id)
            ->where('entity_id', $this->entity_id)
            ->where('store_id', 0)
            ->value('value');
        $value = $value ?: $attr->default_value;

        switch ($attr->backend_type) {
            case 'int':
                $value = !is_null($value) ? intval($value) : null; break;
            case 'decimal':
                $value = !is_null($value) ? floatval($value) : null; break;
        }

        return $value;
    }

    private function _getAttrEav($code)
    {
        if (isset(static::$eavs[$code])) {
            return static::$eavs[$code];
        }

        static::$eavs[$code] = DB::table('eav_attribute')
            ->where('attribute_code', $code)
            ->where('entity_type_id', 4)
            ->first([
                'attribute_id',
                'backend_type',
                'frontend_label',
                'frontend_input',
                'default_value'
            ]);

        return static::$eavs[$code];
    }

    public function getExcludeFields()
    {
        $fields = DB::table('core_config_data')
            ->where('path', 'udprod/form/exclude_fields')
            ->value('value');

        return array_merge(
            array_map(
                fn ($f) => str_replace('product.', '', $f),
                explode(',', $fields)
            ),
            $this->_excludes
        );
    }

    public function getAttributeFields($typeId = null, $column = 'eav.attribute_code')
    {
        $excludes = $this->getExcludeFields();
        $setId = empty($typeId) ? $this->type_id : $typeId;

        $attributes = DB::table('eav_entity_attribute', 'eavattr')
            ->leftJoin(
                'eav_attribute_group AS eavgroup',
                'eavgroup.attribute_group_id', '=', 'eavattr.attribute_group_id'
            )
            ->leftJoin(
                'eav_attribute AS eav',
                'eav.attribute_id', '=', 'eavattr.attribute_id'
            )
            ->where('eavattr.attribute_set_id', $setId)
            ->whereNotNull('eav.frontend_label')
            ->whereNotNull('eav.frontend_input')
            ->whereNotIn('eavgroup.attribute_group_code', [
                'weltpixel-options', 'autosettings', 'bestsellers', 'bundle-items',
                'design', 'dropship-tier-shipping', 'facebook-attribute-group',
                'gift-card-information', 'gift-options', 'positive-indicators',
                'schedule-design-update', 'daily-deal'
            ])
            ->whereNotIn('eav.frontend_input', ['gallery'])
            ->where('eav.attribute_code', 'not like', 'udropship\_%')
            ->where('eav.attribute_code', 'not like', 'bestseller\_%')
            ->where('eav.attribute_code', 'not like', 'msrp%')
            ->where('eav.attribute_code', 'not like', 'aw\_%')
            ->where('eav.attribute_code', 'not like', 'og\_%')
            ->where('eav.attribute_code', 'not like', 'ts\_%')
            ->whereNotIn('eav.attribute_code', $excludes)
            ->orderBy('eavgroup.sort_order')
            ->orderBy('eavattr.sort_order')
        ;

        return $attributes->pluck($column)->all();
    }

    public function toArray()
    {
        $images = ['image', 'small_image', 'thumbnail'];
        $imgUrl = '/media/catalog/product';
        $extra = $this->getAttributeFields();
        $attrs = [
            // 'id',
            'sku', 'name', 'type_id', 'price',
            'qty', 'is_in_stock',
            'weight',
            'created_at', 'updated_at',
            'category_ids',
            'tier_price',
        ];

        $data = [];
        foreach (array_merge($attrs, $extra) as $key) {
            $value = $this->{$key};
            if ($value === null) {
                $value = $this->_getAttrValue($key);
            }
            if (in_array($key, $extra)) {
                if (in_array($key, $images)) {
                    $value = $imgUrl . $value;
                } elseif (is_numeric($value)) {
                    $value = (int) $value;
                }
            }
            $data[$key] = $value;
        }

        return $data;
    }
}
