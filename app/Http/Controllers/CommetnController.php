<?php

namespace App\Http\Controllers;

use App\Models\Commetn;
use Illuminate\Http\Request;

class CommetnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate just post_id and comment from the request
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'comment' => 'required',
        ]);
        
        // Add the user_id to the validated data
        $validated['user_id'] = auth()->id();
        
        // Create the comment
        Commetn::create($validated);
        
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Commetn $commetn)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Commetn $commetn)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Commetn $commetn)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Commetn $commetn)
    {
        // Only admins can delete comments
        if (!auth()->user()->is_admin) {
            abort(403);
        }
        
        $commetn->delete();
        
        return back();
    }
}
