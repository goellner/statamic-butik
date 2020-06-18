<?php

namespace Jonassiewertsen\StatamicButik\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Jonassiewertsen\StatamicButik\Checkout\Customer;
use Jonassiewertsen\StatamicButik\Checkout\Cart;
use Jonassiewertsen\StatamicButik\Http\Models\Order;

class CheckoutController extends Checkout
{
    public function delivery()
    {
        $customer = session()->has('butik.customer') ?
            Session::get('butik.customer') :
            (new Customer())->empty();

        $items         = Cart::get();
        $totalPrice    = Cart::totalPrice();

        return view(config('butik.template_checkout-delivery'), compact('customer', 'items', 'totalPrice'));
    }

    public function saveCustomerData()
    {
        $validatedData = request()->validate($this->rules());

        $customer = new Customer($validatedData);

        Session::put('butik.customer', $customer);

        return redirect()->route('butik.checkout.payment');
    }

    public function payment()
    {
        $customer      = session('butik.customer');
        $items         = Cart::get();
        $totalPrice    = Cart::totalPrice();

        return view(config('butik.template_checkout-payment'), compact('customer', 'items', 'totalPrice'));
    }

    public function receipt(Request $request, $order)
    {
        if (!$request->hasValidSignature()) {
            return $this->showInvalidReceipt();
        }

        if (!$order = Order::find($order)) {
            return $this->showInvalidReceipt();
        }

        $customer = json_decode($order->customer);

        if ($order->status === 'paid') {
            Session::forget('butik.customer');
        }

        return view(config('butik.template_checkout-receipt'), compact('customer', 'order'));
    }
}
