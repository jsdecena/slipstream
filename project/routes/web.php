<?php

use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// For simplicity, we inject data here instead of using controllers for demo purposes
Route::get('/', function () {
    // exclusively return the template, we do data manipulation on another method below
    return Inertia::render('Welcome');
});

// FEtch the customers
Route::get('/customers', function () {
    $data = Customer::paginate(25);
    return response()->json($data);
});

// FEtch the single customer
Route::get('/customers/{id}', function ($id) {
    $data = Customer::findOrFail($id);
    return response()->json($data);
});

// Updates the single customer
Route::put('/customers/{id}', function ($id, StoreCustomerRequest $request) {
    $customer = Customer::findOrFail($id);
    $customer->update([
        'name' => $request->name,
        'reference' => $request->reference,
        'category' => $request->category,
        'start_date' => $request->start_date,
        'description' => $request->description,
    ]);
    $customer->refresh();
    return response()->json($customer);
});

// Create customers
Route::post('/customers', function (StoreCustomerRequest $request) {
    $data = Customer::create([
        'name' => $request->name,
        'reference' => $request->reference,
        'category' => $request->category,
        'start_date' => $request->start_date,
        'description' => $request->description,
    ]);

    return response()->json($data, 201);
});
