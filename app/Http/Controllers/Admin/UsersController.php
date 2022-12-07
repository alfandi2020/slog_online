<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Users\UsersRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UsersController extends Controller {

	private $repo;

	public function __construct(UsersRepository $repo)
	{
	    $this->repo = $repo;
	}

    public function index(Request $request)
    {
        $users = $this->repo->getUsers($request->get('network_id'));
        $networks = $this->repo->getNetworksList();
        return view('admin.users.index',compact('users','networks'));
    }

	public function search(Request $request)
	{
		$users = $this->repo->searchUsers($request->get('q'));
		return view('admin.users.search',compact('users'));
	}

	public function create()
	{
        $networks = $this->repo->getNetworksList();
		return view('admin.users.create', compact('networks'));
	}

	public function store(Request $request)
	{
        $this->validate($request, [
            'name'     => 'required|min:5|max:60',
            'username' => 'required|max:30|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|max:20',
            'password' => 'nullable|between:5,15',
            'role_id'  => 'required|numeric',
            'network_id' => 'required|numeric',
            'gender_id' => 'required|numeric',
        ]);

		$user = $this->repo->create($request->except(['_token']));
		flash(trans('user.created'), 'success');

        if ($request->has('create_and_go_to_network'))
            return redirect()->route('admin.networks.users', $user->network_id);

		return redirect()->route('admin.users.index');
	}

	public function show($userId)
	{
		$user = $this->repo->requireById($userId);
        $networks = $this->repo->getNetworksList();

		return view('admin.users.show', compact('user','networks'));
	}

	public function edit($userId)
	{
		$user = $this->repo->requireById($userId);
        $networks = $this->repo->getNetworksList();

		return view('admin.users.edit',compact('user','networks'));
	}

	public function update(Request $request, $userId)
	{
        $this->validate($request, [
            'name'     => 'required|min:5|max:60',
            'username' => 'required|max:30|unique:users,username,' . $request->segment(3),
            'email'    => 'required|email|unique:users,email,' . $request->segment(3),
            'phone'    => 'required|max:20',
            'password' => 'nullable|between:5,15',
            'role_id'  => 'required|numeric',
            'network_id' => 'required|numeric',
            'gender_id' => 'required|numeric',
        ]);

		$userData = $request->except(['_method','_token']);
		$user = $this->repo->update($userData, $userId);
		flash(trans('user.updated'), 'success');
		return redirect()->back();
	}

	public function delete($userId)
	{
	    $user = $this->repo->requireById($userId);
		return view('admin.users.delete', compact('user'));
	}

    public function destroy(Request $request, $userId)
    {
        $this->validate($request, [
            'user_id' => 'required|exists:users,id'
        ]);

        if ($request->get('user_id') == $userId && $this->repo->requireById($userId)->delete()) {
            flash(trans('user.deleted'), 'success');
            return redirect()->route('admin.users.index');
        }

        flash(trans('user.undeleted'), 'error');
        return back();
    }

}
