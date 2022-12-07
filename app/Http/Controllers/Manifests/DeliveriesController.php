<?php

namespace App\Http\Controllers\Manifests;

use PDF;
use Illuminate\Http\Request;
use App\Entities\Manifests\Manifest;
use App\Http\Controllers\Controller;
use App\Entities\Manifests\DeliveriesRepository;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Requests\Manifests\DeliveryCreateRequest;

class DeliveriesController extends Controller
{
    private $repo;

    public function __construct(DeliveriesRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $type = $request->get('type') == 'in' ? 'in' : 'out';
        $manifests = $this->repo->getLatest($type);

        return view('manifests.deliveries.index', compact('manifests'));
    }

    public function create()
    {
        $couriers = $this->getPickupCouriersList(auth()->user()->network_id);

        return view('manifests.deliveries.create', compact('couriers'));
    }

    public function store(DeliveryCreateRequest $manifestForm)
    {
        $manifest = $manifestForm->persist();
        flash(__('manifest.created'), 'success');
        return redirect()->route('manifests.deliveries.show', $manifest->number);
    }

    public function show(Manifest $manifest)
    {
        $manifest->load(['receipts.packType', 'receipts.origin', 'receipts.destination']);
        return view('manifests.deliveries.show', compact('manifest'));
    }

    public function edit(Manifest $manifest)
    {
        try {
            $this->authorize('edit', $manifest);
        } catch (AuthorizationException $e) {
            flash(__('manifest.uneditable'), 'warning');
            return redirect()->route('manifests.deliveries.show', $manifest->number);
        }

        $couriers = $this->getPickupCouriersList(auth()->user()->network_id);

        return view('manifests.deliveries.edit', compact('manifest', 'couriers'));
    }

    public function update(Request $request, Manifest $manifest)
    {
        $this->validate($request, [
            'delivery_unit_id' => 'nullable|numeric|exists:users,id,role_id,7',
            'weight'           => 'nullable|numeric',
            'pcs_count'        => 'nullable|numeric',
            'notes'            => 'nullable|string|max:255',
        ]);

        $manifest->delivery_unit_id = $request->get('delivery_unit_id');
        $manifest->weight = $request->get('weight');
        $manifest->pcs_count = $request->get('pcs_count');
        $manifest->notes = $request->get('notes');
        $manifest->save();

        flash(__('manifest.updated'));

        return redirect()->route('manifests.deliveries.show', $manifest->number);
    }

    public function pdf(Manifest $manifest)
    {
        $manifest->load('receipts.packType');
        // return view('manifests.pdf', compact('manifest'));

        $pdf = PDF::loadView('manifests.deliveries.pdf', compact('manifest'));
        return $pdf->stream($manifest->number.'.e-manifest.pdf');
    }
}
