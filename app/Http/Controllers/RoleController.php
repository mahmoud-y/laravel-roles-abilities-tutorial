<?php

namespace App\Http\Controllers;

use App\Role;
use App\Ability;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view-any-role');
        
        return view('roles.index', ['roles' => Role::get()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create-role');
        
        return view('roles.create', ['abilities' => Ability::get()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create-role');
        
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'abilities' => ['required'],
        ]);

        $role = Role::create([
            'name' => $validatedData['name'],
        ]);
        $role->abilities()->attach($validatedData['abilities']);
        
        return redirect()->route('roles.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $this->authorize('view-role');
        
        return view('roles.show', ['abilities' => Ability::get(), 'role' => $role]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        $this->authorize('update-role');
        
        return view('roles.edit', ['abilities' => Ability::get(), 'role' => $role]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $this->authorize('update-role');
        
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'abilities' => ['required'],
        ]);

        $role->fill([
            'name' => $validatedData['name'],
        ]);
        if ($role->isDirty()) {
            $role->save();
        }
        $role->abilities()->sync($validatedData['abilities']);
        
        return redirect()->route('roles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $this->authorize('delete-role');
        
        $role->abilities()->detach();
        $role->users()->detach();
        $role->delete();
        
        return redirect()->route('roles.index');
    }
}
