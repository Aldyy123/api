<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiswaModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    //
    /**
     * Auth student
     * Login during check a transaction
     */

    public function auth(Request $request)
    {
        $input = $request->all();

        $user = SiswaModel::find($input['nisn']);
        if ($user) {
            $family = $user->family;
            if ($family && $user['nisn'] == $input['nisn'] && strtolower($family['mother']) === strtolower($input['parent_name'])) {

                return response()->json([
                    'user' => $user,
                    'status' => 'success',
                    'error' => false,
                    'code' => Response::HTTP_OK,
                    'message' => 'User be match'
                ]);
            }
            return response()->json([
                'status' => 'fail',
                'error' => true,
                'code' => Response::HTTP_NOT_FOUND,
                'message' => 'Nama ibu tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'status' => 'fail',
            'error' => true,
            'code' => Response::HTTP_NOT_FOUND,
            'message' => 'Nisn tidak ditemukan'
        ], Response::HTTP_NOT_FOUND);
    }
}
