<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with('user')
            ->orderBy('meeting_date', 'desc')
            ->paginate(15);

        return view('admin.attendances.index', compact('attendances'));
    }

    public function create()
    {
        $users = User::where('role', 'member')->where('status', true)->get();
        return view('admin.attendances.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'meeting_date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.user_id' => 'required|exists:users,id',
            'attendances.*.status' => 'required|in:present,absent,excused',
            'attendances.*.notes' => 'nullable|string'
        ]);

        foreach ($validated['attendances'] as $data) {
            Attendance::updateOrCreate(
                [
                    'user_id' => $data['user_id'],
                    'meeting_date' => $validated['meeting_date']
                ],
                [
                    'status' => $data['status'],
                    'notes' => $data['notes'] ?? null
                ]
            );
        }

        return redirect()->route('attendances.index')
            ->with('success', 'Presenças registradas com sucesso!');
    }
}
