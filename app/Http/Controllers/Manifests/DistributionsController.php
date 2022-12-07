<?php

namespace App\Http\Controllers\Manifests;

use PDF;
use Illuminate\Http\Request;
use App\Entities\Manifests\Manifest;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use App\Entities\Manifests\DistributionsRepository;
use App\Http\Requests\Manifests\DistributionSendRequest;
use App\Http\Requests\Manifests\DistributionCreateRequest;

class DistributionsController extends Controller
{
    private $repo;

    public function __construct(DistributionsRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $manifests = $this->repo->getLatest();
        return view('manifests.distributions.index', compact('manifests'));
    }

    public function create()
    {
        $couriersList = $this->repo->getCouriersList();
        $deliveryUnitsList = $this->repo->getDeliveryUnitsList();
        return view('manifests.distributions.create', compact('couriersList', 'deliveryUnitsList'));
    }

    public function store(DistributionCreateRequest $manifestForm)
    {
        $manifest = $manifestForm->persist();
        flash(__('manifest.created'), 'success');
        return redirect()->route('manifests.distributions.show', $manifest->number);
    }

    public function show(Manifest $manifest)
    {
        $manifest->load(['receipts.packType', 'receipts.origin', 'receipts.destination']);
        return view('manifests.distributions.show', compact('manifest'));
    }

    public function edit(Manifest $manifest)
    {
        try {
            $this->authorize('edit', $manifest);
        } catch (AuthorizationException $e) {
            flash(__('manifest.uneditable'), 'warning');
            return redirect()->route('manifests.distributions.show', $manifest->number);
        }

        $couriersList = $this->repo->getCouriersList();
        $deliveryUnitsList = $this->repo->getDeliveryUnitsList();
        return view('manifests.distributions.edit', compact('manifest', 'couriersList', 'deliveryUnitsList'));
    }

    public function update(Request $request, Manifest $manifest)
    {
        $this->validate($request, [
            'dest_city_id'     => 'required|exists:cities,id',
            'handler_id'       => 'required|numeric|exists:users,id',
            'delivery_unit_id' => 'nullable|numeric|exists:delivery_units,id',
            'deliver_at'       => 'nullable|date_format:Y-m-d H:i',
            'received_at'      => 'nullable|date_format:Y-m-d H:i',
            'start_km'         => 'nullable|numeric',
            'end_km'           => 'nullable|numeric',
            'notes'            => 'nullable|string|max:255',
        ]);

        $manifest = $this->repo->update($request->only(
            'dest_city_id',
            'handler_id', 'delivery_unit_id', 'deliver_at',
            'received_at', 'start_km', 'end_km', 'notes'
        ), $manifest->id);

        flash(__('manifest.updated'), 'success');
        return redirect()->route('manifests.distributions.show', $manifest->number);
    }

    public function send(DistributionSendRequest $request, $manifestId)
    {
        $result = $this->repo->sendManifestWithData($manifestId, $request->only('deliver_at', 'start_km', 'notes'));

        if ($result == false) {
            flash(__('manifest.unsent'), 'warning');
        } else {
            flash(__('manifest.sent'), 'success');
        }

        return back();
    }

    public function pdf(Manifest $manifest)
    {
        $manifest->load('receipts.packType');
        // return view('manifests.pdf', compact('manifest'));

        $pdf = PDF::loadView('manifests.distributions.pdf', compact('manifest'));
        return $pdf->stream($manifest->number.'.e-manifest.pdf');
    }

    public function xls(Manifest $manifest)
    {
        $manifest->load('receipts.packType');
        // return view('manifests.distributions.xls', compact('manifest'));

        \Excel::create('Manifest No. '.$manifest->number, function ($excel) use ($manifest) {
            $excel->sheet('Manifest', function ($sheet) use ($manifest) {
                $sheet->loadView('manifests.distributions.xls', compact('manifest'));
            });
        })->export('xls');
    }
}
