<?php

namespace App\Http\Controllers;

use App\Models\ConsoleType;
use Illuminate\Http\Request;

class ConsoleTypeController extends Controller
{
    public function index()
    {
        $consoleTypes = ConsoleType::withCount('consoles')->paginate(20);
        return view('console-types.index', compact('consoleTypes'));
    }

    public function create()
    {
        return view('console-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'hourly_rate' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        ConsoleType::create($validated);

        return redirect()->route('console-types.index')
            ->with('success', 'Console type created successfully');
    }

    public function edit(ConsoleType $consoleType)
    {
        return view('console-types.edit', compact('consoleType'));
    }

    public function update(Request $request, ConsoleType $consoleType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'hourly_rate' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $consoleType->update($validated);

        return redirect()->route('console-types.index')
            ->with('success', 'Console type updated successfully');
    }

    public function destroy(ConsoleType $consoleType)
    {
        if ($consoleType->consoles()->count() > 0) {
            return back()->with('error', 'Cannot delete console type with existing consoles');
        }

        $consoleType->delete();

        return redirect()->route('console-types.index')
            ->with('success', 'Console type deleted successfully');
    }
}
