<?php

namespace App\Helpers;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

class FunctionHelper
{ 
    public static function todaysDate()
    {
        return Jalalian::now()->format('Y-m-d');
    }

    public static function curDay()
    {
        return Jalalian::now()->format('d');
    }

    public static function curMonth()
    {
        return Jalalian::now()->format('n');
    }

    public static function curYear()
    {
        return Jalalian::now()->format('Y');
    }

    public static function showFullDate()
    {
        return Jalalian::now()->format('Y-m-d H:i:s');
    }

    public static function showFullDate2()
    {
        return Jalalian::now()->format('Y-m-d') . ' ' . date('h:i:s A');
    }

    public static function showThisTimeFormat($date)
    {
        return Jalalian::fromCarbon(\Carbon\Carbon::parse($date))->format('F j, Y, g:i a');
    }

    public static function monthList()
    {
        return [
            "1" => "حمل",
            "2" => "ثور",
            "3" => "جوزا",
            "4" => "سرطان",
            "5" => "اسد",
            "6" => "سنبله",
            "7" => "میزان",
            "8" => "عقرب",
            "9" => "قوس",
            "10" => "جدی",
            "11" => "دلو",
            "12" => "حوت",
        ];
    }

    public static function showCurMonthKey()
    {
        return Jalalian::now()->format('n');
    }

    public static function showCurMonth()
    {
        $months = self::monthList();
        $currentMonth = self::showCurMonthKey();
        return $months[$currentMonth] ?? null;
    }

    public static function persianDayName($day, $month, $year)
    {
        $date = Jalalian::fromFormat('Y-m-d', "$year-$month-$day");
        return $date->format('l');
    }

    public static function newMethodOfFindingDaysAmount($startDate, $endDate)
    {
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $diff = $start->diff($end);
        return $diff->format('%r%a');
    }

    public static function customHash($password)
    {
        $first = substr($password, 0, 6);
        $second = substr($password, -6) . 'nafre';
        return sha1($first . $second);
    }

    public static function showThisData($column, $tableName, $value, $conditionColumn)
    {
        $result = DB::table($tableName)->where($conditionColumn, $value)->value($column);
        return $result ?? null;
    }

    public static function showThisColumn($column, $table, $where)
    {
        $result = DB::table($table)->where($where)->value($column);
        return $result ?? null;
    }

    public static function getNewAccountCode()
    {
        $result = DB::table('account')->orderBy('id', 'DESC')->value('code');
        return $result ? $result + 1 : 1;
    }

    public static function getNewJournalCode()
    {
        $result = DB::table('journal')->orderBy('id', 'DESC')->value('code');
        return $result ? $result + 1 : 1;
    }

    public static function getMedicineBuyCode()
    {
        $result = DB::table('medicine_buy')->orderBy('id', 'DESC')->value('billno');
        return $result ? $result + 1 : 1;
    }
    public static function showJournalCodeByTimes($times)
    {
        $result = DB::table('journal')->where('times', $times)->value('code');
        return $result ?? '0';
    }

    public static function showAccountNameById($accountId)
    {
        $result = DB::table('account')->where('id', $accountId)->value('name');
        return $result ?? ' ';
    }

    public static function getClearedRound($accountId)
    {
        $result = DB::table('journal')
            ->where('account_id', $accountId)
            ->orderBy('cleared_round', 'DESC')
            ->value('cleared_round');
        return $result ? $result + 1 : 1;
    }

    public static function showBaseCurrency()
    {
        return DB::table('currency')->where('is_base', 1)->get(['id', 'name', 'color', 'symbol'])->toArray();
    }

    public static function showBaseCurrencyName()
    {
        $result = DB::table('currency')->where('is_base', 1)->value('name');
        return $result ?? ' ? ';
    }

    public static function showCurrencyName($currencyId)
    {
        $result = DB::table('currency')->where('id', $currencyId)->value('name');
        return $result ?? ' ? ';
    }

    public static function showBaseCurrencyId()
    {
        $result = DB::table('currency')->where('is_base', 1)->value('id');
        return $result ?? ' ? ';
    }

    public static function showConvertedRate($currencyId, $balance)
    {
        $baseCurrencyId = self::showBaseCurrencyId();
        if ((int) $currencyId === (int) $baseCurrencyId) {
            return $balance;
        }

        $rate = DB::table('rate')
            ->where('to_currency_id', $baseCurrencyId)
            ->where('from_currency_id', $currencyId)
            ->value('to_currency_amount');

        if ($rate) {
            return $rate * $balance;
        } else {
            $reverseRate = DB::table('rate')
                ->where('from_currency_id', $baseCurrencyId)
                ->where('to_currency_id', $currencyId)
                ->value('reverse_amount');
            return $reverseRate ? $reverseRate * $balance : '0';
        }
    }

    public static function totalRecords($table, $where)
    {
        return DB::table($table)->where($where)->count();
    }

    public static function show($column, $table)
    {
        $result = DB::table($table)->value($column);
        return $result ?? null;
    }

    public static function showWhere($column, $table, $where = [])
    {
        $query = DB::table($table)->select($column);
        if (!empty($where)) {
            $query->where($where);
        }
        $result = $query->first();
        return $result ? $result->$column : false;
    }

    public static function showActiveHeader()
    {
        $result = DB::table('org_bio')->where('is_active', 1)->value('header');
        return $result ?? null;
    }

    public static function showActivePackage()
    {
        $result = DB::table('packages')->select('type')->where('status', 1)->value('type');;
        return $result ?? null;
    }

    public static function showMonthlyIncome($month, $year, $currencyId)
    {
        $result = DB::table('journal')
            ->join('account', 'account.id', '=', 'journal.account_id')
            ->where([
                ['transaction_type', '=', 1],
                ['payment_type', '=', 2],
                ['parent_code', '=', 1000],
                ['is_cleared', '=', 0],
                ['currency', '=', $currencyId],
                ['year', '=', $year],
                ['month', '=', $month],
            ])
            ->sum('amount');
        return $result ?? '0';
    }

    public static function showMonthlyOutcome($month, $year, $currencyId)
    {
        $result = DB::table('journal')
            ->join('account', 'account.id', '=', 'journal.account_id')
            ->where([
                ['transaction_type', '=', 2],
                ['payment_type', '=', 2],
                ['parent_code', '=', 1000],
                ['is_cleared', '=', 0],
                ['currency', '=', $currencyId],
                ['year', '=', $year],
                ['month', '=', $month],
            ])
            ->sum('amount');
        return $result ?? '0';
    }

    public static function branchName($id)
    {
        $result = DB::table('branch')->where('id', $id)->value('name');
        return $result ?? null;
    }

    public static function showWarehouseName($warehouseId)
    {
        $result = DB::table('warehouse')->where('id', $warehouseId)->value('name');
        return $result ?? null;
    }

    public static function showAll($table)
    {
        return DB::table($table)->orderBy('id', 'ASC')->get()->toArray();
    }
    
}