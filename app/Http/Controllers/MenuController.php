<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display the menu view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('menu.index');
    }

    /**
     * Return menu data as JSON.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMenuData()
    {
        //dd("aqui");
        $menus = Menu::all()->where('actived', '=', 1);
        //$menus = Menu::where('actived', true)->get(['id', 'description', 'icon', 'actived']);
        return response()->json($menus);
    }


        public function getMenuDataEspecifico(Request $request)
    {
        //dd("aqui");
        $menu = Menu::findOrFail($request->id);
        //$menus = Menu::where('actived', true)->get(['id', 'description', 'icon', 'actived']);
        return response()->json($menu);
    }
    /**
     * Store a newly created menu in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'actived' => 'required|boolean',
        ]);

        $msg = "";
        if ($request->id == 0) {
            $menu = Menu::create($validated);
            $msg = "Menu cadastrado com sucesso";
        } else {
            $menu = Menu::find($request->
                id);
            $menu->
                update($validated);
            $msg = "Menu atualizado com sucesso";
        }



        return response()->json(['success' => true, 'message' => $msg, 'menu' => $menu]);
    }


    /**
     * Remove the specified menu from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        try {
            $id = $request->id;
            
            $menu = Menu::findOrFail($id);
            $menu->actived = 0;
            $menu->save();
            return response()->json(['success' => true, 'message' => 'Menu excluído com sucesso']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Menu não encontrado', 'error' => $e->getMessage()]);
        }
    }
}
