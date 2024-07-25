<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\path;
use Illuminate\Support\Facades\DB;
use App\Models\EmailLog;
use Config;
use Mail;
use Request;

class Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	protected $user;
	
	public function __construct() {
		$url	=	Request::fullUrl();
	}// end function __construct()

	public function checkPermission($url){
		$segment1	=	Request()->segment(1);
		$segment2	=	Request()->segment(2);
		$segment3	=	Request()->segment(3);
		
		$segment1_1 = explode(' ', $segment1);
		$segment1_1 = end($segment1_1);
		$segment2_2 = explode(' ', $segment2);
		$segment2_2 = end($segment2_2);
		$segment3_3 = explode(' ', $segment3);
		$segment3_3 = end($segment3_3);
		
		if (in_array($segment1_1,$actions_arr) || in_array($segment2_2,$actions_arr) || in_array($segment3_3,$actions_arr)){
			return 1;
		}
		
		$user_id				=	Auth::user()->id;
		$user_role_id			=	Auth::user()->user_role_id;
		$path					=	Request()->path();
		$action					=	Route::current()->getAction();
		
		$function_name	=	explode("\\",$action['controller']);
		$function_name	=	end($function_name);
		$permissionData			=	DB::table("user_permission_actions")
											->select("user_permission_actions.is_active")
											->leftJoin("acl_admin_actions","acl_admin_actions.id","=","user_permission_actions.admin_module_action_id")
											->where('user_permission_actions.user_id',$user_id)
											->where('user_permission_actions.is_active',1)
											->where('acl_admin_actions.function_name',$function_name)
											->first();
		
		$byDefaultPermissionData = DB::table("acl_admin_actions")
		->where('acl_admin_actions.is_show',0)
		->where('acl_admin_actions.function_name',$function_name)
		->first();
		if(!empty($permissionData) || !empty($byDefaultPermissionData)){
			return 1;
		}else{
			return 0;
		}
	}

    public function buildTree($parentId = 0){
		$user_id	    =	Auth::guard('admin')->user()->id;
		$user_role_id	=	Auth::guard('admin')->user()->user_role_id;
		$branch         =   array();
		$elements       =   array();
		$superadmin = Config('constants.ROLE_ID.SUPER_ADMIN_ROLE_ID');
        $language_id  = Session()->get('sel_lang');
		if($user_role_id == $superadmin){
			$elements = DB::table("acls")
                ->select("acls.*","acls.title as title")
                ->where("acls.parent_id",$parentId)
                ->where("acls.is_active",1)
                ->orderBy('acls.module_order','ASC')
                ->get();
		}else {
			if($parentId == 0){
				$elements = DB::table("acls")
                    ->select("acls.*","acls.title as title")
                    ->where("acls.parent_id",$parentId)
                    ->where("acls.is_active",1)
                    ->where("acls.id",DB::raw("(select admin_module_id from user_permissions where user_permissions.admin_module_id = acls.id AND is_active = 1 AND user_id = $user_id LIMIT 1)"))
                    ->orderBy('acls.module_order','ASC')
                    ->get();
			}else{ 
				$elements = 	DB::table("acls")
                    ->where("acls.parent_id",$parentId)
                    ->where("acls.is_active",1)
                    ->where("acls.id",DB::raw("(select admin_sub_module_id from user_permission_actions where user_permission_actions.admin_sub_module_id = acls.id AND is_active = 1 AND user_id = $user_id LIMIT 1)"))
                    ->orderBy('acls.module_order','ASC')
                    ->get();  
			}
		}
        
		foreach($elements as $element){
			if ($element->parent_id == $parentId){
				$children = $this->buildTree($element->id);
				if ($children){
					$element->children = $children;
				}
				$branch[] = $element;
			}
		}
		return $branch;
	}

	public function arrayStripTags($array)
    {
        $result = array();
        foreach ($array as $key => $value) {
            // Don't allow tags on key either, maybe useful for dynamic forms.
            $key = strip_tags($key, config('constants.ALLOWED_TAGS_XSS'));

            // If the value is an array, we will just recurse back into the
            // function to keep stripping the tags out of the array,
            // otherwise we will set the stripped value.
            if (is_array($value)) {
                $result[$key] = $this->arrayStripTags($value);
            } else {
                // I am using strip_tags(), you may use htmlentities(),
                // also I am doing trim() here, you may remove it, if you wish.
                $result[$key] = trim(strip_tags($value, config('constants.ALLOWED_TAGS_XSS')));
            }
        }

        return $result;

    }

	public function change_error_msg_layout($errors = array())
    {
        $response = array();
        $response["status"] = "error";
        if (!empty($errors)) {
            $error_msg = "";
            foreach ($errors as $errormsg) {
                $error_msg1 = (!empty($errormsg[0])) ? $errormsg[0] : "";
                $error_msg .= $error_msg1 . ", ";
            }
            $response["msg"] = trim($error_msg, ", ");
        } else {
            $response["msg"] = "";
        }
        $response["data"] = (object) array();
        $response["errors"] = $errors;
        return $response;
    }

    public function change_error_msg_layout_with_array($errors = array())
    {
        $response = array();
        $response["status"] = "error";
        if (!empty($errors)) {
            $error_msg = "";
            foreach ($errors as $errormsg) {
                $error_msg1 = (!empty($errormsg[0])) ? $errormsg[0] : "";
                $error_msg .= $error_msg1 . ", ";
            }
            $response["msg"] = trim($error_msg, ", ");
        } else {
            $response["msg"] = "";
        }
        $response["data"] = array();
        $response["errors"] = $errors;
        return $response;
    }

	public function getVerificationCode(){
		$code	=	rand(10000,99999);
	   
		return $code;
	}
	public function sendMail($to, $fullName, $subject, $messageBody, $from = '', $files = false, $path = '', $attachmentName = '')
    {
        $from = Config::get("Site.from_email");
        $data = array();
        $data['to'] = $to;
        $data['from'] = (!empty($from) ? $from : Config::get("Site.email"));
        $data['fullName'] = $fullName;
        $data['subject'] = $subject;
        $data['filepath'] = $path;
        $data['attachmentName'] = $attachmentName;
        if ($files === false) {
            Mail::send('emails.template', array('messageBody' => $messageBody), function ($message) use ($data) {
                $message->to($data['to'], $data['fullName'])->from($data['from'])->subject($data['subject']);
            });
        } else {
            if ($attachmentName != '') {
                Mail::send('emails.template', array('messageBody' => $messageBody), function ($message) use ($data) {
                    $message->to($data['to'], $data['fullName'])->from($data['from'])->subject($data['subject'])->attach($data['filepath'], array('as' => $data['attachmentName']));
                });
            } else {
                Mail::send('emails.template', array('messageBody' => $messageBody), function ($message) use ($data) {
                    $message->to($data['to'], $data['fullName'])->from($data['from'])->subject($data['subject'])->attach($data['filepath']);
                });
            }
        }
        $obj                  =  new EmailLog;
        $obj->email_to        =  $data['to'];
        $obj->email_from      =  $from;
        $obj->subject         =  $data['subject'];
        $obj->message         =  $messageBody;
        $obj->save();
    }

    public function current_language_id(){
		$language_code  = session()->get('admin_applocale');
        $language        = DB::table('languages')->where('lang_code',$language_code)->first();
        $language_id    = $language->id ?? 1;
		
		return $language_id;
	}
    
    public function saveCkeditorImages()
    {
        if (!empty($_GET['CKEditorFuncNum'])) {
            $image_url = "";
            $msg = "";
            // Will be returned empty if no problems
            $callback = ($_GET['CKEditorFuncNum']); // Tells CKeditor which function you are executing
            $image_details = getimagesize($_FILES['upload']["tmp_name"]);
            $image_mime_type = (isset($image_details["mime"]) && !empty($image_details["mime"])) ? $image_details["mime"] : "";
            if ($image_mime_type == 'image/jpeg' || $image_mime_type == 'image/jpg' || $image_mime_type == 'image/gif' || $image_mime_type == 'image/png') {
                $ext = $this->getExtension($_FILES['upload']['name']);
                $fileName = "ck_editor_" . time() . "." . $ext;
                $upload_path = config('constants.CK_EDITOR_ROOT_PATH');
                if (move_uploaded_file($_FILES['upload']['tmp_name'], $upload_path . $fileName)) {
                    $image_url = config('constants.CK_EDITOR_URL') . $fileName;
                }
            } else {
                $msg = 'error : Please select a valid image. valid extension are jpeg, jpg, gif, png';
            }
            $output = '<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction(' . $callback . ', "' . $image_url . '","' . $msg . '");</script>';
            echo $output;
            exit;
        }
    }

    public function getExtension($str)
    {
        $i = strrpos($str, ".");
        if (!$i) {return "";}
        $l = strlen($str) - $i;
        $ext = substr($str, $i + 1, $l);
        $ext = strtolower($ext);
        return $ext;
    }
}
