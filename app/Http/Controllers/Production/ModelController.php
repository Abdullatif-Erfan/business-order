<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Production\Models;
use App\Models\Buy\BuyPreList;
use App\Models\Production\ModelDetails;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;


class ModelController extends Controller
{
    protected $branch_id, $isAdmin, $packageId;
    public function __construct()
    {
        if (auth()->check()) {
            $this->branch_id = session('branch_id', auth()->user()->branch_id ?? 0);
            $this->isAdmin = session('isAdmin', auth()->user()->isAdmin == 1);
            $this->packageId = session('package_type') ? session('package_type') : 1;
        } else {
            $this->branch_id = 0;
            $this->isAdmin = false;
            $this->packageId = 1;
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $models = Models::query()
        // ->select('id', 'name', 'branch_id')
        // ->withCount('modelDetailsRelation')
        // ->withSum('modelDetailsRelation', 'total_price')
        // ->with([
        //     'modelDetailsRelation.currencyRelation:id,name' // assuming `name` column in currencies table
        // ])
        // ->where('branch_id', $this->branch_id)
        // ->orderByDesc('id')
        // ->get();

        // $models = DB::table('models')
        // ->select(
        //     'models.id',
        //     'models.name',
        //     'models.branch_id',
        //     DB::raw('COUNT(model_details.id) as model_details_count'),
        //     DB::raw('SUM(model_details.total_price) as model_details_total_price'),
        //     'currencies.name as currency_name'
        // )
        // ->leftJoin('model_details', 'models.id', '=', 'model_details.model_id')
        // ->leftJoin('currencies', 'model_details.currency_id', '=', 'currencies.id')
        // ->where('models.branch_id', $this->branch_id)
        // ->groupBy('models.id', 'models.name', 'models.branch_id', 'currencies.name')
        // ->orderByDesc('models.id')
        // ->get();

        // return response()->json($models);
        // die();

        $branch_id = $this->branch_id;
        return view('production/model/list', compact('branch_id'));
    }

     /**
     * Show the journal data
     */
    public function getData(Request $request)
    {
        $models = DB::table('models')
        ->select(
            'models.id',
            'models.name',
            'models.branch_id',
            DB::raw('COUNT(model_details.id) as model_details_count'),
            DB::raw('SUM(model_details.total_price) as model_details_total_price'),
            'currencies.name as currency_name'
        )
        ->leftJoin('model_details', 'models.id', '=', 'model_details.model_id')
        ->leftJoin('currencies', 'model_details.currency_id', '=', 'currencies.id')
        ->where('models.branch_id', $this->branch_id)
        ->groupBy('models.id', 'models.name', 'models.branch_id', 'currencies.name')
        ->orderByDesc('models.id');
    
        return DataTables::of($models)
            
            ->addIndexColumn()
            
            // ->addColumn('addItem', function ($models) {
            //     return '<a href="roles/permissions/' . $models->id . '" class="hidden-print">
            //                 <i class="btn btn-sm btn-success" data-id="' . $models->id . '">
            //                     ' . __('user.priviledge') . ' ( + / - )
            //                 </i>
            //             </a>';

            ->editColumn('model_details_total_price', function ($row) {
                return number_format((float)$row->model_details_total_price, 2);
            })

            ->addColumn('addItem', function ($models) {
                $label = $models->model_details_count > 0
                    ? ' ( '. $models->model_details_count . ' ) ' . __('common.edit')
                    : __('production.add_sub_items');
            
                return '<a href="modelDetails/create/' . $models->id . '" class="hidden-print">
                            <i class="btn btn-sm btn-success" data-id="' . $models->id . '">' . $label . '</i>
                        </a>';
            })

            ->addColumn('edit', function($models) {
                return '<i class="fas fa-pen-square editIcon" data-id="'.$models->id.'" style="font-size:20px;"></i>';
            })
            ->addColumn('delete', function($models) {
                return '<i class="fas fa-trash-alt deleteIcon" data-id="'.$models->id.'" style="font-size:20px; color:red;"></i>';
            })
        
            ->rawColumns(['addItem','edit','delete'])
            ->make(true);
    }
    

    
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $modelList = Models::where('branch_id', $this->branch_id)->get();
        $model = Models::findOrFail($id);

        return view('production.model.edit', compact('models','modelList'));
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
    public function store(Request $request)
    {
        $messages = [
            'name.required' => __('validate.pre_list_name_required'),
            'name.string' => __('validate.pre_list_name_string'),
            'name.max' => __('validate.pre_list_name_max'),
            'name.min' => __('validate.pre_list_name_min'),
            'name.unique' => __('validate.pre_list_name_unique'),
            'branch_id.exists' => __('validate.pre_list_branch_id_exists'),
        ];

        $times = time();

        $validated = $request->validate([
            'name' => 'required|string|max:255|min:3|unique:models,name',
            'branch_id' => 'required|exists:branches,id',
        ], $messages);

        DB::beginTransaction();

        try {
        
            Models::create([
                'branch_id' => $validated['branch_id'],
                'name' => $validated['name'],
            ]);
    
            // store in bought_item_pre_list
            BuyPreList::create([
                'name' => $validated['name'],
                'branch_id' => $validated['branch_id'],
                'times' => $times,
                'image_path' => '',
                'barcode_path' => ''
            ]);

        DB::commit();

        return response()->json(['status' => 'success', 'message' => 
        __('common.added_successfully')]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 
            __('common.add_failed') . $e->getMessage()], 500);
        }
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $id = $request->id;
        $messages = [
            'name.required' => __('validate.pre_list_name_required'),
            'name.string' => __('validate.pre_list_name_string'),
            'name.max' => __('validate.pre_list_name_max'),
            'name.min' => __('validate.pre_list_name_min'),
            'name.unique' => __('validate.pre_list_name_unique'),
        ];
    
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'min:3',
            ],
        ], $messages);
    
        $prevData = Models::find($id);
    
        if (!$prevData) {
            return response()->json([
                'status' => 'error',
                'message' => 'سطر مورد نظر پیدا نشد'
            ], 404);
        }
    
        $prevData->name = $validated['name'];
        $prevData->save();
    
        return response()->json([
            'status' => 'success',
            'message' => __('common.updated_successfully'),
            'data' => $prevData
        ]);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bpList = Models::findOrFail($id);

        if ($bpList) {
            $modelDetails = ModelDetails::query()
                ->where('model_id', $id)
                ->where('branch_id', $this->branch_id)
                ->get();

            if ($modelDetails->isNotEmpty()) {
                // Delete all model details
                foreach ($modelDetails as $md) {
                    $md->delete();
                }
            }

            // Delete the parent model record
            $bpList->delete();

            return response()->json([
                'status' => 'success',
                'message' => __('common.deleted_successfully')
            ]);
        }

        return response()->json([
            'status' => 'failed',
            'message' => __('common.delete_failed')
        ]);
    }

}
