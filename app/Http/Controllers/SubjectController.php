<?php

namespace App\Http\Controllers;

use App\Models\MyFavoriteSubject;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(MyFavoriteSubject::all());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('subjects/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|min:3',
            'description' => 'required|string|min:10|max:150',
            'category' => 'required',
            'emoji' => 'nullable|string|max:2',
            'intensity' => 'required|integer|min:1|max:10',
            'color' => 'nullable|string|max:7',
        ]);

        MyFavoriteSubject::create($validated);

        return redirect('/display-subjects');
    }

    /**
     * Display the specified resource.
     */
    public function show(MyFavoriteSubject $subject)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MyFavoriteSubject $subject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MyFavoriteSubject $subject)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MyFavoriteSubject $subject)
    {
        //
    }
}
