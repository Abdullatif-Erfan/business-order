<?php

namespace App\Services;

class TaxCalculationService
{
    /**
     * Calculate buying tax details (matches JavaScript logic exactly)
     *
     * @param float $amount Quantity
     * @param float $unitPrice Unit price without tax
     * @param float $taxPercent Tax percentage (0-100)
     * @return array
     */
    public function calculateBuyingTax(float $amount, float $unitPrice, float $taxPercent): array
    {
        // Calculate total without VAT
        $totalWithoutTax = $amount * $unitPrice;
        
        // Calculate total VAT amount (matching JavaScript)
        $taxAmount = ($totalWithoutTax * $taxPercent) / 100;
        
        // Unit price WITH VAT (matching JavaScript logic: unitPrice + taxAmount)
        $unitPriceWithVAT = $unitPrice + $taxAmount;
        
        // Total WITH VAT (matching JavaScript logic: unitPriceWithVAT * quantity)
        $totalWithVAT = $unitPriceWithVAT * $amount;

        return [
            'buy_tax_price' => round($taxAmount, 2),           
            'buy_up_vat' => round($unitPriceWithVAT, 2),      
            'total_vat' => round($totalWithVAT, 2),           
            'buy_total_without_tax' => round($totalWithoutTax, 2), // Renamed to avoid conflict
            'buy_tax_percentage' => $taxPercent, // Renamed to avoid conflict
        ];
    }

    /**
     * Calculate selling tax details (matches JavaScript logic exactly)
     *
     * @param float $amount Quantity
     * @param float $unitPrice Unit price without tax
     * @param float $taxPercent Tax percentage (0-100)
     * @return array
     */
    public function calculateSellingTax(float $amount, float $unitPrice, float $taxPercent): array
    {
        if ($unitPrice <= 0 || $taxPercent <= 0) {
            return [
                'sell_tax_price' => 0,
                'sell_up_vat' => 0,
                'total_sales_with_tax' => 0,
                'sell_total_without_tax' => 0,
                'sell_tax_percentage' => $taxPercent,
            ];
        }

        // Calculate total without VAT
        $totalWithoutTax = $amount * $unitPrice;
        
        // Calculate total VAT amount (matching JavaScript)
        $taxAmount = ($totalWithoutTax * $taxPercent) / 100;
        
        // Unit price WITH VAT (matching JavaScript logic: unitPrice + taxAmount)
        $unitPriceWithTax = $unitPrice + $taxAmount;
        
        // Total WITH VAT (matching JavaScript logic: unitPriceWithTax * quantity)
        $totalWithTax = $unitPriceWithTax * $amount;

        return [
            'sell_tax_price' => round($taxAmount, 2),          
            'sell_up_vat' => round($unitPriceWithTax, 2),     
            'total_sales_with_tax' => round($totalWithTax, 2), 
            'sell_total_without_tax' => round($totalWithoutTax, 2), // Renamed to avoid conflict
            'sell_tax_percentage' => $taxPercent, // Renamed to avoid conflict
        ];
    }

    /**
     * Calculate complete tax details for both buying and selling
     *
     * @param float $amount Quantity
     * @param float $buyUnitPrice Buy unit price without tax
     * @param float $buyTaxPercent Buy tax percentage (0-100)
     * @param float|null $sellUnitPrice Sell unit price without tax
     * @param float|null $sellTaxPercent Sell tax percentage (0-100)
     * @return array
     */
    public function calculateFullTax(
        float $amount,
        float $buyUnitPrice,
        float $buyTaxPercent,
        ?float $sellUnitPrice = null,
        ?float $sellTaxPercent = null
    ): array {
        $result = [];
        
        // Get buying results
        $buyingResult = $this->calculateBuyingTax($amount, $buyUnitPrice, $buyTaxPercent);
        $result = array_merge($result, $buyingResult);
        
        // Get selling results if provided
        if ($sellUnitPrice !== null && $sellTaxPercent !== null) {
            $sellingResult = $this->calculateSellingTax($amount, $sellUnitPrice, $sellTaxPercent);
            $result = array_merge($result, $sellingResult);
        }
        
        return $result;
    }

    /**
     * Calculate total price based on amount and unit price
     *
     * @param float $amount Quantity
     * @param float $unitPrice Unit price
     * @return float
     */
    public function calculateTotalPrice(float $amount, float $unitPrice): float
    {
        return round($amount * $unitPrice, 2);
    }

    /**
     * Calculate Exact Tax Price based on total and percentage
     *
     * @param float $total Total amount
     * @param float $taxPercent Tax percentage
     * @return float
     */
    public function calculateVATAmount(float $total, float $taxPercent): float
    {
        return round(($total * $taxPercent) / 100, 2);
    }

    /**
     * Validate warehouse amounts against total amount
     *
     * @param float $totalAmount Total quantity
     * @param array $warehouseAmounts Array of warehouse quantities
     * @return array
     */
    public function validateWarehouseAmounts(float $totalAmount, array $warehouseAmounts): array
    {
        $sumWarehouseAmount = array_sum(array_map('floatval', $warehouseAmounts));
        
        if ($sumWarehouseAmount > $totalAmount) {
            return [
                'valid' => false,
                'error' => 'Warehouse amounts exceed total amount',
                'sum_warehouse' => round($sumWarehouseAmount, 2),
                'difference' => round($sumWarehouseAmount - $totalAmount, 2)
            ];
        }
        
        if ($sumWarehouseAmount < $totalAmount) {
            return [
                'valid' => false,
                'error' => 'Warehouse amounts are less than total amount',
                'sum_warehouse' => round($sumWarehouseAmount, 2),
                'difference' => round($totalAmount - $sumWarehouseAmount, 2)
            ];
        }

        return [
            'valid' => true,
            'sum_warehouse' => round($sumWarehouseAmount, 2),
            'message' => 'Warehouse amounts match total amount'
        ];
    }
}