<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Ads\{
    DeleteAdRequest,
    StoreAdRequest
};
use App\Models\Ad;
use App\Traits\Files;


class AdsController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->generalResponse(
            Ad::latest()->paginate(10),
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAdRequest $request)
    {
        return $request->store();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteAdRequest $request, Ad $ad)
    {
        Files::deleteFile(public_path("Images/Ads/{$ad->image}"));
        $ad->delete();
        return $this->generalResponse(null, 'Deleted Successfully', 200);
    }
}
