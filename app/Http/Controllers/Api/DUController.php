<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DUModel;
use App\Models\SiswaModel;
use App\Models\SPPTransaction as ModelsSPPTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use SppTransaction;

class DUController extends Controller
{

    public function __construct() {
        $this->model = new DUModel();
    }

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
        //
    }

    public function paid_du_transaction(Request $request, $nisn, $study_year){
        $userExist = SiswaModel::check_user($nisn);
        if ($userExist) {
            $errors = $this->model->validation_input_du($request);
            if (count($errors) == 0) {
                $data = ModelsSPPTransaction::request_data_collection($request->all(), $nisn, $study_year);
                return $this->model->transaction_du($data, $errors);
            }
            return $errors;
        }
        return response()->json([
            'message' => 'User not found please for input nisn which valid',
            'code' => 404,
        ], Response::HTTP_NOT_FOUND);
    }

    public function update_du_transaction(Request $request, $nisn, $year_name, $id_du)
    {
        $user = SiswaModel::check_user($nisn);
        $study_year = ModelsSPPTransaction::check_study_year($year_name);
        $spp_transaction = DUModel::check_id_du($id_du);
        if ($user) {
            if ($study_year) {
                if ($spp_transaction) {
                    $validation = $this->model->validation_input_du($request);
                    if (count($validation) > 0) {
                        return response()->json($validation, Response::HTTP_BAD_REQUEST);
                    }
                    $transaction = DUModel::find($id_du);
                    $data = ModelsSPPTransaction::request_data_collection($request->all(), $nisn, $year_name);
                    $transaction->update($data);
                    return response()->json([
                        'transaction' => $transaction,
                        'error' => false,
                        'code' => 200,
                        'message' => 'Data has been updated'
                    ]);
                }
                return response()->json([
                    'message' => 'DU not found',
                    'code' => 404,
                ], Response::HTTP_NOT_FOUND);
            }
            return response()->json([
                'message' => 'Study Year not found',
                'code' => 404,
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'message' => 'User not found please for input nisn which valid',
            'code' => 404,
        ], Response::HTTP_NOT_FOUND);
    }

    public function delete_du_transaction($nisn, $year_name, $id_du)
    {
        $user = SiswaModel::check_user($nisn);
        $study_year = ModelsSPPTransaction::check_study_year($year_name);
        $spp_transaction = DUModel::check_id_du($id_du);
        if ($user) {
            if ($study_year) {
                if ($spp_transaction) {
                    $spp_transaction = DUModel::find($id_du);
                    $spp_transaction->delete();
                    return response()->json([
                        'error' => false,
                        'code' => 200,
                        'message' => 'Data has been Deleted'
                    ]);
                }
                return response()->json([
                    'message' => 'Du not found',
                    'code' => 404,
                ], Response::HTTP_NOT_FOUND);
            }
            return response()->json([
                'message' => 'Study Year not found',
                'code' => 404,
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'message' => 'User not found please for input nisn which valid',
            'code' => 404,
        ], Response::HTTP_NOT_FOUND);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
