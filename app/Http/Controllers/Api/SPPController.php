<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiswaModel;
use App\Models\SPPTransaction;
use App\Models\StudyYear;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

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
                $check_paid_off_month = SPPTransaction::check_month_paided($nisn, $data['month'], $year_slash);

                if (!$check_paid_off_month) {
                    $data = SPPTransaction::request_data_collection($data, $nisn, $study_year);
                    return $this->model->transaction_spp($data, $errors);
                }

                return response()->json([
                    'message' => 'Sorry, this month is paid off spp',
                    'error' => true,
                    'code' => Response::HTTP_BAD_REQUEST,
                ], Response::HTTP_BAD_REQUEST);
            }
        }
        return response()->json([
            'message' => 'User not found please for input nisn which valid',
            'error' => true,
            'code' => 404,
        ], Response::HTTP_NOT_FOUND);
    }

    public function index(Request $request)
    {
        $query = $request->query();
        $data = SPPTransaction::with('siswa')->orderBy('created_at', 'desc');
        $count = $data->get()->count();

        if (isset($query['limit']) && isset($query['page'])) {
            $data = $data->skip(($query['page'] - 1) * $query['limit'])->take($query['limit']);
        }

        if (isset($query['study_year'])) {
            $data = $data->where('study_year_id', 'LIKE', "%{$query['study_year']}%");
        }
        if (isset($query['nisn'])) {
            $data = $data->where('nisn_siswa', 'LIKE', "%{$query['nisn']}%");
        }

        if (isset($query['paid_off'])) {
            $query['paid_off'] = $query['paid_off'] > 1 ? 1 : $query['paid_off'];
            $query['paid_off'] = $query['paid_off'] < 0 ? 0 : $query['paid_off'];
            $data = $data->where('paid_off', $query['paid_off']);
        }

        if (isset($query['month'])) {
            $data = $data->where('month', 'LIKE', "%{$query['month']}%");
        }

        if (isset($query['id'])) {
            $data = $data->where('id', $query['id']);
        }

        if (isset($query['paid_user'])) {
            $data = $data->where('paid_user', 'LIKE', "%{$query['paid_user']}%");
        }


        return response()->json([
            'data' => $data->get(),
            'error' => false,
            'count' => $count
        ]);
    }


    public function update_spp_transaction(Request $request, $nisn, $year_name, $id_spp)
    {
        $user_exist = SiswaModel::check_user($nisn);
        $study_year_exist = SPPTransaction::check_study_year($year_name);
        $spp_transaction_exist = $this->model->check_id_spp($id_spp);
        if ($user_exist) {
            if ($study_year_exist) {
                if ($spp_transaction_exist) {
                    $validation = $this->model->validation_input_spp($request);
                    if (count($validation) > 0) {
                        return response()->json($validation, Response::HTTP_BAD_REQUEST);
                    }

                    $input = $request->all();
                    $year_slash = StudyYear::separate_study_year($year_name);
                    $check_paid_off_month = SPPTransaction::check_month_paided($nisn, $input['month'], $year_slash);

                    if (!$check_paid_off_month) {
                        $transaction = SPPTransaction::find($id_spp);
                        $data = SPPTransaction::request_data_collection($request->all(), $nisn, $year_name);
                        $transaction->update($data);
                        return response()->json([
                            'data' => $transaction,
                            'error' => false,
                            'code' => 200,
                            'message' => 'Data has been updated'
                        ]);
                    }
                    return response()->json([
                        'message' => 'Maaf, spp bulan tersebut sudah lunas',
                        'code' => Response::HTTP_BAD_REQUEST,
                        'error' => true,
                    ], Response::HTTP_BAD_REQUEST);
                }
                return response()->json([
                    'message' => 'SPP tidak ada',
                    'error' => true,
                    'code' => 404,
                ], Response::HTTP_NOT_FOUND);
            }
            return response()->json([
                'message' => 'Tahun ajaran tidak ada',
                'error' => true,
                'code' => 404,
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'message' => 'User not found please for input nisn which valid',
            'error' => true,
            'code' => 404,
        ], Response::HTTP_NOT_FOUND);
    }

    public function delete_spp_transaction($nisn, $year_name, $id_spp)
    {
        $user_exist = SiswaModel::check_user($nisn);
        $study_year_exist = SPPTransaction::check_study_year($year_name);
        $spp_transaction = $this->model->check_id_spp($id_spp);
        if ($user_exist) {
            if ($study_year_exist) {
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
                    'error' => true
                ], Response::HTTP_NOT_FOUND);
            }
            return response()->json([
                'message' => 'Study Year not found',
                'code' => 404,
                'error' => true
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'message' => 'User not found please for input nisn which valid',
            'code' => 404,
            'error' => true

        ], Response::HTTP_NOT_FOUND);
    }

    public function count_spp(Request $request)
    {
        try {
            $query = $request->query();
            $data = SPPTransaction::all();

            if (isset($query['paid_off'])) {
                $query['paid_off'] = $query['paid_off'] > 1 ? 1 : $query['paid_off'];
                $query['paid_off'] = $query['paid_off'] < 0 ? 0 : $query['paid_off'];
                $data = $data->where('paid_off', $query['paid_off']);
            }

            return response()->json([
                'count' => $data->count(),
                'error' => false,
                'message' => 'Success count',
                'code' => Response::HTTP_OK
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'message' => $th->getMessage(),
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function show($id)
    {
        try {
            $spp = SPPTransaction::find($id);
            if ($spp) {
                return response()->json([
                    'data' => $spp,
                    'code' => Response::HTTP_OK,
                    'message' => 'Transaksi berhasil ditemukan',
                    'error' => false
                ]);
            }
            return response()->json([
                'code' => Response::HTTP_NOT_FOUND,
                'error' => true,
                'message' => 'Transaksi tidak ditemukan',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'error' => true,
                'message' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
