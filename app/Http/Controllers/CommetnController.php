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
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'comment' => 'required',
        ]);

        $validated['user_id'] = auth()->id();

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
        // Allow users to delete their own comments OR admins to delete any comment
        if (auth()->id() !== $commetn->user_id && ! auth()->user()->is_admin) {
            abort(403);
        }

        $commetn->delete();

        return back();
    }
}
