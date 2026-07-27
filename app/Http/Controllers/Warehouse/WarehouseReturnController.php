<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Setting\Currency;
use App\Models\Setting\Account;
use App\Models\Transaction\Journal;
use Carbon\Carbon;
use App\Models\Setting\OrgBio;
use App\Models\Setting\Unit;
use App\Models\Buy\BuyPreList;
use App\Models\Setting\Warehouse;
use App\Models\Warehouse\WarehouseItem;
use App\Models\Warehouse\WarehouseReturn;
use Yajra\DataTables\Facades\DataTables;

class WarehouseReturnController extends Controller
{
    protected $isAdmin, $userId, $carIds, $userName;
    
    public function __construct()
    {
        if (auth()->check()) {
            $this->isAdmin = session('isAdmin', auth()->user()->isAdmin == 1);
            $this->userName = auth()->user()->full_name;
            $this->userId = session('userId', auth()->user()->id);
            $this->carIds = session('carIds', []);
        } else {
            $this->isAdmin = false;
            $this->userId = 0;
            $this->userName = '';
            $this->carIds = [];
        }
    }
    
    public function returnList()
    {
        $orgbios = OrgBio::all();
        $todaysDate = Carbon::now()->format('Y-m-d');
        $suppliers = Account::select('id','name')->whereIn('account_type_id',[4])->get();
        
        return view('warehouseitem.return.list', compact('orgbios', 'todaysDate', 'suppliers'));
    }

    public function getData(Request $request)
    {
        $returns = WarehouseReturn::with([
            'supplier', 
            'preList', 
            'unit',
            'currency',
            'car',
            'warehouseItem',
        ])->orderBy('id', 'DESC');
        
        // Apply filters
        if ($request->return_number) {
            $returns->where('return_number', 'LIKE', "%{$request->return_number}%");
        }
        
        if ($request->billno) {
            $returns->where('billno', $request->billno);
        }
        
        if ($request->supplier_id) {
            $returns->where('supplier_account_id', $request->supplier_id);
        }
        
        if ($request->start_date && $request->end_date) {
            $returns->whereBetween('return_date', [$request->start_date, $request->end_date]);
        } elseif ($request->start_date) {
            $returns->whereDate('return_date', '=', $request->start_date);
        } elseif ($request->end_date) {
            $returns->whereDate('return_date', '<=', $request->end_date);
        }
        
        return DataTables::of($returns)
            ->addIndexColumn()
            ->addColumn('return_number', function($return) {
                return '<strong>' . $return->return_number . '</strong>';
            })
            ->addColumn('billno', function($return) {
                return 'BUY_' . ($return->billno ?? '');
            })
            ->addColumn('supplier_name', function($return) {
                return $return->supplier->name ?? '-';
            })
            ->addColumn('item_name', function($return) {
                return $return->preList->name ?? '-';
            })
            ->addColumn('unit_name', function($return) {
                return $return->unit->name ?? '-';
            })
            ->addColumn('quantity', function($return) {
                return number_format($return->quantity, 2);
            })
            ->addColumn('total', function($return) {
                return number_format($return->total, 2);
            })
            ->addColumn('return_date', function($return) {
                return $return->return_date ? $return->return_date->format('Y-m-d') : '-';
            })
            ->addColumn('reason', function($return) {
                return $return->reason ?? '-';
            })
            ->addColumn('car_name', function($return) {
                return $return->car->name ?? '-';
            })
            ->addColumn('status_badge', function($return) {
                return $return->status_badge;
            })
            ->addColumn('created_by', function($return) {
                return $return->user_name ?? '-';
            })
            ->addColumn('action', function($return) {
                $actions = '<button class="btn btn-sm btn-info viewReturn" data-id="' . $return->id . '">
                                <i class="fas fa-eye"></i>
                            </button>';
                $actions .= '<button class="btn btn-sm btn-warning editReturn m-r-5" data-id="' . $return->id . '">
                                <i class="fas fa-edit"></i>
                            </button>';
                
                // if ($return->isEditable()) {
                //     $actions .= ' <a href="' . route('return.editReturn', $return->id) . '" class="btn btn-sm btn-warning">
                //                     <i class="fas fa-edit"></i>
                //                 </a>';
                // }
                
                return $actions;
            })
            ->rawColumns(['return_number', 'status_badge', 'action'])
            ->make(true);
    }

    public function viewReturn($id)
    {
        $return = WarehouseReturn::with([
            'supplier', 
            'preList', 
            'unit',
            'currency',
            'car',
            'warehouseItem',
            'user'
        ])->find($id);
        
        if (!$return) {
            return response()->json([
                'status' => 'error',
                'message' => 'Return not found'
            ], 404);
        }
        
        return view('warehouseitem.return.view', compact('return'));
    }

    public function editReturn($id)
    {
        $return = WarehouseReturn::with([
            'supplier', 
            'preList', 
            'unit',
            'currency',
        ])->find($id);
        
        if (!$return) {
            return redirect()->route('return.list')
                ->with('notification', [
                    'type' => 'danger',
                    'message' => 'Return not found'
                ]);
        }
        
        if (!$return->isEditable()) {
            return redirect()->route('return.list')
                ->with('notification', [
                    'type' => 'warning',
                    'message' => 'This return cannot be edited'
                ]);
        }
        
        // $preLists = BuyPreList::select('id', 'name')->get();
        // $units = Unit::select('id', 'name')->get();
        // $currencies = Currency::select('id', 'name')->get();
        $ownBanks = Account::select('id', 'name')->whereIn('account_type_id',  [1,6])->get();
        $suppliers = Account::select('id', 'name')->whereIn('account_type_id', [4])->get();
        
        return view('warehouseitem.return.edit', compact(
            'return', 
            'ownBanks', 
            'suppliers'
        ));
    }

    public function updateReturn(Request $request)
    {
        // return response()->json($request->all());
       
        $validated = $request->validate([
            'id' => 'required|exists:warehouse_returns,id',
            'billno' => 'required|exists:warehouse_returns,billno',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_type' => 'required|in:1,2',
            'payer_account_id' => 'required|exists:accounts,id',
            'receiver_account_id' => 'required|exists:accounts,id',
        ]);

        DB::beginTransaction();
        try {
            $return = WarehouseReturn::findOrFail($validated['id']);
            
            // Update paid amount
            if (isset($validated['paid_amount']) && $validated['paid_amount'] > 0) {
                $newPaidAmount = $return->paid_amount + $validated['paid_amount'];
                $newRemaining = $return->total - $newPaidAmount;
                
                $return->update([
                    'paid_amount' => $newPaidAmount,
                    'remaining_amount' => $newRemaining,
                ]);
            }

            // Create journal entry for payment
            if ($validated['paid_amount'] > 0) 
            {
                $journalCode = Journal::max('code') + 1;
                $times = time();

                $payment_type = (int)$validated['payment_type'];

                // Payer journal (debit)
                $payerJournal = new Journal();
                // Receiver journal (credit)
                $receiverJournal = new Journal();

                $payer_type_id = Account::where('id', $validated['payer_account_id'])->value('account_type_id');
                $reciever_type_id = Account::where('id', $validated['receiver_account_id'])->value('account_type_id');
                /**
                 * payment_type = 1: معاملات نقد به نقد  
                 * payment_type = 2: معاملات نسیه به نسیه
                 */
                if($payment_type === 1)  
                {
                       // تهیه کننده نقد پرداخت میکند و خزانه نقد دریافت میکند
                        // T2P1 = Cache Paid = ثبت پرداخت تهیه کننده
                        $payerJournal->account_id = $validated['payer_account_id'];
                        $payerJournal->transaction_type = 2; // Paid
                        $payerJournal->payment_type = 1;
                        $payerJournal->amount = $validated['paid_amount'];
                        $payerJournal->account_type_id = $payer_type_id;
                        $payerJournal->details = __('validate.cache_payment') .'RET_'.'BUY_'. $return->billno;


                        // T1P1 = Cache Recieved = ثبت دریافت نقد خزانه
                        $receiverJournal->account_id = $validated['receiver_account_id'];
                        $receiverJournal->currency_id = $return->currency_id;
                        $receiverJournal->transaction_type = 1; // Received
                        $receiverJournal->payment_type = 1;
                        $receiverJournal->account_type_id = $reciever_type_id;
                        $receiverJournal->details = __('validate.cache_recieved') .'RET_'.'BUY_'. $return->billno;

                } 
                else 
                { // تهیه کننده قرضدار ثبت شود و خزانه طلب ثبت شود
                        // T2P2 = ثبت طلب خزانه
                        $receiverJournal->account_id = $validated['receiver_account_id'];
                        $receiverJournal->currency_id = $return->currency_id;
                        $receiverJournal->transaction_type = 2; // Received
                        $receiverJournal->payment_type = 2;
                         $receiverJournal->account_type_id = $reciever_type_id;
                        $receiverJournal->details = __('validate.saved_talab') .'RET_'.'_BUY_'. $return->billno;

                        // t1p2 = ثبت قرض تهیه کننده
                        $payerJournal->account_id = $validated['payer_account_id'];
                        $payerJournal->transaction_type = 1; // Paid
                        $payerJournal->payment_type = 2;
                        $payerJournal->amount = $validated['paid_amount'];
                        $payerJournal->account_type_id = $payer_type_id;
                        $payerJournal->details = __('validate.saved_loan') .'RET_'.'_BUY_'. $return->billno;
                }
                
                
                $payerJournal->code = $journalCode;
                $payerJournal->bill_no = $return->billno;
                $payerJournal->currency_id = $return->currency_id;
                $payerJournal->status = 11; // Return
                $payerJournal->idate = now()->format('Y-m-d');
                $payerJournal->year = now()->year;
                $payerJournal->month = now()->month;
                $payerJournal->day = now()->day;
                $payerJournal->user_id = $this->userId;
                $payerJournal->user_name = $this->userName ?? 'System';
                $payerJournal->times = $times;
                $payerJournal->is_cleared = 0;
                $payerJournal->is_single_record = 1;
                $payerJournal->save();
                
                
                $receiverJournal->code = $journalCode;
                $receiverJournal->bill_no = $return->billno;
                $receiverJournal->amount = $validated['paid_amount'];
                $receiverJournal->status = 11; // Return
                $receiverJournal->idate = now()->format('Y-m-d');
                $receiverJournal->year = now()->year;
                $receiverJournal->month = now()->month;
                $receiverJournal->day = now()->day;
                $receiverJournal->user_id = $this->userId;
                $receiverJournal->user_name = $this->userName ?? 'System';
                $receiverJournal->times = $times;
                $receiverJournal->is_cleared = 0;
                $receiverJournal->is_single_record = 1;
                $receiverJournal->save();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => __('common.updated_successfully')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Return Update Error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => __('common.error_occurred') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteReturn($id)
    {
        $return = WarehouseReturn::find($id);
        
        if (!$return) {
            return response()->json([
                'status' => 'error',
                'message' => 'Return not found'
            ], 404);
        }
        
        if (!$return->isDeletable()) {
            return response()->json([
                'status' => 'error',
                'message' => 'This return cannot be deleted'
            ], 400);
        }
        
        DB::beginTransaction();
        try {
            $return->delete();
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => __('common.deleted_successfully')
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Return Delete Error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => __('common.delete_failed') . ': ' . $e->getMessage()
            ], 500);
        }
    }
}