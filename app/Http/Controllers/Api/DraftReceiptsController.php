<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\ChargeCalculator;
use App\Services\ReceiptCollection;
use App\Entities\Customers\Customer;
use App\Http\Controllers\Controller;

class DraftReceiptsController extends Controller
{
    private $receiptCollection;
    private $instance = 'new_receipts';

    public function __construct()
    {
        $this->receiptCollection = new ReceiptCollection;
        $this->receiptCollection->instance($this->instance);
    }

    public function getChargeCalculation(Request $request)
    {
        $this->validate($request, [
            'customer_id'      => 'nullable|numeric',
            'dest_city_id'     => 'required|numeric',
            'dest_district_id' => 'nullable|numeric',
            'service_id'       => 'required|numeric',
            'pcs_count'        => 'required|numeric|min:1|max:9999',
            'items_count'      => 'required|numeric|min:1|max:9999',
            'charged_weight'   => 'required|numeric',
            'charged_on'       => 'required|numeric',
            'pack_type_id'     => 'required|numeric|exists:site_references,id,cat,pack_type',
            'be_insured'       => 'nullable|numeric',
            'package_value'    => 'nullable|numeric',
            'discount'         => 'required|numeric',
            'packing_cost'     => 'required|numeric',
            'admin_fee'        => 'required|numeric',
            'add_cost'         => 'required|numeric',
        ]);

        $response = [];

        $receiptQuery = $request->all();

        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);
        $response = $calculator->toArray();

        $response['success'] = false;
        $response['message'] = '';

        if ($calculator->hasBaseRate() && $response['base_charge']) {
            if (isset($receiptQuery['receipt_key'])) {
                $this->receiptCollection->updateReceiptData($receiptQuery['receipt_key'], $response);
            }

            $response['success'] = true;
            $response['display_base_charge'] = formatRp($response['base_charge']);
            $response['display_discount'] = formatRp($response['discount']);
            $response['display_subtotal'] = formatRp($response['subtotal']);
            $response['display_insurance_cost'] = formatRp($response['insurance_cost']);
            $response['display_add_cost'] = formatRp($response['add_cost']);
            $response['display_packing_cost'] = formatRp($response['packing_cost']);
            $response['display_admin_fee'] = formatRp($response['admin_fee']);
            $response['display_total'] = formatRp($response['total']);
            if ($calculator->isMinChargeApplied()) {
                $response['message'] = 'Berat minimum: '.$calculator->rate->min_weight.' Kg.';
            }

        } else {
            $response['message'] = 'Tarif Tidak ditemukan';
        }

        return response()->json($response, 200);
    }

    public function getCustomerData(Request $request)
    {
        $this->validate($request, [
            'customer_id' => 'required|numeric',
            'receipt_key' => 'required',
        ]);

        $customer = Customer::find($request->get('customer_id'));

        $data['customer_id'] = $customer->id;
        $data['payment_type_id'] = 2;
        $data['consignor']['name'] = $customer->name;
        $data['consignor']['address'][1] = $customer->address[1];
        $data['consignor']['address'][2] = $customer->address[2];
        $data['consignor']['address'][3] = $customer->address[3];
        $data['consignor']['phone'] = $customer->pic['phone'];
        $data['consignor']['postal_code'] = $customer->postal_code;
        $this->receiptCollection->updateReceiptData($request->get('receipt_key'), $data);

        return $customer;
    }
}
