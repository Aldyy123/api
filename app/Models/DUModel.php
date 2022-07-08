<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class DUModel extends Model
{
    use HasFactory;
    protected $table = 'du_transaction';
    protected $fillable = [
        'price',
        'study_year_id',
        'nisn_siswa',
        'remain_payment',
        'paid_off',
        'paid_user',
    ];

    public function siswa(){
        return $this->belongsTo(SiswaModel::class, 'nisn_siswa', 'nisn');
    }

    public static function check_study_year_paid_off($study_year){
        $du = self::where('study_year_id', $study_year)
        ->where('paid_off', 1)->first();

        if($du){
            return true;
        }
        return false;
    }

    public function validation_input_du($request){
        $messages = [
            'required' => 'the :attribute field is required',
            'max' => 'the :attribute fields is :max',
            'min' => 'the :attribute fields is :min',
        ];
        $validator = Validator::make($request->all(), [
            'price' => 'required|max:30|min:1',
            'paid_user' => 'required|max:30|min:1',
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

    public function transaction_du($data, $errors)
    {
        try {
            return response()->json([
                'du_transaction' => self::create($data),
                'code' => 200,
                'message' => 'Transaction has been success for created',
                'error' => false
            ]);
        } catch (QueryException $th) {
            if ($th->getCode() === '23000') {
                return response()->json([
                    'code' => 500,
                    'message' => 'Error query year is a wrong',
                    'error' => false
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return response()->json($errors, Response::HTTP_BAD_REQUEST);
        }
    }

    public static function check_id_du($id){
        $du = self::find($id);
        if($du){
            return true;
        }
        return false;
    }
}
