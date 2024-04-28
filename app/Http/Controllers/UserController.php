<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:create-user|edit-user|delete-user', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-user', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-user', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-user', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('users.index',
        [
            'users' => User::latest('id')->paginate(3),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create',[
            'roles' => Role::pluck('name')->all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request) : RedirectResponse
    {
        $input = $request->all();
        $input['password'] = Hash::make($request->password);

        $user = User::create($input);
        $user->assignRole($request->role);
        return redirect()->route('users.index')->withSuccess('New user is added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', [
            'user' => $user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        if($user->hasRole('adminbaak')){
            if($user->id != auth()->user()->id)
            {
                abort(403, 'Unauthorized action.');
            }
        }

        return view('users.edit',[
            'user' => $user,
            'roles' => Role::pluck('name')->all(),
            'userRole' => $user->roles->pluck('name')->all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user) : RedirectResponse
    {
        $input = $request->all();
        if(!empty($request->password)) {
            $input['password'] = Hash::make($request->password);
        } else {
            $input = $request->except('password');
        }

        $user->update($input);
        $user->syncRoles($request->role);
        return redirect()->route('users.index')->withSuccess('User is updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user) : RedirectResponse
    {
        if($user->hasRole('adminbaak') || $user->id == auth()->user()->id)
        {
            abort(403, 'Unauthorized action');
        }
        $user->syncRoles([]);
        $user->delete();
        return redirect()->route('users.index')->withSuccess('User is deleted successfully');
    }
}
