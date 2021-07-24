<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Chapter;
use App\Models\Mentor;
use App\Models\MyCourse;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class CourseController extends Controller
{
    public function getUsers($user_ids = [])
    {
        try {
            if (count($user_ids) === 0) {
                return response()->json([
                    "status" => "success",
                    'http_code' => 200,
                    "data" => []
                ]);
            }

            $response = Http::timeout(10)->get('http://localhost:5000/users/', [
                'user_id[]' => $user_ids
            ]);
            $data = $response->json();
            $data['http_code'] = $response->status();
            return $data;
        } catch (\Throwable $th) {
            return [
                "status" => "error",
                "status_code" => 500,
                "message" => "service unavailable"
            ];
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $courses = Course::query();

        $name = $request->query('name');
        $status = $request->query('status');

        $courses->when($name, function ($query) use ($name) {
            return $query->whereRaw("name LIKE '%" . strtolower($name) . "%'");
        });

        $courses->when($status, function ($query) use ($status) {
            return $query->where("status", '=' . $status);
        });


        return response([
            "status" => "success",
            "data" => $courses->paginate(10)
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
    public function show($id)
    {
        $course = Course::with('chapter')
            ->with('chapter')
            ->with('mentor')
            ->with('image')
            ->with('chapter.lesson')
            ->find($id);
        if (!$course) {
            return response()->json([
                "status" => "error",
                "message" => "course not found"
            ], 404);
        }

        $total_student = MyCourse::where('course_id', $id)->count();
        $reviews = Review::where('course_id', $id)->get()->toArray();
        if (count($reviews) > 0) {
            $user_ids = array_column($reviews, 'user_id');
            $users = $this->getUsers($user_ids);
            if ($users['status'] == 'error') {
                $reviews = [];
            } else {
                foreach ($reviews as $key => $review) {
                    $userIndex = array_search($review['user_id'], array_column($users['data'], 'id'));
                    $reviews[$key]['users'] = $users['data'][$userIndex];
                }
            }
        }

        $totalVideos = Chapter::where('course_id', $id)->withCount('lesson')->get()->toArray();
        $finalTotalVideos = array_sum(array_column($totalVideos, 'lesson_count'));
        $course['total_student'] = $total_student;
        $course['reviews'] = $reviews;
        $course['tottal_videos'] = $finalTotalVideos;

        return response()->json([
            'status' => "success",
            "data" => $course
        ]);
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
    public function destroy($id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                "status" => "error",
                "message" => "course not found"
            ], 404);
        }

        $course->delete();
        return response()->json([
            "status" => "success",
            "message" => "course deleted"
        ]);
    }
}
