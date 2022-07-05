<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiswaModel;
use App\Models\SPPTransaction;
use App\Models\StudyYear;
use Illuminate\Http\Response;

class Siswa extends Controller
{
    public function __construct()
    {
        $this->student = new SiswaModel();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = SiswaModel::all();
        return response()->json([
            'siswa' => $data
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

    public function check_spp_transaction(Request $request, $nisn)
    {
        $params_query = $request->query();
        $siswa = SiswaModel::find($nisn);
        if ($siswa) {
            if (isset($params_query['paid_off'])) {
                return response()->json([
                    'spp_Transaction' => $siswa->spp_transaction->where('paid_off', $params_query['paid_off']),
                    'code' => Response::HTTP_OK,
                    'error' => false,
                    'message' => 'Success full get spp transaction'
                ]);
                return;
            } else if (isset($params_query['year'])) {
                return response()->json([
                    'spp_transaction' =>  $siswa[0]->spp_transaction->where('study_year_id', $params_query['year']),
                    'code' => Response::HTTP_OK,
                    'error' => false,
                    'message' => 'Success full get spp transaction'
                ]);
            }
            return response()->json([
                'spp_transaction' => $siswa->spp_transaction
            ]);
        }
        return response()->json([
            'siswa' => $siswa,
            'code' => 404,
            'error' => true,
            'message' => 'User not found'
        ], Response::HTTP_NOT_FOUND);
    }


    public function check_du_transaction(Request $request, $nisn)
    {
        $params_query = $request->query();
        $siswa = SiswaModel::find($nisn);
        if ($siswa) {
            if (isset($params_query['paid_off'])) {
                return response()->json([
                    'du_transaction' => $siswa->du_transaction->where('paid_off', $params_query['paid_off']),
                    'code' => Response::HTTP_OK,
                    'error' => false,
                    'message' => 'Success full get du transaction'
                ]);
                return;
            } else if (isset($params_query['year'])) {
                return response()->json([
                    'du_transaction' =>  $siswa[0]->du_transaction->where('study_year_id', $params_query['year']),
                    'code' => Response::HTTP_OK,
                    'error' => false,
                    'message' => 'Success full get du transaction'
                ]);
            }
            return response()->json([
                'du_transaction' => $siswa->du_transaction
            ]);
        }
        return response()->json([
            'siswa' => $siswa,
            'code' => 404,
            'error' => true,
            'message' => 'User not found'
        ], Response::HTTP_NOT_FOUND);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = $this->student->validation_input_siswa($request);
        if ($validation['error']) {
            return response()->json([
                $validation
            ], Response::HTTP_NOT_ACCEPTABLE);
        }
        $regis = SiswaModel::create($request->all());
        return response()->json([
            'user' => $regis,
            'code' => 200,
            'error' => false,
            'message' => 'User has been created'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $userIsExist = SiswaModel::check_user($id);
        $student = SiswaModel::find($id);
        if ($userIsExist) {
            return response()->json([
                'siswa' => $student,
                'code' => 200,
                'error' => false,
                'message' => 'User is successfull'
            ]);
        }

        return response()->json([
            'siswa' => $student,
            'code' => 404,
            'error' => true,
            'message' => 'User not found'
        ], Response::HTTP_NOT_FOUND);
    }

   


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
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
        $userIsExist = SiswaModel::check_user($id);
        $student = SiswaModel::find($id);
        if ($userIsExist) {
            $student->update($request->all());
            return response()->json([
                'siswa' => $student,
                'error' => false,
                'code' => 200,
                'message' => 'Data has been updated'
            ]);
        }
        return response()->json([
            'siswa' => $student,
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
        if (SiswaModel::check_user($id)) {
            $siswa = SiswaModel::find($id);
            $siswa->delete();
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
    }
}
