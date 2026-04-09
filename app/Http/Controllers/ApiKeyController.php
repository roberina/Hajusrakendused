<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ApiKeyController extends Controller
{
    public function index()
    {
        return Inertia::render('ApiKeys/Index', [
            'apiKeys' => auth()->user()->apiKeys()->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $key = ApiKey::create([
            'user_id' => auth()->id(),
            'key'     => Str::random(40),
            'name'    => $request->name,
        ]);

        return redirect()->back()->with('new_key', $key->key);
    }

    public function destroy(ApiKey $apiKey)
    {
        if ($apiKey->user_id !== auth()->id()) abort(403);
        $apiKey->delete();
        return redirect()->back()->with('success', 'Võti kustutatud!');
    }
}