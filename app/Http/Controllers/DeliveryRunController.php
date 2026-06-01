<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRun;
use App\Models\DeliveryVehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliveryRunController extends Controller
{
    public function index(): View
    {
        return view('delivery-runs.index', [
            'vehicles' => DeliveryVehicle::orderBy('name')->get(),
            'runs' => DeliveryRun::with('vehicle')->withCount('transactions')->orderByDesc('run_date')->orderByDesc('id')->paginate(30),
        ]);
    }

    public function storeVehicle(Request $request): RedirectResponse
    {
        DeliveryVehicle::create($request->validate([
            'name' => ['required', 'string', 'max:100'],
            'plate_number' => ['nullable', 'string', 'max:30'],
            'driver_name' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => false]);

        return back()->with('success', 'Mobil berhasil ditambahkan.');
    }

    public function store(Request $request): RedirectResponse
    {
        DeliveryRun::create($this->validatedRun($request));

        return back()->with('success', 'Mobil berjalan berhasil dibuat.');
    }

    public function update(Request $request, DeliveryRun $deliveryRun): RedirectResponse
    {
        $deliveryRun->update($this->validatedRun($request));

        return back()->with('success', 'Mobil berjalan berhasil diperbarui.');
    }

    public function destroy(DeliveryRun $deliveryRun): RedirectResponse
    {
        $deliveryRun->update(['status' => 0]);

        return back()->with('success', 'Mobil berjalan dinonaktifkan.');
    }

    private function validatedRun(Request $request): array
    {
        return $request->validate([
            'delivery_vehicle_id' => ['required', 'exists:delivery_vehicles,id'],
            'run_date' => ['required', 'date'],
            'driver_name' => ['nullable', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', 'integer', 'in:0,1'],
        ]) + ['status' => 1];
    }
}
