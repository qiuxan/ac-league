<?php

namespace App\Http\Controllers\Admin;
use App\Member;
use App\ProductAttribute;
use App\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Product;
use Exception;
use App\Utils;

class ProductController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view( 'admin.products.index' );
    }

    public function getProducts(Request $request) {
        $pageSize = $request->input('pageSize');
        $products = DB::table('products')->select('products.*', 'members.company_en')
            ->join('members','members.id','=','products.member_id')
            ->where('products.deleted', 0)
            ->orderBy('id', 'desc')->paginate( $pageSize );

        return response()->json($products);
    }

    public function getProductForm() {
        $members = Member::where('deleted', 0)->orderBy('company_en', 'asc')->get();
        return view( 'admin.products.form', compact('members') );
    }

    public function getProductList() {
        return view( 'admin.products.list' );
    }

    public function getProduct(Request $request) {
        $id = $request->input('id');
        $product = DB::table('products')->select('products.*')->where(['products.id' => $id, 'deleted' => 0])->first();

        return response()->json($product);
    }

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();
            $product_id = $request->input('id');
            $product = Product::find($product_id);
            if(!$product)
            {
                $product = new Product();
                $product->created_by = auth()->user()->id;
            }
            else
            {
                $product->updated_by = auth()->user()->id;
            }
            $product->fill($request->all());
            if(!$request->input('origin_en'))
            {
                $product->origin_en = '';
            }
            if(!$request->input('origin_cn'))
            {
                $product->origin_cn = '';
            }

            $product->save();

            DB::commit();
            return json_encode([
                'product_id' => $product->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'product_id' => 0
            ]);
        }
    }

    public function deleteProducts(Request $request) {
        $ids = $request->input('ids');
        try {
            DB::beginTransaction();

            foreach($ids as $id)
            {
                $product = Product::find($id);

                if($product)
                {
                    $product->deleted = 1;
                    $product->save();
                }
            }

            DB::commit();
            return json_encode([
                'result' => 1
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'result' => 0
            ]);
        }
    }

    public function addProductImage( Request $request )
    {
        try {
            DB::beginTransaction();
            $product_image = new ProductImage();
            $product_image->product_id = $request->input('product_id');
            $product_image->file_id = $request->input('file_id');

            $product_image->save();

            DB::commit();
            return json_encode([
                'result' => 1
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'result' => 0
            ]);
        }
    }

    public function getProductImages( Request $request )
    {
        $pageSize = $request->input('pageSize');
        $product_id = $request->input('product_id');
        $products = DB::table('product_images')->select('product_images.*', 'files.location', 'files.original_name')
            ->join('files','product_images.file_id','=','files.id')
            ->where(['product_images.product_id' => $product_id])
            ->orderBy('product_images.priority', 'asc')->paginate( $pageSize );

        return response()->json($products);
    }

    public function setProductThumbnail( Request $request )
    {
        try {
            DB::beginTransaction();
            $product_id = $request->input('product_id');
            $file_id = $request->input('file_id');
            ProductImage::where('product_id', '=', $product_id)
                ->update(['thumbnail' => 0]);

            ProductImage::where(['product_id'=>$product_id, 'file_id'=>$file_id])->update(['thumbnail' => 1]);

            DB::commit();
            return json_encode([
                'result' => 1
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'result' => 0
            ]);
        }
    }

    public function setProductImagePriority( Request $request )
    {
        try {
            DB::beginTransaction();
            $product_id = $request->input('product_id');
            $file_ids = explode("-", $request->input('file_id_list'));
            for($i = 0; $i < count($file_ids); $i++)
            {
                ProductImage::where(['product_id'=>$product_id, 'file_id'=>$file_ids[$i]])->update(['priority' => $i+1]);
            }
            DB::commit();
            return json_encode([
                'result' => 1
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'result' => 0
            ]);
        }
    }

    public function setProductImageDescription( Request $request )
    {
        try {
            DB::beginTransaction();
            $product_id = $request->input('product_id');
            $file_id = $request->input('file_id');
            $description = $request->input('description');

            // English
            if($request->input('lang') == 1)
            {
                ProductImage::where(['product_id'=>$product_id, 'file_id'=>$file_id])->update(['description_en' => $description]);
            }
            // Chinese
            else if ($request->input('lang') == 0)
            {
                ProductImage::where(['product_id'=>$product_id, 'file_id'=>$file_id])->update(['description_cn' => $description]);
            }
            // Traditional Chinese
            else 
            {
                ProductImage::where(['product_id'=>$product_id, 'file_id'=>$file_id])->update(['description_tr' => $description]);                
            }

            DB::commit();
            return json_encode([
                'result' => 1
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'result' => 0
            ]);
        }
    }

    public function deleteProductImage( Request $request )
    {
        try {
            DB::beginTransaction();
            $product_id = $request->input('product_id');
            $file_id = $request->input('file_id');

            ProductImage::where(['product_id'=>$product_id, 'file_id'=>$file_id])->delete();

            DB::commit();
            return json_encode([
                'result' => 1
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'result' => 0
            ]);
        }
    }

    public function getMemberProducts(Request $request) {
        $member_id = $request->input('member_id');
        $products = Product::where(['deleted'=>0, 'member_id'=>$member_id])->orderBy('name_en', 'asc')->get();

        return response()->json($products);
    }

    public function getProductAttributes(Request $request) {
        $product_id = $request->input('product_id');

        $pageSize = $request->input('pageSize');
        $product_attributes = DB::table('product_attributes')->select('product_attributes.*')
            ->join('products','product_attributes.product_id','=','products.id')
            ->where(['product_attributes.product_id' => $product_id,
                'product_attributes.deleted' => 0])
            ->orderBy('priority', 'asc')->paginate( $pageSize );

        return response()->json($product_attributes);
    }

    public function getProductAttributeForm() {
        return view( 'admin.products.product-attribute-form' );
    }

    public function getProductAttribute(Request $request) {
        $id = $request->input('id');
        $product = DB::table('product_attributes')
            ->select('product_attributes.*', DB::raw("
            IF(product_attributes.type = " . ProductAttribute::TYPE_TEXT_BOX . ", product_attributes.value, '') AS value_textBox,
            IF(product_attributes.type = " . ProductAttribute::TYPE_TEXT_AREA . ", product_attributes.value, '') AS value_textArea,
            IF(product_attributes.type = " . ProductAttribute::TYPE_IMAGE . ", product_attributes.value, '') AS value_image
            "))
            ->join('products','product_attributes.product_id','=','products.id')
            ->where(['product_attributes.id' => $id, 'product_attributes.deleted' => 0])
            ->first();

        return response()->json($product);
    }

    public function storeProductAttribute( Request $request )
    {
        try {
            DB::beginTransaction();
            $product_id = $request->input('product_id');
            $product = Product::where(['id'=>$product_id, 'deleted' => 0])->first();

            if($product)
            {
                $product_attribute_id = $request->input('id');
                $product_attribute = ProductAttribute::where(['id' => $product_attribute_id,
                    'product_id' => $product->id, 'deleted' => 0])
                    ->first();
                if(!$product_attribute)
                {
                    $product_attribute = new ProductAttribute();
                    $product_attribute->product_id = $product->id;
                    $product_attribute->created_by = auth()->user()->id;

                    $max_priority = DB::table('product_attributes')
                        ->where(['product_attributes.product_id' => $product->id, 'product_attributes.deleted' => 0])
                        ->max('priority');
                    $product_attribute->priority = $max_priority + 1;
                }
                else
                {
                    $product_attribute->updated_by = auth()->user()->id;
                }
                $product_attribute->fill($request->all());

                if($product_attribute->type == ProductAttribute::TYPE_TEXT_BOX)
                {
                    if(trim($request->input('value_textBox')))
                    {
                        $product_attribute->value = trim($request->input('value_textBox'));
                    }
                    else
                    {
                        DB::commit();
                        return json_encode([
                            'product_attribute_id' => -1
                        ]);
                    }
                }
                else if($product_attribute->type == ProductAttribute::TYPE_TEXT_AREA)
                {
                    if(trim($request->input('value_textArea')))
                    {
                        $product_attribute->value = trim($request->input('value_textArea'));
                    }
                    else
                    {
                        DB::commit();
                        return json_encode([
                            'product_attribute_id' => -1
                        ]);
                    }
                }
                else if($product_attribute->type == ProductAttribute::TYPE_IMAGE)
                {
                    if(trim($request->input('value_image')))
                    {
                        $product_attribute->value = trim($request->input('value_image'));
                    }
                    else
                    {
                        DB::commit();
                        return json_encode([
                            'product_attribute_id' => -1
                        ]);
                    }
                }
                else if($product_attribute->type == ProductAttribute::TYPE_DOCUMENT)
                {
                    if(trim($request->input('value_document')))
                    {
                        $product_attribute->value = trim($request->input('value_document'));
                    }
                    else
                    {
                        DB::commit();
                        return json_encode([
                            'product_attribute_id' => -1
                        ]);
                    }
                }

                $product_attribute->save();

                DB::commit();
                return json_encode([
                    'product_attribute_id' => $product_attribute->id
                ]);
            }
            else
            {
                DB::commit();
                Utils::trace("Invalid access!");
                return json_encode([
                    'product_attribute_id' => 0
                ]);
            }
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'product_attribute_id' => 0,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function deleteProductAttributes(Request $request) {
        $ids = $request->input('ids');
        try {
            DB::beginTransaction();
            foreach($ids as $id)
            {
                $product_attribute = ProductAttribute::find($id);
                if($product_attribute)
                {
                    $product_attribute->deleted = 1;
                    $product_attribute->save();
                }
            }

            DB::commit();
            return json_encode([
                'result' => 1
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'result' => 0
            ]);
        }
    }

    public function updateProductAttributePriorities( Request $request )
    {
        try {
            DB::beginTransaction();
            $product_id = $request->input('product_id');
            $ids = $request->input('ids');
            $min_priority = DB::table('product_attributes')
                ->join('products','product_attributes.product_id','=','products.id')
                ->where(['product_attributes.product_id' => $product_id, 'product_attributes.deleted' => 0])
                ->whereIn('product_attributes.id', $ids)
                ->min('priority');
            $max_priority = DB::table('product_attributes')
                ->join('products','product_attributes.product_id','=','products.id')
                ->where(['product_attributes.product_id' => $product_id, 'product_attributes.deleted' => 0])
                ->whereIn('product_attributes.id', $ids)
                ->max('priority');

            for($i = 0; $i < count($ids); $i++)
            {
                ProductAttribute::where(['product_id'=>$product_id, 'id'=>$ids[$i]])->update(['priority' => $min_priority++]);
            }
            if($min_priority - 1 > $max_priority)
            {
                DB::rollBack();
                Utils::trace("Invalid data!");
                return json_encode([
                    'result' => 0
                ]);
            }

            DB::commit();
            return json_encode([
                'result' => 1
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'result' => 0
            ]);
        }
    }
}