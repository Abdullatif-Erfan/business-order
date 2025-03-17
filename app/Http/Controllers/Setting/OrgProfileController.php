<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\OrgBio;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class OrgProfileController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $orgBio = OrgBio::where('is_active', 1);
            return DataTables::eloquent($orgBio)
                ->addIndexColumn()
                ->addColumn('edit', function ($orgBio) {
                    return '<i class="fas fa-pen-square editOrgBio" data-id="' . $orgBio->id . '" style="font-size:20px;"></i>';
                })

                ->addColumn('header', function ($orgBio) {
                    if ($orgBio->header) {
                        return '<img src="' . asset($orgBio->header) . '" width="100" height="50" class="img-thumbnail">';
                    }
                    return 'No Image';
                })
                ->addColumn('logos', function ($orgBio) {
                    if ($orgBio->logos) {
                        return '<img src="' . asset($orgBio->logos) . '" width="50" height="50" class="img-thumbnail">';
                    }
                    return 'No Image';
                })

                ->rawColumns(['edit','header','logos'])
                ->make(true);
        }
        return view('settings.organization.list');
    }

    public function create()
    {
        return view('settings.organization.addForm');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255|min:2|unique:org_bios,name',
            'address' => 'nullable|string|max:255',
            'phone'   => 'nullable|string',
            'header'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'logos'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'note_for_print' => 'nullable|string',
            'expired_after_days' => 'required|min:5|max:60',
            'is_active' => 'boolean'
        ]);

        $orgBio = new OrgBio($validated);
        
        if ($request->hasFile('header')) {
            $orgBio->header = $request->file('header')->store('org_headers', 'public');
        }
        
        if ($request->hasFile('logos')) {
            $orgBio->logos = $request->file('logos')->store('org_logos', 'public');
        }
        
        $orgBio->save();

        return response()->json(['status' => 'success', 'message' => 'موفقانه ثبت گردید']);
    }

    public function show($id)
    {
        $orgBio = OrgBio::find($id);
        if ($orgBio) {
            return response()->json($orgBio);
        }
        return response()->json(['message' => 'یافت نگردید'], 404);
    }

    public function edit($id)
    {
        $orgBio = OrgBio::findOrFail($id);
        return view('settings.organization.editForm', compact('orgBio'));
    }

    public function update(Request $request)
    {
        $messages = [
            'name.required' => 'نام ضروری میباشد',
            'name.string' => 'نام باید شامل حروف باشد',
            'name.max' => 'حداکثر ۲۵۵ کاراکتر مجاز میباشد',
            'phone.required' => 'شماره تلفن ضروری میباشد',
            'address.required' => 'آدرس ضروری میباشد',
            'header.image' => 'هیدر باید یک فایل تصویر باشد',
            'logos.image' => 'لوگو باید یک فایل تصویر باشد',
            'expired_after_days.required' => 'موعد تاریخ انقضا ضروری میباشد',
            'expired_after_days.min' => 'موعد تاریخ انقضا حد اقل پنج روز میباشد',
            'expired_after_days.max' => 'موعد تاریخ انقضا حد اکثر ۶۰ روز میباشد',
        ];
    
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string',
            'address' => 'required|string|max:255',
            'header' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'logos' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'note_for_print' => 'nullable|string',
            'expired_after_days' => 'required|integer|min:5|max:60',
        ], $messages);
    
        $orgBio = OrgBio::findOrFail($request->id);
    
        $orgBio->name = $request->name;
        $orgBio->phone = $request->phone;
        $orgBio->address = $request->address;
        $orgBio->note_for_print = $request->note_for_print;
        $orgBio->expired_after_days = $request->expired_after_days;

    
        // Handle file uploads
        // if ($request->hasFile('header')) {
        //     $headerPath = $request->file('header')->store('headers', 'public');
        //     $orgBio->header = $headerPath;
        // }
    
        // if ($request->hasFile('logos')) {
        //     $logoPath = $request->file('logos')->store('logos', 'public');
        //     $orgBio->logos = $logoPath;
        // }
        if ($request->hasFile('header')) {
            $file = $request->file('header');
            $fileName = time() . '_' . $file->getClientOriginalName(); 
            $file->move(public_path('headers'), $fileName); 
            $orgBio->header = 'headers/' . $fileName; // Save the relative path
        }
        
        if ($request->hasFile('logos')) {
            $file = $request->file('logos');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('logos'), $fileName);
            $orgBio->logos = 'logos/' . $fileName;
        }
    
        $orgBio->save();
    
        return response()->json(['status' => 'success', 'message' => 'ویرایش موفقیت‌آمیز بود']);
    }
    

    public function destroy($id)
    {
        $orgBio = OrgBio::findOrFail($id);
        if ($orgBio) {
            if ($orgBio->header) {
                Storage::disk('public')->delete($orgBio->header);
            }
            if ($orgBio->logos) {
                Storage::disk('public')->delete($orgBio->logos);
            }
            $orgBio->delete();
            return response()->json(['status' => 'success', 'message' => 'موفقانه حذف گردید']);
        }
        return response()->json(['status' => 'failed', 'message' => ' حذف نگردید']);
    }
}
