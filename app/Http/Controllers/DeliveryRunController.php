<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRun;
use App\Models\DeliveryRoute;
use App\Models\DeliveryVehicle;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliveryRunController extends Controller
{
    public function index(): View
    {
        return view('delivery-runs.index', [
            'vehicles' => DeliveryVehicle::with(['user', 'routes'])->orderBy('name')->get(),
            'users' => User::where('is_active', true)->orderBy('name')->get(),
            'routes' => DeliveryRoute::with('vehicle.user')->orderBy('day_of_week')->orderBy('name')->get(),
            'runs' => DeliveryRun::with(['vehicle.user', 'route'])->withCount('transactions')->orderByDesc('run_date')->orderByDesc('id')->paginate(30),
            'dayOptions' => $this->dayOptions(),
        ]);
    }

    public function storeVehicle(Request $request): RedirectResponse
    {
        DeliveryVehicle::create($request->validate([
            'name' => ['required', 'string', 'max:100'],
            'user_id' => ['nullable', 'exists:users,id'],
            'plate_number' => ['nullable', 'string', 'max:30'],
            'driver_name' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => false]);

        return back()->with('success', 'Mobil berhasil ditambahkan.');
    }

    public function storeRoute(Request $request): RedirectResponse
    {
        DeliveryRoute::create($request->validate([
            'delivery_vehicle_id' => ['nullable', 'required_without:delivery_route_id', 'exists:delivery_vehicles,id'],
            'day_of_week' => ['required', 'integer', 'between:0,6'],
            'name' => ['required', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => false]);

        return back()->with('success', 'Rute mingguan berhasil ditambahkan.');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedRun($request);

        if (! empty($data['delivery_route_id'])) {
            $route = DeliveryRoute::with('vehicle.user')->findOrFail($data['delivery_route_id']);
            $data['delivery_vehicle_id'] = $route->delivery_vehicle_id;
            $data['driver_name'] = ($data['driver_name'] ?? null) ?: $route->vehicle?->driverLabel();
            $data['area'] = ($data['area'] ?? null) ?: $route->area ?: $route->name;
            $data['notes'] = ($data['notes'] ?? null) ?: $route->notes;
        } else {
            $vehicle = DeliveryVehicle::with('user')->find($data['delivery_vehicle_id']);
            $data['driver_name'] = ($data['driver_name'] ?? null) ?: $vehicle?->driverLabel();
        }

        DeliveryRun::create($data);

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
            'delivery_vehicle_id' => ['nullable', 'required_without:delivery_route_id', 'exists:delivery_vehicles,id'],
            'delivery_route_id' => ['nullable', 'exists:delivery_routes,id'],
            'run_date' => ['required', 'date'],
            'driver_name' => ['nullable', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', 'integer', 'in:0,1'],
        ]) + ['status' => 1];
    }

    private function dayOptions(): array
    {
        return [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            0 => 'Minggu',
        ];
    }
}
