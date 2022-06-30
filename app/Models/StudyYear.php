<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class StudyYear extends Model
{
    use HasFactory;
    protected $table = 'study_year';
    protected $primaryKey = 'study_year';
    protected $fillable = [
        'study_year',
        'year',
        'active'
    ];

    public static function validation_input_year($request)
    {
        $messages = [
            'required' => 'the :attribute field is required',
            'max' => 'the :attribute fields is :max',
            'min' => 'the :attribute fields is :min',
        ];
        $validator = Validator::make($request->all(), [
            'year' => 'required|max:10|min:9',
        ], $messages);
        
        return self::message_validation($validator);
    }

    public static function message_validation($validator){
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

    public static function request_filter_year($request){
        $data = $request->all();
        $data['study_year'] = $data['year'];
        return $data;
    }

    public static function range_study_year($year_study, $separator){
        $year = explode($separator, $year_study);
        $range = $year[0] + 1;
        return $range == $year[1];
    }

    public static function check_study_year($year_study){
        $year = self::find($year_study);
        if($year){
            return true;
        }
        return false;
    }

    public static function convert_to_slash($text, $separator){
        $year = explode($separator, $text);
        $year = implode('/', $year);
        return $year;
    }

}
