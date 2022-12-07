<?php

namespace App\Http\Controllers\Transactions;

use App\Entities\Transactions\PaymentMethod;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentMethodsController extends Controller
{
    /**
     * Display a listing of the paymentmethod.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $editablePaymentMethod = null;
        $paymentMethods = PaymentMethod::get();

        if (in_array(request('action'), ['edit', 'delete']) && request('id') != null) {
            $editablePaymentMethod = PaymentMethod::find(request('id'));
        }

        return view('payment-methods.index', compact('paymentMethods', 'editablePaymentMethod'));
    }

    /**
     * Store a newly created paymentmethod in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);

        PaymentMethod::create($request->only('name', 'description'));

        return redirect()->route('payment-methods.index');
    }

    /**
     * Update the specified paymentmethod in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  PaymentMethod  $paymentMethod
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $this->validate($request, [
            'name' => 'required|max:60',
            'description' => 'nullable|max:255',
            'is_active' => 'required|boolean',
        ]);

        $routeParam = request()->only('page', 'q');

        $paymentMethod = $paymentMethod->update(
            $request->only('name', 'description', 'is_active')
        );

        return redirect()->route('payment-methods.index', $routeParam);
    }

    /**
     * Remove the specified paymentmethod from storage.
     *
     * @param  PaymentMethod  $paymentMethod
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        $this->validate(request(), [
            'payment_method_id' => 'required',
        ]);

        $routeParam = request()->only('page', 'q');

        if (request('payment_method_id') == $paymentMethod->id && $paymentMethod->delete()) {
            return redirect()->route('payment-methods.index', $routeParam);
        }

        return back();
    }
}
