<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Config;
use App\Models\Lookup;  
use App\Models\User;
use Carbon\Carbon;
use App\Models\EmailAction;
use App\Models\EmailTemplate;
use Redirect,Session;

class UsersController extends Controller
{
    public $model      =   'users';
    public $sectionNameSingular      =   'customers';
    public function __construct(Request $request)
    {   
        parent::__construct();
        View()->share('model', $this->model);
        View()->share('sectionNameSingular', $this->sectionNameSingular);
        $this->request = $request;
    }
    public function index(Request $request)
    {
        $DB					=	User::query();
        $searchVariable		=	array();
        $inputGet			=	$request->all();
        if ($request->all()) {
            $searchData			=	$request->all();
            unset($searchData['display']);
            unset($searchData['_token']);

            if (isset($searchData['order'])) {
                unset($searchData['order']);
            }
            if (isset($searchData['sortBy'])) {
                unset($searchData['sortBy']);
            }
            if (isset($searchData['page'])) {
                unset($searchData['page']);
            }
            if ((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))) {
                $dateS = date("Y-m-d",strtotime($searchData['date_from']));
                $dateE =  date("Y-m-d",strtotime($searchData['date_to']));
                $DB->whereBetween('users.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('users.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('users.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "first_name") {
                        $DB->where("users.first_name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "address") {
                        $DB->where("users.address", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "last_name") {
                        $DB->where("users.last_name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "name") {
                        $DB->where("users.name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "email") {
                        $DB->where("users.email", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "is_active") {
                        $DB->where("users.is_active", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "is_deleted") {
                        $DB->where("users.is_deleted", 'like', '%' . $fieldValue . '%');
                    }else{
                        $DB->where("users.is_deleted", 0);
                    }
                }
                $searchVariable	=	array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }

        $DB->where("users.user_role_id", 2);
                $DB->where("users.is_deleted", 0);
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'users.created_at';
        $order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
        $records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
        $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        $complete_string		=	$request->query();
        unset($complete_string["sortBy"]);
        unset($complete_string["order"]);
        $query_string			=	http_build_query($complete_string);
        $results->appends($inputGet)->render();
        $resultcount = $results->count();
        return  View("admin.$this->model.index", compact('resultcount', 'results', 'searchVariable', 'sortBy', 'order', 'query_string'));
    }
    public function create(Request $request)
    {       
        return  View("admin.$this->model.add");
    }
    public function Save(Request $request){
        if ($request->isMethod('POST')) {
            $thisData = $request->all();
            $validator                    =   Validator::make(
                $request->all(), 
                array(
                    'name'              => "required",
                    'email'             => 'required|email|regex:/(.+)@(.+)\.(.+)/i|unique:users,email',
                    'password'          => ['required',Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
                    'confirm_password'  => 'required|same:password',
                ),
                array(
                    "name.required"              => trans("The  name field is required."),
                    "email.required"             => trans("The email field is required."),
                    "email.email"                => trans("The email must be a valid email address"),
                    "password.min"               => trans("The Password must be atleast 8 characters with combination of atleast have one alpha, one numeral and one special character."),
                    "confirm_password.required"  => trans("The confirm password field is required."),
                    "confirm_password.same"      => trans("The confirm password not matched with password."),
                )
            );
            $password = $request->input('password');
            if (preg_match('#[0-9]#', $password) && preg_match('#[a-zA-Z]#', $password) && preg_match('#[\W]#', $password)) {
                $correctPassword = Hash::make($password);
            } else {
                $errors = $validator->messages();
                $errors->add('password', trans("The Password must be atleast 8 characters with combination of atleast have one alpha, one numeral and one special character."));
                return Redirect::back()->withErrors($errors)->withInput();
            }
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            }else{
                $user                               =   new User;
                $user->user_role_id                 =   Config('constants.ROLE_ID.CUSTOMER_ROLE_ID');
                $user->name                         =   $request->input('name');
                $user->email                        =   $request->email;
                $user->password                     =   Hash::make($request->password);
                $SavedResponse = $user->save();
                if (!$SavedResponse) {
                    Session()->flash('error', trans("Something went wrong."));
                    return Redirect()->back()->withInput();
                } else {
                    Session()->flash('success', ucfirst(Config('constants.CUSTOMER.CUSTOMERS_TITLE')." has been added successfully"));
                    return Redirect()->route($this->model . ".index");
                }
            }
        } 
    }
    public function edit(Request $request,  $enuserid = null)
    { 
        $user_id = '';
        if (!empty($enuserid)) {
            $user_id        = base64_decode($enuserid);
            $userDetails    = User::find($user_id);

            return  View("admin.$this->model.edit", compact('userDetails'));
        } else {
            return redirect()->route($this->model . ".index");
        }
    }
    public function update(Request $request,  $enuserid = null){
        if ($request->isMethod('POST')) {
            $thisData = $request->all();
            $user_id = '';
            $image = "";
            if (!empty($enuserid)) {
                $user_id = base64_decode($enuserid);
            } else {
                return redirect()->route($this->model . ".index");
            }
            $validator                    =   Validator::make(
                $request->all(), 
                array(
                    'name'              => "required",
                    'email'             => 'required|email|regex:/(.+)@(.+)\.(.+)/i|unique:users,email,'.$user_id,
                ),
                array(
                    "name.required"              => trans("The user name field is required."),
                    "email.required"             => trans("The email field is required."),
                    "email.email"                => trans("The email must be a valid email address"),
                )
            );
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            }else{

                $user                               =   User::where("id",$user_id)->first();
                $user->name                         =   $request->input('name');
                $user->email                        =   $request->email;
                $SavedResponse = $user->save();
                if (!$SavedResponse) {
                    Session()->flash('error', trans("Something went wrong."));
                    return Redirect()->back()->withInput();
                }
                Session()->flash('success', ucfirst(Config('constants.CUSTOMER.CUSTOMERS_TITLE')." has been updated successfully"));
                return Redirect()->route($this->model . ".index");
            }
        }
    }
    public function destroy( $enuserid)
    {
        $user_id = '';
        if (!empty($enuserid)) {
            $user_id = base64_decode($enuserid);
        }
        $userDetails   =   User::find($user_id);
        if (empty($userDetails)) {
            return Redirect()->route($this->model . '.index');
        }
        if ($user_id) {
            $email              =   'delete_' . $user_id . '_' .!empty($userDetails->email);
            $phone_number       =   'delete_' . $user_id . '_' .!empty($userDetails->phone_number);

            User::where('id', $user_id)->update(array(
                'is_deleted'    => 1, 
                'email'         => $email, 
            ));

            Session()->flash('flash_notice', trans(ucfirst( "User has been removed successfully")));
        }
        return back();
    }
    public function changeStatus($modelId = 0, $status = 0)
    {
        if ($status == 1) {
            $statusMessage   =   trans(Config('constants.CUSTOMER.CUSTOMERS_TITLE'). " has been deactivated successfully");
        } else {
            $statusMessage   =   trans(Config('constants.CUSTOMER.CUSTOMERS_TITLE'). " has been activated successfully");
        }
        $user = User::find($modelId);
        if ($user) {
            $currentStatus = $user->is_active;
            if (isset($currentStatus) && $currentStatus == 0) {
                $NewStatus = 1;
            } else {
                $NewStatus = 0;
            }
            $user->is_active = $NewStatus;
            $ResponseStatus = $user->save();
        }
        Session()->flash('flash_notice', $statusMessage);
        return back();
    }
    public function changedPassword(Request $request, $enuserid = null)
    {
        $user_id = '';
        if (!empty($enuserid)) {
            $user_id = base64_decode($enuserid);
        } else {
            return redirect()->route($this->model . ".index");
        }
        if ($request->isMethod('POST')) {
            if (!empty($user_id)) {
                $validator                  =   Validator::make(
                    $request->all(),
                    array(
                        'new_password'      => ['required',Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
                        'confirm_password'  => 'required|same:new_password',
                    ),
                    array(
                        "new_password.required"      => trans("The new password field is required."),
                        "new_password.min"           => trans("The Password must be atleast 8 characters with combination of atleast have one alpha, one numeral and one special character."),
                        "confirm_password.required"  => trans("The confirm password field is required."),
                        "confirm_password.same"      => trans("The confirm password not matched with new password."),
                    )
                );
                $password = $request->input('new_password');
                if (preg_match('#[0-9]#', $password) && preg_match('#[a-zA-Z]#', $password) && preg_match('#[\W]#', $password)) {
                    $correctPassword = Hash::make($password);
                } else {
                    $errors = $validator->messages();
                    $errors->add('new_password', trans("The Password must be atleast 8 characters with combination of atleast have one alpha, one numeral and one special character."));
                    return Redirect::back()->withErrors($errors)->withInput();
                }if ($validator->fails()) {

                    return Redirect::back()->withErrors($validator)->withInput();
                } else {

                    $userDetails   =  User::find($user_id);
                    $userDetails->password     =  Hash::make($request->new_password);
                    $SavedResponse =  $userDetails->save();
                    if (!$SavedResponse) {
                        Session()->flash('error', trans("Something went wrong."));
                        return Redirect()->back();
                    }
                    Session()->flash('success', trans("Password changed successfully."));
                    return Redirect()->route($this->model . '.index');
                }
            }
        }
        $userDetails = array();
        $userDetails   =  User::find($user_id);
        $data = compact('userDetails');
        return view("admin.$this->model.change_password", $data);
    }
    public function view($enuserid = null)
    {
        $user_id = '';
        if (!empty($enuserid)) {
            $user_id = base64_decode($enuserid);
        } else {
            return redirect()->route($this->model . ".index");
        }
        $userDetails    =    User::where('users.id', $user_id)->first();
        return  View("admin.$this->model.view", compact('userDetails'));
    }
    public function sendCredentials($id){
        if(empty($id)){
            return redirect()->back();
        }
        $random = str_shuffle('abcdefghjklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ234567890!$%^&!$%^&');
        $password = substr($random, 0, 10);
        $user  = 	User::find($id);
        $settingsEmail 	= 	Config::get("Site.from_email");
        $full_name 		= 	$user->name;
        $email 			=	$user->email;
        $user->password = Hash::make($password);
        $user->save();
        $emailActions 	= 	EmailAction::where('action','=','send_login_credentials')->get()->toArray();
        $emailTemplates = 	EmailTemplate::where('action','=','send_login_credentials')->get(array('name','subject','action','body'))-> toArray();
        $cons 			= 	explode(',',$emailActions[0]['options']);
        $constants 		= 	array();
        foreach($cons as $key => $val){
            $constants[] = '{'.$val.'}';
        }
        $subject 		= 	$emailTemplates[0]['subject'];
        $route_url      =  	Config('constants.WEBSITE_ADMIN_URL').'/login';		
        $rep_Array 		= 	array($full_name,$email,$password,$route_url);
        $messageBody 	= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
        $this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);
        Session()->flash('flash_notice', trans("Login credentials send successfully"));
        return redirect()->back();
    }    
}
