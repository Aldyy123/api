<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DUModel;
use App\Models\SiswaModel;
use App\Models\SPPTransaction as ModelsSPPTransaction;
use App\Models\StudyYear;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DUController extends Controller
{

    public function __construct()
    {
        $this->model = new DUModel();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $data = DUModel::with('siswa')->orderBy('created_at', 'desc');
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
            'count' => $count,
            'error' => false,
            'message' => 'Success get DU',
            'code' => Response::HTTP_OK
        ]);
    }

    public function count_du(Request $request)
    {
        try {
            $query = $request->all();
            $data = DUModel::all();

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

    public function paid_du_transaction(Request $request, $nisn, $study_year)
    {
        $userExist = SiswaModel::check_user($nisn);
        if ($userExist) {
            $errors = $this->model->validation_input_du($request);
            if (count($errors) == 0) {

                $year_slash = StudyYear::separate_study_year($study_year);
                $check_paid_off_study_year = DUModel::check_study_year_paid_off($year_slash, $nisn);

                if (!$check_paid_off_study_year) {
                    $data = ModelsSPPTransaction::request_data_collection($request->all(), $nisn, $study_year);
                    return $this->model->transaction_du($data, $errors);
                }

                return response()->json([
                    'message' => 'Maaf, tahun ajaran ini telah lunas',
                    'code' => Response::HTTP_BAD_REQUEST,
                    'error' => true,
                ], Response::HTTP_BAD_REQUEST);
            }
            return $errors;
        }
        return response()->json([
            'message' => 'User tidak ditemukan, mohon untuk cek kembali',
            'code' => Response::HTTP_NOT_FOUND,
            'error' => true,
        ], Response::HTTP_NOT_FOUND);
    }

    public function update_du_transaction(Request $request, $nisn, $year_name, $id_du)
    {
        $userExist = SiswaModel::check_user($nisn);
        $check_study_year = ModelsSPPTransaction::check_study_year($year_name);
        $check_du_transaction = DUModel::check_id_du($id_du);

        if ($userExist) {
            if ($check_study_year) {
                if ($check_du_transaction) {
                    $validation = $this->model->validation_input_du($request);
                    if (count($validation) > 0) {
                        return response()->json($validation, Response::HTTP_BAD_REQUEST);
                    }

                    $year_slash = StudyYear::separate_study_year($year_name);
                    $check_paid_off_study_year = DUModel::check_study_year_paid_off($year_slash, $nisn);

                    if (!$check_paid_off_study_year) {
                        $transaction = DUModel::find($id_du);
                        $data = ModelsSPPTransaction::request_data_collection($request->all(), $nisn, $year_name);
                        $transaction->update($data);
                        return response()->json([
                            'data' => $transaction,
                            'error' => false,
                            'code' => 200,
                            'message' => 'Data telah di update'
                        ]);
                    }

                    return response()->json([
                        'message' => 'Maaf, Tahun ajaran ini sudah lunas',
                        'code' => Response::HTTP_BAD_REQUEST,
                        'error' => true,
                    ], Response::HTTP_BAD_REQUEST);
                }
                return response()->json([
                    'message' => 'Daftar Ulang tidak ditemukan',
                    'code' => 404,
                    'error' => true,
                ], Response::HTTP_NOT_FOUND);
            }
            return response()->json([
                'message' => 'Tahun ajaran Tidak ditemukan',
                'code' => 404,
                'error' => true,
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'message' => 'User tidak ditemukan',
            'code' => 404,
            'error' => true,
        ], Response::HTTP_NOT_FOUND);
    }

    public function delete_du_transaction($nisn, $year_name, $id_du)
    {
        $userExist = SiswaModel::check_user($nisn);
        $check_study_year = ModelsSPPTransaction::check_study_year($year_name);
        $check_du_transaction = DUModel::check_id_du($id_du);

        if ($userExist) {
            if ($check_study_year) {
                if ($check_du_transaction) {
                    $du_transaction = DUModel::find($id_du);
                    $du_transaction->delete();
                    return response()->json([
                        'error' => false,
                        'code' => 200,
                        'message' => 'Data has been Deleted'
                    ]);
                }
                return response()->json([
                    'message' => 'Daftar Ulang tidak ditemukan',
                    'code' => 404,
                    'error' => true,
                ], Response::HTTP_NOT_FOUND);
            }
            return response()->json([
                'message' => 'Tahun ajaran Tidak ditemukan',
                'code' => 404,
                'error' => true,
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'message' => 'User tidak ditemukan',
            'code' => 404,
            'error' => true,
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
        try {
            $transaksi_daftar_ulang = DUModel::find($id);
            if ($transaksi_daftar_ulang) {
                return response()->json([
                    'data' => $transaksi_daftar_ulang,
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
    }
}
