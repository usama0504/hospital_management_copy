<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with('doctors')->latest()->get();
        return Inertia::render('Departments/Index', ['departments' => $departments,]);
    }
    public function create()
    {
        return Inertia::render('Departments/Create');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string',
            'status' => 'boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = collect($validated)->except('image')->toArray();

        if ($request->hasFile('image')) {
            $data['image_url'] = Storage::url($request->file('image')->store('departments', 'public'));
        }

        Department::create($data);
        return redirect()->route('departments.index')->with('success', 'Department created successfully.');
    }
    public function edit(Department $department)
    {
        return Inertia::render('Departments/Edit', ['department' => $department,]);
    }
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'description' => 'nullable|string',
            'status' => 'boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = collect($validated)->except('image')->toArray();

        if ($request->hasFile('image')) {
            // Purani image storage se hata dein taake disk na bhare
            if ($department->image_url) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $department->image_url));
            }

            $data['image_url'] = Storage::url($request->file('image')->store('departments', 'public'));
        }

        $department->update($data);
        return redirect()->route('departments.index')->with('success', 'Department updated successfully.');
    }
    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('departments.index')->with('success', 'Department deleted successfully.');
    }
}