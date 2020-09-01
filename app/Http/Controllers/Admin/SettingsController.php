<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class SettingsController extends Controller
{
    public function index(){
        return view('admin.settings');
    }

    public function updateProfile(Request $request){
          $this->validate($request,[
              'name' => 'required',
              'email' => 'required|email',
              'image' => 'required|image'
          ]);

          $image = $request->file('image');
          $slug = str_slug($request->name);
          $user = User::findOrFail(Auth::id());

          if(isset($image)){
              $currentDate = Carbon::now()->toDateString();
              $imageName = $slug.'-'.'$currentDate'.'-'.uniqid().'-'.$image->getClientOriginalExtension();

              if(!Storage::disk('public')->exists('profile')){
                  Storage::disk('public')->makeDirectory('profile');
              }

              if(Storage::disk('public')->exists('profile/'.$user->image)){
                  Storage::disk('public')->delete('profile/'.$user->image);
              }

              $profile = Image::make($image)->resize(500,500)->save();
              Storage::disk('public')->put('profile/'.$imageName,$profile);
          }else{
              $imageName = $user->image;
          }

          $user->name = $request->name;
          $user->email = $request->email;
          $user->image = $imageName;
          $user->about = $request->about;
          $user->save();

          Toastr::success('Profile Successfully Updated','success');
          return redirect()->back();

    }

    public function updatePassword(Request $request){
        $this->validate($request,[
            'old_password'=>'required',
            'password'=>'required|confirmed',
        ]);

        $hashedpassword = Auth::user()->password;

        if (Hash::check($request->old_password,$hashedpassword)){
            if (!Hash::check($request->password,$hashedpassword)){
                $user = User::find(Auth::id());
                $user->password = Hash::make($request->password);
                $user->save();
                Toastr::success('Password Successfully Updated','success');
                Auth::logout();
                return redirect()->back();
            }else{
                Toastr::error('New password does not match','Error');
                return redirect()->back();

            }
        }else{
            Toastr::error('Current Password not match','Error');
            return redirect()->back();

        }


    }
}