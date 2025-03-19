<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth; 
use Morilog\Jalali\Jalalian;
use Illuminate\Support\Facades\DB;
use App\Models\Warehouse\WarehouseSales;
use App\Models\Buy\BoughtItem;
use App\Models\Setting\Currency;
use App\Models\Setting\Account;
use App\Models\Journal\Journal;
use App\Models\Warehouse\SalesDetails;
use App\Models\Setting\OrgBio;

class ProfitAndLossController extends Controller
{
    protected $branch_id, $isAdmin;
    public function __construct()
    {
        if (auth()->check()) {
            $this->branch_id = session('branch_id', auth()->user()->branch_id ?? 0);
            $this->isAdmin = session('isAdmin', auth()->user()->isAdmin == 1);
        } else {
            $this->branch_id = 0;
            $this->isAdmin = false;
        }
    }

    public function index()
    {
        // Step 2: Get all currencies
        $orgbios = OrgBio::all();
        $currencies = Currency::all();
        $data = $this->getIncomeSection();
        $branch_id = $this->branch_id ?? 0;
        // $talabat = $this->getTalabat();
        // $talabat = $this->getTalabat()->map(fn($item) => (array) $item)->toArray(); // Convert objects to arrays
        // return ['talabat' => $talabat];

        // $transactionSummary = $this->getTransactionSummary(); 
        // return ['data' => $transactionSummary];
        $transactionSummary = $this->getTransactionSummary($branch_id)->map(fn($item) => (array) $item)->toArray(); // Convert objects to arrays
        $warehouseValue = $this->getWarehouseValue($branch_id)->map(fn($item) => (array) $item)->toArray();
        $salesProfit = $this->getSalesProfit($branch_id)->map(fn($item) => (array) $item)->toArray();
        // return ['salesProfit' => $salesProfit];

        return view('report.profitAndLoss.list', compact('transactionSummary','currencies','warehouseValue','salesProfit','orgbios'));
    }

    private function getSalesProfit($branch_id)
    {
        $baseCurrency = DB::table('currencies')->where('is_base', 'yes')->first();
        if (!$baseCurrency) {
            throw new \Exception("Base currency not set in the database.");
        }

        $currencies = DB::table('currencies')
            ->select('id', 'name', 'symbols', 'is_base', 'color')
            ->get()
            ->keyBy('id');

        $rates = DB::table('rates')
            ->where(function ($query) use ($currencies, $baseCurrency) {
                $query->whereIn('from_currency_id', $currencies->pluck('id')->toArray())
                    ->where('to_currency_id', $baseCurrency->id);
            })
            ->orWhere(function ($query) use ($baseCurrency) {
                $query->where('from_currency_id', $baseCurrency->id);
            })
            ->get()
            ->keyBy(fn($item) => "{$item->from_currency_id}_{$item->to_currency_id}");

        $warehouseData = DB::table('sales_details')
            ->join('warehouse_sales', 'warehouse_sales.id', '=', 'sales_details.warehouse_sales_id')
            ->selectRaw('SUM(sales_details.profit) as total_profit, warehouse_sales.currency_id')
            ->where('warehouse_sales.branch_id', $branch_id)
            ->groupBy('warehouse_sales.currency_id')
            ->get()
            ->keyBy('currency_id');

        // Process Data & Convert to Base Currency
        $result = collect($currencies)->map(function ($currency) use ($warehouseData, $rates, $baseCurrency) {
            $item = new \stdClass();
            $item->currency_id = $currency->id;
            $item->currency_name = $currency->name;
            $item->symbols = $currency->symbols;
            $item->is_base = ($currency->is_base === 'yes') ? 1 : 0;
            $item->color = $currency->color;

            // Calculate total profit
            $item->total_profit = optional($warehouseData->get($currency->id))->total_profit ?? 0;

            // Convert to Base Currency
            if ($currency->id == $baseCurrency->id) {
                $item->exchange_rate = 1;
                $item->converted_total_profit = $item->total_profit;
            } else {
                $rateKeyFromBase = "{$baseCurrency->id}_{$currency->id}";
                $rateKeyToBase = "{$currency->id}_{$baseCurrency->id}";

                $rateFromBase = $rates->get($rateKeyFromBase);
                $rateToBase = $rates->get($rateKeyToBase);

                if (!$rateFromBase && !$rateToBase) {
                    $item->exchange_rate = null;
                    $item->converted_total_profit = 0;
                } else {
                    if ($rateFromBase) {
                        $item->exchange_rate = $rateFromBase->reverse_amount;
                        $item->converted_total_profit = $item->total_profit * $item->exchange_rate;
                    } elseif ($rateToBase && $rateToBase->reverse_amount != 0) {
                        $item->exchange_rate = $rateToBase->reverse_amount;
                        $item->converted_total_profit = $item->total_profit / $item->exchange_rate;
                    }
                }
            }

            return $item;
        });

        return $result;
    }


    
    private function getWarehouseValue($branch_id)
    {
        $baseCurrency = DB::table('currencies')->where('is_base', 'yes')->first();
        if (!$baseCurrency) {
            throw new \Exception("Base currency not set in the database.");
        }

        $currencies = DB::table('currencies')
            ->select('id', 'name', 'symbols', 'is_base','color')
            ->get()
            ->keyBy('id');

        $rates = DB::table('rates')
            ->whereIn('from_currency_id', $currencies->pluck('id')->toArray())
            ->where('to_currency_id', $baseCurrency->id)
            ->orWhere('from_currency_id', $baseCurrency->id)
            ->get()
            ->keyBy(fn($item) => "{$item->from_currency_id}_{$item->to_currency_id}");

        $warehouseData = DB::table('warehouse_items')
            ->selectRaw('currency_id, SUM(available_amount * avg_up) as total_value, SUM(wastage_total) as total_wastage')
            ->where('branch_id', $branch_id)
            ->groupBy('currency_id')
            ->get()
            ->keyBy('currency_id');

        // Process Data & Convert to Base Currency
        $result = collect($currencies)->map(function ($currency) use ($warehouseData, $rates, $baseCurrency) {
            $item = new \stdClass();
            $item->currency_id = $currency->id;
            $item->currency_name = $currency->name;
            $item->symbols = $currency->symbols;
            $item->is_base = ($currency->is_base === 'yes') ? 1 : 0; // Convert 'yes' to 1, otherwise 0
            $item->color = $currency->color;

            // Calculate total warehouse value (including wastage)
            $total_value = $warehouseData[$currency->id]->total_value ?? 0;
            $total_wastage = $warehouseData[$currency->id]->total_wastage ?? 0;
            $item->total_warehouse_value = $total_value - $total_wastage;

            // Convert to Base Currency
            if ($currency->id == $baseCurrency->id) {
                $item->exchange_rate = 1;
                $item->converted_total_warehouse_value = $item->total_warehouse_value;
            } else {
                $rateKeyFromBase = "{$baseCurrency->id}_{$currency->id}";
                $rateKeyToBase = "{$currency->id}_{$baseCurrency->id}";

                $rateFromBase = $rates->get($rateKeyFromBase);
                $rateToBase = $rates->get($rateKeyToBase);

                if (!$rateFromBase && !$rateToBase) {
                    $item->exchange_rate = null;
                    $item->converted_total_warehouse_value = 0;
                } else {
                    if ($rateFromBase) {
                        $item->exchange_rate = $rateFromBase->reverse_amount;
                        $item->converted_total_warehouse_value = $item->total_warehouse_value * $item->exchange_rate;
                    } elseif ($rateToBase) {
                        $item->exchange_rate = $rateToBase->reverse_amount;
                        $item->converted_total_warehouse_value = $item->total_warehouse_value / $item->exchange_rate;
                    }
                }
            }

            return $item;
        });

        return $result;
    }


    




    private function getTalabat()
    {
        /***
         * 1: Get total_talabs from journal and group by currency_id and join to get currency feilds as well
         * 2: map throught each item and check if rates exists (if currency_id == from_currency_id) exists
         * 3: check if greater multiply with to_currency_amount else multiply with reverse_amount
         * 4: combine converted_amount to the result
         */
        $company_account_type_id = 1; // صرف خزانه شرکت
        $banks_account_type_id = 6; // صرافی و بانک ها

        // Step 1: Get the Base Currency (AFN)
        $baseCurrency = DB::table('currencies')->where('is_base', 'yes')->first();
        if (!$baseCurrency) {
            throw new \Exception("Base currency not set in the database.");
        }

        // Step 2: Get all currencies
        $currencies = DB::table('currencies')
            ->select('id', 'name', 'is_base', 'color', 'symbols')
            ->get()
            ->keyBy('id'); // Store by currency_id for easy lookup

        // Step 3: Fetch Exchange Rates for both directions (from_currency to base, base to to_currency)
        $rates = DB::table('rates')
            ->whereIn('from_currency_id', $currencies->pluck('id')->toArray())  // Get rates for relevant currencies
            ->where('to_currency_id', $baseCurrency->id) // Convert to base currency
            ->orWhere('from_currency_id', $baseCurrency->id)  // Convert from base currency
            ->get()
            ->keyBy(function($item) {
                return "{$item->from_currency_id}_{$item->to_currency_id}";
            });

        // Step 4: Fetch Talabat Data
        $talabatData = DB::table('journals')
            ->selectRaw("
                journals.currency_id,
                SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 2 THEN amount ELSE 0 END) as total_talabat
            ")
            ->join('accounts', 'accounts.id', '=', 'journals.account_id')
            ->whereIn('accounts.account_type_id', [$company_account_type_id, $banks_account_type_id])
            ->where('journals.is_cleared', 0)
            ->groupBy('journals.currency_id')
            ->get()
            ->keyBy('currency_id'); // Store by currency_id for easy lookup

        // Step 5: Prepare Final Report with All Currencies
        $result = collect($currencies)->map(function ($currency) use ($talabatData, $rates, $baseCurrency) {
            $item = new \stdClass();
            $item->currency_id = $currency->id;
            $item->currency_name = $currency->name;
            $item->is_base = ($currency->is_base === 'yes') ? 1 : 0; // Convert 'yes' to 1, otherwise 0
            $item->color = $currency->color;
            $item->symbols = $currency->symbols;
            $item->total_talabat = $talabatData[$currency->id]->total_talabat ?? 0; // If no transactions, show 0

            // Step 6: Convert to Base Currency (Include Exchange Rate)
            if ($currency->id == $baseCurrency->id) {
                // If it's the base currency, no conversion needed
                $item->exchange_rate = 1; // No conversion needed
                $item->converted_total = $item->total_talabat;
                $item->to_currency_amount = null; // No to_currency_amount for base
            } else {
                // Find the rate from the database (handle both directions)
                $rateKeyFromBase = "{$baseCurrency->id}_{$currency->id}";
                $rateKeyToBase = "{$currency->id}_{$baseCurrency->id}";

                // Get the rates for both directions
                $rateFromBase = $rates->get($rateKeyFromBase); 
                $rateToBase = $rates->get($rateKeyToBase); 

                // If no rate found, skip conversion and set converted total to zero
                if (!$rateFromBase && !$rateToBase) {
                    $item->exchange_rate = null;
                    $item->converted_total = 0;
                    $item->to_currency_amount = null;
                    return $item;
                }

                // Initialize conversion logic based on rates
                $greaterCurrencyId = null;
                if ($rateFromBase) {
                    $greaterCurrencyId = $rateFromBase->greater_account_id;
                }
                if ($rateToBase) {
                    $greaterCurrencyId = $rateToBase->greater_account_id;
                }

                if ($item->total_talabat > 0) {
                    if ($rateFromBase) {
                        // Use reverse_amount when the rate is from base to another currency
                        $item->exchange_rate = $rateFromBase->reverse_amount; // reverse_amount used for multiplication
                        $item->converted_total = $item->total_talabat * $item->exchange_rate; // Multiply for greater
                        $item->to_currency_amount = $rateFromBase->to_currency_amount; // Add to_currency_amount
                    } elseif ($rateToBase) {
                        // Use reverse_amount when the rate is from another currency to base
                        $item->exchange_rate = $rateToBase->reverse_amount; // reverse_amount used for multiplication
                        $item->converted_total = $item->total_talabat / $item->exchange_rate; // Divide for smaller
                        $item->to_currency_amount = $rateToBase->to_currency_amount; // Add to_currency_amount
                    }
                } else {
                    // If total talabat is zero, no need for conversion
                    $item->exchange_rate = null;
                    $item->converted_total = 0;
                    $item->to_currency_amount = null;
                }
            }

            return $item;
        });

        return $result;
    }

        
        
    private function getTransactionSummary($branch_id)
    {
        $company_account_type_id = 1; // صرف خزانه شرکت
        $banks_account_type_id = 6; // صرافی و بانک ها

        // Step 1: Get the Base Currency (AFN)
        $baseCurrency = DB::table('currencies')->where('is_base', 'yes')->first();
        if (!$baseCurrency) {
            throw new \Exception("Base currency not set in the database.");
        }

        // Step 2: Get all currencies
        $currencies = DB::table('currencies')
            ->select('id', 'name', 'is_base', 'color', 'symbols')
            ->get()
            ->keyBy('id'); // Store by currency_id for easy lookup

        // Step 3: Fetch Exchange Rates
        $rates = DB::table('rates')
            ->whereIn('from_currency_id', $currencies->pluck('id')->toArray())
            ->where('to_currency_id', $baseCurrency->id)
            ->orWhere('from_currency_id', $baseCurrency->id)
            ->get()
            ->keyBy(function($item) {
                return "{$item->from_currency_id}_{$item->to_currency_id}";
            });

        // Step 4: Fetch Transaction Data
        $transactionData = DB::table('journals')
            ->selectRaw("
                journals.currency_id,
                SUM(CASE WHEN journals.status = 4 THEN amount ELSE 0 END) as total_expense,
                SUM(CASE WHEN journals.status = 3 THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN journals.status = 5 THEN amount ELSE 0 END) as total_salary,
                SUM(CASE WHEN journals.status = 7 THEN amount ELSE 0 END) as total_bought,
                SUM(CASE WHEN journals.status = 8 THEN amount ELSE 0 END) as total_sold,
                SUM(CASE WHEN journals.transaction_type = 1 AND payment_type = 1 THEN amount ELSE 0 END) as total_cache_in,
                SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 1 THEN amount ELSE 0 END) as total_cache_out,
                SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 2 THEN amount ELSE 0 END) as total_talabat,
                SUM(CASE WHEN journals.transaction_type = 1 AND payment_type = 2 THEN amount ELSE 0 END) as total_loan
            ")
            ->whereIn('journals.account_type_id', [$company_account_type_id, $banks_account_type_id])
            ->where('journals.is_cleared', 0)
            ->where('journals.branch_id', $branch_id)
            ->groupBy('journals.currency_id')
            ->get()
            ->keyBy('currency_id');

        // Step 5: Prepare Final Report
        $result = collect($currencies)->map(function ($currency) use ($transactionData, $rates, $baseCurrency) {
            $item = new \stdClass();
            $item->currency_id = $currency->id;
            $item->currency_name = $currency->name;
            $item->is_base = ($currency->is_base === 'yes') ? 1 : 0;
            $item->color = $currency->color;
            $item->symbols = $currency->symbols;

            // Set totals for each field
            foreach (['total_expense', 'total_income', 'total_salary', 'total_bought', 'total_sold', 'total_cache_in', 'total_cache_out', 'total_talabat', 'total_loan'] as $field) {
                $item->$field = $transactionData[$currency->id]->$field ?? 0;
            }

            // Initialize exchange rate
            $item->exchange_rate = 1; // Default (for base currency)

            // Convert to Base Currency
            if ($currency->id != $baseCurrency->id) {
                $rateKeyFromBase = "{$baseCurrency->id}_{$currency->id}";
                $rateKeyToBase = "{$currency->id}_{$baseCurrency->id}";

                $rateFromBase = $rates->get($rateKeyFromBase);
                $rateToBase = $rates->get($rateKeyToBase);

                // if ($rateFromBase) {
                //     $item->exchange_rate = round($rateFromBase->reverse_amount, 2);
                // } elseif ($rateToBase) {
                //     $item->exchange_rate = round(1 / $rateToBase->reverse_amount, 2);
                // } else {
                //     $item->exchange_rate = null; // No conversion available
                // }

                if ($rateFromBase) {
                    $item->exchange_rate = $rateFromBase->reverse_amount;
                } elseif ($rateToBase) {
                    $item->exchange_rate = 1 / $rateToBase->reverse_amount;
                } else {
                    $item->exchange_rate = null; // No conversion available
                }
            }

            // Convert amounts
            foreach (['total_expense', 'total_income', 'total_salary', 'total_bought', 'total_sold', 'total_cache_in', 'total_cache_out', 'total_talabat', 'total_loan'] as $field) {
                $item->{"converted_$field"} = ($item->exchange_rate !== null) ? $item->$field * $item->exchange_rate : 0;
            }

            return $item;
        });

        return $result;
    }

    

    
    

    


    // private function getTalabat()
    // {
    //     $company_account_type_id = 1; // صرف خزانه شرکت
    //     $banks_account_type_id = 6; // صرافی و بانک ها

    //     // Step 1: Get the Base Currency (AFN)
    //     $baseCurrency = DB::table('currencies')->where('is_base', 'yes')->first();
    //     if (!$baseCurrency) {
    //         throw new \Exception("Base currency not set in the database.");
    //     }

    //     // Step 2: Get all currencies
    //     $currencies = DB::table('currencies')
    //         ->select('id', 'name', 'is_base','color','symbols')
    //         ->get()
    //         ->keyBy('id'); // Store by currency_id for easy lookup

    //     // Step 3: Fetch Exchange Rates (Convert to Base Currency)
    //     $rates = DB::table('rates')
    //         ->where('to_currency_id', $baseCurrency->id) // Get rates for converting to base currency
    //         ->pluck('to_currency_amount', 'from_currency_id'); // Key: from_currency_id, Value: rate to base currency

    //     // Step 4: Fetch Talabat Data
    //     $talabatData = DB::table('journals')
    //         ->selectRaw("
    //             journals.currency_id,
    //             SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 2 THEN amount ELSE 0 END) as total_talabat
    //         ")
    //         ->join('accounts', 'accounts.id', '=', 'journals.account_id')
    //         ->whereIn('accounts.account_type_id', [$company_account_type_id, $banks_account_type_id])
    //         ->where('journals.is_cleared', 0)
    //         ->groupBy('journals.currency_id')
    //         ->get()
    //         ->keyBy('currency_id'); // Store by currency_id for easy lookup

    //     // Step 5: Prepare Final Report with All Currencies
    //     $result = collect($currencies)->map(function ($currency) use ($talabatData, $rates, $baseCurrency) {
    //         $item = new \stdClass();
    //         $item->currency_id = $currency->id;
    //         $item->currency_name = $currency->name;
    //         $item->is_base = ($currency->is_base === 'yes') ? 1 : 0; // Convert 'yes' to 1, otherwise 0
    //         $item->color = $currency->color;
    //         $item->symbols = $currency->symbols;
    //         $item->total_talabat = $talabatData[$currency->id]->total_talabat ?? 0; // If no transactions, show 0

    //         // Step 6: Convert to Base Currency (Include Exchange Rate)
    //         if ($currency->id == $baseCurrency->id) {
    //             $item->exchange_rate = 1; // No conversion needed
    //             $item->converted_total = $item->total_talabat;
    //         } elseif (isset($rates[$currency->id])) {
    //             $item->exchange_rate = $rates[$currency->id]; // Get rate
    //             $item->converted_total = $item->total_talabat * $item->exchange_rate;
    //         } else {
    //             $item->exchange_rate = null; // No rate available
    //             $item->converted_total = null;
    //         }

    //         return $item;
    //     });

    //     return $result;
    // }



    // -------------- بدون ریکاردهای خالی کرنسی ها -------------------
    // private function getTalabat()
    // {
    //     $company_account_type_id = 1; // صرف خزانه شرکت
    //     $banks_account_type_id = 6; // صرافی و بانک ها

    //     // Step 1: Get the Base Currency (AFN)
    //     $baseCurrency = DB::table('currencies')->where('is_base', 'yes')->first();
    //     if (!$baseCurrency) {
    //         throw new \Exception("Base currency not set in the database.");
    //     }

    //     $currencies = DB::table('currencies')->pluck('name', 'id');

    //     // Step 2: Fetch Exchange Rates (Converting all to Base Currency)
    //     $rates = DB::table('rates')
    //         ->where('to_currency_id', $baseCurrency->id) // Get rates for converting to base currency
    //         ->pluck('to_currency_amount', 'from_currency_id'); // Key: from_currency_id, Value: rate to base currency

    //     // Step 3: Fetch Talabat Data
    //     $result = DB::table('journals')
    //         ->selectRaw("
    //             journals.currency_id,
    //             SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 2 THEN amount ELSE 0 END) as total_talabat
    //         ")
    //         ->join('accounts', 'accounts.id', '=', 'journals.account_id')
    //         ->whereIn('accounts.account_type_id', [$company_account_type_id, $banks_account_type_id])
    //         ->where('journals.is_cleared', 0)
    //         ->groupBy('journals.currency_id')
    //         ->get()
    //         ->map(function ($item) use ($currencies, $rates, $baseCurrency) {
    //             $item->currency_name = $currencies[$item->currency_id] ?? 'Unknown';

    //             // Step 4: Convert to Base Currency (Include Exchange Rate)
    //             if ($item->currency_id == $baseCurrency->id) {
    //                 $item->exchange_rate = 1; // No conversion needed
    //                 $item->converted_total = $item->total_talabat;
    //             } elseif (isset($rates[$item->currency_id])) {
    //                 $item->exchange_rate = $rates[$item->currency_id]; // Get rate
    //                 $item->converted_total = $item->total_talabat * $item->exchange_rate;
    //             } else {
    //                 $item->exchange_rate = null; // No rate available
    //                 $item->converted_total = null;
    //             }

    //             return $item;
    //         });

    //     return $result;
    // }

    // ------------------- قبل از کانورت کردن کرنسی ها ----------------
    // private function getTalabat()
    // {
    //     $company_account_type_id = 1; // صرف خزانه شرکت
    //     $banks_account_type_id = 6; // صرافی و بانک ها
        
    //         $currencies = DB::table('currencies')->pluck('name', 'id');
    //         $result = DB::table('journals')
    //                     ->selectRaw("
    //                         journals.currency_id,
    //                         SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 2 THEN amount ELSE 0 END) as total_talabat
    //                     ")
    //                     ->join('accounts', 'accounts.id', '=', 'journals.account_id')
    //                     ->whereIn('accounts.account_type_id', [$company_account_type_id, $banks_account_type_id])
    //                     ->where('journals.is_cleared', 0)
    //                     ->groupBy('journals.currency_id')
    //                     ->get()
    //                     ->map(function ($item) use ($currencies) {
    //                         $item->currency_name = $currencies[$item->currency_id] ?? 'Unknown';
    //                         return $item;
    //                 });
    //      return $result;
    // }

    function getIncomeSection()
    {
        $company_account_type_id = 1; // صرف خزانه شرکت
        $banks_account_type_id = 6; // صرافی و بانک ها

        // Fetch currency names
        $currencies = DB::table('currencies')->pluck('name', 'id');

        // Total Goods in Warehouse
        $total_warehouse_value = DB::table('warehouse_items')
            ->selectRaw('currency_id, SUM(available_amount * avg_up) as total_value, SUM(wastage_total) as total_wastage')
            ->where('is_cleared', 0)
            ->groupBy('currency_id')
            ->get()
            ->map(function ($item) use ($currencies) {
                $item->currency_name = $currencies[$item->currency_id] ?? 'Unknown';
                return $item;
            });

        /**
         * دریافت پول نقد شرکت = Cache Recieved = p1t1
         * پرداخت پول نقد شرکت = Cache Paid = p1t2
         * طلبات شرکت = Paid Loan = p2t2
         * قرضه شرکت = Recieved Loan = p2t1
         */

        $result = DB::table('journals')
            ->selectRaw("
                journals.currency_id,
                SUM(CASE WHEN journals.status = 4 THEN amount ELSE 0 END) as total_expense,
                SUM(CASE WHEN journals.status = 3 THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN journals.status = 5 THEN amount ELSE 0 END) as total_salary,
                SUM(CASE WHEN journals.status = 7 THEN amount ELSE 0 END) as total_bought,
                SUM(CASE WHEN journals.status = 8 THEN amount ELSE 0 END) as total_sold,
                SUM(CASE WHEN journals.transaction_type = 1 AND payment_type = 1 THEN amount ELSE 0 END) as total_cache_in,
                SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 1 THEN amount ELSE 0 END) as total_cache_out,
                SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 2 THEN amount ELSE 0 END) as total_talabat,
                SUM(CASE WHEN journals.transaction_type = 1 AND payment_type = 2 THEN amount ELSE 0 END) as total_loan
            ")
            ->join('accounts', 'accounts.id', '=', 'journals.account_id')
            ->whereIn('accounts.account_type_id', [$company_account_type_id, $banks_account_type_id])
            ->where('journals.is_cleared', 0)
            ->groupBy('journals.currency_id')
            ->get()
            ->map(function ($item) use ($currencies) {
                $item->currency_name = $currencies[$item->currency_id] ?? 'Unknown';
                return $item;
            });

        // Sold Profits   =  مفاد فروشات 
        $sold_profits = SalesDetails::selectRaw('warehouse_sales.currency_id, SUM(profit) as total_profit')
            ->join('warehouse_sales', 'warehouse_sales.id', '=', 'sales_details.warehouse_sales_id')
            ->where('warehouse_sales.is_cleared', 0)
            ->groupBy('warehouse_sales.currency_id')
            ->get()
            ->map(function ($item) use ($currencies) {
                $item->currency_name = $currencies[$item->currency_id] ?? 'Unknown';
                return $item;
            });

        return compact('result', 'sold_profits', 'total_warehouse_value');
    }


}
