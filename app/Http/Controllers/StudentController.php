<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::all();
        return view('home', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'subject' => 'required',
            'marks' => 'required|integer',
        ]);
    
        $existingStudent = Student::where('name', $request->name)
            ->where('subject', $request->subject)
            ->first();
    
        if ($existingStudent) {
            // Update marks for existing student
            $existingStudent->marks += $request->marks;
            $existingStudent->save();
        } else {
            // Create new student record
            Student::create([
                'name' => $request->name,
                'subject' => $request->subject,
                'marks' => $request->marks,
            ]);
        }
    
        return redirect()->route('home');
    }
    
    public function checkDuplicate(Request $request)
    {
        $name = $request->input('name');
        $subject = $request->input('subject');

        // Check if a student with the same name and subject exists
        $existingStudent = Student::where('name', $name)
                                  ->where('subject', $subject)
                                  ->exists();

        return response()->json(['exists' => $existingStudent]);
    }
    public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required',
        'subject' => 'required',
        'marks' => 'required|integer',
    ]);

    $student = Student::findOrFail($id);
    $student->update([
        'name' => $request->name,
        'subject' => $request->subject,
        'marks' => $request->marks,
    ]);

    return redirect()->route('home');
}
    public function destroy($id)
    {
        Student::findOrFail($id)->delete();
        return redirect()->route('home');
    }
}
