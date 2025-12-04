<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Ads\{
    DeleteAdRequest,
    StoreAdRequest
};
use App\Models\Ad;
use App\Services\GoogleTranslateService;
use App\Traits\Files;


class AdsController extends Controller
{
        public function __construct(private GoogleTranslateService $service){}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ads = Ad::latest()->paginate(10);
        $target = app()->getLocale();
        $ads->getCollection()->transform(function ($ad) use($target) {
            if(false/*$target != 'ar'*/) {
                $ad->text_ar = $this->service->translate($ad->text_ar, $target);
            }
            return $ad;
        });
        return $this->generalResponse($ads);
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
