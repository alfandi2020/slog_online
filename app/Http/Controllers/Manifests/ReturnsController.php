<?php

namespace App\Http\Controllers\Manifests;

use App\Entities\Manifests\Manifest;
use App\Entities\Manifests\ReturnsRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manifests\ReturnCreateRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use PDF;

class ReturnsController extends Controller
{
    private $repo;

    public function __construct(ReturnsRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $type = $request->get('type') == 'in' ? 'in' : 'out';
        $manifests = $this->repo->getLatest($type);
        return view('manifests.returns.index', compact('manifests'));
    }

    public function create()
    {
        return view('manifests.returns.create');
    }

    public function store(ReturnCreateRequest $manifestForm)
    {
        $manifest = $manifestForm->persist();
        flash(trans('manifest.created'), 'success');
        return redirect()->route('manifests.returns.show', $manifest->number);
    }

    public function show(Manifest $manifest)
    {
        $manifest->load(['receipts.packType', 'receipts.origin', 'receipts.destination']);
        return view('manifests.returns.show', compact('manifest'));
    }

    public function edit(Manifest $manifest)
    {
        try {
            $this->authorize('edit', $manifest);
        } catch (AuthorizationException $e) {
            flash(trans('manifest.uneditable'), 'warning');
            return redirect()->route('manifests.returns.show', $manifest->number);
        }

        return view('manifests.returns.edit', compact('manifest'));
    }

    public function update(Request $request, Manifest $manifest)
    {
        $this->validate($request, [
            'dest_network_id' => 'required|numeric|exists:networks,id',
            'notes'           => 'nullable|string|max:255',
        ]);

        $manifest->dest_network_id = $request->get('dest_network_id');
        $manifest->notes = $request->get('notes');
        $manifest->save();

        flash(trans('manifest.updated'));

        return redirect()->route('manifests.deliveries.show', $manifest->number);
    }

    public function pdf(Manifest $manifest)
    {
        $manifest->load('receipts.packType');
        // return view('manifests.pdf', compact('manifest'));

        $pdf = PDF::loadView('manifests.returns.pdf', compact('manifest'));
        return $pdf->stream($manifest->number . '.e-manifest.pdf');
    }
}
