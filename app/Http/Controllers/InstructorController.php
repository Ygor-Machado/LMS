<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class InstructorController extends Controller
{
    public function InstructorDashboard()
    {
        return view('instructor.index');
    }

    public function InstructorLogout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/instructor/login');
    }

    public function InstructorLogin()
    {
        return view('instructor.instructor_login');
    }

    public function InstructorProfile()
    {
        $id = Auth::user()->id;
        $profileData = User::find($id);

        return view('instructor.instructor_profile_view', compact('profileData'));
    }

    public function InstructorProfileStore(Request $request)
    {
        $id = Auth::user()->id;
        $profileData = User::find($id);
        $profileData->name = $request->name;
        $profileData->username = $request->username;
        $profileData->email = $request->email;
        $profileData->phone = $request->phone;
        $profileData->address = $request->address;

        if ($request->file('photo')) {
            $file = $request->file('photo');
            @unlink(public_path('upload/instructor_images/'.$profileData->photo));
            $filename = date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/instructor_images'), $filename);
            $profileData['photo'] = $filename;
        }

        $profileData->save();

        $notification = array(
            'message' => 'Admin Profile Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function InstructorChangePassword()
    {
        $id = Auth::user()->id;
        $profileData = User::find($id);

        return view('instructor.instructor_change_password', compact('profileData'));
    }

    public function instructorPasswordUpdate(AdminUpdateRequest $request)
    {

        if (!Hash::check($request->old_password, Auth::user()->password)) {
            $notification = array(
                'message' => 'Old Password Does Not Match',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }

        User::whereId(Auth::user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        $notification = array(
            'message' => 'Password Changed Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }
}
