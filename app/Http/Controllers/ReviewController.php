<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class ReviewController extends Controller
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
        $rules = [
            "user_id" => "required|integer",
            "course_id" => "required|integer",
            "rate" => "required|integer|min:1|max:5",
            "note" => "string"
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

        $user = $this->getUser($request->input('user_id'));
        if ($user['status'] == "error") {
            return response()->json([
                'status' => $user['status'],
                'message' => $user['message']
            ], ($user['http_code']) ? $user['http_code'] : 404);
        }

        $isExist = Review::where('course_id', $request->input('course_id'))
            ->where('user_id', $request->input('user_id'))->get();

        if ($isExist->count() > 0) {
            return response()->json([
                "status" => "error",
                "message" => "user already review this course"
            ], 409);
        }

        $review = Review::create($data);

        return response()->json([
            "status" => "success",
            "data" => $review
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function show(Review $review)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function edit(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Review $review)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function destroy(Review $review)
    {
        //
    }
}
