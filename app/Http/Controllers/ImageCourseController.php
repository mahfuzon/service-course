<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ImageCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImageCourseController extends Controller
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
            "image" => "required|url",
            "course_id" => 'required|integer'
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => "error",
                "message" => $validator->errors()
            ], 400);
        }

        $course = Course::find($request->input('course_id'));
        if (!$course) {
            return response()->json([
                "status" => "error",
                "message" => "course not found"
            ], 404);
        }

        $imageCourse = ImageCourse::create($data);

        return response()->json([
            "status" => "success",
            "data" => $imageCourse
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ImageCourse  $imageCourse
     * @return \Illuminate\Http\Response
     */
    public function show(ImageCourse $imageCourse)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ImageCourse  $imageCourse
     * @return \Illuminate\Http\Response
     */
    public function edit(ImageCourse $imageCourse)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ImageCourse  $imageCourse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ImageCourse $imageCourse)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ImageCourse  $imageCourse
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $imageCourse = ImageCourse::find($id);

        if (!$imageCourse) {
            return response()->json([
                "status" => "error",
                "message" => "image not found"
            ], 404);
        }

        $imageCourse->delete();
        return response()->json([
            "status" => "success",
            "message" => "image deleted"
        ]);
    }
}
