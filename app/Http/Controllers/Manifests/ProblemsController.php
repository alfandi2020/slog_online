<?php

namespace App\Http\Controllers\Manifests;

use App\Entities\Manifests\Manifest;
use App\Entities\Manifests\Problem as ProblemManifest;
use App\Entities\Receipts\Receipt;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manifests\ProblemCreateRequest;
use DB;
use Illuminate\Http\Request;

class ProblemsController extends Controller
{
    public function index(Request $request)
    {
        $manifests = ProblemManifest::with('creator', 'handler', 'receipts')->paginate(25);
        return view('manifests.problems.index', compact('manifests'));
    }

    public function create(Request $request)
    {
        $refManifest = null;
        if ($request->has('manifest_number')) {
            $refManifest = Manifest::where('number', $request->get('manifest_number'))->first();
            $receipts = (!is_null($refManifest))
                ? $refManifest->receipts()->where('status_code', 'no')->get()
                : collect([]);
        } else {
            $receipts = Receipt::where('last_officer_id', auth()->id())->where('status_code', 'no')->get();
        }

        return view('manifests.problems.create', compact('receipts', 'refManifest'));
    }

    public function store(ProblemCreateRequest $manifestForm)
    {
        DB::beginTransaction();
        $manifest = $manifestForm->persist();
        DB::commit();

        flash(trans('manifest.problems.created'), 'success');

        return redirect()->route('manifests.problems.show', $manifest->number);
    }

    public function show(Manifest $manifest)
    {
        $nullProblemNotesExists = $manifest->receipts->pluck('pivot.notes')->contains(null);
        return view('manifests.problems.show', compact('manifest', 'nullProblemNotesExists'));
    }

    public function edit(Manifest $manifest)
    {
        if (auth()->user()->can('edit', $manifest) == false) {
            flash(trans('manifest.uneditable'), 'warning');
            return redirect()->route('manifests.problems.show', $manifest->number);
        }

        return view('manifests.problems.edit', compact('manifest'));
    }

    public function update(Request $request, Manifest $manifest)
    {
        $this->validate($request, [
            'handler_id' => 'required|numeric|exists:users,id',
            'notes'   => 'nullable|string|max:255',
        ]);

        $manifest->handler_id = $request->get('handler_id');
        $manifest->notes = $request->get('notes');
        $manifest->save();

        flash(trans('manifest.updated'));

        return redirect()->route('manifests.problems.show', $manifest->number);
    }

    public function send(Request $request, $manifestId)
    {
        $this->validate($request, [
            'manifest_number' => 'required|exists:manifests,number,id,' . $manifestId,
        ]);

        $result = ProblemManifest::findOrFail($manifestId)->send();

        if ($result == false)
            flash(trans('manifest.unsent'), 'warning');
        else
            flash(trans('manifest.sent'), 'success');

        return back();
    }

    public function patchReceive(Request $request, $manifestId)
    {
        $this->validate($request, [
            'manifest_number' => 'required|exists:manifests,number,id,' . $manifestId,
        ]);

        $manifest = ProblemManifest::findOrFail($manifestId)->receive();

        if ($manifest == false) {
            flash(trans('manifest.cannot_received'), 'warning');
            return back();
        } else {
            flash(trans('manifest.received'), 'success');
            return redirect()->route('manifests.problems.show', $manifest->number);
        }
    }
}
