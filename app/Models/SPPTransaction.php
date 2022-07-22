<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SPPTransaction extends Model
{
    use HasFactory;
    protected $table = 'spp_transaction';
    protected $fillable = [
        'paid_user',
        'nisn_siswa',
        'price',
        'month',
        'paid_off',
        'remain_payment',
        'updated_at',
        'created_at',
        'study_year_id'
    ];

    public function siswa()
    {
        return $this->belongsTo(SiswaModel::class, 'nisn_siswa', 'nisn');
    }

    public function study()
    {
        return $this->belongsTo(StudyYear::class, 'study_year_id', 'study_year');
    }

    public static function check_month_paided($nisn, $month, $study_year){
        $spp = self::where('month', $month)
        ->where('study_year_id', $study_year)
        ->where('nisn_siswa', $nisn)
        ->where('paid_off', 1)
        ->first();
        if($spp){
            return true;
        }

        return false;
    }
    


    public static function request_data_collection($data, $id, $study_year){
        $year = StudyYear::separate_study_year($study_year);

        $data['nisn_siswa'] = $id;
        $data['study_year_id'] = $year;
        $data['remain_payment'] = $data['paid_user'] - $data['price'];
        if ($data['remain_payment'] >= 0) {
            $data['paid_off'] = true;
        }else{
            $data['paid_off'] = false;
        }

        return $data;
    }

    public function transaction_spp($data, $errors)
    {
        try {
            return response()->json([
                'data' => self::create($data),
                'code' => 200,
                'message' => 'Transaction has been success for created',
                'error' => false
            ]);
        } catch (QueryException $th) {
            if ($th->getCode() === '23000') {
                return response()->json([
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => 'Maaf, input tahun ajaran tidak ada',
                    'error' => true
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }elseif($th->getCode() === '01000'){
                return response()->json([
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => 'Error bulan yang kamu masukan tidak ada',
                    'error' => true
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return response()->json($errors, Response::HTTP_BAD_REQUEST);
        }
    }

    public function check_id_spp($id_spp)
    {
        $transaction_spp = DB::table('spp_transaction')->where('id', '=', $id_spp)->get();
        if (count($transaction_spp)) {
            return true;
        } else {
            return false;
        }
    }

    

    public static function check_study_year($study_year_name)
    {
       $year = StudyYear::separate_study_year($study_year_name);
        $study_year = DB::table('study_year')->where('year', '=', $year)->get();
        if (count($study_year)) {
            return true;
        } else {
            return false;
        }
    }

    public function validation_input_spp($request)
    {
        $messages = [
            'required' => 'the :attribute field is required',
            'max' => 'the :attribute fields is :max',
            'min' => 'the :attribute fields is :min',
        ];
        $validator = Validator::make($request->all(), [
            'price' => 'required|max:30|min:1',
            'paid_user' => 'required|max:30|min:1',
            'month' => 'required'
        ], $messages);

        if ($validator->fails()) {
            return [
                'message' => $validator->errors(),
                'code' => 400,
                'error' => true
            ];
        }
        return $validator->errors();
    }
}
