<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
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
        $data = $request->all();

        $rules = [
            "name" => "required|string",
            "certificate" => "required|boolean",
            "thumbnail" => "string|url",
            "type" => "required|in:free,premium",
            "status" => "required|in:draft,published",
            "price" => "integer",
            "level" => "required|in:all-level,beginner,intermediate,advance",
            "mentor_id" => "required|integer",
            "description" => "string"
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "message" => $validator->errors()
            ]);
        }

        $mentor = Mentor::find($request->mentor_id);
        if (!$mentor) {
            return response()->json([
                "status" => "error",
                "message" => "mentor not found"
            ]);
        }

        $course = Course::create($data);

        return response()->json([
            "status" => "success",
            "data" => $course
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function show(Course $course)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function edit(Course $course)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();

        $rules = [
            "name" => "string",
            "certificate" => "boolean",
            "thumbnail" => "string|url",
            "type" => "in:free,premium",
            "status" => "in:draft,published",
            "price" => "integer",
            "level" => "in:all-level,beginner,intermediate,advance",
            "mentor_id" => "integer",
            "description" => "string"
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "message" => $validator->errors()
            ], 400);
        }

        $course = Course::find($id);
        if (!$course) {
            return response()->json([
                "status" => "error",
                "message" => "course not found"
            ], 404);
        }

        $mentor_id = $request->mentor_id;
        if ($mentor_id) {
            $mentor = Mentor::find($mentor_id);
            if (!$mentor) {
                return response()->json([
                    "status" => "error",
                    "message" => "mentor not found"
                ], 404);
            }
        }


        $course->update($data);

        return response()->json([
            "status" => "success",
            "data" => $course
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function destroy(Course $course)
    {
        //
    }
}
