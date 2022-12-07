<?php

namespace App\Http\Controllers\Receipts;

use App\Entities\Users\User;
use Illuminate\Http\Request;
use App\Entities\Regions\City;
use App\Entities\Receipts\Item;
use App\Entities\Receipts\Receipt;
use App\Services\ReceiptCollection;
use App\Http\Controllers\Controller;

class DraftReceiptsController extends Controller
{
    protected $instance = 'new_receipts';

    public function __construct()
    {
        $this->receiptCollection = new ReceiptCollection;
        $this->receiptCollection->instance($this->instance);
    }

    public function index()
    {
        if ($this->receiptCollection->hasContent()) {
            $receipt = $this->receiptCollection->content()->first();
            $cityOrigin = auth()->user()->network->cityOrigin;
            $destinationDistricts = $this->getDestinationDistrictsListOf($cityOrigin, $receipt->dest_city_id);
            $destinationCities = $this->getDestinationCitiesListOf($cityOrigin);
            $availableCourierList = $this->getCouriersList();

            $view = 'retail';

            if ($receipt->service_id == 41) {
                $view = 'project';
            }

            return view(
                'receipts.drafts.'.$view,
                compact(
                    'receipt', 'receiptKey', 'destinationCities',
                    'destinationDistricts', 'availableCourierList'
                )
            );
        }

        return view('receipts.drafts.index');
    }

    public function show(Request $request, $receiptKey)
    {
        if ($this->receiptCollection->content()->keys()->contains($receiptKey) == false) {
            return redirect()->route('receipts.drafts');
        }

        $receipt = $this->receiptCollection->get($receiptKey);

        if ($request->get('step') == 3 && $receipt->consignee == null) {
            flash(trans('receipt.prevent_review'), 'warning');
            return redirect()->route('receipts.draft', [$receiptKey, 'step' => 1]);
        }

        $cityOrigin = auth()->user()->network->cityOrigin;

        $destinationCities = $this->getDestinationCitiesListOf($cityOrigin);
        $destinationDistricts = $this->getDestinationDistrictsListOf($cityOrigin, $receipt->dest_city_id);
        $availableCourierList = $this->getCouriersList();

        $view = 'retail';

        if ($receipt->service_id == 41) {
            $view = 'project';
            $destinationCities = City::pluck('name', 'id');
        }

        return view(
            'receipts.drafts.'.$view,
            compact(
                'receipt', 'receiptKey', 'destinationCities',
                'destinationDistricts', 'availableCourierList'
            )
        );
    }

    public function destroy(Request $request, $receiptKey)
    {
        $this->receiptCollection->removeReceipt($receiptKey);

        $firstReceiptKey = $this->receiptCollection->content()->keys()->first();

        flash()->warning(trans('receipt.draft_deleted'));

        if ($firstReceiptKey) {
            return redirect()->route('receipts.draft', $firstReceiptKey);
        }

        return redirect()->route('receipts.drafts');
    }

    public function draftItems(Request $request, $receiptKey)
    {
        $this->validate($request, [
            'new_item_weight'  => 'required|numeric',
            'new_item_length'  => 'nullable|numeric',
            'new_item_width'   => 'nullable|numeric',
            'new_item_height'  => 'nullable|numeric',
            'new_item_type_id' => 'required|numeric',
            'new_item_notes'   => 'nullable|string|max:100',
        ]);

        $item = new Item(
            $request->get('new_item_weight'),
            $request->get('new_item_length'),
            $request->get('new_item_width'),
            $request->get('new_item_height'),
            $request->get('new_item_type_id'),
            $request->get('new_item_notes')
        );

        $this->receiptCollection->addItemToReceipt($receiptKey, $item);

        flash(trans('receipt.item_added'), 'success');
        return back();
    }

    public function draftItemsUpdate(Request $request, $receiptKey, $itemKey)
    {
        $this->validate($request, [
            'weight.'.$itemKey  => 'required|numeric',
            'length.'.$itemKey  => 'nullable|numeric',
            'width.'.$itemKey   => 'nullable|numeric',
            'height.'.$itemKey  => 'nullable|numeric',
            'type_id.'.$itemKey => 'required|numeric',
            'notes.'.$itemKey   => 'nullable|string|max:100',
        ]);

        $itemData['weight'] = $request->get('weight')[$itemKey];
        $itemData['length'] = $request->get('length')[$itemKey];
        $itemData['width'] = $request->get('width')[$itemKey];
        $itemData['height'] = $request->get('height')[$itemKey];
        $itemData['type_id'] = $request->get('type_id')[$itemKey];
        $itemData['notes'] = $request->get('notes')[$itemKey];

        $this->receiptCollection->updateReceiptItem($receiptKey, $itemKey, $itemData);

        flash(trans('receipt.item_updated'), 'info');
        return back();
    }

    public function draftItemsDelete(Request $request, $receiptKey, $itemKey)
    {
        $this->receiptCollection->removeItemFromReceipt($receiptKey, $itemKey);
        flash(trans('receipt.item_deleted'), 'danger');
        return back();
    }

    public function getNewReceiptNumber($manualNumber)
    {
        if ($manualNumber) {
            return $manualNumber;
        }

        $networkCode = auth()->user()->network->code;
        $prefix = $networkCode.date('ym');
        $lastManifest = Receipt::orderBy('id', 'desc')
            ->where('number', 'like', $networkCode.'%')
            ->whereRaw(\DB::raw('CHAR_LENGTH(number) = 18'))
            ->withTrashed()
            ->first();

        if ($lastManifest) {
            if (substr($lastManifest->number, 0, 10) != $networkCode.date('y')) {
                return $prefix.'000001';
            }

            $currentNumber = substr($lastManifest->number, -6);
            $currentNumber = $prefix.$currentNumber;
            return ++$currentNumber;
        }

        return $prefix.'000001';
    }

    protected function getDestinationCitiesListOf(City $city)
    {
        $destinationCities = [];
        foreach ($city->destinationCities()->with('province')->get() as $city) {
            $destinationCities[$city->province->name][$city->id] = $city->name;
        }
        return $destinationCities;
    }

    protected function getDestinationDistrictsListOf(City $city, $destCityId)
    {
        $destinationDistricts = [];
        foreach ($city->destinationDistricts()->where('dest_city_id', $destCityId)->get() as $district) {
            $destinationDistricts[$district->id] = $district->name;
        }
        return $destinationDistricts;
    }

    protected function getCouriersList()
    {
        return User::where([
            'network_id' => auth()->user()->network_id,
            'role_id'    => 7,
            'is_active'  => 1,
        ])->pluck('name', 'id');
    }
}
