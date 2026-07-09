<?php

namespace App\Http\Controllers\Buy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Models\Setting\OrgBio;
use App\Models\Setting\Unit;
use App\Models\Buy\BuyPreList;
use App\Models\Buy\BoughtItem;
use App\Models\Setting\Currency;
use App\Models\Buy\BoughtItemDetails; 
use App\Models\Transaction\Journal;
use App\Models\Setting\Warehouse;
use App\Models\Warehouse\WarehouseItem;

use App\Models\Setting\Account;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Buy\BoughtReturn;

class BoughtDetailsBasedItemController extends Controller
{
    protected  $isAdmin;
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
        // $boughtItemDetails = BoughtItemDetails::with(['boughtItemRelation','accountRelation','preListRelation','unitRelation'])->get();
        // return response()->json(['boughtItemDetails' => $boughtItemDetails]);

        $currencies = Currency::all();
        $orgbios = OrgBio::all();
        $todaysDate = Carbon::now()->format('Y-m-d');

        return view('buy.bought.item_list',compact('currencies','todaysDate','orgbios'));
    }

    public function getData(Request $request)
    {
        $tax_activation = $request->input('tax_activation', 0);
        
        // Use BoughtItemDetails as the base query
        $boughtItems = BoughtItemDetails::with([
            'boughtItemRelation', 
            'accountRelation', 
            'preListRelation', 
            'unitRelation'
        ])->orderBy('id', 'DESC');
        
        // Apply filters
        if ($request->customer_name) {
            $boughtItems->whereHas('accountRelation', function ($query) use ($request) {
                $query->where('name', 'LIKE', "%{$request->customer_name}%");
            });
        }
        
        if ($request->item_name) {
            $boughtItems->whereHas('preListRelation', function ($query) use ($request) {
                $query->where('name', 'LIKE', "%{$request->item_name}%");
            });
        }

        if ($request->currency_id) {
            $boughtItems->whereHas('boughtItemRelation', function ($query) use ($request) {
                $query->where('currency_id', $request->currency_id);
            });
        }
        
        if ($request->start_date && $request->end_date) {
            $boughtItems->whereHas('boughtItemRelation', function ($query) use ($request) {
                $query->whereBetween('idate', [$request->start_date, $request->end_date]);
            });
        } elseif ($request->start_date) {
            $boughtItems->whereHas('boughtItemRelation', function ($query) use ($request) {
                $query->whereDate('idate', '=', $request->start_date);
            });
        } elseif ($request->end_date) {
            $boughtItems->whereHas('boughtItemRelation', function ($query) use ($request) {
                $query->whereDate('idate', '<=', $request->start_date);
            });
        }
        
        if ($request->bill_number) {
            $boughtItems->where('billno', $request->bill_number);
        }
        
        return DataTables::of($boughtItems->get())
            ->addIndexColumn()
            
            ->addColumn('billno', function($boughtItem) {
                // Get the billno from the bought item relation
                return $boughtItem->boughtItemRelation->billno ?? $boughtItem->billno ?? '';
            })
            
            ->addColumn('buy_up', function($boughtItem) {
                // If tax is enabled, show price with VAT
                if ($boughtItem->buy_tax_per && $boughtItem->buy_tax_per > 0) {
                    return $boughtItem->buy_up_vat;
                }
                return $boughtItem->buy_up; 
            })
            
            ->addColumn('buy_up_vat', function($boughtItem) {
                return $boughtItem->buy_up_vat; 
            })
            
            ->addColumn('buy_tax_per', function($boughtItem) {
                return "% " . $boughtItem->buy_tax_per;
            })
            
            ->addColumn('total', function ($boughtItem) use ($tax_activation) {
                // If tax is enabled, show total with VAT
                if ($boughtItem->buy_tax_per && $boughtItem->buy_tax_per > 0) {
                    return $boughtItem->total_vat;
                }
                return $boughtItem->total;
            })
            
            // =============================================
            // FIXED: RETURN COLUMN
            // =============================================
            ->addColumn('return', function ($boughtItem) {
                // Get the related BoughtItem
                $boughtItemRelation = $boughtItem->boughtItemRelation;
                
                // Check if we have a relation and if it has_invoice = 0 and amount > 0
                $canReturn = false;
                
                if ($boughtItemRelation) {
                    $hasInvoice = (int) ($boughtItemRelation->has_invoice ?? 0);
                    $amount = (float) ($boughtItem->amount ?? 0);
                    
                    // Can return if: no invoice created, amount > 0, and not already returned
                    $canReturn = ($hasInvoice === 0 && $amount > 0);
                }
                
                if ($canReturn) {
                    return '<i class="fas fa-exchange-alt returnItem" 
                            data-id="' . $boughtItem->id . '" 
                            data-billno="' . ($boughtItemRelation->billno ?? '') . '"
                            data-amount="' . $boughtItem->amount . '"
                            data-times="' . ($boughtItemRelation->times ?? '') . '"
                            style="font-size:20px; color: #05a7eb; cursor: pointer;" 
                            title="Return this item">
                            </i>';
                }
                
                // Show checkmark if already returned or cannot return
                if ($boughtItemRelation && $boughtItemRelation->is_returned == 1) {
                    return '<i class="fas fa-check-circle returnItem" style="font-size:20px; color: #05a7eb;" title="Already returned"></i>';
                }
                
                return '<i class="fas fa-times-circle" style="font-size:20px; color: #b2bec3;" title="Cannot return"></i>';
            })
            
            ->rawColumns(['billno', 'view', 'return'])
            ->make(true);
    }


    // get for return
    public function getSingleRecordForReturn(string $id)
    {
        $units = Unit::select('id','name')->get();
        $tax_activation = OrgBio::select('tax_activation')->first();
        $boughtItemDetails = BoughtItemDetails::with(['accountRelation','preListRelation','unitRelation'])->where('id', $id)->first();

        if (!$boughtItemDetails) {
            return response()->json(['error' => 'Bought Item Details not found'], 404);
        }

        return view('buy.bought.returnModalContent', compact('boughtItemDetails', 'units','tax_activation'));
    }


    public function addReturn(Request $request)
    {
        // return response()->json(['formData' => $request->all()]);

        DB::beginTransaction();

        try {
            $request->validate([
                'id' => 'required|exists:bought_item_details,id',
                'amount' => 'required|numeric|min:0.01',
                'reason' => 'nullable|string|max:500',
            ]);

            $id = $request->id;
            $returnAmount = (float) $request->amount;
            
            // Get the bought item details
            $boughtItemDetail = BoughtItemDetails::with([
                'boughtItemRelation',
                'preListRelation',
                'unitRelation',
                'accountRelation'
                ])->findOrFail($id);
                
                $boughtItem = $boughtItemDetail->boughtItemRelation;
                
            
            if (!$boughtItem) {
                DB::rollBack();
                Session::put('notification', [
                    'message' => __('common.record_not_found'),
                    'type' => 'danger',
                ]);
                return redirect()->route('boughtListBasedItem.index');
            }

            // Validate return amount
            if ($returnAmount > $boughtItemDetail->amount) {
                DB::rollBack();
                Session::put('notification', [
                    'message' => __('buy.return_amount_exceeds_quantity'),
                    'type' => 'danger',
                ]);
                return redirect()->route('boughtListBasedItem.index');
            }

            // Check if invoice exists
            if ($boughtItem->has_invoice == 1) {
                DB::rollBack();
                Session::put('notification', [
                    'message' => __('buy.cannot_return_invoiced'),
                    'type' => 'danger',
                ]);
                return redirect()->route('boughtListBasedItem.index');
            }


             

            // =============================================
            // FIX 1: Define all variables properly
            // =============================================
            $buyUp = (float) ($boughtItemDetail->buy_up ?? 0);
            $buyUpVat = (float) ($boughtItemDetail->buy_up_vat ?? $buyUp);
            $taxPercentage = (float) ($boughtItemDetail->buy_tax_per ?? 0);
            $taxAmount = (float) ($boughtItemDetail->buy_tax_price ?? 0);
            
            // Determine which price to use (with or without tax)
            $unitPrice = $taxPercentage > 0 ? $buyUpVat : $buyUp;
            
            // Calculate totals
            $totalPriceReturned = $returnAmount * $unitPrice;


            // Check if this bought has payment and remained is less than totalPriceReturned, should update from bought details
            if ($totalPriceReturned > $boughtItem->remained) {
                DB::rollBack();
                  Session::put('notification', [
                    'message' => __('buy.should_update_from_bought_list'),
                    'type' => 'danger',
                ]);
                return redirect()->route('boughtListBasedItem.index');
            }


            // =============================================
            // 1. CREATE RETURN RECORD
            // =============================================
            $returnNumber = 'RET-' . date('Ymd') . '-' . str_pad(BoughtReturn::count() + 1, 4, '0', STR_PAD_LEFT);
            
            $boughtReturn = BoughtReturn::create([
                'bought_item_id' => $boughtItem->id,
                'bought_item_detail_id' => $boughtItemDetail->id,
                'billno' => $boughtItem->billno,
                'return_number' => $returnNumber,
                'return_date' => now()->format('Y-m-d'),
                'supplier_account_id' => $boughtItem->supplier_account_id,
                'pre_list_id' => $boughtItemDetail->pre_list_id,
                'unit_id' => $boughtItemDetail->unit_id,
                'quantity' => $returnAmount,
                'unit_price' => $unitPrice,
                'total' => $totalPriceReturned,
                'tax_percentage' => $taxPercentage,
                'tax_amount' => $taxAmount,
                'currency_id' => $boughtItem->currency_id,
                'reason' => $request->reason,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->full_name ?? '',
            ]);

            // =============================================
            // 2. UPDATE WAREHOUSE ITEMS 
            // =============================================
            $warehouseItem = WarehouseItem::where('buy_pre_id', $boughtItemDetail->pre_list_id)
                ->where('unit_id', $boughtItemDetail->unit_id)
                ->where('times', $boughtItem->times)
                ->first();

            $newAvailableAmount = $warehouseItem->available_amount - $returnAmount;
            $newInAmount = $warehouseItem->in_amount - $returnAmount;

            // Determine which price to use based on tax
            $pricePerUnit = $warehouseItem->buy_tax_per > 0 
                ? $warehouseItem->buy_up_vat 
                : $warehouseItem->buy_up;

            // Calculate totals using the determined price
            $newAvailableTotal = $pricePerUnit * $newAvailableAmount;
            $newWarehouseTotal = $pricePerUnit * $newInAmount;

            if ($warehouseItem) 
            {
                $warehouseItem->in_amount = $newInAmount;
                $warehouseItem->available_amount = $newAvailableAmount;
                $warehouseItem->total = $newWarehouseTotal;
                $warehouseItem->available_total = $newAvailableTotal;
                $warehouseItem->save();

                if ($warehouseItem->available_amount <= 0) {
                    $warehouseItem->delete();
                }
            }

            // =============================================
            // 3. UPDATE BOUGHT ITEM DETAILS
            // =============================================
            $newAmount = $boughtItemDetail->amount - $returnAmount;
            
            if ($newAmount <= 0) {
                $boughtItemDetail->delete();
            } 
            else 
            {
                $boughtItemDetailTotal = $boughtItemDetail->buy_up * $newAmount;
                $boughtItemDetailTotalVat = $boughtItemDetail->buy_up_vat * $newAmount;
                
                $newTotal = $newAmount * $buyUpVat;
                $boughtItemDetail->update([
                    'amount' => $newAmount,
                    'total' => $boughtItemDetailTotal,
                    'total_vat' => $boughtItemDetailTotalVat,
                ]);
            }

            // =============================================
            // 4. UPDATE BOUGHT ITEM
            // =============================================
            $newTotal = $boughtItem->total - $totalPriceReturned;
            $newTotal = max(0, $newTotal);
            
            // FIX 3: Fix remained calculation
            $newRemained = $newTotal - $boughtItem->cur_pay;
            $newRemained = max(0, $newRemained);
            
            $updateData = [
                'total' => $newTotal,
                'remained' => $newRemained
            ];
            
            
            $boughtItem->update($updateData);

            // =============================================
            // 5. CREATE RETURN JOURNAL ENTRIES (Only if total > 0)
            // =============================================
            if ($totalPriceReturned > 0) {
                $this->createReturnJournalEntries($boughtReturn);
            }

            DB::commit();

            Session::put('notification', [
                    'message' => __('buy.returned_successfully'),
                    'type' => 'success',
                ]);
            return redirect()->route('boughtListBasedItem.index');


        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Process Return Error: ' . $e->getMessage());
            Session::put('notification', [
                    'message' => __('common.error_occurred'),
                    'type' => 'danger',
            ]);
            return redirect()->route('boughtListBasedItem.index');
        }
    }

  
    /**
     * Create return journal entries
     */
    private function createReturnJournalEntries($boughtReturn)
    {
        $date = Carbon::now();
        $short_date = $date->format('Y-m-d');
        $time = time();
        $newCode = Journal::max('code') + 1;

        // Get account details
        $supplierAccount = Account::find($boughtReturn->supplier_account_id);
        $cashAccount = Account::whereIn('account_type_id', [1, 6])->first();

        // Create journal entries for the return
        // 1. Supplier account (Credit)
        // ثبت طلب مشتری
        Journal::create([
            'bill_no' => $boughtReturn->billno,
            'code' => $newCode,
            'account_type_id' => $supplierAccount->account_type_id ?? 4,
            'account_id' => $boughtReturn->supplier_account_id,
            'amount' => $boughtReturn->total,
            'currency_id' => $boughtReturn->currency_id,
            'transaction_type' => 2, // Paid 
            'payment_type' => 2, // Loan
            'option_label' => 'Return from supplier',
            'dynamic_type' => 11,
            'dt_comment' => 'returned',
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->full_name ?? '',
            'year' => $date->year,
            'month' => $date->month,
            'day' => $date->day,
            'idate' => $short_date,
            'details' => 'برگشتی  #' . $boughtReturn->return_number . ' - BUY_'.$boughtReturn->billno,
            'status' => 11,
            'times' => $time,
            'is_single_record' => 1,
        ]);

        // 2. Cash/Purchase account (Debit)
        // ثبت قرض شرکت یا خزانه شرکت
        if ($cashAccount) {
            Journal::create([
                'bill_no' => $boughtReturn->billno,
                'code' => $newCode,
                'account_type_id' => $cashAccount->account_type_id,
                'account_id' => $cashAccount->id,
                'amount' => $boughtReturn->total,
                'currency_id' => $boughtReturn->currency_id,
                'transaction_type' => 1, // Recieved
                'payment_type' => 2, // Loan
                'option_label' => 'Return payment',
                'dynamic_type' => 11,
                'dt_comment' => 'returned',
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->full_name ?? '',
                'year' => $date->year,
                'month' => $date->month,
                'day' => $date->day,
                'idate' => $short_date,
                'details' => 'برگشتی  #' . $boughtReturn->return_number . ' - BUY_'.$boughtReturn->billno,
                'status' => 11,
                'times' => $time,
                'is_single_record' => 1,
            ]);
        }
    }





}
