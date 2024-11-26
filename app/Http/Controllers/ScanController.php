<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Participant;
use App\Models\Scan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ScanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Scan::all();

        return response()->json([
            'status' => 'success',
            'message' => 'oke',
            'data' => $data,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'validation errors',
                'errors' => $validator->errors(),
            ], 400);
        }

        $scan = new Scan();
        $scan->title = $request->title;
        $result = $scan->save();

        if ($result) {
            return response()->json([
                'status' => 'success',
                'message' => 'save data success',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'save data failed',
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Scan::find($id);

        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'data not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'oke',
            'data' => $data,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $scan = Scan::find($id);

        if (!$scan) {
            return response()->json([
                'status' => 'error',
                'message' => 'data not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'validation errors',
                'errors' => $validator->errors(),
            ], 400);
        }

        $scan->title = $request->title;
        $result = $scan->save();

        if ($result) {
            return response()->json([
                'status' => 'success',
                'message' => 'update data success',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'update data failed',
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $scan = Scan::find($id);

        if (!$scan) {
            return response()->json([
                'status' => 'error',
                'message' => 'data not found',
            ], 404);
        }

        $result = $scan->delete();

        if ($result) {
            return response()->json([
                'status' => 'success',
                'message' => 'delete data success',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'delete data failed',
            ], 400);
        }
    }

    /**
     * Scan qr code.
     */
    public function scan_qr(Request $request)
    {
        $request->validate([
            'id_scan' => 'required',
            'qr_content' => 'required',
        ]);

        $user = Auth::user();

        $is_id_scan = Scan::where('id', $request->id_scan)->first();

        if (!$is_id_scan) {
            return response()->json([
                'status' => 'error',
                'message' => 'id scan not found',
                'errors' => [
                    'id_scan' => 'Not found',
                ],
            ], 400);
        }

        $is_participant = Participant::where('qr_content', $request->qr_content)->first();

        if (!$is_participant) {
            return response()->json([
                'status' => 'error',
                'message' => 'id participant not found',
                'errors' => [
                    'qr_content' => 'Not found',
                ],
            ], 400);
        }

        $today = now()->startOfDay();

        $alreadyScan = Attendance::where('participant_id', $is_participant->id)
            ->where('id_scan', $is_id_scan->id)
            ->whereDate('scan_at', $today)
            ->first();

        if ($alreadyScan) {
            return response()->json([
                'status' => 'ok',
                'message' => 'anda sudah scan hari ini',
            ]);
        }

        $attendance = new Attendance();
        $attendance->participant_id = $is_participant->id;
        $attendance->id_scan = $is_id_scan->id;
        $attendance->scan_at = now();
        $attendance->scan_by = $user->id;

        $attendance->save();

        if ($attendance) {
            return response()->json([
                'status' => 'success',
                'message' => $is_id_scan->title . ' - ' . $request->qr_content . ' Success',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'error when saving data',
            ], 422);
        }
    }
}

