<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App;
use Illuminate\Support\Facades\DB;

class MenusComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $menus = $this->getPublicMenus(0);
        $view->with('menus', $menus);
    }

    private function getPublicMenus($parent_id)
    {
        if(App::getLocale() == 'cn')
        {
            $language = 'cn';
        }
        elseif(App::getLocale() == 'tr')
        {
            $language = 'tr';
        }
        else
        {
            $language = 'en';
        }
        $menus = DB::table('menus')->select(DB::raw('menus.*, IF(children.parent_id IS NULL, 0, 1) AS hasChildren'))
            ->leftJoin(DB::raw('menus AS children'), function($join)
            {
                $join->on(DB::raw('children.parent_id'), '=', 'menus.id');
                $join->on('children.deleted','=', DB::raw("0"));
            })
            ->where(['menus.parent_id' => $parent_id, 'menus.deleted' => 0, 'menus.published' => 1, 'menus.language' => $language])
            ->groupBy('menus.id')
            ->orderBy('menus.priority', 'asc')
            ->get();
        foreach($menus as $menu)
        {
            if($menu->hasChildren == 1)
            {
                $menu->children = $this->getPublicMenus($menu->id);
            }
        }

        return $menus;
    }
}