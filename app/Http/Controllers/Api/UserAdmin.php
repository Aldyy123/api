<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UserAdmin extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        try {
            $input = $request->all();
            if (count($input) > 2) {
                $input['password'] = password_hash($input['password'], PASSWORD_BCRYPT);
                $user = DB::table('user_admin')->insertGetId($input);
                return response()->json([
                    'data' => $user,
                    'code' => Response::HTTP_OK,
                    'error' => false,
                    'message' => 'Berhasil membuat akun admin'
                ]);
            }

            return response()->json([
                'code' => Response::HTTP_BAD_REQUEST,
                'error' => true,
                'message' => 'Form harus diisi'
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'error' => true,
                'message' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $input = $request->all();
            $checkUser = DB::table('user_admin')->where('id', $id)->get()->toArray();
            if ($checkUser) {
                if (count($input) > 0) {
                    if (isset($input['password'])) $input['password'] = password_hash($input['password'], PASSWORD_BCRYPT);
                    $user = DB::table('user_admin')->where('id', $id)->update($input);
                    return response()->json([
                        'data' => $user,
                        'code' => Response::HTTP_OK,
                        'error' => false,
                        'message' => 'Berhasil mengupdate akun admin'
                    ]);
                }
                return response()->json([
                    'code' => Response::HTTP_BAD_REQUEST,
                    'error' => true,
                    'message' => 'Form harus diisi'
                ], Response::HTTP_BAD_REQUEST);
            }

            return response()->json([
                'code' => Response::HTTP_NOT_FOUND,
                'error' => true,
                'message' => 'User tidak ada'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'error' => true,
                'message' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $checkUser = DB::table('user_admin')->where('id', $id)->get()->toArray();
            if($checkUser){
                DB::table('user_admin')->delete($id);
                return response()->json([
                    'message' => 'Admin User berhasil di hapus',
                    'code' => Response::HTTP_OK,
                    'error' => false
                ]);
            }
            return response()->json([
                'message' => 'User admin tidak ada',
                'code' => Response::HTTP_NOT_FOUND,
                'error' => false
            ], Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'error' => true,
                'message' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
