<?php

namespace App\Http\Controllers\Booking;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Booking\CustomTax;
use App\Http\Controllers\Controller;
use App\Models\Booking\CustomTaxSetting;
use App\Models\Booking\Extra;
use App\Models\Booking\Location;
use App\Models\Booking\Room;
use App\Models\Booking\TransferExtra;
use App\Services\Booking\TaxService;
use Illuminate\Http\RedirectResponse;

class TaxesController extends Controller
{
    /**
     * Display a listing of the taxes.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(): View
    {
        $taxes = CustomTax::orderBy('sort', 'asc')->get();

        return view('Booking.taxes.index', compact('taxes'));
    }

    /**
     * Show the form for editing tax.
     * 
     * @return \Illuminate\Http\Response
     */
    public function show(int $id): View
    {
        $tax = CustomTax::with(['settings'])->find($id);

        $taxes = TaxService::getActiveTaxes();

        $locations = Location::with(['rooms' => fn ($query) => $query->where('active', 1)])->where('active', 1)->get();

        $addons = Extra::where('active', 1)->orderBy('sort', 'asc')->get();

        $transfers = TransferExtra::orderBy('id', 'asc')->get();

        $settings = CustomTaxSetting::all()->map(function ($setting) {
            return [
                'model_id' => $setting->model_id,
                'model_path' => $setting->model_path,
                'custom_tax_id' => $setting->custom_tax_id
            ];
        })
            ->groupBy('model_path')
            ->map(fn ($group) => collect($group)->pluck('model_id'))
            ->toArray();

        return view('Booking.taxes.show', compact('tax', 'taxes', 'addons', 'locations', 'settings', 'transfers'));
    }

    /**
     * Show the form for creating a new tax.
     * 
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        return view('Booking.taxes.new');
    }

    /**
     * Store a newly created tax in storage.
     * 
     * @return \Illuminate\Http\Response
     */
    public function insert(): RedirectResponse
    {
        $validated = request()->validate([
            'name' => 'required',
            'type' => 'required',
            'rate' => 'required',
            'calculation_type' => 'required_if:type,flat',
            'calculation_charge' => 'required_if:type,flat',
            'tax_type' => 'required'
        ]);

        CustomTax::create([
            'name' => request('name'),
            'type' => request('type'),
            'rate' => request('rate'),
            'calculation_type' => request('calculation_type'),
            'calculation_charge' => request('calculation_charge'),
            'tax_type' => request('tax_type'),
            'is_active' => request()->has('is_active')
        ]);

        return redirect()->route('tenant.taxes');
    }

    /**
     * Update tax information.
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(int $id): RedirectResponse
    {
        $tax = CustomTax::find($id);

        $tax->update([
            'name' => request('name'),
            'type' => request('type'),
            'rate' => request('rate'),
            'calculation_type' => request('calculation_type'),
            'calculation_charge' => request('calculation_charge'),
            'tax_type' => request('tax_type'),
            'is_active' => request()->has('is_active')
        ]);

        $this->syncTaxSettings(request('tax_location'), Location::class, $tax->id);
        $this->syncTaxSettings(request('tax_accommodations'), Room::class, $tax->id);
        $this->syncTaxSettings(request('tax_addons'), Extra::class, $tax->id);
        $this->syncTaxSettings(request('tax_transfers'), TransferExtra::class, $tax->id);

        return redirect()->route('tenant.taxes');
    }

    protected function syncTaxSettings(array|null $data, string $model, int $custom_tax_id): void
    {
        if (!$data) {
            CustomTaxSetting::where('model_path', $model)->where('custom_tax_id', $custom_tax_id)->delete();
            return;
        }

        $model_ids = collect($data)->keys()->toArray();

        CustomTaxSetting::where('model_path', $model)
            ->where('custom_tax_id', $custom_tax_id)
            ->whereNotIn('model_id', $model_ids)
            ->delete();

        foreach ($data as $model_id => $value) {
            CustomTaxSetting::updateOrCreate([
                'model_id' => $model_id,
                'model_path' => $model,
                'custom_tax_id' => $custom_tax_id
            ]);
        }
    }

    /**
     * Delete tax from database.
     * 
     * @return \Illuminate\Http\Response
     */
    public function delete(int $id): RedirectResponse
    {
        CustomTax::find($id)->delete();

        return redirect()->route('tenant.taxes');
    }

    /**
     * Sort taxes by position
     * 
     * @return \Illuminate\Http\Response
     */
    public function sort(): Response
    {
        $sort = request('data');

        foreach ($sort as $pos => $id) {
            CustomTax::find($id)->update([
                'sort' => intVal($pos) + 1
            ]);
        }

        return response('OK');
    }
}
