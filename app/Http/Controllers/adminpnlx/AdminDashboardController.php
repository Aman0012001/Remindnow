<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Validation\Rules\Password;
use App\Models\Admin;

class AdminDashboardController extends Controller
{
   public $model = 'dashboard';
   public function __construct(Request $request)
   {  
      parent::__construct();
      View()->share('model', $this->model);
      $this->request = $request;
   }

   public function showdashboard()
   {
      return  View('admin.dashboard.dashboard');
   }

   public function myaccount(Request $request)
   {
      if ($request->isMethod('POST')) {
         $validated = $request->validate([
            'name' => 'required',
            'email'  => 'required|email|regex:/(.+)@(.+)\.(.+)/i',
         ]);
         $user             =    Admin::find(Auth::guard('admin')->user()->id);
         $user->name       =    $request->name;
         $user->email       =    $request->email;
         if ($user->save()) {
            return Redirect()->route('dashboard')->with('success', 'Information updated successfully.');
         }
      }
      $userInfo   =   Auth::guard("admin")->user();
      return  View("admin.$this->model.myaccount", compact('userInfo'));
   }

   public function changedPassword(Request $request)
   {
      if ($request->isMethod('POST')) {
         $validated = $request->validate([
            'old_password' => 'required',
            'new_password' => ['required',Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'confirm_password' => 'required|same:new_password',
         ]);
         $user = Admin::find(Auth::guard('admin')->user()->id);
         $oldpassword = $request->old_password;
         if (Hash::check($oldpassword, $user->getAuthPassword())) {
            $user->password = Hash::make($request->new_password);
            $user->save();
            return Redirect()->route('dashboard')
               ->with('success', 'Password changed successfully.');
         } else {
            return Redirect()->route('dashboard')
               ->with('error', 'Your old password is incorrect.');
         }
      }
      return  View("admin.$this->model.changedPassword");
   }
}
