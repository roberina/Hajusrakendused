<?php

namespace App\Http\Controllers;

use App\Models\Shark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class SharkController extends Controller
{
    // Veebileht
    public function index()
    {
        return Inertia::render('Sharks/Index');
    }

    // Lisa uus vorm
    public function create()
    {
        return Inertia::render('Sharks/Form', ['shark' => null]);
    }

    // Salvesta uus
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'image'        => 'nullable|url|max:500',
            'description'  => 'required|string',
            'max_length'   => 'required|numeric|min:0.1|max:30',
            'habitat'      => 'required|string|max:255',
            'danger_level' => 'required|in:madal,keskmine,kõrge',
        ]);

        $data['user_id'] = auth()->id();
        Shark::create($data);
        Cache::flush();

        return redirect()->route('sharks.index')->with('success', 'Hai lisatud!');
    }

    // Muuda vorm
    public function edit(Shark $shark)
    {
        return Inertia::render('Sharks/Form', ['shark' => $shark]);
    }

    // Salvesta muudatused
    public function update(Request $request, Shark $shark)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'image'        => 'nullable|url|max:500',
            'description'  => 'required|string',
            'max_length'   => 'required|numeric|min:0.1|max:30',
            'habitat'      => 'required|string|max:255',
            'danger_level' => 'required|in:madal,keskmine,kõrge',
        ]);

        $shark->update($data);
        Cache::flush();

        return redirect()->route('sharks.index')->with('success', 'Hai uuendatud!');
    }

    // Kustuta
    public function destroy(Shark $shark)
    {
        $shark->delete();
        Cache::flush();

        return redirect()->route('sharks.index')->with('success', 'Hai kustutatud!');
    }

   
     
    public function api(Request $request)
    {
        $search      = $request->query('search', '');
        $habitat     = $request->query('habitat', '');
        $danger      = $request->query('danger_level', '');
        $sort        = in_array($request->query('sort'), ['title', 'max_length', 'created_at']) ? $request->query('sort') : 'created_at';
        $order       = $request->query('order', 'desc') === 'asc' ? 'asc' : 'desc';
        $limit       = min((int) $request->query('limit', 10), 100);

        $cacheKey = "sharks_api_{$search}_{$habitat}_{$danger}_{$sort}_{$order}_{$limit}";

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($search, $habitat, $danger, $sort, $order, $limit) {
            $query = Shark::with('user:id,name');

            if ($search) {
                $query->where('title', 'like', "%{$search}%");
            }
            if ($habitat) {
                $query->where('habitat', 'like', "%{$habitat}%");
            }
            if ($danger) {
                $query->where('danger_level', $danger);
            }

            return $query->orderBy($sort, $order)->limit($limit)->get();
        });

        return response()->json([
            'meta' => [
                'total'        => $data->count(),
                'limit'        => $limit,
                'sort'         => $sort,
                'order'        => $order,
                'search'       => $search,
                'habitat'      => $habitat,
                'danger_level' => $danger,
                'cached'       => true,
            ],
            'data' => $data,
        ]);
    }
}