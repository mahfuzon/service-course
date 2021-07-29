<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChapterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $chapters = Chapter::query();
        $course_id = $request->query('course_id');

        $chapters->when($course_id, function ($query, $course_id) {
            return $query->where('course_id', $course_id);
        });

        return response()->json([
            "status" => "success",
            "data" => $chapters->get()
        ]);
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
        $rules = [
            "name" => "required|string",
            "course_id" => "required|integer"
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
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

        $chapter = Chapter::create($data);

        return response()->json([
            "status" => "success",
            "data" => $chapter
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Chapter  $chapter
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $chapter = Chapter::find($id);
        if (!$chapter) {
            return response()->json([
                "status" => "error",
                "message" => "chapter not found"
            ], 404);
        }

        return response()->json([
            "status" => "success",
            "data" => $chapter
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Chapter  $chapter
     * @return \Illuminate\Http\Response
     */
    public function edit(Chapter $chapter)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Chapter  $chapter
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            "name" => "string",
            "chapter_id" => "integer"
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "message" => $validator->errors()
            ], 400);
        }

        $chapter = Chapter::find($id);
        if (!$chapter) {
            return response()->json([
                "status" => "error",
                "message" => "chapter not found"
            ], 404);
        }

        $course_id = $request->input('course_id');
        if ($course_id) {
            $course = Course::find($course_id);
            if (!$course) {
                return response()->json([
                    "status" => "error",
                    "message" => "course not found"
                ], 404);
            }
        }

        $chapter->update($data);

        return response()->json([
            "status" => "success",
            "data" => $chapter
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Chapter  $chapter
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $chapter = Chapter::find($id);
        if (!$chapter) {
            return response()->json([
                "status" => "error",
                "message" => "chapter not found"
            ], 404);
        }

        $chapter->delete();
        return response()->json([
            "status" => "success",
            "message" => "course deleted"
        ]);
    }
}
