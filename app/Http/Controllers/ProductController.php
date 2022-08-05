<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        $allowedSorts = [
            'sku' => 'prod.sku',
            'status' => 'pstat.value',
            'qty' => 'stock.qty',
            'price' => 'price.value',
            'type_id' => 'prod.attribute_set_id',
            'created_at' => 'prod.created_at',
            'updated_at' => 'prod.updated_at'
        ];

        $this->validate(request(), [
            'sku' => 'nullable|alpha_dash',
            'status' => 'nullable|integer',
            'type_id' => 'nullable|integer',
            'is_in_stock' => 'nullable|in:0,1',
            'page' => 'nullable|integer',
            'limit' => 'nullable|integer',
            'sort' => 'nullable|string|in:' . implode(',', array_keys($allowedSorts)),
            'dir' => 'nullable|string|in:asc,desc'
        ]);

        // Filters
        $filters = [];
        $sku = request()->query('sku');
        $status = request()->query('status');
        $typeId = request()->query('type_id');
        $inStock = request()->query('is_in_stock');

        if (!empty($sku)) $filters['sku'] = "prod.sku LIKE '%$sku%'";
        if (!empty($status)) $filters['status'] = "status.value = $status";
        if (!empty($typeId)) $filters['type_id'] = "prod.attribute_set_id = $typeId";
        if (!empty($inStock)) $filters['is_in_stock'] = "stock.is_in_stock = $inStock";

        // pagination
        $page = (int) request()->query('page', 1);
        $limit = (int) request()->query('limit', 10);
        $sort = $allowedSorts[ request()->query('sort', 'created_at') ];
        $dir = request()->query('dir', 'desc');


        /** @var \App\Models\Vendor $vendor */
        $vendor = request()->user();

        $products = DB::table('catalog_product_entity', 'prod')
            ->select([
                'prod.sku AS sku',
                'pname.value AS name',
                'status.value AS status',
                'prod.attribute_set_id AS type_id',
                DB::raw('CAST(price.value AS UNSIGNED) AS price'),
                DB::raw('CAST(stock.qty AS UNSIGNED) AS qty'),
                'stock.is_in_stock AS is_in_stock',
                'prod.created_at AS created_at',
                'prod.updated_at AS updated_at',
            ])
            // Get Vendor
            ->leftJoin(
                'catalog_product_entity_int AS vendor',
                'vendor.entity_id', '=', 'prod.entity_id'
            )
            ->where([
                'vendor.store_id' => 0,
                'vendor.attribute_id' => DB::raw("(
                    SELECT attribute_id FROM eav_attribute
                    WHERE attribute_code = 'udropship_vendor'
                    AND entity_type_id = 4 LIMIT 1
                )")
            ])
            // Get Name
            ->leftJoin(
                'catalog_product_entity_varchar AS pname',
                'pname.entity_id', '=', 'prod.entity_id'
            )
            ->where([
                'pname.store_id' => 0,
                'pname.attribute_id' => DB::raw("(
                    SELECT attribute_id FROM eav_attribute
                    WHERE attribute_code = 'name'
                    AND entity_type_id = 4 LIMIT 1
                )")
            ])
            // Get Price
            ->leftJoin(
                'catalog_product_entity_decimal AS price',
                'price.entity_id', '=', 'prod.entity_id'
            )
            ->where([
                'price.store_id' => 0,
                'price.attribute_id' => DB::raw("(
                    SELECT attribute_id FROM eav_attribute
                    WHERE attribute_code = 'price'
                    AND entity_type_id = 4 LIMIT 1
                )")
            ])
            // Get Status
            ->leftJoin(
                'catalog_product_entity_int AS status',
                'status.entity_id', '=', 'prod.entity_id'
            )
            ->where([
                'status.store_id' => 0,
                'status.attribute_id' => DB::raw("(
                    SELECT attribute_id FROM eav_attribute
                    WHERE attribute_code = 'status'
                    AND entity_type_id = 4 LIMIT 1
                )")
            ])
            // Get Stock
            ->leftJoin(
                'cataloginventory_stock_item AS stock',
                'stock.product_id', '=', 'prod.entity_id'
            )
            ->where('stock.stock_id', 1)
            // Get Set Type
            ->leftJoin(
                'eav_attribute_set AS type',
                'type.attribute_set_id', '=', 'prod.attribute_set_id'
            )
            ->where('type.entity_type_id', 4)
        ;

        $products->where('vendor.value', $vendor->vendor_id);
        $products->where('type.sort_order', '<', 200);
        $products->whereNotNull('price.value');

        foreach ($filters as $filter) {
            $products->whereRaw($filter);
        }

        $products->orderBy($sort, $dir);

        $data = $products->paginate($limit);
        $meta = [
            'page' => $page,
            'limit' => $limit,
            'sort' => array_search($sort, $allowedSorts),
            'dir' => $dir,
            'total' => $data->total(),
            'filter' => []
        ];

        if (!empty($sku)) $meta['filter']['sku'] = $sku;
        if (!empty($status)) $meta['filter']['status'] = $status;
        if (!empty($typeId)) $meta['filter']['type_id'] = $typeId;
        if (!empty($inStock)) $meta['filter']['is_in_stock'] = $inStock;

        return response()->json([
            'success' => true,
            'message' => 'ok',
            'data' => $data->items(),
            'meta' => $meta
        ]);
    }

    public function product($sku, Request $request, string $message = null)
    {
        $vendor = $request->user();
        $product = Product::whereSku($sku)->first();

        if (!$product || !$product->entity_id) {
            return response()->json([
                'success' => false,
                'message' => 'Product with SKU: '.$sku.' not found.'
            ], 404);
        }

        if ((int)$vendor->vendor_id !== (int)$product->vendor_id) {
            return response()->json([
                'success' => false,
                'message' => 'This product is not yours.'
            ], 404);
        }

        return response()->json([
            'success' => false,
            'message' => $message ?: 'ok',
            'data' => $product->toArray()
        ]);
    }

    public function crproduct(Request $request)
    {
        $sku = $request->input('sku');
        $setId = $request->input('type_id');
        $product = Product::bySku($sku);
        /** @var Vendor $user */
        $user = $request->user();

        if ($product && $product->exists && intval($product->vendor_id) !== intval($user->vendor_id)) {
            return response()->json([
                'success' => false,
                'message' => 'The product SKU: ' . $sku . ' is available and not your product.'
            ], 404);
        }

        if (!$product && empty($setId)) {
            return response()->json([
                'success' => false,
                'message' => 'Product type ID is required when creating new product.'
            ], 412);
        }

        if (empty($setId) && $product && $product->attribute_set_id) {
            $setId = (int) $product->attribute_set_id;
        }

        $data = [];
        $rootAttributes = [
            'sku',
            'name',
            'price',
            'status',
            'weight'
        ];
        $customAttributes = [];
        $extensionAttributes = [];

        foreach ($rootAttributes as $rootAttr) {
            if ($value = $request->input($rootAttr)) {
                $data[$rootAttr] = $value;
            }
        }

        if (!$product) {
            $data['visibility'] = 4;
            $data['type_id'] = 'simple';
            $data['attribute_set_id'] = $setId;
            $customAttributes[] = [
                'attribute_code' => 'udropship_vendor',
                'value' => $user->vendor_id
            ];
        }

        if ($qty = $request->input('qty')) {
            $extensionAttributes['stock_item'] = [
                'qty' => (int)$qty,
                'is_in_stock' => (int)$qty > 0,
                'manage_stock' => true,
                'use_config_manage_stock' => 1
            ];
        }

        if (($tiers = $request->input('tier_price')) && is_array($tiers) && count($tiers)) {
            $data['tier_prices'] = [];
            foreach ($tiers as $tier) {
                if (isset($tier['qty']) && isset($tier['value'])) {
                    $data['tier_prices'][] = [
                        'qty' => intval($tier['qty']),
                        'value' => intval($tier['value']),
                        'customer_group_id' => 32000,
                        'extension_attributes' => ['website_id' => 0]
                    ];
                }
            }
        }

        $attributes = array_filter(
            (new Product)->getAttributeFields($setId),
            fn ($attr) => !in_array($attr, $rootAttributes)
        );

        foreach ($attributes as $attr) {
            if ($value = $request->input($attr)) {
                if ($attr == 'category_ids' && !is_array($value)) {
                    $value = explode(',', $value);
                }
                $customAttributes[] = [
                    'attribute_code' => $attr,
                    'value' => $value
                ];
            }
        }

        if (count($extensionAttributes)) {
            $data['extension_attributes'] = $extensionAttributes;
        }
        if (count($customAttributes)) {
            $data['custom_attributes'] = $customAttributes;
        }

        $successMsg = $product ? 'updated' : 'created';
        $productData = ['product' => $data];

        if ($product) {
            unset($productData['product']['sku']);
            $response = $this->httpPut('/products/'.$sku, $productData);
        } else {
            $response = $this->httpPost('/products', $productData);
        }

        if ($response->ok()) {
            $product = Product::bySku($sku);

            if ($product && !empty($product->entity_id)) {
                DB::table('udropship_vendor_product_assoc')
                    ->insert([
                        'vendor_id' => $user->vendor_id,
                        'product_id' => $product->entity_id,
                        'is_attribute' => 1,
                        'is_udmulti' => 0,
                        'as_parent' => 0,
                        'as_parent_udmulti' => 1
                    ]);
            }

            return $this->product($sku, $request, $successMsg);
        }

        return response()->json([
            'success' => $response->ok(),
            'message' => $response->json('message', 'Failed!')
        ], 412);
    }

    public function categories()
    {
        $query = DB::table('catalog_category_entity', 'p')
            // Name
            ->leftJoin(
                'catalog_category_entity_varchar AS n',
                'p.entity_id', '=', 'n.entity_id'
            )
            ->where('n.attribute_id', DB::raw("(
                SELECT attribute_id FROM eav_attribute
                WHERE attribute_code = 'name'
                AND entity_type_id = 3 LIMIT 1
            )"))
            ->where('n.store_id', 0)
            // Status
            ->leftJoin(
                'catalog_category_entity_int AS s',
                's.entity_id', '=', 'n.entity_id'
            )
            ->where('s.attribute_id', DB::raw("(
                SELECT attribute_id FROM eav_attribute
                WHERE attribute_code = 'is_active'
                AND entity_type_id = 3 LIMIT 1
            )"))
            ->where('s.store_id', 0)
            // Parent
            ->leftJoin(
                'catalog_category_entity AS pp',
                'p.parent_id', '=', 'pp.entity_id'
            )
            // Parent Name
            ->leftJoin(
                'catalog_category_entity_varchar AS pn',
                'pn.entity_id', '=', 'pp.entity_id'
            )
            ->where('pn.attribute_id', DB::raw("(
                SELECT attribute_id FROM eav_attribute
                WHERE attribute_code = 'name'
                AND entity_type_id = 3 LIMIT 1
            )"))
            ->where('pn.store_id', 0);

        $query->where('s.value', 1);
        $query->where('p.level', '>=', 2);
        $query->orderBy(DB::raw(2));

        $data = $query->get([
            'p.entity_id AS value',
            DB::raw("IF(pp.level = 1, n.value, concat_ws (' > ', pn.value, n.value)) AS `label`")
        ]);

        return response()->json([
            'success' => true,
            'message' => 'ok',
            'data' => $data
        ]);
    }

    public function types()
    {
        $config = DB::table('core_config_data')
            ->where('path', 'udprod/general/type_of_product')
            ->value('value');

        $setIds = [];
        $config = @json_decode($config);

        if (count($config) && !empty($config[0]->attribute_set)) {
            $setIds = array_map(fn($n) => intval($n), $config[0]->attribute_set);
        }

        $query = DB::table('eav_attribute_set')
            ->select([
                'attribute_set_id AS id',
                'attribute_set_name AS name'
            ])
            ->where('entity_type_id', 4)
            ->where('sort_order', 0)
            ->orderBy('attribute_set_id')
        ;

        if (count($setIds) > 0) {
            $query->whereIn('attribute_set_id', $setIds);
        }

        return response()->json([
            'success' => true,
            'message' => 'ok',
            'data' => $query->get()
        ]);
    }

    public function attributes($id, Product $product)
    {
        $attributeIds = $product->getAttributeFields($id, 'eav.attribute_id');

        $response = $this->httpGet('/products/attributes', [
            'searchCriteria' => [
                'filterGroups' => [
                    [
                        'filters' => [
                            [
                                'field' => 'attribute_id',
                                'value' => implode(',', $attributeIds),
                                'condition_type' => 'in'
                            ]
                        ]
                    ]
                ]
            ],
            'fields' => 'items['.
                implode(',', [
                    'attribute_code',
                    'default_frontend_label',
                    'note',
                    'frontend_input',
                    'is_required',
                    'options'
                ])
            .']'
        ]);

        return response()->json([
            'success' => $response->ok(),
            'message' => $response->json('message', 'ok'),
            'data' => $response->ok() ? $response->json('items') : null
        ]);
    }
}
