<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\UserRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;
use App\Models\Role;
use App\Models\User;
use Hash;
use DB;
use Illuminate\Database\Eloquent\Model;

class UserController extends AppBaseController
{
    /** @var  UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the User.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $users = $this->userRepository->all();
        return view('users.index')
        ->with('users', $users);
    }

    /**
     * Show the form for creating a new User.
     *
     * @return Response
     */
    public function create()
    {
        if (!(\Auth::user()->hasPermission('users.create')))
        {
            Flash::error('<span class="glyphicon glyphicon-exclamation-sign"></span> You have not permission to add a new user');
            return redirect(route('users.index'));
        }

        $roles = Role::get();
        return view('users.create')->with('roles', $roles);
    }

    /**
     * Store a newly created User in storage.
     *
     * @param CreateUserRequest $request
     *
     * @return Response
     */
    public function store(CreateUserRequest $request)
    {
        $validator = \Validator::make($data = $request->all(), User::rules());
        if ($validator->fails()) return back()->withErrors($validator)->withInput();

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $user = $this->userRepository->create($input);

        foreach ($request->input('roles') as $role) {
            $user->attachRole($role);
        }

        Flash::success('User saved successfully.');

        return redirect(route('users.index'));
    }

    /**
     * Display the specified User.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $user = $this->userRepository->find($id);
        if (empty($user)) {
            Flash::error('User not found');
            return redirect(route('users.index'));
        }
        if ((!(\Auth::user()->hasPermission('users.read'))) || ((\Auth::user()->id != $id) && (\Auth::user()->name != "Superadministrator")))
        {
            Flash::error('<span class="glyphicon glyphicon-exclamation-sign"></span> You have not permission to view this user');
            return redirect(route('users.index'));
        }
        if((\Auth::user()->id == $id) || (\Auth::user()->name == "Superadministrator"))
        {
            return view('users.show')->with('user', $user);
        }
    }

    /**
     * Show the form for editing the specified User.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $user = $this->userRepository->find($id);
        $roles = Role::get();

        if (empty($user)) {
            Flash::error('User not found');

            return redirect(route('users.index'));
        }
        if ((!(\Auth::user()->hasPermission('users.read'))) || ((\Auth::user()->id != $id) && (\Auth::user()->name != "Superadministrator")))
        {
            Flash::error('<span class="glyphicon glyphicon-exclamation-sign"></span> You have not permission to change this user');
            return redirect(route('users.index'));
        }
        if((\Auth::user()->id == $id) || (\Auth::user()->name == "Superadministrator"))
        {
            return view('users.edit')->with(['user' => $user, 'roles' => $roles]);
        }
    }

    /**
     * Update the specified User in storage.
     *
     * @param int $id
     * @param UpdateUserRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUserRequest $request)
    {
        $validator = \Validator::make($data = $request->all(), User::rules($id));
        if($validator->fails()) return back()->withErrors($validator)->withInput();

        $user = $this->userRepository->find($id);

        $input = $request->all();

        if (empty($user)) {
            Flash::error('User not found');

            return redirect(route('users.index'));
        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $user = $this->userRepository->update($input, $id);

        DB::table('role_user')->where('user_id', $id)->delete();

        foreach($request->input('roles') as $role){
            $user->attachRole($role);
        }

        Flash::success('User updated successfully.');

        return redirect(route('users.index'));
    }

    /**
     * Remove the specified User from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            Flash::error('User not found');

            return redirect(route('users.index'));
        }

        if (!(\Auth::user()->hasPermission('users.delete')))
        {
            Flash::error('<span class="glyphicon glyphicon-exclamation-sign"></span> You have not permission to delete this user');
            return redirect(route('users.index'));
        }

        User::find($id)->delete();

        Flash::success('User deleted successfully.');

        return redirect(route('users.index'));
    }
}
