<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;

class SiswaModel extends Model
{
    use HasFactory, HasApiTokens;
    protected $table = 'siswa';
    protected $primaryKey = 'nisn';


    protected $fillable = [
        'nisn',
        'date_birth',
        'parent_name',
        'start_year',
        'name_student',
        'end_year',
        'study_year_id'
    ];

    public function spp_transaction()
    {
        return $this->hasMany(SPPTransaction::class, 'nisn_siswa', 'nisn');
    }
    
    public function study_year()
    {
        return $this->belongsTo(StudyYear::class, 'study_year_id', 'study_year');
    }
    public function du_transaction(){
        return $this->hasMany(DUModel::class, 'nisn_siswa', 'nisn');
    }

    public function validation_input_siswa($request)
    {
        $messages = [
            'required' => 'the :attribute field is required',
            'max' => 'the :attribute fields is :max',
            'min' => 'the :attribute fields is :min',
            'unique' => 'the :attribute mush fields is unique'
        ];
        $validator = Validator::make($request->all(), [
            'nisn' => 'required|min:1|unique:siswa',
            'date_birth' => 'required',
            'parent_name' => 'required',
            'start_year' => 'required|max:10',
            'name_student' => 'required',
            'study_year_id' => 'required'
        ], $messages);
        
        return $this->message_validation($validator);
    }

    public function validation_edit_siswa($request){
        $messages = [
            'required' => 'the :attribute field is required',
            'max' => 'the :attribute fields is :max',
            'min' => 'the :attribute fields is :min',
            'unique' => 'the :attribute mush fields is unique'
        ];
        $validator = Validator::make($request->all(), [
            'nisn' => 'present|min:1|unique:siswa',
            'date_birth' => 'present',
            'parent_name' => 'present',
            'start_year' => 'present|max:10',
            'name_student' => 'present',
            'study_year_id' => 'present'
        ], $messages);
        
        return $this->message_validation($validator);
    }

    public function message_validation($validator){
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

    public static function check_user($nisn)
    {
        $user = DB::table('siswa')->where('nisn', '=', $nisn)->get();
        if (count($user)) {
            return true;
        } else {
            return false;
        }
    }
}
