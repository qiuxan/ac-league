<?php

namespace App\Http\Controllers\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Menu;
use Exception;
use App\Utils;

class MenuController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view( 'staff.menus.index' );
    }

    public function getMenu(Request $request) {
        $id = $request->input('id');
        $menu = DB::table('menus')->select('menus.*')->where(['menus.id' => $id, 'menus.deleted' => 0])->first();

        return response()->json($menu);
    }

    public function getMenus(Request $request) {
        $parent_id = $request->input('id');
        if(!$parent_id)
        {
            $parent_id = 0;
        }
        $menus = DB::table('menus')->select(DB::raw('menus.*, IF(children.parent_id IS NULL, 0, 1) AS hasChildren'))
            ->leftJoin(DB::raw('menus AS children'), function($join)
            {
                $join->on(DB::raw('children.parent_id'), '=', 'menus.id');
                $join->on('children.deleted','=', DB::raw("0"));
            })
            ->where(['menus.parent_id' => $parent_id, 'menus.deleted' => 0])
            ->groupBy('menus.id')
            ->orderBy('menus.priority', 'asc')
            ->get();

        return response()->json($menus);
    }

    public function deleteMenu(Request $request) {
        $id = $request->input('menu_id');
        try {
            DB::beginTransaction();

            $menu = Menu::find($id);

            if($menu)
            {
                $menu->deleted = 1;
                $menu->save();
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

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();
            $menu_id = $request->input('id');
            $menu = Menu::find($menu_id);
            
            if (!$menu) {
                $menu = new Menu();
                $menu->created_by = auth()->user()->id;
            } else {
                $menu->updated_by = auth()->user()->id;
            }

            // store new menu into database
            $menu->parent_id = $request->input('parent_id');
            if ($request->input('language')) {
                $menu->language = $request->input('language');
            }
            $menu->name = $request->input('name');
            $menu->alias = $request->input('alias');
            $menu->external_link = $request->input('external_link');
            $menu->published = $request->input('published');

            $menu->save();

            DB::commit();
            return json_encode([
                'menu_id' => $menu->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'menu_id' => 0
            ]);
        }
    }

    public function saveMenuTree( Request $request )
    {
        try {
            DB::beginTransaction();
            $tree = $request->input('tree');

            $this->saveTree($tree, 0);

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

    private function saveTree( $tree, $parent_id )
    {
        $priority = 0;
        foreach( $tree as $node ) {
            $menu = Menu::find($node['id']);
            if(!$menu)
            {
                throw new Exception("Invalid request");
            }
            $menu->name = $node['name'];

            $menu->priority = $priority;
            $menu->parent_id = $parent_id;
            $menu->save();
            if( isset($node['children']) && $node['children'] && $node['hasChildren'] == true ) {
                $this->saveTree( $node['children'], $menu->id );
            }
            $priority++;
        }
    }

}