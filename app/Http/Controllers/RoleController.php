<?php

namespace App\Http\Controllers;

use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Controller;

use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Repositories\RoleRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;
use App\Models\Permission;
use App\Models\Role;
use DB;
use Hash;

class RoleController extends AppBaseController
{
    /** @var  RoleRepository */
    private $roleRepository;

    public function __construct(RoleRepository $roleRepo)
    {
        $this->roleRepository = $roleRepo;
    }

    /**
     * Display a listing of the Role.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $roles = $this->roleRepository->all();

        return view('roles.index')
        ->with('roles', $roles);
    }

    /**
     * Show the form for creating a new Role.
     *
     * @return Response
     */
    public function create()
    {
     \App\Commons\Pemission::sync();
     $pGroups = Permission::whereNotNull('module')->groupBy('module')->select('module')->get()->toArray();
     $pGroups = array_column($pGroups, 'module', 'module');
        // Lấy tên tất cả các route của mình để chuyển về tất cả các module
     foreach ($pGroups as $key => $value) {
        $tmp = Permission::where('module', $key)->orderBy('action')->select('id', 'display_name', 'action')->get()->toArray();
        $pGroups[$key] = array_column($tmp, 'id', 'action');
    }
    return view('roles.create', compact('pGroups'));
}

    /**
     * Store a newly created Role in storage.
     *
     * @param CreateRoleRequest $request
     *
     * @return Response
     */
    public function store(CreateRoleRequest $request)
    {
        $input = $request->all();

        $validator = \Validator::make($input = $request->all(), Role::rules());
        if ($validator->fails()) return back()->withErrors($validator)->withInput();

        $role = $this->roleRepository->create($input);

        foreach ($request->input('permissions') as $permission) {
            $role->attachPermission($permission);
        }

        Flash::success('Role saved successfully.');

        return redirect(route('roles.index'));
    }

    /**
     * Display the specified Role.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $role = $this->roleRepository->find($id);

        if (empty($role)) {
            Flash::error('Role not found');

            return redirect(route('roles.index'));
        }

        return view('roles.show')->with('role', $role);
    }

    /**
     * Show the form for editing the specified Role.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $role = $this->roleRepository->find($id);

        $permissions = array_column($role->permissions()->select("id")->get()->toArray(), 'id', 'id');
        // dd($permissions);

        $pGroups = Permission::whereNotNull('module')->groupBy('module')->select('module')->get()->toArray();
        $pGroups = array_column($pGroups, 'module', 'module');
        foreach ($pGroups as $key => $value) {
            $tmp = Permission::where('module', $key)->orderBy('action')->select('id', 'display_name', 'action')->get()->toArray();
            $pGroups[$key] = array_column($tmp, 'id', 'action');
        }

        if (empty($role)) {
            Flash::error('Role not found');

            return redirect(route('roles.index'));
        }

        return view('roles.edit', compact('role', 'pGroups', 'permissions'));
    }

    /**
     * Update the specified Role in storage.
     *
     * @param int $id
     * @param UpdateRoleRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateRoleRequest $request)
    {
        $role = $this->roleRepository->find($id);
        if (empty($role)) {
            Flash::error('Role not found');

            return redirect(route('roles.index'));
        }

        $role = $this->roleRepository->update($request->all(), $id);

        DB::table('permission_role')->where('role_id', $id)->delete();

        if ($request->input('permissions') == null)
        {
            Flash::success('Permissions which you picked for Role is empty');
            return redirect(route('roles.index', $id));
        }

        foreach ($request->input('permissions') as $permission) {
            $role->attachPermission($permission);
        }

        Flash::success('Role updated successfully.');

        return redirect(route('roles.index'));
    }

    /**
     * Remove the specified Role from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $role = $this->roleRepository->find($id);

        if (empty($role)) {
            Flash::error('Role not found');

            return redirect(route('roles.index'));
        }

        $this->roleRepository->delete($id);

        Flash::success('Role deleted successfully.');

        return redirect(route('roles.index'));
    }
}
