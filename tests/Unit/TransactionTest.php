<?php

namespace Jonassiewertsen\StatamicButik\Tests\Unit;

use Jonassiewertsen\StatamicButik\Checkout\Customer;
use Jonassiewertsen\StatamicButik\Checkout\Item;
use Jonassiewertsen\StatamicButik\Checkout\Transaction;
use Jonassiewertsen\StatamicButik\Http\Models\Product;
use Jonassiewertsen\StatamicButik\Tests\TestCase;

class TransactionTest extends TestCase
{
    protected Transaction $transaction;

    public function setUp(): void {
        parent::setUp();
        $this->transaction = new Transaction();
    }

    /** @test */
    public function the_currency_will_be_set_automatically(){
        $this->assertEquals(
            config('butik.currency_isoCode'),
            $this->transaction->currencyIsoCode);

        $this->assertEquals(
            config('butik.currency_symbol'),
            $this->transaction->currencySymbol);
    }

    /** @test */
    public function a_id_can_be_added(){
        $id = 'some-id-234';
        $this->transaction->id($id);

        $this->assertEquals($id, $this->transaction->id);
    }

    /** @test */
    public function a_transaction_id_can_be_added(){
        $id = 'some-id-234';
        $this->transaction->transactionId($id);

        $this->assertEquals($id, $this->transaction->transactionId);
    }

    /** @test */
    public function a_status_can_be_added(){
        $status = 'open';
        $this->transaction->status($status);

        $this->assertEquals($status, $this->transaction->status);
    }

    /** @test */
    public function a_method_can_be_added(){
        $method = 'paypal';
        $this->transaction->method($method);

        $this->assertEquals($method, $this->transaction->method);
    }

    /** @test */
    public function a_currency_iso_code_can_be_added(){
        $currencyIsCode = 'paypal';
        $this->transaction->currencyIsoCode($currencyIsCode);

        $this->assertEquals($currencyIsCode, $this->transaction->currencyIsoCode);
    }

    /** @test */
    public function a_currency_symbol_can_be_added(){
        $currencySymbol = '€';
        $this->transaction->currencySymbol($currencySymbol);

        $this->assertEquals($currencySymbol, $this->transaction->currencySymbol);
    }

    /** @test */
    public function a_total_amount_can_be_added(){
        $totalAmount = '10.00';
        $this->transaction->totalAmount($totalAmount);

        $this->assertEquals($totalAmount, $this->transaction->totalAmount);
    }

    /** @test */
    public function items_will_be_added_and_mapped_correctly()
    {
        $product = create(Product::class, [])->first();
        $items = collect()->push(new Item($product->slug));

        $mappedItems = $items->map(function($item) {
            return [
                'id'            => $item->slug,
                'name'          => $item->name,
                'description'   => $item->description,
                'quantity'      => $item->getQuantity(),
                'singlePrice'   => $item->singlePrice(),
                'totalPrice'    => $item->totalPrice(),
                'taxRate'       => $item->taxRate,
            ];
        });

        // Push into transaction object
        $this->transaction->items($items);

        $this->assertEquals($mappedItems, $this->transaction->items);
    }

    /** @test */
    public function a_customer_can_be_added()
    {
       $customer = new Customer();
       $this->transaction->customer($customer);
        $this->assertInstanceOf(Customer::class, $this->transaction->customer);
    }

    /** @test */
    public function created_at_can_be_added()
    {
        $date = now();
        $this->transaction->createdAt($date);
        $this->assertEquals($date, $this->transaction->createdAt);
    }

    /** @test */
    public function paid_at_can_be_added()
    {
        $date = now();
        $this->transaction->paidAt($date);
        $this->assertEquals($date, $this->transaction->paidAt);
    }
}
