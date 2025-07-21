<?php

namespace App\Http\Controllers\Buy;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\Facades\Image;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Milon\Barcode\DNS1D;

use App\Models\Setting\Branch;
use App\Models\Buy\BuyPreList;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class BuyPreListController extends Controller
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
        // $branchs = Branch::all();
        // $buyPreLists = BuyPreList::with('branchRelation')->get();

        // return response()->json(['data' => $buyPreLists]);

        $branchs = Branch::where('id',$this->branch_id)->get();
        if($this->packageId == 4) 
        {
           return view('buy.prelist.pos_list', compact('branchs'));
        }
        else 
        {
            return view('buy.prelist.list', compact('branchs'));
        }
    }

    /**
     * Show the journal data
     */
    public function getData(Request $request)
    {
        $buyPreLists = BuyPreList::with('branchRelation')
        ->select('id','code', 'name','image_path','barcode_path','times', 'branch_id')
        ->where('branch_id',$this->branch_id)
        ->orderBy('id', 'DESC');
    
        return DataTables::of($buyPreLists)
            
            ->addIndexColumn()

            ->addColumn('branch', function($buyPreList) {
                return $buyPreList->branchRelation->name;
            })
            ->addColumn('edit', function($buyPreList) {
                return '<i class="fas fa-pen-square editIcon" data-id="'.$buyPreList->id.'" style="font-size:20px;"></i>';
            })
            ->addColumn('delete', function($buyPreList) {
                return '<i class="fas fa-trash-alt deleteIcon" data-id="'.$buyPreList->id.'" style="font-size:20px; color:red;"></i>';
            })
        
            ->rawColumns(['edit','delete'])
            ->make(true);
    }
    

    /**
     * Show PosData
     */
    public function getPosData(Request $request)
    {
        $buyPreLists = BuyPreList::with('branchRelation')
        ->select('id','code', 'name','image_path','barcode_path','times', 'branch_id')
        ->where('branch_id',$this->branch_id)
        ->orderBy('id', 'DESC');
    
        return DataTables::of($buyPreLists)
            
            ->addIndexColumn()

            ->addColumn('branch', function($buyPreList) {
                return $buyPreList->branchRelation->name;
            })
            ->addColumn('edit', function($buyPreList) {
                return '<i class="fas fa-pen-square editIcon" data-id="'.$buyPreList->id.'" style="font-size:20px;"></i>';
            })
            ->addColumn('delete', function($buyPreList) {
                return '<i class="fas fa-trash-alt deleteIcon" data-id="'.$buyPreList->id.'" style="font-size:20px; color:red;"></i>';
            })
            ->addColumn('image', function ($buyPreList) {
                if ($buyPreList->image_path) {
                    return '<img src="' . asset('storage/' . $buyPreList->image_path) . '" width="50" height="50" class="img-thumbnail">';
                }
                return 'No Image';
            })
            
            // ->addColumn('barcode', function ($buyPreList) {
            //     if ($buyPreList->barcode_path) {
            //         return '<img src="' . asset('storage/' . $buyPreList->barcode_path) . '" width="50" height="50" class="img-thumbnail">';
            //     }
            //     return 'No Image';
            // })
            ->addColumn('barcode', function ($buyPreList) {
                if ($buyPreList->barcode_path) {
                    $url = asset('storage/' . $buyPreList->barcode_path);
                    return '<img src="' . $url . '" width="100" height="40" class="img-thumbnail">';
                }
                return 'No Image';
            })

            ->rawColumns(['edit','delete','image','barcode'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function print_barcode(Request $request)
    {
        $perPage = 20;

        $preList = BuyPreList::select('id','code', 'name','image_path','barcode_path','times', 'branch_id')
            ->where('branch_id', $this->branch_id)
            ->orderBy('id', 'DESC')
            ->paginate($perPage);
    
        // return response()->json($buyPreLists);
        return view('buy.prelist.print', compact('preList'));
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
            'name' => 'required|string|max:255|min:3|unique:bought_item_pre_lists,name',
            'branch_id' => 'required|exists:branches,id',
        ], $messages);

        BuyPreList::create([
            'name' => $validated['name'],
            'branch_id' => $validated['branch_id'],
            'times' => $times,
            'image_path' => '',
            'barcode_path' => ''
        ]);

        return response()->json(['status' => 'success', 'message' => __('common.added_successfully')]);
    }

    public function storeWithQRcodePNG(Request $request)
    {
        $messages = [
            'name.required' => __('validate.pre_list_name_required'),
            'name.string' => __('validate.pre_list_name_string'),
            'name.max' => __('validate.pre_list_name_max'),
            'name.min' => __('validate.pre_list_name_min'),
            'name.unique' => __('validate.pre_list_name_unique'),
            'branch_id.exists' => __('validate.pre_list_branch_id_exists'),
        ];


        $code = time();

        // ✅ Ensure barcodes folder exists
        $barcodeDir = storage_path('app/public/barcodes');
        if (!File::exists($barcodeDir)) {
            File::makeDirectory($barcodeDir, 0755, true);
        }

        // ✅ Generate QR code with label
        $qrPng = QrCode::format('png')
            ->size(300)
            ->margin(10)
            ->color(0, 0, 0)
            ->backgroundColor(255, 255, 255)
            ->generate($code);

        // Create image with label using Intervention Image
        $image = Image::make($qrPng);
        
        // Add label at the bottom of the QR code
        $label = $request->name; // Using the item name as label
        $image->resizeCanvas(0, 50, 'bottom', true, '#ffffff'); // Add white space at bottom
        $image->text($label, $image->width()/2, $image->height() - 15, function($font) {
            $font->file(public_path('barcode_font/majalla.ttf')); // Make sure you have this font
            $font->size(16);
            $font->color('#000000');
            $font->align('center');
            $font->valign('bottom');
        });

        // ✅ Save QR code with label to storage/app/public/barcodes
        $qrFileName = $code . '.png';
        $qrStoragePath = 'barcodes/' . $qrFileName;
        $image->save(storage_path('app/public/' . $qrStoragePath));

        // ✅ Handle image upload if any
        $image_path = '';
        if ($request->hasFile('image')) {
            $image_path = $request->file('image')->store('item_images', 'public');
            Log::info('Document uploaded', ['path' => $image_path]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|min:3|unique:bought_item_pre_lists,name',
            'branch_id' => 'required|exists:branches,id',
        ], $messages);

        // ✅ Save in DB
        BuyPreList::create([
            'name' => $validated['name'],
            'branch_id' => $validated['branch_id'],
            'times' => $code,
            'image_path' => $image_path,
            'barcode_path' => $qrStoragePath
        ]);

        return response()->json(['status' => 'success', 'message' => 'موفقانه ثبت گردید']);
    }

    public function pos_store(Request $request)
    {
        // Log::info('Starting QR code generation process');
        
        $messages = [
            'name.required' => __('validate.pre_list_name_required'),
            'name.string' => __('validate.pre_list_name_string'),
            'name.max' => __('validate.pre_list_name_max'),
            'name.min' => __('validate.pre_list_name_min'),
            'name.unique' => __('validate.pre_list_name_unique'),
            'branch_id.exists' => __('validate.pre_list_branch_id_exists'),
        ];

        
        $validated = $request->validate([
            'name' => 'required|string|max:255|min:3|unique:bought_item_pre_lists,name',
            'branch_id' => 'required|exists:branches,id',
        ], $messages);

        $times = time();
        // Log::debug('Generated unique code: '.$code);

        // ✅ Ensure barcodes folder exists
        $barcodeDir = storage_path('app/public/barcodes');
        if (!File::exists($barcodeDir)) {
            // Log::debug('Creating barcodes directory: '.$barcodeDir);
            File::makeDirectory($barcodeDir, 0755, true);
        }

        try {

            $code = DB::table('bought_item_pre_lists')->where('branch_id', $this->branch_id)->lockForUpdate()->max('code') + 1;
    
            // Log::debug('Generating base QR code SVG');
            $qrSvg = QrCode::format('svg')
                ->size(300)
                ->margin(10)
                ->color(0, 0, 0)
                ->backgroundColor(255, 255, 255)
                ->errorCorrection('H')
                ->encoding('UTF-8')
                ->generate($code);

            // Log::debug('Base QR code generated successfully');
            // Log::debug('SVG length: '.strlen($qrSvg).' bytes');
            
            $label = $request->name;
            // Log::debug('Preparing to add label: "'.$label.'"');

            $labeledSvg = $this->addLabelInsideSvg($qrSvg, $label, [
                'font_size' => 16,
                'font_family' => 'Arial',
                'text_color' => '#000000',
                'bottom_margin' => 10
            ]);

            // Log::debug('Label added successfully');
            // Log::debug('Final SVG length: '.strlen($labeledSvg).' bytes');

            $qrFileName = $code . '.svg';
            $qrStoragePath = 'barcodes/' . $qrFileName;
            
            // Log::debug('Saving to: '.$qrStoragePath);
            $saveResult = Storage::disk('public')->put($qrStoragePath, $labeledSvg);
            
            if (!$saveResult) {
                Log::error('Failed to save SVG file');
                throw new \Exception('Failed to save QR code image');
            }
            // Log::info('QR code saved successfully');

            // ✅ Handle image upload if any
            $image_path = '';
            if ($request->hasFile('image')) {
                $image_path = $request->file('image')->store('item_images', 'public');
                Log::info('Document uploaded', ['path' => $image_path]);
            }


            // ✅ Save in DB
            BuyPreList::create([
                'name' => $validated['name'],
                'code' => $code,
                'branch_id' => $validated['branch_id'],
                'times' => $times,
                'image_path' => $image_path,
                'barcode_path' => $qrStoragePath
            ]);

            return response()->json(['status' => 'success', 'message' => 
            __('common.added_successfully')]);

        } catch (\Exception $e) {
            Log::error('QR code generation failed: '.$e->getMessage());
            Log::error('Stack trace: '.$e->getTraceAsString());
            throw $e; // Re-throw after logging
        }
    }

    // used 
    public function storeWithBarcodeGeneration(Request $request)
    {
        $messages = [
            'name.required' => __('validate.pre_list_name_required'),
            'name.string' => __('validate.pre_list_name_string'),
            'name.max' => __('validate.pre_list_name_max'),
            'name.min' => __('validate.pre_list_name_min'),
            'name.unique' => __('validate.pre_list_name_unique'),
            'branch_id.exists' => __('validate.pre_list_branch_id_exists'),
        ];
    
        $validated = $request->validate([
            'name' => 'required|string|max:255|min:3|unique:bought_item_pre_lists,name',
            'branch_id' => 'required|exists:branches,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], $messages);
    
        $times = time();
    
        // Ensure barcode directory exists
        $barcodeDir = storage_path('app/public/barcodes');
        if (!File::exists($barcodeDir)) {
             File::makeDirectory($barcodeDir, 0755, true);
             Log::info('Barcode directory created', ['path' => $barcodeDir]);
        }
    
        DB::beginTransaction();
    
        try {
            $code = '';
            $is_prev_barcode = 0;
    
            // Use previous barcode if provided
            if ($request->input('prev_barcode')) {
                $code = $request->input('prev_barcode');
                $is_prev_barcode = 1;
            } else {
                // Safely get next code using lock to prevent race condition
                $lastItem = DB::table('bought_item_pre_lists')
                    ->where('branch_id', $validated['branch_id'])
                    ->where('is_prev_barcode', 0)
                    ->lockForUpdate()
                    ->orderBy('id', 'DESC')
                    ->first();
    
                $code = $lastItem ? (int) $lastItem->code + 1 : 1;
                $is_prev_barcode = 0;
            }
    
            // Validate code
            $code = preg_replace('/\D/', '', (string) $code); // ensure it's numeric
            if (!$code || strlen($code) > 20) {
                Log::error('Invalid barcode code', ['code' => $code]);
                throw new \Exception('Invalid barcode code generated');
            }
    
            $code = str_pad($code, 4, '0', STR_PAD_LEFT); // e.g., 0001, 0002

            // Generate PNG barcode using milon/barcode
            $barcode = new DNS1D();
            $barcode->setStorPath($barcodeDir); // Optional
            $base64Png = $barcode->getBarcodePNG($code, 'C128');

    
            if (empty($base64Png)) {
                Log::error('Failed to generate base64 PNG for barcode', ['code' => $code]);
                throw new \Exception('Failed to generate barcode PNG');
            }
    
            $pngData = base64_decode($base64Png);
    
            // Save file with unique name
            $barcodeFileName = $code . '_' . uniqid() . '.png';
            // $barcodeFileName = $code . '.png';
            $barcodePath = 'barcodes/' . $barcodeFileName;
    
            $saveResult = Storage::disk('public')->put($barcodePath, $pngData);
            if (!$saveResult) {
                Log::error("Failed to save barcode image", ['path' => $barcodePath]);
                throw new \Exception('Failed to save barcode image');
            }
            Log::info('Barcode image saved', ['path' => $barcodePath]);
    
            // Handle optional image upload
            $image_path = '';
            if ($request->hasFile('image')) {
                $image_path = $request->file('image')->store('item_images', 'public');
                Log::info('Item image uploaded', ['path' => $image_path]);
            }
    
            // Save record
            BuyPreList::create([
                'name' => $validated['name'],
                'code' => $code,
                'branch_id' => $validated['branch_id'],
                'is_prev_barcode' => $is_prev_barcode,
                'times' => $times,
                'image_path' => $image_path,
                'barcode_path' => $barcodePath,
            ]);
    
            DB::commit();
            Log::info('BuyPreList created', ['code' => $code]);
    
            return response()->json(['status' => 'success', 'message' => 
            __('common.added_successfully')]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Barcode generation failed', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 
            __('common.add_failed') . $e->getMessage()], 500);
        }
    }

protected function addLabelInsideSvg(string $svg, string $label, array $options = []): string
{
    // Log::debug('Starting SVG label addition');
    
    $defaultOptions = [
        'font_size' => 16,
        'font_family' => 'Arial',
        'text_color' => '#000000',
        'bottom_margin' => 10,
        'text_y_offset' => 30
    ];
    $options = array_merge($defaultOptions, $options);

    try {
        // Log::debug('Loading SVG DOM');
        $dom = new \DOMDocument();
        $loadResult = $dom->loadXML($svg);
        
        if (!$loadResult) {
            Log::error('Failed to parse SVG XML');
            throw new \Exception('Invalid SVG content');
        }
        
        $svgElement = $dom->getElementsByTagName('svg')[0];
        if (!$svgElement) {
            Log::error('No SVG element found in XML');
            throw new \Exception('Invalid SVG structure');
        }

        $width = (int)$svgElement->getAttribute('width');
        $height = (int)$svgElement->getAttribute('height');
        // Log::debug("Original SVG dimensions: {$width}x{$height}");


        $result = $dom->saveXML();
        if (!$result) {
            // Log::error('Failed to save modified SVG');
            throw new \Exception('SVG generation failed');
        }

        // Log::debug('SVG modification completed successfully');
        return $result;

    } catch (\Exception $e) {
        Log::error('SVG processing error: '.$e->getMessage());
        throw $e;
    }
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $branchs = Branch::where('id', $this->branch_id)->get();
        $buyPreLists = BuyPreList::with('branchRelation')->where('id',$id)->get();
        return view('buy.prelist.edit', compact('branchs','buyPreLists'));
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
        // Define custom validation messages
        // Log::info('Request Data', $request->all());
        $messages = [
            'name.required' => __('validate.pre_list_name_required'),
            'name.string' => __('validate.pre_list_name_string'),
            'name.max' => __('validate.pre_list_name_max'),
            'name.min' => __('validate.pre_list_name_min'),
            'name.unique' => __('validate.pre_list_name_unique'),
        ];

        // Validate the request
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'min:3',
                Rule::unique('bought_item_pre_lists')->ignore($request->id), // Exclude the current record
            ],
            'branch_id' => 'required|exists:branches,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], $messages);
    
        // Find the record to update
        $prevData = BuyPreList::find($request->id);
    
        // Check if record exists
        if (!$prevData) {
            return response()->json(['status' => 'error', 'message' => 'سطر مورد نظر پیدا نشد']);
        }
    
        if ($request->hasFile('image')) {
            // Store the uploaded image
            $image_path = $request->file('image')->store('item_images', 'public');
            $prevData->image_path = $image_path; // Save the image path
        }
        // Update the data
        $prevData->name = $request->name;
        $prevData->save();
    
        // Return success response
        return response()->json(['status' => 'success', 'message' => 
        __('common.updated_successfully')]);
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bpList = BuyPreList::findOrFail($id);

        if ($bpList) {
            // Delete image if exists
            if ($bpList->image_path && Storage::disk('public')->exists($bpList->image_path)) {
                Storage::disk('public')->delete($bpList->image_path);
            }

            // Delete barcode if exists
            if ($bpList->barcode_path && Storage::disk('public')->exists($bpList->barcode_path)) {
                Storage::disk('public')->delete($bpList->barcode_path);
            }

            // Delete the database record
            $bpList->delete();

            return response()->json(['status' => 'success', 'message' =>
             __('common.deleted_successfully')]);
        }

        return response()->json(['status' => 'failed', 'message' => __('common.delete_failed')]);
    }

}
