<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Template;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TemplateController extends AppBaseController
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request): JsonResponse
    {

        $query = Template::query();
        $template = $query->latest()->paginate(10);
        return $this->sendResponse($template,'success');
    }

    public function searchTemplate($search): JsonResponse
    {
        $searchText = $search;

        $query = Template::query();
        $searchArray = explode(' ',$searchText);

        $query = $query->whereJsonContains('tags', $searchText);

        if( count($searchArray) > 1 ){
            foreach( $searchArray as $searchContent ){
                $query = $query->orWhereJsonContains('tags', $searchContent);
            }
            
        }
        

        $templates = $query->latest()->get();
        $searchContentAr = $this->orderMappingSearch($templates,$searchArray);

        $data = $this->paginate($searchContentAr);
        
        return $this->sendResponse($data,'success');

    }

    public function orderMappingSearch($templates,$searchArray): Array
    {
        $result = [];

        foreach( $searchArray as $key =>  $searchIndex){
            foreach( $templates as $key2 =>  $template){
                if( in_array($searchIndex,$template['tags']) ){
                    $result[$key][] = $template;
                }    
            }
        }
        return $result;
    }

    public function paginate($items, $perPage = 10, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

   
}
