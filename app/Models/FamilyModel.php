<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class FamilyModel extends Model
{
    use HasFactory;
    protected $table = 'data_family';
    protected $primaryKey = 'id';
    protected $fillable = [
        'father', 'mother', 'address',
        'kecamatan', 'kelurahan', 'dusun',
        'phone', 'nisn_siswa'
    ];

    public function siswa()
    {
        return $this->belongsTo(SiswaModel::class, 'nisn_siswa', 'nisn');
    }

    public static function newFamily($data)
    {
        try {
            $family = self::create($data);
            return response()->json([
                'data' => $family,
                'code' => Response::HTTP_OK,
                'message' => 'Data keluarga berhasil diinput',
                'error' => false
            ]);
        } catch (\Throwable $th) {
            if ($th->getCode() == 23000) {
                return response()->json([
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => 'Maaf, biodata sudah diinput',
                    'error' => true
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return response()->json([
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $th->getMessage(),
                'error' => true
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function validation_family($request)
    {
        $messages = [
            'required' => 'the :attribute field is required',
            'max' => 'the :attribute fields is :max',
            'min' => 'the :attribute fields is :min',
            'unique' => 'the :attribute mush fields is unique',
            'present' => 'the :attribute must be present'
        ];
        $validator = Validator::make($request, [
            'father' => 'present',
            'mother' => 'present',
            'phone' => 'present|max:14',
            'address' => 'present',
            'kelurahan' => 'present',
            'kecamatan' => 'present',
            'rt' => 'present',
            'rw' => 'present',
        ], $messages);

        return $this->message_validation($validator);
    }

    public function message_validation($validator)
    {
        if ($validator->fails()) {
            return [
                'message' => $validator->errors(),
                'code' => 400,
                'error' => true
            ];
        }
        return [
            'message' => $validator->errors(),
            'code' => 200,
            'error' => false
        ];
    }

    public static function check_family($id)
    {
        try {
            $family = self::find($id);

            if ($family) {
                return true;
            }
            return false;
        } catch (\Throwable $th) {
            return response()->json([
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $th->getMessage(),
                'error' => true
            ]);
        }
    }
}
