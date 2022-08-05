<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

/**
 * \App\Models\Vendor
 *
 * @property int $vendor_id Vendor_id
 * @property string $vendor_name Vendor_name
 * @property string $vendor_attn Vendor_attn
 * @property string $email Email
 * @property string|null $telephone Telephone
 * @property string|null $fax Fax
 * @property string $street Street
 * @property string $city City
 * @property string|null $zip Zip
 * @property string $country_id Country_id
 * @property int|null $region_id Region_id
 * @property string|null $region Region
 * @property int $billing_use_shipping Billing_use_shipping
 * @property string $billing_vendor_attn Billing_vendor_attn
 * @property string $billing_email Billing_email
 * @property string|null $billing_telephone Billing_telephone
 * @property string|null $billing_fax Billing_fax
 * @property string $billing_street Billing_street
 * @property string $billing_city Billing_city
 * @property string|null $billing_zip Billing_zip
 * @property string $billing_country_id Billing_country_id
 * @property int|null $billing_region_id Billing_region_id
 * @property string|null $billing_region Billing_region
 * @property string $status Status
 * @property string|null $password Password
 * @property string|null $password_hash Password_hash
 * @property string|null $password_enc Password_enc
 * @property string|null $carrier_code Carrier_code
 * @property int $notify_new_order Notify_new_order
 * @property string $label_type Label_type
 * @property int $test_mode Test_mode
 * @property string $handling_fee Handling_fee
 * @property string $ups_shipper_number Ups_shipper_number
 * @property string $custom_data_combined Custom_data_combined
 * @property string $custom_vars_combined Custom_vars_combined
 * @property int $email_template Email_template
 * @property string|null $url_key Url_key
 * @property string|null $random_hash Random_hash
 * @property \Illuminate\Support\Carbon|null $created_at Created_at
 * @property int $use_handling_fee Use_handling_fee
 * @property int $allow_shipping_extra_charge Allow_shipping_extra_charge
 * @property string $default_shipping_extra_charge_suffix Default_shipping_extra_charge_suffix
 * @property string $default_shipping_extra_charge_type Default_shipping_extra_charge_type
 * @property string $default_shipping_extra_charge Default_shipping_extra_charge
 * @property int $is_extra_charge_shipping_default Is_extra_charge_shipping_default
 * @property int|null $default_shipping_id Default_shipping_id
 * @property int $use_rates_fallback Use_rates_fallback
 * @property int $notify_lowstock Notify_lowstock
 * @property string $notify_lowstock_qty Notify_lowstock_qty
 * @property string $batch_export_orders_method batch_export_orders_method
 * @property string $batch_export_orders_schedule batch_export_orders_schedule
 * @property string $batch_import_orders_method batch_import_orders_method
 * @property string $batch_import_orders_schedule batch_import_orders_schedule
 * @property string $batch_import_inventory_method batch_import_inventory_method
 * @property string $batch_import_inventory_schedule batch_import_inventory_schedule
 * @property string|null $batch_import_inventory_ts batch_import_inventory_ts
 * @property string|null $batch_import_orders_ts batch_import_orders_ts
 * @property string|null $tiercom_rates tiercom_rates
 * @property string|null $tiercom_fixed_rule tiercom_fixed_rule
 * @property string|null $tiercom_fixed_rates tiercom_fixed_rates
 * @property string|null $tiercom_fixed_calc_type tiercom_fixed_calc_type
 * @property int|null $tiership_use_v2_rates tiership_use_v2_rates
 * @property int|null $vacation_mode vacation_mode
 * @property string|null $vacation_end vacation_end
 * @property string|null $vacation_message vacation_message
 * @property string|null $longitude vendor longitude
 * @property string|null $latitude vendor latitude
 * @property string|null $origin_code vendor origin code
 * @property string|null $jne_method_option JNE method option
 * @property string|null $jne_method_selected JNE method selected
 * @property int|null $position position
 * @property string|null $shipperid_method_option shipperid method option
 * @property string|null $shipperid_method_selected shipperid method selected
 * @property int|null $shipperid_is_free_shipping shipperid is free shipping
 * @property string|null $shipperid_free_shipping shipperid free shipping
 * @property string|null $origin_coord Origin Coordinates
 * @property string|null $origin_id Origin ID
 * @property string|null $tawk_script Tawk.to Script
 * @property string|null $trucking_origin JNE Trucking Origin
 * @property int|null $type_vendor Type Vendor
 * @property string|null $website Website
 * @property string|null $vendor_doc_legal Vendor Doc Legal
 * @property string|null $account_holder_name Account Holder Name
 * @property string|null $bank_name Bank Name
 * @property string|null $account_number
 * @property int|null $sent_email_activation Sent Email Activation
 * @property string|null $banner_toko Banner Toko
 * @property string|null $deskripsi_toko Deskripsi Toko
 * @property string|null $lokasi_toko Lokasi Toko
 * @property string|null $jam_buka_toko_dari Jam Buka Toko Dari
 * @property string|null $jam_buka_toko_sampai Jam Buka Toko Dari
 * @property string|null $mobile_apps_tawk_script Mobile Apps Tawk.to Script
 * @property string|null $popaket_method_selected Popaket Method Selected
 * @property string|null $popaket_method_option Popaket Method Option
 * @property string|null $popaket_free_shipping Popaket Free Shipping
 * @property int|null $popaket_is_free_shipping Popaket Free Shipping
 * @property int|null $popaket_insurance Popaket Insurance
 * @property string|null $webhook Webhook
 * @property int|null $enable_api Enable API
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor query()
 * @mixin \Eloquent
 */
class Vendor extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * @var string
     */
    protected $table = 'udropship_vendor';

    /**
     * @var string
     */
    protected $primaryKey = 'vendor_id';

    /**
     * @var string[]
     */
    public $timestamps = ['created_at'];

    /**
     * @var string
     */
    const UPDATED_AT = null;

    /**
     * @var string[]
     */
    protected $guarded = ['*'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var string[]
     */
    protected $hidden = [
        'password_hash',
        'random_hash',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var string[]
     */
    protected $casts = [
        'vendor_id' => 'int'
    ];

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
}
