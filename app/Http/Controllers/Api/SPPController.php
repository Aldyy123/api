<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiswaModel;
use App\Models\SPPTransaction;
use App\Models\StudyYear;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SPPController extends Controller
{
    public function __construct()
    {
        $this->model = new SPPTransaction();
    }
    //

    /*
     * Create transaction SPP
     * 
     * @return \Illuminate\Http\Response
    */

    public function paid_spp_transaction(Request $request, $nisn, $study_year)
    {
        $userExist = SiswaModel::check_user($nisn);
        if ($userExist) {
            $errors = $this->model->validation_input_spp($request);
            if (count($errors) == 0) {
                $data = $request->all();
                $year_slash = StudyYear::separate_study_year($study_year);
                $check_paid_off_month = SPPTransaction::check_month_paided($data['month'], $year_slash);

                if (!$check_paid_off_month) {
                    $data = SPPTransaction::request_data_collection($data, $nisn, $study_year);
                    return $this->model->transaction_spp($data, $errors);
                }

                return response()->json([
                    'message' => 'Sorry, this month is paid off spp',
                    'code' => Response::HTTP_BAD_REQUEST,
                ], Response::HTTP_BAD_REQUEST);
            }
        }
        return response()->json([
            'message' => 'User not found please for input nisn which valid',
            'code' => 404,
        ], Response::HTTP_NOT_FOUND);
    }


    public function update_spp_transaction(Request $request, $nisn, $year_name, $id_spp)
    {
        $user = SiswaModel::check_user($nisn);
        $study_year = SPPTransaction::check_study_year($year_name);
        $spp_transaction = $this->model->check_id_spp($id_spp);
        if ($user) {
            if ($study_year) {
                if ($spp_transaction) {
                    $validation = $this->model->validation_input_spp($request);
                    if (count($validation) > 0) {
                        return response()->json($validation, Response::HTTP_BAD_REQUEST);
                    }

                    $input = $request->all();
                    $year_slash = StudyYear::separate_study_year($year_name);
                    $check_paid_off_month = SPPTransaction::check_month_paided($input['month'], $year_slash);

                    if (!$check_paid_off_month) {
                        $transaction = SPPTransaction::find($id_spp);
                        $data = SPPTransaction::request_data_collection($request->all(), $nisn, $year_name);
                        $transaction->update($data);
                        return response()->json([
                            'transaction' => $transaction,
                            'error' => false,
                            'code' => 200,
                            'message' => 'Data has been updated'
                        ]);
                    }
                    return response()->json([
                        'message' => 'Sorry, this month is paid off spp',
                        'code' => Response::HTTP_BAD_REQUEST,
                    ], Response::HTTP_BAD_REQUEST);
                }
                return response()->json([
                    'message' => 'Spp not found',
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

    public function delete_spp_transaction($nisn, $year_name, $id_spp)
    {
        $user = SiswaModel::check_user($nisn);
        $study_year = SPPTransaction::check_study_year($year_name);
        $spp_transaction = $this->model->check_id_spp($id_spp);
        if ($user) {
            if ($study_year) {
                if ($spp_transaction) {
                    $spp_transaction = SPPTransaction::find($id_spp);
                    $spp_transaction->delete();
                    return response()->json([
                        'error' => false,
                        'code' => 200,
                        'message' => 'Data has been Deleted'
                    ]);
                }
                return response()->json([
                    'message' => 'Spp not found',
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
}
