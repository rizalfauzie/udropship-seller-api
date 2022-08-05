<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * \App\Models\Stock
 *
 * @property int $item_id Item ID
 * @property int $product_id Product ID
 * @property int $stock_id Stock ID
 * @property string|null $qty Qty
 * @property string $min_qty Min Qty
 * @property int $use_config_min_qty Use Config Min Qty
 * @property int $is_qty_decimal Is Qty Decimal
 * @property int $backorders Backorders
 * @property int $use_config_backorders Use Config Backorders
 * @property string $min_sale_qty Min Sale Qty
 * @property int $use_config_min_sale_qty Use Config Min Sale Qty
 * @property string $max_sale_qty Max Sale Qty
 * @property int $use_config_max_sale_qty Use Config Max Sale Qty
 * @property int $is_in_stock Is In Stock
 * @property string|null $low_stock_date Low Stock Date
 * @property string|null $notify_stock_qty Notify Stock Qty
 * @property int $use_config_notify_stock_qty Use Config Notify Stock Qty
 * @property int $manage_stock Manage Stock
 * @property int $use_config_manage_stock Use Config Manage Stock
 * @property int $stock_status_changed_auto Stock Status Changed Automatically
 * @property int $use_config_qty_increments Use Config Qty Increments
 * @property string $qty_increments Qty Increments
 * @property int $use_config_enable_qty_inc Use Config Enable Qty Increments
 * @property int $enable_qty_increments Enable Qty Increments
 * @property int $is_decimal_divided Is Divided into Multiple Boxes for Shipping
 * @property int $website_id Website ID
 * @property-read \App\Models\Product $product
 * @method static Builder|Stock newModelQuery()
 * @method static Builder|Stock newQuery()
 * @method static Builder|Stock query()
 * @method static Builder|Stock whereBackorders($value)
 * @method static Builder|Stock whereEnableQtyIncrements($value)
 * @method static Builder|Stock whereIsDecimalDivided($value)
 * @method static Builder|Stock whereIsInStock($value)
 * @method static Builder|Stock whereIsQtyDecimal($value)
 * @method static Builder|Stock whereItemId($value)
 * @method static Builder|Stock whereLowStockDate($value)
 * @method static Builder|Stock whereManageStock($value)
 * @method static Builder|Stock whereMaxSaleQty($value)
 * @method static Builder|Stock whereMinQty($value)
 * @method static Builder|Stock whereMinSaleQty($value)
 * @method static Builder|Stock whereNotifyStockQty($value)
 * @method static Builder|Stock whereProductId($value)
 * @method static Builder|Stock whereQty($value)
 * @method static Builder|Stock whereQtyIncrements($value)
 * @method static Builder|Stock whereStockId($value)
 * @method static Builder|Stock whereStockStatusChangedAuto($value)
 * @method static Builder|Stock whereUseConfigBackorders($value)
 * @method static Builder|Stock whereUseConfigEnableQtyInc($value)
 * @method static Builder|Stock whereUseConfigManageStock($value)
 * @method static Builder|Stock whereUseConfigMaxSaleQty($value)
 * @method static Builder|Stock whereUseConfigMinQty($value)
 * @method static Builder|Stock whereUseConfigMinSaleQty($value)
 * @method static Builder|Stock whereUseConfigNotifyStockQty($value)
 * @method static Builder|Stock whereUseConfigQtyIncrements($value)
 * @method static Builder|Stock whereWebsiteId($value)
 * @mixin \Eloquent
 */
class Stock extends Model
{
    /**
     * @var string
     */
    protected $table = 'cataloginventory_stock_item';

    /**
     * @var string
     */
    protected $primaryKey = 'item_id';

    /**
     * @var bool|array
     */
    public $timestamps = false;

    /**
     * {@inheritDoc}
     */
    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('product_id');
        });

        static::addGlobalScope('website', function (Builder $builder) {
            $builder->where('website_id', 0);
        });

        static::addGlobalScope('defaultStock', function (Builder $builder) {
            $builder->where('stock_id', 1);
        });
    }

    /**
     * Get mitra paernt associated with this client.
     *
     * @return void
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'entity_id');
    }
}
