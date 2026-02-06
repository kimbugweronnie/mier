<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProjectController extends Controller
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
    public function store(OrderService $service, Request $request)
    {
        $order = $service->createOrder(
            $request->product_id,
            $request->quantity,
            $request->only(['user_id', 'notes'])
        );

        return response()->json($order);
    }

    public function update(OrderService $service, Request $request,
        $id)
    {
        $order = $service->updateOrder($id, $request->all());

        return response()->json($order);
    }

    public function destroy(OrderService $service, $id)
    {
        $service->deleteOrder($id);

        return response()->json(['message' => 'Deleted']);
    }

    public function restore(OrderService $service, $id)
    {
        $service->restoreOrder($id);

        return response()->json(['message' => 'Restored']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, string $id)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(string $id)
    // {
    //     //
    // }
}
