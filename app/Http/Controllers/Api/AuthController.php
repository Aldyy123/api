<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiswaModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

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
                    'data' => $user,
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

    public function authAdmin(Request $request)
    {
        $input = $request->all();

        try {
            $user_admin = DB::table('user_admin')->where('username', '=', $input['username'])->get()->first();

            if ($user_admin) {
                $verify_pass = password_verify($input['password'], $user_admin->password);
                if ($verify_pass) {
                    return response()->json([
                        'data' => $user_admin,
                        'error' => false,
                        'message' => 'Berhasil Login',
                        'code' => Response::HTTP_OK
                    ]);
                } else {
                    return response()->json([
                        'message' => 'Password tidak sama',
                        'error' => true,
                        'code' => Response::HTTP_NOT_ACCEPTABLE
                    ], Response::HTTP_NOT_ACCEPTABLE);
                }
            }

            return response()->json([
                'message' => 'User tidak temukan',
                'error' => true,
                'code' => Response::HTTP_NOT_ACCEPTABLE
            ], Response::HTTP_NOT_ACCEPTABLE);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'message' => $th->getMessage(),
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function register_admin(Request $request)
    {
        try {
            $input = $request->all();
            $input['password'] = password_hash($input['password'], PASSWORD_BCRYPT);
            $user_admin = DB::table('user_admin')->insert($input);
            return response()->json([
                'data' => $user_admin,
                'code' => Response::HTTP_OK,
                'message' => 'Success membuat akun',
                'error' => false
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'message' => $th->getMessage(),
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
