<?php

namespace App\Http\Controllers\Admin;

use App\Entities\References\Reference;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerComoditiesController extends Controller
{
    public function index(Request $request)
    {
        $editableComodity = null;
        $comodities = Reference::whereCat('comodity')->withCount('customers')->get();

        if (in_array($request->get('action'), ['edit','delete']) && $request->has('id'))
            $editableComodity = Reference::find($request->get('id'));

        return view('admin.references.comodities', compact('comodities','editableComodity'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:20',
            'cat' => 'required|in:comodity',
        ]);

        Reference::create($request->only('name','cat'));

        flash(trans('comodity.created'), 'success');

        return redirect()->route('admin.comodities.index');
    }

    public function update(Request $request, $comodityId)
    {
        $this->validate($request, [
            'name' => 'required|max:20',
            'cat' => 'required|in:comodity',
        ]);

        $comodity = Reference::findOrFail($comodityId)->update($request->only('name'));

        flash(trans('comodity.updated'), 'success');

        return redirect()->route('admin.comodities.index');
    }

    public function destroy(Request $request, $comodityId)
    {
        $this->validate($request, [
            'comodity_id' => 'required|exists:site_references,id|not_exists:customers,comodity_id',
        ], [
            'comodity_id.not_exists' => trans('comodity.undeleted')
        ]);

        if ($request->get('comodity_id') == $comodityId && Reference::findOrFail($comodityId)->delete()) {
            flash(trans('comodity.deleted'), 'success');
            return redirect()->route('admin.comodities.index');
        }

        flash(trans('comodity.undeleted'), 'error');
        return back();
    }
}
