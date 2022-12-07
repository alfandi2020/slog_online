<?php

namespace App\Http\Controllers\Admin;

use App\Entities\References\Reference;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PackageTypesController extends Controller
{
    public function index(Request $request)
    {
        $editableType = null;
        $packageTypes = Reference::whereCat('pack_type')->get();

        if (in_array($request->get('action'), ['edit','delete']) && $request->has('id'))
            $editableType = Reference::find($request->get('id'));

        return view('admin.references.package-types', compact('packageTypes','editableType'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:20',
            'cat' => 'required|in:pack_type',
        ]);

        Reference::create($request->only('name','cat'));

        flash(trans('package_type.created'), 'success');

        return redirect()->route('admin.package-types.index');
    }

    public function update(Request $request, $packageTypeId)
    {
        $this->validate($request, [
            'name' => 'required|max:20',
            'cat' => 'required|in:pack_type',
        ]);

        $package_type = Reference::findOrFail($packageTypeId)->update($request->only('name'));

        flash(trans('package_type.updated'), 'success');

        return redirect()->route('admin.package-types.index');
    }

    public function destroy(Request $request, $packageTypeId)
    {
        $this->validate($request, [
            'package_type_id' => 'required|exists:site_references,id'
        ]);

        if ($request->get('package_type_id') == $packageTypeId && Reference::findOrFail($packageTypeId)->delete()) {
            flash(trans('package_type.deleted'), 'success');
            return redirect()->route('admin.package-types.index');
        }

        flash(trans('package_type.undeleted'), 'error');
        return back();
    }
}
