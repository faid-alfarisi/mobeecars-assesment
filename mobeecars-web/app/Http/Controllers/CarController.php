<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class CarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        return view('cars');
    }

    public function data(Request $request)
    {
        $cars = Car::select([
            'id',
            'brand',
            'model',
            'type',
            'image',
            'created_at',
        ]);

        return DataTables::of($cars)
            // ->addIndexColumn()
            ->editColumn('image', function ($row) {
                $imageUrl = asset('storage/'.$row->image);
                return '<img src="'.$imageUrl.'" alt="Car Image" onclick="show_image(this);" style="height: 80px; object-fit: cover; border-radius: 6px; cursor: pointer;">';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d/m/Y H:i');
            })
            ->addColumn('action', function ($row) {
                $editButton = '
                    <button
                        type="button"
                        class="btn btn-sm btn-success btn-edit"
                        data-id="'.$row->id.'"
                        data-url="'.route('cars.update', $row->id).'"
                        style="width: 1.5rem; height: 1.5rem; padding: 0;">
                        <i class="fa fa-edit"></i>
                    </button>
                ';
                $deleteButton = '
                    <button
                        type="button"
                        class="btn btn-sm btn-danger btn-delete"
                        data-id="'.$row->id.'"
                        data-url="'.route('cars.destroy', $row->id).'"
                        style="width: 1.5rem; height: 1.5rem; padding: 0;">
                        <i class="fa fa-trash"></i>
                    </button>
                ';
                return '
                    <div class="d-flex gap-2 justify-content-center">
                        '.$editButton.'
                        '.$deleteButton.'
                    </div>
                ';
            })
            ->rawColumns(['image', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand' => ['required'],
            'model' => ['required'],
            'type' => ['required'],
            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],
        ]);

        $imagePath = $request->file('image')->store('cars', 'public');

        Car::create([
            'brand' => $validated['brand'],
            'model' => $validated['model'],
            'type' => $validated['type'],
            'image' => $imagePath,
        ]);

        return response()->json([
            'success' => true,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $car = Car::findOrFail($id);

        $validated = $request->validate([
            'brand' => ['required'],
            'model' => ['required'],
            'type' => ['required'],
            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],
        ]);

        $updateData = [
            'brand' => $validated['brand'],
            'model' => $validated['model'],
            'type' => $validated['type'],
        ];

        if ($request->hasFile('image')) {
            if ($car->image) {
                Storage::disk('public')->delete($car->image);
            }
            $updateData['image'] = $request->file('image')->store('cars', 'public');
        }

        $car->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Car updated successfully.',
        ]);
    }

    public function destroy(string $id)
    {
        $car = Car::findOrFail($id);

        if ($car->image) {
            Storage::disk('public')->delete($car->image);
        }

        $car->delete();

        return response()->json([
            'success' => true,
            'message' => 'Car deleted successfully.',
        ]);
    }
}
