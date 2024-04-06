<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TemplateFormRequest;
use App\Models\Template;

class TemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('template.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TemplateFormRequest $request)
    {
        $inputs = $request->except('_token','submit');
        
        $tags = isset($inputs['tags']) ? explode(",",$inputs['tags']) : [];
        $tags = array_map('strtolower',$tags);
        
        $imageName = '';
        if(  $request->hasFile('image') ){
            $image = $request->file('image');
            $imageName = time().'-'.$image->getClientOriginalName();
            $image->storeAs('public/template',$imageName);

        }

        try {

            Template::create([
                'title'=>$inputs['title'],
                'image'=>$imageName,
                'tags'=>$tags,
            ]);

            return redirect()->route('templates.index')->with('success',"Template created success.");
        } catch (\Exception $e) {
            
            \Log::error($e->getMessage());
            return redirect()->route('templates.index')->with('error',$e->getMessage());
           
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**`
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
