<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FamilyModel;
use App\Models\SiswaModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Family extends Controller
{

    public function __construct()
    {
        $this->family = new FamilyModel();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $data = null;

        if (isset($query['sortby'])) {
            $data = FamilyModel::orderBy('nisn_siswa', $query['sortby']);
        } else {
            $data = FamilyModel::orderBy('nisn_siswa');
        }

        $count = $data->get()->count();

        if (isset($query['address'])) {
            $data->where('address', 'LIKE', "%{$query['address']}%");
        }
        if (isset($query['nisn'])) {
            $data->where('nisn_siswa', 'LIKE', "%{$query['nisn']}%");
        }

        if (isset($query['ibu'])) {
            $data->where('mother', 'LIKE', "%{$query['mother']}%");
        }

        if (isset($query['ayah'])) {
            $data->where('father', 'LIKE', "%{$query['father']}%");
        }

        if (isset($query['kelurahan'])) {
            $data->where('kelurahan', 'LIKE', "%{$query['kelurahan']}%");
        }

        if (isset($query['kecamatan'])) {
            $data->where('kecamatan', 'LIKE', "%{$query['kecamatan']}%");
        }

        if (isset($query['phone'])) {
            $data->where('phone', 'LIKE', "%{$query['phone']}%");
        }

        if (isset($query['limit']) && isset($query['page'])) {
            $query['page'] = $query['page'] <= 1 ? $query['page'] = 0 : $query['page'];
            $data->skip($query['page'] * $query['limit'])->take($query['limit']);
        }


        return response()->json([
            'data' => $data->get(),
            'count' => $count,
            'code' => Response::HTTP_OK,
            'message' => 'Data keluarga berhasil diambil',
            'error' => false
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            $check_nisn = SiswaModel::check_user($input['nisn_siswa']);
            $validate = $this->family->validation_family($input);
            if ($check_nisn) {
                if (!$validate['error']) {
                    return FamilyModel::newFamily($input);
                }

                return $validate;
            }
        } catch (\Throwable $th) {
            return response()->json([
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $th->getMessage(),
                'error' => true,
            ]);
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
        try {
            if (FamilyModel::check_family($id)) {
                $family = FamilyModel::find($id);
                return response()->json([
                    'data' => $family,
                    'code' => Response::HTTP_OK,
                    'error' => false,
                    'message' => 'Berhasil'
                ]);
            }
            return response()->json([
                'code' => Response::HTTP_NOT_FOUND,
                'error' => true,
                'message' => 'Data tidak ada'
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
        $familyIsExist = FamilyModel::check_family($id);
        $family = FamilyModel::find($id);
        $validate = $this->family->validation_family($request->all());

        if ($familyIsExist) {
            if (!$validate['error']) {
                $family->update($request->all());
                return response()->json([
                    'data' => $family,
                    'error' => false,
                    'code' => 200,
                    'message' => 'Data has been updated'
                ]);
            }

            return $validate;
        }
        return response()->json([
            'data' => $family,
            'error' => true,
            'code' => Response::HTTP_NOT_FOUND,
            'message' => 'User not found'
        ], Response::HTTP_NOT_FOUND);
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
            if (FamilyModel::check_family($id)) {
                $family = FamilyModel::find($id);
                $family->delete();
                return response()->json([
                    'message' => 'Data has been deleted',
                    'error' => false,
                    'code' => Response::HTTP_OK
                ]);
            }

            return response()->json([
                'message' => 'User not found',
                'error' => true,
                'code' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
                'error' => true,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
