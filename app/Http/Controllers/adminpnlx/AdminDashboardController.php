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
use App\Models\User;
use App\Models\Festival;
use App\Models\Temple;
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
    // Get total counts
    $totalUsers = User::count();
    $deletedUsers = User::where('is_deleted', 1)->count(); // Add deleted users count
    $activeUsers = $totalUsers - $deletedUsers; // Active users
    $totalFestivals = Festival::where('is_deleted',0)->count();
    $totalTemples = Temple::where('is_deleted',0)->count();
    
    // Get user growth data by month for the current year
    $currentYear = date('Y');
    $userGrowthData = User::select(
            DB::raw('COUNT(*) as total'),
            DB::raw('MONTH(created_at) as month')
        )
        ->whereYear('created_at', $currentYear)
        ->groupBy(DB::raw('MONTH(created_at)'))
        ->orderBy('month', 'asc')
        ->get()
        ->pluck('total', 'month')
        ->toArray();
    
    // Create arrays for all 12 months
    $monthsData = [];
    $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    
    for ($i = 1; $i <= 12; $i++) {
        // Fill data (0 if no data for that month)
        $monthsData[] = $userGrowthData[$i] ?? 0;
    }
 $routes = [
        'total_users' => route('users.index'),
        'active_users' => route('users.index', ['is_active' => 1]),
        'deleted_users' => route('users.index', ['is_deleted' => 1]),
        'temples' => route('temples.index'),
        'festivals' => route('festivals.index'),
    ];
    return view('admin.dashboard.dashboard', compact(
        'totalUsers',
        'activeUsers',
        'deletedUsers',
        'totalFestivals', 
        'totalTemples',
        'monthsData',
        'monthNames',
        'currentYear',
                'routes'
    ));
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
