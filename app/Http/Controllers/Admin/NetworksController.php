<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Networks\DeliveryUnit;
use App\Entities\Networks\Network;
use App\Entities\Networks\NetworksRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\Networks\CreateRequest;
use App\Http\Requests\Networks\UpdateRequest;
use Illuminate\Http\Request;

class NetworksController extends Controller
{
    private $repo;

    public function __construct(NetworksRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $networks = Network::paginate(25);
        return view('admin.networks.index', compact('networks'));
    }

    public function create()
    {
        return view('admin.networks.create');
    }

    public function store(CreateRequest $createForm)
    {
        $createForm->persist();
        flash(trans('network.created'));
        return redirect()->route('admin.networks.index');
    }

    public function show(Network $network)
    {
        $chartData = $this->repo->getNetworkYearlyReports(date('Y'), '', $network->id);
        return view('admin.networks.show', compact('network','chartData'));
    }

    public function edit(Network $network)
    {
        return view('admin.networks.edit', compact('network'));
    }

    public function update(UpdateRequest $updateForm, Network $network)
    {
        $updateForm->persist();
        flash(trans('network.updated'));
        return redirect()->route('admin.networks.show', $network->id);
    }

    public function delete(Network $network)
    {
        return view('admin.networks.delete', compact('network'));
    }

    public function destroy(Request $request, Network $network)
    {
        if ($request->user()->cannot('delete', $network)) {
            flash(trans('network.undeleted'), 'danger');
            return back();
        }

        $network->delete();
        flash(trans('network.deleted'));
        return redirect()->route('admin.networks.index');
    }

    public function customers(Network $network)
    {
        return view('admin.networks.customers', compact('network'));
    }

    public function deliveryUnits(Request $request, Network $network)
    {
        $editableUnit = null;

        if (in_array($request->get('action'), ['edit','delete']) && $request->has('id'))
            $editableUnit = DeliveryUnit::find($request->get('id'));

        return view('admin.networks.delivery-units', compact('network','editableUnit'));
    }

    public function deliveryUnitStore(Request $request, Network $network)
    {
        $this->validate($request, [
            'name' => 'required|max:60',
            'plat_no' => 'required|max:20|unique:delivery_units,plat_no',
            'type_id' => 'required|in:1,2,3,4,5',
            'network_id' => 'required|numeric',
            'description' => 'nullable|max:255',
        ]);

        DeliveryUnit::create($request->only('name','plat_no','type_id','network_id','description'));

        flash(trans('delivery_unit.created'), 'success');

        return redirect()->route('admin.networks.delivery-units', $network->id);
    }

    public function deliveryUnitUpdate(Request $request, Network $network, DeliveryUnit $delivery_unit)
    {
        $this->validate($request, [
            'name' => 'required|max:60',
            'plat_no' => 'required|max:20|unique:delivery_units,plat_no,' . $delivery_unit->id,
            'type_id' => 'required|in:1,2,3,4,5',
            'network_id' => 'required|in:' . $network->id,
            'description' => 'nullable|max:255',
            'is_active' => 'required|in:0,1',
        ]);

        $delivery_unit->update($request->only(
                'name','plat_no','type_id',
                'network_id','description','is_active'
            ));

        flash(trans('delivery_unit.updated'), 'success');

        return redirect()->route('admin.networks.delivery-units', $network->id);
    }

    public function deliveryUnitDestroy(Request $request, Network $network, DeliveryUnit $delivery_unit)
    {
        $this->validate($request, [
            'delivery_unit_id' => 'required|exists:delivery_units,id|not_exists:manifests,delivery_unit_id'
        ], [
            'delivery_unit_id.not_exists' => 'Data Armada tidak dapat dihapus karena sudah digunakan untuk transaksi Manifest.'
        ]);

        if ($request->get('delivery_unit_id') == $delivery_unit->id && $delivery_unit->delete()) {
            flash(trans('delivery_unit.deleted'), 'success');
            return redirect()->route('admin.networks.delivery-units', $network->id);
        }

        flash(trans('delivery_unit.undeleted'), 'error');
        return back();
    }

    public function users(Network $network)
    {
        return view('admin.networks.users', compact('network'));
    }
}
