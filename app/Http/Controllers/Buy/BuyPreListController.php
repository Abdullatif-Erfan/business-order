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

use App\Models\Setting\Category;
use App\Models\Setting\Account;

use App\Models\Buy\BuyPreList;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class BuyPreListController extends Controller
{
    protected $isAdmin, $packageId;
    public function __construct()
    {
        if (auth()->check()) {
            $this->isAdmin = session('isAdmin', auth()->user()->isAdmin == 1);
        } else {
            $this->isAdmin = false;
        }
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $suppliers = Account::select('id','name')->where('account_type_id',4)->get();
       $categories = Category::select('id','name')->get();
        return view('buy.prelist.list', compact('categories','suppliers'));    
    }

    /**
     * Show the journal data
     */
    public function getData(Request $request)
    {
        $buyPreLists = BuyPreList::with(['categoryRelation','supplier'])
        ->select('id', 'name','category_id','supplier_id')
        ->orderBy('id', 'DESC');
    
        return DataTables::of($buyPreLists)
            
            ->addIndexColumn()


            ->addColumn('category', function($buyPreList) {
                return $buyPreList->categoryRelation->name ?? '';
            })
            ->addColumn('supplier', function($buyPreList) {
                return $buyPreList->supplier->name ?? '';
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
        ];

        $times = time();


        $validated = $request->validate([
            'name' => 'required|string|max:255|min:2|unique:bought_item_pre_lists,name',
        ], $messages);

        BuyPreList::create([
            'name' => $validated['name'],
            'category_id' => $request->category_id,
            'supplier_id' => $request->supplier_id,
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
        ], $messages);

        // ✅ Save in DB
        BuyPreList::create([
            'name' => $validated['name'],
            'times' => $code,
            'image_path' => $image_path,
            'barcode_path' => $qrStoragePath
        ]);

        return response()->json(['status' => 'success', 'message' => 'موفقانه ثبت گردید']);
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
        ];
    
        $validated = $request->validate([
            'name' => 'required|string|max:255|min:3|unique:bought_item_pre_lists,name',
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
        $suppliers = Account::select('id','name')->where('account_type_id',4)->get();
        $categories = Category::select('id','name')->get();
        $buyPreLists = BuyPreList::where('id',$id)->get();

        return view('buy.prelist.edit', compact('buyPreLists','categories','suppliers'));
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
        $prevData->category_id = $request->category_id ?? null;
        $prevData->supplier_id = $request->supplier_id ?? null;
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
    
        return response()->json([
            'status'  => 'success',
            'message' => __('common.deleted_successfully'),
        ]);
    }
    

}
