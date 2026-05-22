<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Car;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AppController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'     => 'required',
            'password'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 200);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid Credentials',
            ], 401);
        }

        $token = $user->createToken($user->name.$user->email.'-AuthToken')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'user' => $user,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'logged out',
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8',
            'confirm_password' => 'required|min:8',
            'old_password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 200);
        }
        if ($request->password != $request->confirm_password) {
            return response()->json([
                'status' => 'error',
                'errors' => 'The password field confirmation does not match.',
            ], 200);
        }

        // Find user
        $user = User::find($request->user()->id);

        if ($user && Hash::check($request->old_password, $user->password)) {
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'message' => 'Password updated successfully',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'errors' => 'Current password is incorrect',
            ], 200);
        }

        return response()->json([
            'message' => 'something wrong.',
        ], 404);
    }

    public function getCars(Request $request) {
        $cars = Car::all();
        return response()->json([
            'data' => $cars,
        ], 200);
    }

    public function getPreferences(Request $request) {
        $prefs = UserPreference::where('user_id', $request->user()->id)->get();
        return response()->json([
            'data' => $prefs,
        ], 200);
    }

    public function savePreferences(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prefs' => 'required|array|min:1',
            'prefs.*.car_id' => 'required|integer',
            'prefs.*.liked' => 'required|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 200);
        }

        $user_id = $request->user()->id;
        $now = now();
        $insertData = [];

        foreach ($request->prefs as $pref) {
            $insertData[] = [
                'user_id' => $user_id,
                'car_id' => $pref['car_id'],
                'liked' => $pref['liked'],
                'updated_at' => $now,
                'created_at' => $now,
            ];
        }

        UserPreference::upsert(
            $insertData,
            ['user_id', 'car_id'], // unique by
            ['liked', 'updated_at'] // columns to update
        );

        return response()->json([
            'status' => 'success',
        ], 200);
    }

    private function getMostLiked($column)
    {
        return DB::table('user_preferences as p')
            ->join('cars as c', 'c.id', '=', 'p.car_id')
            ->where('p.liked', 1)
            ->select("c.$column", DB::raw("COUNT(*) as total"))
            ->groupBy("c.$column")
            ->orderByDesc('total')
            ->value($column);
    }

    public function globalPreferences(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'favorite_brand' => $this->getMostLiked('brand'),
                'favorite_model' => $this->getMostLiked('model'),
                'favorite_type' => $this->getMostLiked('type'),
            ]
        ], 200);
    }
}
