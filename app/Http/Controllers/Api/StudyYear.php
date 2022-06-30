<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudyYear as ModelsStudyYear;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StudyYear extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $view = ModelsStudyYear::all();
        return response()->json([
            'study_year' => $view,
            'error' => false,
            'code' => Response::HTTP_OK,
            'message' => 'Successfull to retrive data'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = ModelsStudyYear::validation_input_year($request);
        if ($validation['error']) {
            return response()->json([
                $validation,
                'code' => Response::HTTP_NOT_ACCEPTABLE,
                'error' => true,
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        $range_validation = ModelsStudyYear::range_study_year($request->all()['year'], '/');
        $exist = ModelsStudyYear::check_study_year($request->all()['year']);
        if ($exist) {
            return response()->json([
                'code' => Response::HTTP_CONFLICT,
                'error' => true,
                'message' => 'Study year already exist'
            ], Response::HTTP_CONFLICT);
        }

        if ($range_validation) {
            $studyYear = ModelsStudyYear::request_filter_year($request);
            ModelsStudyYear::create($studyYear);
            return response()->json([
                'study_year' => $studyYear,
                'code' => Response::HTTP_CREATED,
                'error' => false,
                'message' => 'Study year has been created'
            ], Response::HTTP_CREATED);
        }

        return response()->json([
            'code' => Response::HTTP_BAD_REQUEST,
            'error' => true,
            'message' => 'Study year error because range year not ussualy'
        ], Response::HTTP_BAD_REQUEST);
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
        $slah_year = ModelsStudyYear::convert_to_slash($id, '-');
        $exist = ModelsStudyYear::check_study_year($slah_year);
        
        if ($exist) {

            $validation = ModelsStudyYear::validation_input_year($request);
            if($validation['error']){
                return $validation;
            }

            $year = ModelsStudyYear::range_study_year($request->all()['year'], '/');
            if ($year) {
                $study_year_model = ModelsStudyYear::find($slah_year);
                $filter_study_year = ModelsStudyYear::request_filter_year($request);
                $study_year_model->update($filter_study_year);
                return response()->json([
                    'study_year' => $study_year_model,
                    'message' => 'Study year successfull to update',
                    'code' => Response::HTTP_OK,
                    'error' => false
                ]);
            }
            return response()->json([
                'error' => true,
                'message' => 'Type writing study year not ussualy rule',
                'code' => Response::HTTP_BAD_REQUEST
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'error' => true,
            'message' => 'Study year not found',
            'code' => Response::HTTP_NOT_FOUND
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
        $year = ModelsStudyYear::convert_to_slash($id, '-');
        $study = ModelsStudyYear::find($year);
        if ($study) {
            $study->delete();
            return response()->json([
                'study_year' => $study,
                'code' => 200,
                'error' => false,
                'message' => 'Study year successfull to deleted'
            ]);
        }
        return response()->json([
            'code' => Response::HTTP_NOT_FOUND,
            'error' => true,
            'message' => 'Study year not found'
        ], Response::HTTP_NOT_FOUND);
    }
}
