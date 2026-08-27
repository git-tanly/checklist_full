<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    private $positions = [
        'Restaurant Manager',
        'Assistant Restaurant Manager',
        'F&B Supervisor',
        'Waiter',
        'Cashier',
        'Bartender',
        'Daily Worker',
        'Trainee'
    ];

    public function index()
    {
        $employees = Employee::with('restaurant')->get();
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $restaurants = Restaurant::all();
        $positions = $this->positions;
        return view('employees.create', compact('restaurants', 'positions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'name' => 'required|string|max:255',
            'position' => 'required|string|in:' . implode(',', $this->positions),
        ]);

        Employee::create($request->all());

        return redirect()->route('employees.index')
            ->with('success', 'Employee berhasil ditambahkan.');
    }

    public function edit(Employee $employee)
    {
        $restaurants = Restaurant::all();
        $positions = $this->positions;
        return view('employees.edit', compact('employee', 'restaurants', 'positions'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'name' => 'required|string|max:255',
            'position' => 'required|string|in:' . implode(',', $this->positions),
        ]);

        $employee->update($request->all());

        return redirect()->route('employees.index')
            ->with('success', 'Employee berhasil diupdate.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')
            ->with('success', 'Employee berhasil dihapus.');
    }
}
