<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Notifications\CategoryPublished;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;


class CategoryController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Category $category)
    {
        $categories = Category::all();
        return view('category.index', ['categories' => $categories]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
            $request->validate([
            'name'  => 'required|min:4|max:255',
            // 'icon'  => 'required|url',
            'slug'  => 'required|min:4|string',
            'icon_url'  =>  'required_without:icon_upload|url|nullable',
            'icon_upload'  =>  'required_without:icon_url|file|image'
        ]);

        // TODO: Handel file upload for icon
         $category = new Category();
         $category->name = $request->name;
         $category->slug = $request->slug;
        if ($request->has('icon_upload')) {
            $icon = $request->icon_upload;
            $path = $icon->store('category-icon', 'public');
            $category->icon = $path;
        } else {
            $category->icon = $request->icon_url;
        }
      //  Category::create($request->all());


      $category->save();
      Notification::send(User::all() , new CategoryPublished($category));
        // Category::create($request->all());

        return redirect()->route('categories.show', $category);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return view('category.show', ['category' => $category]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        return view('category.edit',  ['category' => $category]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Category $category, Request $request)
    {
        $request->validate([
            'name'  => 'required|min:4|max:255',
            // 'icon'  => 'required|url',
            'slug'  => 'required|min:4|string'
        ]);

        // TODO: Handel file upload for icon

         $category->update($request->all());
    //    $category = Category::findOrFail($category->id);
    //      $category->name = $request->name;
    //     $category->slug = $request->slug;
        if ($request->has('icon_upload')) {
            $icon = $request->icon_upload;
            $path = $icon->store('category-icon', 'public');
            $category->icon = $path;
        } else {
            $category->icon = $request->icon_url;
        }
    // if ($request->has('icon_upload')) {
    //     $icon = $request->icon_upload;
    //     $path = $icon->store('category-icon', 'public');
    //     $category->icon = $path;
    // } else {
    //     $category->icon = $request->icon_url;
    // }
        $category->save();
        return redirect()->route('categories.show', $category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index');
    }
}
