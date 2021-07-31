<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\returnSelf;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $lessons = Lesson::query();
        $chapter_id = $request->query('chapter_id');

        $lessons->when($chapter_id, function ($query, $chapter_id) {
            return $query->where('chapter_id', $chapter_id);
        });

        return response()->json([
            "status" => "success",
            "data" => $lessons->get()
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
            "video" => "required|string",
            "chapter_id" => "required|integer"
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "message" => $validator->errors()
            ], 400);
        }

        $chapter = Chapter::find($request->input('chapter_id'));
        if (!$chapter) {
            return response()->json([
                "status" => "error",
                "message" => "chapter not found"
            ], 404);
        }

        $lesson = Lesson::create($data);

        return response()->json([
            "status" => "success",
            "data" => $lesson
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Lesson  $lesson
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return response()->json([
                "status" => "error",
                "message" => "lesson not found"
            ], 404);
        }

        return response()->json([
            "status" => "success",
            "data" => $lesson
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Lesson  $lesson
     * @return \Illuminate\Http\Response
     */
    public function edit(Lesson $lesson)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Lesson  $lesson
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            "name" => "string",
            "video" => "string",
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

        $lesson = Lesson::find($id);
        if (!$lesson) {
            return response()->json([
                "status" => "error",
                "message" => "lesson not found"
            ], 404);
        }

        if ($request->has('chapter_id')) {
            $chapter = Chapter::find($request->input('chapter_id'));
            if (!$chapter) {
                return response()->json([
                    "status" => "error",
                    "message" => "chapter not found"
                ], 404);
            }
        }

        $lesson->update($data);
        return response()->json([
            "status" => "success",
            "data" => $lesson
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Lesson  $lesson
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return response()->json([
                "status" => "error",
                "message" => "lesson not found"
            ], 404);
        }

        $lesson->delete();

        return response()->json([
            "status" => "success",
            "message" => "lesson deleted"
        ], 404);
    }
}
