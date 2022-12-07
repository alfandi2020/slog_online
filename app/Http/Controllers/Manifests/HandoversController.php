<?php

namespace App\Http\Controllers\Manifests;

use App\Entities\Manifests\HandoversRepository;
use App\Entities\Manifests\Manifest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manifests\HandoverCreateRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use PDF;

class HandoversController extends Controller
{
    private $repo;

    public function __construct(HandoversRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $type = $request->get('type') ?: 'out';
        $manifests = $this->repo->getLatest($type);
        return view('manifests.handovers.index',compact('manifests'));
    }

    public function create()
    {
        return view('manifests.handovers.create');
    }

    public function store(HandoverCreateRequest $manifestForm)
    {
        $manifest = $manifestForm->persist();
        flash(trans('manifest.created'), 'success');
        return redirect()->route('manifests.handovers.show', $manifest->number);
    }

    public function show(Manifest $manifest)
    {
        $manifest->load(['receipts.packType', 'receipts.origin', 'receipts.destination']);
        return view('manifests.handovers.show', compact('manifest'));
    }

    public function edit(Manifest $manifest)
    {
        try {
            $this->authorize('edit', $manifest);
        } catch (AuthorizationException $e) {
            flash(trans('manifest.uneditable'), 'warning');
            return redirect()->route('manifests.handovers.show', $manifest->number);
        }

        return view('manifests.handovers.edit', compact('manifest'));
    }

    public function update(Request $request, Manifest $manifest)
    {
        $this->validate($request, [
            'notes' => 'nullable|string|max:255',
        ]);

        $manifest->notes = $request->get('notes');
        $manifest->save();

        flash(trans('manifest.updated'));

        return redirect()->route('manifests.handovers.show', $manifest->number);
    }

    public function pdf(Manifest $manifest)
    {
        $manifest->load('receipts.packType');
        // return view('manifests.pdf', compact('manifest'));

        $pdf = PDF::loadView('manifests.handovers.pdf', compact('manifest'));
        return $pdf->stream($manifest->number . '.e-manifest.pdf');
    }
}
