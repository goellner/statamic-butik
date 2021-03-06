<?php

namespace Jonassiewertsen\StatamicButik\Http\Controllers\CP;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Jonassiewertsen\StatamicButik\Blueprints\ShippingProfileBlueprint;
use Jonassiewertsen\StatamicButik\Http\Controllers\CpController;
use Jonassiewertsen\StatamicButik\Http\Models\ShippingProfile;
use Statamic\CP\Column;

class ShippingProfilesController extends CpController
{
    public function index()
    {
        $this->authorize('index', ShippingProfile::class);

        return ShippingProfile::all()->map(function ($shipping) {
            return [
                'title' => $shipping->title,
                'slug'  => $shipping->slug,
                'zones' => $shipping->zones,
            ];
        })->toArray();
    }

    public function show(ShippingProfile $shippingProfile)
    {
        $this->authorize('show', ShippingProfile::class);

        return $shippingProfile->load('zones.rates');
    }

    public function store(Request $request)
    {
        $this->authorize('store', ShippingProfile::class);

        $blueprint = new ShippingProfileBlueprint();
        $fields    = $blueprint()->fields()->addValues($request->all());
        $fields->validate();
        $values = $fields->process()->values();
        ShippingProfile::create($values->toArray());
    }

    public function update(Request $request, ShippingProfile $shippingProfile)
    {
        $this->authorize('update', $shippingProfile);

        $blueprint = new ShippingProfileBlueprint();
        $fields    = $blueprint()->fields()->addValues($request->all());
        $fields->validate();
        $values = $fields->process()->values();
        $shippingProfile->update($values->toArray());
    }

    public function destroy(ShippingProfile $shippingProfile)
    {
        $this->authorize('delete', $shippingProfile);

        $shippingProfile->delete();
    }
}
