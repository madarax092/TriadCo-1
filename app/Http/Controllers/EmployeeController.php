<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;

class EmployeeController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $employees = Employee::with('user')->get(); // Load employees with their associated user
        return view('employees.index', compact('employees'));
    }

    public function edit(Employee $employee)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => ['reqquired', 'string', 'max:56', 'unique:users,name', 'regex:/^[a-zA-Z0-9\s]+$/'],
            'email' => 'required|email|unique:users,email,' . $employee->user_id,
            'password' => 'nullable|string|min:5|confirmed',
            'first_name' => 'required|string|max:56',
            'last_name' => 'required|string|max:56',
            'address' => 'required|string|max:56',
            'contact_number' => 'required|string|max:15',
            'sss_number' => 'required|string|max:20',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update the associated user
        $employee->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'] ? bcrypt($validated['password']) : $employee->user->password,
        ]);

        // Handle profile picture upload
        $profilePicture = $employee->profile_picture;
        if ($request->hasFile('profile_picture')) {
            $profilePicture = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        // Update the employee details
        $employee->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'address' => $validated['address'],
            'contact_number' => $validated['contact_number'],
            'sss_number' => $validated['sss_number'],
            'profile_picture' => $profilePicture,
        ]);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully!');
    }

    public function destroy(Employee $employee)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        // Delete the associated user and employee
        $employee->user->delete();
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully!');
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:56', 'unique:users,name', 'regex:/^[a-zA-Z0-9\s]+$/'],
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:5|confirmed',
            'first_name' => 'required|string|max:56',
            'last_name' => 'required|string|max:56',
            'address' => 'required|string|max:56',
            'contact_number' => 'required|string|max:15',
            'sss_number' => 'required|string|max:20',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'employee', 
        ]);

        $profilePicture = 'images/TCEmployeeProfile.png';
        if ($request->hasFile('profile_picture')) {
            $profilePicture = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        Employee::create([
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address' => $request->address,
            'contact_number' => $request->contact_number,
            'email' => $request->email,
            'sss_number' => $request->sss_number,
            'profile_picture' => $profilePicture,
        ]);

        return redirect()->route('employees.index')->with('success', 'Employee account created successfully!');
    }
}