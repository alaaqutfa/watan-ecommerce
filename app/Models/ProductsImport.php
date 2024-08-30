<?php

namespace App\Models;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\User;
use App\Traits\PreventDemoModeChanges;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;
use Auth;
use Carbon\Carbon;
use Storage;

//class ProductsImport implements ToModel, WithHeadingRow, WithValidation
class ProductsImport implements ToCollection, WithHeadingRow, WithValidation, ToModel
{
    use PreventDemoModeChanges;

    private $rows = 0;

    public function collection(Collection $rows)
    {
        $canImport = true;
        $user = Auth::user();
        if ($user->user_type == 'seller' && addon_is_activated('seller_subscription')) {
            if ((count($rows) + $user->products()->count()) > $user->shop->product_upload_limit
                || $user->shop->package_invalid_at == null
                || Carbon::now()->diffInDays(Carbon::parse($user->shop->package_invalid_at), false) < 0
            ) {
                $canImport = false;
                flash(translate('Please upgrade your package.'))->warning();
            }
        }

        if ($canImport) {
            foreach ($rows as $row) {
                $approved = 1;
                if ($user->user_type == 'seller' && get_setting('product_approve_by_admin') == 1) {
                    $approved = 0;
                }   
                
                $slug = Str::slug($row['name']);

                $same_slug_count = Product::where('slug', 'LIKE', $slug . '%')->count();
                $slug_suffix = $same_slug_count ? '-' . ($same_slug_count + 1) : '';
                $slug .= $slug_suffix;

                $productId = Product::create([
                    'name' => $row['name'],
                    'description' => $row['description'],
                    // 'added_by' => $user->user_type == 'seller' ? 'seller' : 'admin',
                    'added_by' =>'seller',
                    // 'user_id' => $user->user_type == 'seller' ? $user->id : User::where('user_type', 'admin')->first()->id,
                    'user_id' => $row['user_id'],
                    'approved' => $approved,
                    'category_id' => $row['category_id'],
                    //'brand_id' => $row['brand_id'],
                    // 'video_provider' => $row['video_provider'],
                    // 'video_link' => $row['video_link'],
                    //'tags' => $row['tags'],
                    'unit_price' => $row['unit_price'],
                    'weight'=> $row['weight'],
                    'unit' => $row['unit'],
                    // 'meta_title' => $row['meta_title'],
                    // 'meta_description' => $row['meta_description'],
                    'colors' => isset($row['colors']) && !empty($row['colors']) ? $row['colors'] : json_encode([]),
                    'est_shipping_days' => $row['est_shipping_days'],
                    'attributes' => isset($row['attributes']) && !empty($row['attributes']) ? $row['attributes'] : json_encode([]),                    
                     'choice_options' => isset($row['choice_options']) && !empty($row['choice_options']) ? $row['choice_options'] : json_encode([]),
                    // 'variations' => json_encode(array()),
                    // 'slug' => preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', strtolower($row['slug']))) . '-' . Str::random(5),
                    'slug' => $slug,
                    'thumbnail_img' => $row['thumbnail_img'],
                    'photos' => $row['photos'],
                    'variant_product' => (isset($row['colors']) && !empty($row['colors'])) || 
                         (isset($row['attributes']) && !empty($row['attributes'])) ? '1' : '0',
                    'current_stock'=>$row['current_stock'],
                ]);
                ProductStock::create([
                    'product_id' => $productId->id,
                    'qty' => $row['current_stock'],
                    'price' => $row['unit_price'],
                    //'sku' => $row['sku'],
                    'variant' => '',
                ]);
                if($row['multi_categories'] != null){
                    foreach (explode(',', $row['multi_categories']) as $category_id) {
                        ProductCategory::insert([
                            "product_id" => $productId->id,
                            "category_id" => $category_id
                        ]);
                    }
                }
            }

            flash(translate('Products imported successfully'))->success();
        }
    }

    public function model(array $row)
    {
        ++$this->rows;
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    public function rules(): array
    {
        return [
            // Can also use callback validation rules
            'unit_price' => function ($attribute, $value, $onFailure) {
                if (!is_numeric($value)) {
                    $onFailure('Unit price is not numeric');
                }
            }
        ];
    }

    public function downloadThumbnail($url)
    {
        try {
            $upload = new Upload;
            $upload->external_link = $url;
            $upload->type = 'image';
            $upload->save();

            return $upload->id;
        } catch (\Exception $e) {
        }
        return null;
    }

    public function downloadGalleryImages($urls)
    {
        $data = array();
        foreach (explode(',', str_replace(' ', '', $urls)) as $url) {
            $data[] = $this->downloadThumbnail($url);
        }
        return implode(',', $data);
    }
}
