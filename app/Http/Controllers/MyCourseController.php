<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\MyCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class MyCourseController extends Controller
{
    // mendapatkan data user dari service user
    public function getUser($user_id)
    {
        // $url = env('SERVICE_USER_URL') . 'users/' . $user_id;
        try {
            $response = Http::timeout(10)->get('http://localhost:5000/users/' . $user_id);
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

    public function getUsers($user_id = [])
    {
        try {
            if (count($user_id) == 0) {
                return response()->json([
                    "status" => "success",
                    'http_code' => 200,
                    "data" => []
                ]);
            }

            $response = Http::timeout(10)->get('http://localhost:5000/users/' . $user_id, [
                'user_id[]' => $user_id
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
        $myCourse = MyCourse::query()->with('course');
        $userId = $request->query('user_id');

        $myCourse->when($userId, function ($query, $userId) {
            return $query->where('user_id', $userId);
        });

        return response()->json([
            "status" => "success",
            "data" => $myCourse->get()
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
            'course_id' => "required|integer",
            'user_id' => "required|integer"
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "message" => $validator->errors()
            ], 400);
        }

        $course_id = $request->input('course_id');
        $course = Course::find($course_id);
        if (!$course) {
            return response()->json([
                "status" => "error",
                "message" => "course not found"
            ], 404);
        }

        $user_id = $request->input('user_id');
        $user = $this->getUser($request->input('user_id'));
        if ($user['status'] == "error") {
            return response()->json([
                'status' => $user['status'],
                'message' => $user['message']
            ], 404);
        }


        $isExist = MyCourse::where('course_id', $course_id)
            ->where('user_id', $user_id)->get();

        if ($isExist->count() > 0) {
            return response()->json([
                "status" => "error",
                "message" => "user already take this course"
            ], 409);
        }

        if ($course->type === 'premium') {
            if ($course->price === 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Price can\'t be 0'
                ]);
            }
            $order = postOrder([
                'user' => $user['data'],
                'course' => $course->toArray()
            ]);

            if ($order['status'] === 'error') {
                return  response()->json([
                    'status' => $order['status'],
                    'message' => $order['message'],
                ], $order['http_code']);
            }

            return response()->json([
                'status' => $order['status'],
                'data' => $order['data']
            ]);
        } else {
            $myCourse = MyCourse::create($data);

            return response()->json([
                "status" => "success",
                "data" => $myCourse
            ]);
        }
    }

    public function createPremiumAccess(Request $request)
    {
        $data = $request->all();
        $myCourse = MyCourse::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $myCourse
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MyCourse  $myCourse
     * @return \Illuminate\Http\Response
     */
    public function show(MyCourse $myCourse)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MyCourse  $myCourse
     * @return \Illuminate\Http\Response
     */
    public function edit(MyCourse $myCourse)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MyCourse  $myCourse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MyCourse $myCourse)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MyCourse  $myCourse
     * @return \Illuminate\Http\Response
     */
    public function destroy(MyCourse $myCourse)
    {
        //
    }
}
