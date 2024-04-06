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

        return $this->sendResponse($template, 'success');
    }

    public function searchTemplate($search): JsonResponse
    {
        $searchText = $search;

        $query = Template::query();

        $data = $this->fullTestSearch($query, $searchText);

        if (empty($data->toArray()['data']) && count($data->toArray()['data']) == 0) {
            $data = $this->halfOneOneRationFilter($query, $searchText);
        }

        if (empty($data->toArray()['data']) && count($data->toArray()['data']) == 0) {
            $data = $this->remaingWordCountMinusOneRationFilter($query, $searchText);
        }

        if (empty($data->toArray()['data']) && count($data->toArray()['data']) == 0) {
            $data = $this->oneWordRationFilter($query, $searchText);
        }
        return $this->sendResponse($data, 'success');
    }

    /**
     * fullTestSearch: make filteration base on searchText full string
     * @param mixed $query
     * @var string searchText
     * @return Illuminate\Pagination\LengthAwarePaginator  $data
     */

    function fullTestSearch($query, $searchText): LengthAwarePaginator
    {
        $query = $query->whereJsonContains('tags', $searchText);
        $templates = $query->latest()->get();
        $data = $this->paginate($templates);
        return $data;
    }

    /**
     * remaingWordCountMinusOneRationFilter: make filteration base on 2:1 ration
     * @param mixed $query
     * @var string searchText
     * @return Illuminate\Pagination\LengthAwarePaginator  $data
     */

    public function remaingWordCountMinusOneRationFilter($query, $searchText): LengthAwarePaginator
    {
        $searchArray = explode(' ', $searchText);
        $searchArray = $this->makeArrayTotalCountMinusOne($searchArray);
        $query = $query->whereJsonContains('tags', $searchText);
        if (count($searchArray) > 1) {
            foreach ($searchArray as $searchContent) {
                $query = $query->orWhereJsonContains('tags', $searchContent);
            }
        }
        $templates = $query->latest()->get();
        $searchContentAr = $this->orderMappingSearch($templates, $searchArray);
        $searchContentAr = $this->removeDuplicateRecords($searchContentAr);
        $data = $this->paginate($searchContentAr);
        return $data;
    }

    public function makeArrayTotalCountMinusOne($searchArray): array
    {
        $phrases = [];
        for ($i = 0; $i < count($searchArray) - 1; $i++) {
            if ($i < (count($searchArray) - 1) - 1) {
                $phrase = $searchArray[$i] . ' ' . $searchArray[$i + 1];
                $phrases[] = strtolower($phrase);
                continue;
            }
            $phrases[] = strtolower($searchArray[count($searchArray) - 1]);
        }
        return $phrases;
    }

    /**
     * halfOneOneRationFilter: make filteration base on 2:1:1 ration
     * @param mixed $query
     * @var string searchText
     * @return Illuminate\Pagination\LengthAwarePaginator  $data
     */

    public function halfOneOneRationFilter($query, $searchText): LengthAwarePaginator
    {
        $searchArray = explode(' ', $searchText);
        $searchArray = $this->makeArrayHalfOneOne($searchArray);
        $query = $query->whereJsonContains('tags', $searchText);
        if (count($searchArray) > 1) {
            foreach ($searchArray as $searchContent) {
                $query = $query->orWhereJsonContains('tags', $searchContent);
            }
        }
        $templates = $query->latest()->get();
        $searchContentAr = $this->orderMappingSearch($templates, $searchArray);
        $searchContentAr = $this->removeDuplicateRecords($searchContentAr);
        $data = $this->paginate($searchContentAr);
        return $data;
    }


    public function makeArrayHalfOneOne($searchArray): array
    {
        $phrases = [];
        for ($i = 0; $i < count($searchArray) - 1; $i++) {
            if ($i < ((count($searchArray) / 2) - 1)) {
                $phrase = $searchArray[$i] . ' ' . $searchArray[$i + 1];
                $phrases[] = strtolower($phrase);
            } else {
                $phrases[] = strtolower($searchArray[$i + 1]);
            }
        }
        return $phrases;
    }

    /**
     * oneWordRationFilter: make filteration base on 1:1:1 ration
     * @param mixed $query
     * @var string searchText
     * @return Illuminate\Pagination\LengthAwarePaginator  $data
     */

    public function oneWordRationFilter($query, $searchText): LengthAwarePaginator
    {
        $searchArray = explode(' ', $searchText);
        $query = $query->whereJsonContains('tags', $searchText);
        if (count($searchArray) > 1) {
            foreach ($searchArray as $searchContent) {
                $query = $query->orWhereJsonContains('tags', $searchContent);
            }
        }
        $templates = $query->latest()->get();
        $searchContentAr = $this->orderMappingSearch($templates, $searchArray);
        $searchContentAr = $this->removeDuplicateRecords($searchContentAr);

        $data = $this->paginate($searchContentAr);
        return $data;
    }

    public function orderMappingSearch($templates, $searchArray): array
    {
        $result = [];
        foreach ($searchArray as $key =>  $searchIndex) {
            foreach ($templates as $key2 =>  $template) {
                if (in_array($searchIndex, $template['tags'])) {
                    $result[$key][] = $template;
                }
            }
        }
        return $result;
    }


    /**
     * @removeDuplicateRecords  reduce duplicate filtering data object cuse
     * @param mixed $dataArray
     * @var array uniqueRecords
     * @return array $uniqueRecords
     */

    public function removeDuplicateRecords($dataArray): array
    {
        $uniqueRecords = [];
        $encounteredIds = [];

        foreach ($dataArray as $subArray) {
            foreach ($subArray as $record) {
                $recordId = $record['id'];
                if (!in_array($recordId, $encounteredIds)) {
                    $uniqueRecords[] = $record;
                    $encounteredIds[] = $recordId;
                }
            }
        }
        return $uniqueRecords;
    }

    /**
     * paginate: make Array To Paginator
     * @param mixed $items
     * @param int $perPage
     * @param mixed $page
     * @param array $options
     * @return Illuminate\Pagination\LengthAwarePaginator
     */

    public function paginate($items, $perPage = 10, $page = null, $options = []): LengthAwarePaginator

    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
