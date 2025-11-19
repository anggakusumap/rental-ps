<?php

namespace App\Http\Controllers;

use App\Models\Console;
use App\Models\ConsoleType;
use Illuminate\Http\Request;

class ConsoleController extends Controller
{
    public function index()
    {
        $consoles = Console::with('consoleType')->paginate(20);
        return view('consoles.index', compact('consoles'));
    }

    public function create()
    {
        $consoleTypes = ConsoleType::where('is_active', true)->get();
        return view('consoles.create', compact('consoleTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'console_type_id' => 'required|exists:console_types,id',
            'console_number' => 'required|unique:consoles,console_number',
            'notes' => 'nullable|string',
        ]);

        Console::create($validated);

        return redirect()->route('consoles.index')
            ->with('success', 'Console created successfully');
    }

    public function edit(Console $console)
    {
        $consoleTypes = ConsoleType::where('is_active', true)->get();
        return view('consoles.edit', compact('console', 'consoleTypes'));
    }

    public function update(Request $request, Console $console)
    {
        $validated = $request->validate([
            'console_type_id' => 'required|exists:console_types,id',
            'console_number' => 'required|unique:consoles,console_number,' . $console->id,
            'status' => 'required|in:available,occupied,maintenance',
            'notes' => 'nullable|string',
        ]);

        $console->update($validated);

        return redirect()->route('consoles.index')
            ->with('success', 'Console updated successfully');
    }

    public function destroy(Console $console)
    {
        if ($console->status === 'occupied') {
            return back()->with('error', 'Cannot delete occupied console');
        }

        $console->delete();

        return redirect()->route('consoles.index')
            ->with('success', 'Console deleted successfully');
    }
}
