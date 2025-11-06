<?php

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\StoreContactRequest;
use App\Models\Contact;
use App\Models\Customer;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// For simplicity, we inject data here instead of using controllers for demo purposes
Route::get('/', function () {
    // exclusively return the template, we do data manipulation on another method below
    return Inertia::render('Welcome');
});

// ================================= Customers endpoint 
// FEtch all the customers
Route::get('/customers', function () {
    $data = Customer::paginate(25);
    return response()->json($data);
});

// FEtch the single customer
Route::get('/customers/{id}', function ($id) {
    $data = Customer::findOrFail($id);
    return response()->json($data);
});

// Fetch the customer's contacts
Route::get('/customers/{id}/contacts', function ($id) {
    $customer = Customer::findOrFail($id);
    return response()->json($customer->contacts);
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


// ================================= Contacts endpoint 
// Create customers
Route::post('/contacts', function (StoreContactRequest $request) {
    $data = Contact::create([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'customer_id' => $request->customer_id,
    ]);

    return response()->json($data);
});

// Create customers
Route::put('/contacts/{id}', function ($id, StoreContactRequest $request) {
    $contact = Contact::findOrFail($id);
    $data = $contact->update([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'customer_id' => $request->customer_id,
    ]);

    return response()->json($data);
});