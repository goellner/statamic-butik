<?php

namespace Jonassiewertsen\StatamicButik\Shipping;

use Jonassiewertsen\StatamicButik\Http\Models\ShippingProfile;
use Jonassiewertsen\StatamicButik\Http\Models\ShippingRate;
use Jonassiewertsen\StatamicButik\Http\Models\Tax;
use Jonassiewertsen\StatamicButik\Http\Traits\MoneyTrait;

class ShippingAmount
{
    use MoneyTrait;

    /**
     * The Shipping profile title of this shipping amount
     */
    public string $profileTitle;

    /**
     * The name of the choosen rate title
     */
    public string $rateTitle;

    /**
     * The Shipping profile slug of this shipping amount
     */
    public string $profileSlug;

    /**
     * The total amount for all items belonging to the named shipping profile
     */
    public string $total;

    /**
     * The tax amount for all items belonging for the used shipping
     */
    public string $taxRate;
    public string $taxAmount;

    public function __construct(string $total, ShippingProfile $profile, ShippingRate $rate, Tax $tax)
    {
        $this->profileTitle = $profile->title;
        $this->profileSlug  = $profile->slug;
        $this->rateTitle    = $rate->title;
        $this->total        = $total;
        $this->taxRate      = (string) number_format($tax->percentage, 2);
        $this->taxAmount    = $this->calculateTaxAmount($tax->percentage, $total);
    }

    private function calculateTaxAmount($taxRate, $amount): string
    {
        // Format values
        $amount = str_replace(',', '.', $amount);
        $taxRate = str_replace(',', '.', $taxRate);
        // Calculate tax amount
        $taxAmount = $amount * ($taxRate / (100 + $taxRate));

        return $this->humanPriceWithDot(round($taxAmount, 2));
    }
}
