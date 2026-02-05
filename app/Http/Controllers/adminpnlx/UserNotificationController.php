<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\App;
use Config;
use Carbon\Carbon;
use Redirect, Session;
use App\Models\UserDeviceToken;
class UserNotificationController extends Controller
{
    public $model = 'user-notifications';
    public $sectionNameSingular = 'User Notification';
    
    public function __construct(Request $request)
    {   
        parent::__construct();
        View()->share('model', $this->model);
        View()->share('sectionNameSingular', $this->sectionNameSingular);
        $this->request = $request;
    }
    
  public function index(Request $request)
{
    $DB = UserNotification::with('user');
    $searchVariable = array();
    $inputGet = $request->all();
    
    if ($request->all()) {
        $searchData = $request->all();
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
            $dateS = date("Y-m-d", strtotime($searchData['date_from']));
            $dateE = date("Y-m-d", strtotime($searchData['date_to']));
            $DB->whereBetween('user_notifications.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
        } elseif (!empty($searchData['date_from'])) {
            $dateS = $searchData['date_from'];
            $DB->where('user_notifications.created_at', '>=', [$dateS . " 00:00:00"]);
        } elseif (!empty($searchData['date_to'])) {
            $dateE = $searchData['date_to'];
            $DB->where('user_notifications.created_at', '<=', [$dateE . " 00:00:00"]);
        }
        
        foreach ($searchData as $fieldName => $fieldValue) {
            if ($fieldValue != "") {
                if ($fieldName == "title") {
                    $DB->where("user_notifications.title", 'like', '%' . $fieldValue . '%');
                }
                if ($fieldName == "device_id") {
                    $DB->where("user_notifications.device_id", 'like', '%' . $fieldValue . '%');
                }
                if ($fieldName == "user_id") {
                    $DB->where("user_notifications.user_id", $fieldValue);
                }
                if ($fieldName == "is_sent") {
                    $DB->where("user_notifications.is_sent", $fieldValue);
                }
            }
            $searchVariable = array_merge($searchVariable, array($fieldName => $fieldValue));
        }
    }

    $DB->where("user_notifications.is_deleted", 0);
    $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'user_notifications.created_at';
    $order = ($request->input('order')) ? $request->input('order') : 'DESC';
    $records_per_page = ($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
    $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
    $complete_string = $request->query();
    unset($complete_string["sortBy"]);
    unset($complete_string["order"]);
    $query_string = http_build_query($complete_string);
    $results->appends($inputGet)->render();
    $resultcount = $results->count();
    
    // Get users for dropdown
    $users = User::where('is_active', 1)->where('is_deleted', 0)->get();
    
    // Get all user IDs to fetch user names
    $userIds = UserNotification::whereNotNull('user_ids')
        ->where('user_ids', '!=', '')
        ->pluck('user_ids')
        ->flatMap(function ($userIds) {
            return explode(',', $userIds);
        })
        ->unique()
        ->filter()
        ->toArray();
    
    $allUsers = [];
    if (!empty($userIds)) {
        $allUsers = User::whereIn('id', $userIds)
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->get()
            ->keyBy('id');
    }
    
    return View("admin.$this->model.index", compact(
        'resultcount', 
        'results', 
        'searchVariable', 
        'sortBy', 
        'order', 
        'query_string', 
        'users',
        'allUsers'
    ));
}

public function getUsersByIds(Request $request)
{
    try {
        $userIds = explode(',', $request->input('user_ids'));
                $userIds = array_filter(array_map('trim', $userIds));
        $userIds = array_filter($userIds, 'is_numeric');
        
        if (empty($userIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid user IDs provided',
                'users' => []
            ]);
        }
        
        $users = User::whereIn('id', $userIds)
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->select('id', 'name', 'email', 'phone_number')
            ->get();
        
        return response()->json([
            'success' => true,
            'users' => $users,
            'count' => $users->count()
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fetching users',
            'error' => $e->getMessage()
        ], 500);
    }
}
    
public function create(Request $request)
{
    $users = User::where('is_active', 1)
        ->where('is_deleted', 0)
        ->with(['deviceTokens' => function($query) {
            $query->select('user_id', 'device_id', 'device_type')
                  ->where('device_id', '!=', '')
                  ->whereNotNull('device_id');
        }])
        ->orderBy('name', 'asc')
        ->get(['id', 'name', 'email']);
    
    return View("admin.$this->model.create", compact('users'));
}
    
public function store(Request $request)
{
    if ($request->isMethod('POST')) {
     $isSelectAll = $request->boolean('select_all_users');

// Base validation data & rules
$validatorData = [
    'title' => $request->input('title'),
];

$rules = [
    'title' => 'required|string|max:255',
];

if ($isSelectAll) {
    // Fetch all user IDs if select all
    $userIds = User::pluck('id')->toArray();
} else {
    // Add user_ids to validation
    $validatorData['user_ids'] = $request->input('user_ids');

    $rules['user_ids'] = 'required|array|min:1';
    $rules['user_ids.*'] = 'exists:users,id';

    $userIds = $request->input('user_ids');
}

// Validate
$validator = Validator::make($validatorData, $rules);

if ($validator->fails()) {
    return Redirect::back()
        ->withErrors($validator)
        ->withInput();
}
else {
            $notification = new UserNotification;
            $notification->title = $request->input('title');
            $notification->description = $request->input('description');
            $notification->is_active = $request->has('is_active') ? 1 : 0;
            $notification->is_sent = $request->has('send_now') ? 1 : 0;
            
            // Handle user_ids - now we have $userIds variable
            $notification->user_ids = implode(',', $userIds);
            
            // Get device_ids from user_device_tokens table for selected users
            $deviceTokens = UserDeviceToken::whereIn('user_id', $userIds)
                ->whereNotNull('device_id')
                ->where('device_id', '!=', '')
                ->pluck('device_id')
                ->toArray();
            
            if (!empty($deviceTokens)) {
                $notification->device_ids = implode(',', $deviceTokens);
            } else {
                $notification->device_ids = null;
            }
            
            // Add flag to indicate if all users were selected
            $notification->is_all_users = $isSelectAll ? 1 : 0;
            
            // Handle image upload
            if ($request->hasFile('image')) {
                try {
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $fileName = time() . '-notification.' . $extension;
                    $folderName = strtoupper(date('M') . date('Y')) . "/";
                    
                    // Get the root path - should be server path like public_path('uploads/notifications/')
                    $rootPath = Config::get('constants.NOTIFICATION_IMAGE_ROOT_PATH');
                    
                    // Debug: Check what path we're getting
                    \Log::info('Root Path: ' . $rootPath);
                    
                    // Full path for the folder
                    $folderPath = $rootPath . $folderName;
                    
                    \Log::info('Folder Path: ' . $folderPath);
                    
                    // Create directory if it doesn't exist
                    if (!File::exists($folderPath)) {
                        // Create directory recursively
                        File::makeDirectory($folderPath, 0755, true, true);
                    }
                    
                    // Move the uploaded file
                    $file = $request->file('image');
                    if ($file->move($folderPath, $fileName)) {
                        $notification->image = $folderName . $fileName;
                    } else {
                        throw new \Exception("Failed to move uploaded file.");
                    }
                    
                } catch (\Exception $e) {
                    // Log the error
                    \Log::error('Image upload failed: ' . $e->getMessage());
                    
                    // Return with error message
                    Session()->flash('error', 'Image upload failed: ' . $e->getMessage());
                    return Redirect()->back()->withInput();
                }
            }
            
            $SavedResponse = $notification->save();
            
            // Send notification if requested
            if ($SavedResponse && $notification->is_sent && $notification->device_ids) {
                $this->sendPushNotification($notification);
            }
            
            if (!$SavedResponse) {
                Session()->flash('error', trans("Something went wrong."));
                return Redirect()->back()->withInput();
            } else {
                Session()->flash('success', ucfirst($this->sectionNameSingular . " has been added successfully"));
                return Redirect()->route($this->model . ".index");
            }
        }
    }
}
public function getUserDeviceIds(Request $request)
{
    $userIds = $request->input('user_ids', []);
    
    if (!empty($userIds)) {
        $deviceTokens = UserDeviceToken::whereIn('user_id', $userIds)
            ->whereNotNull('device_id')
            ->where('device_id', '!=', '')
            ->select('user_id', 'device_id')
            ->get();
        
        $allUsers = User::whereIn('id', $userIds)->count();
        $usersWithDevices = $deviceTokens->pluck('user_id')->unique()->count();
        $usersWithoutDevice = $allUsers - $usersWithDevices;
        
        // Extract device IDs
        $deviceIds = $deviceTokens->pluck('device_id')->toArray();
        
        return response()->json([
            'success' => true,
            'device_ids' => $deviceIds,
            'device_count' => count($deviceIds),
            'user_count' => count($userIds),
            'users_without_device' => $usersWithoutDevice
        ]);
    }
    
    return response()->json([
        'success' => false,
        'device_ids' => [],
        'device_count' => 0,
        'user_count' => 0,
        'users_without_device' => 0
    ]);
}
    
    public function edit(Request $request, $enuserid = null)
    {
        $notification_id = '';
        if (!empty($enuserid)) {
            $notification_id = base64_decode($enuserid);
            $notificationDetails = UserNotification::find($notification_id);
            
            if ($notificationDetails) {
                $users = User::where('is_active', 1)->where('is_deleted', 0)->get();
                return View("admin.$this->model.edit", compact('notificationDetails', 'users'));
            }
        }
        
        return Redirect()->route($this->model . ".index");
    }
    
    public function update(Request $request, $enuserid = null)
    {
        $notification_id = '';
        if (!empty($enuserid)) {
            $notification_id = base64_decode($enuserid);
            
            $validator = Validator::make(
                array(
                    'title' => $request->input('title'),
                    'user_type' => $request->input('user_type'),
                ),
                array(
                    'title' => 'required|string|max:255',
                    'user_type' => 'required|in:specific,all',
                )
            );
            
            // Conditional validation for specific user
            if ($request->input('user_type') == 'specific') {
                $validator->sometimes('user_id', 'required|exists:users,id', function($input) {
                    return true;
                });
            }
            
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            } else {
                $notification = UserNotification::where("id", $notification_id)->first();
                $notification->title = $request->input('title');
                $notification->description = $request->input('description');
                $notification->is_active = $request->has('is_active') ? 1 : 0;
                
                // Handle user selection
                if ($request->input('user_type') == 'specific' && $request->has('user_id')) {
                    $notification->user_id = $request->input('user_id');
                    // Get device_id from user if available
                    $user = User::find($request->input('user_id'));
                    if ($user && $user->device_id) {
                        $notification->device_id = $user->device_id;
                    }
                } else {
                    // For all users
                    $notification->user_id = null;
                    $notification->device_id = null;
                }
                
                // Handle custom device_id
                if ($request->filled('device_id')) {
                    $notification->device_id = $request->input('device_id');
                }
                
                // Handle image upload
                if ($request->hasFile('image')) {
                    // Delete old image if exists
                    if (!empty($notification->image) && File::exists(Config::get('constants.NOTIFICATION_IMAGE_ROOT_PATH') . $notification->image)) {
                        File::delete(Config::get('constants.NOTIFICATION_IMAGE_ROOT_PATH') . $notification->image);
                    }
                    
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $fileName = time() . '-notification.' . $extension;
                    $folderName = strtoupper(date('M') . date('Y')) . "/";
                    $folderPath = Config::get('constants.NOTIFICATION_IMAGE_ROOT_PATH') . $folderName;
                    
                    // Create directory if it doesn't exist
                    if (!File::exists($folderPath)) {
                        File::makeDirectory($folderPath, 0777, true, true);
                    }
                    
                    if ($request->file('image')->move($folderPath, $fileName)) {
                        $notification->image = $folderName . $fileName;
                    }
                }
                
                $SavedResponse = $notification->save();
                
                // Resend notification if requested
                if ($SavedResponse && $request->has('resend_notification')) {
                    $notification->is_sent = 1;
                    $notification->save();
                    $this->sendPushNotification($notification);
                }
                
                if (!$SavedResponse) {
                    Session()->flash('error', trans("Something went wrong."));
                    return Redirect()->back()->withInput();
                } else {
                    Session()->flash('success', ucfirst($this->sectionNameSingular . " has been updated successfully"));
                    return Redirect()->route($this->model . ".index");
                }
            }
        }
    }
    
    public function destroy($enuserid)
    {
        $notification_id = '';
        if (!empty($enuserid)) {
            $notification_id = base64_decode($enuserid);
        }
        
        $notificationDetails = UserNotification::find($notification_id);
        if (empty($notificationDetails)) {
            return Redirect()->route($this->model . '.index');
        }
        
        if ($notification_id) {
            // Delete image if exists
            if (!empty($notificationDetails->image) && File::exists(Config::get('constants.NOTIFICATION_IMAGE_ROOT_PATH') . $notificationDetails->image)) {
                File::delete(Config::get('constants.NOTIFICATION_IMAGE_ROOT_PATH') . $notificationDetails->image);
            }
            
            UserNotification::where('id', $notification_id)->update(array(
                'is_deleted' => 1,
            ));
            
            Session()->flash('flash_notice', trans(ucfirst($this->sectionNameSingular . " has been removed successfully")));
        }
        return back();
    }
    
    public function changeStatus($modelId = 0, $status = 0)
    {
        if ($status == 1) {
            $statusMessage = trans($this->sectionNameSingular . " has been activated successfully");
        } else {
            $statusMessage = trans($this->sectionNameSingular . " has been deactivated successfully");
        }
        
        $notification = UserNotification::find($modelId);
        if ($notification) {
            $currentStatus = $notification->is_active;
            if (isset($currentStatus) && $currentStatus == 0) {
                $NewStatus = 1;
            } else {
                $NewStatus = 0;
            }
            $notification->is_active = $NewStatus;
            $ResponseStatus = $notification->save();
        }
        Session()->flash('flash_notice', $statusMessage);
        return back();
    }

   public function sendNow($enuserid)
{
    $notification_id = '';
    if (!empty($enuserid)) {
        $notification_id = base64_decode($enuserid);
    }
    
    $notification = UserNotification::find($notification_id);
    if ($notification) {
        $this->sendPushNotification($notification);
        $notification->is_sent = 1;
        $notification->save();
        
        Session()->flash('success', trans("Notification has been sent successfully"));
    } else {
        Session()->flash('error', trans("Notification not found"));
    }
    
    return back();
}

/**
 * Send push notification to multiple users
 */
private function sendPushNotification($notification)
{
    try {
        // Get all user IDs from the notification
        $userIds = explode(',', $notification->user_ids);
        
        // Get device tokens for all users
        $deviceTokens = UserDeviceToken::whereIn('user_id', $userIds)
            ->whereNotNull('device_id')
            ->get();
        
        $sentCount = 0;
        $failedCount = 0;
        
        foreach ($deviceTokens as $deviceToken) {
            // Prepare notification data
            try {
                \App\Models\Notification::create([
                    'user_id' => $deviceToken->user_id,
                    'title' => $notification->title,
                    'description' => $notification->description,
                    'is_read' => 0
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to create notification entry: ' . $e->getMessage());
            }
            $notificationData = [
                'extra' => 'data' // Add your custom data here
            ];
            
            // Send notification using the existing function
            $response = $this->send_push_notification(
                $deviceToken->device_id,
                $deviceToken->device_type,
                $notification->description,
                $notification->title,
                'test_notification',
                $notificationData
            );
            
            // Check response and log accordingly
            if ($response && !empty($response['response'])) {
                $responseData = json_decode($response['response'], true);
                if (isset($responseData['name']) || isset($responseData['success']) || strpos($response['response'], 'error') === false) {
                    $sentCount++;
                    \Log::info('Push notification sent successfully:', [
                        'user_id' => $deviceToken->user_id,
                        'device_id' => $deviceToken->device_id,
                        'notification_id' => $notification->id
                    ]);
                } else {
                    $failedCount++;
                    \Log::warning('Push notification failed:', [
                        'user_id' => $deviceToken->user_id,
                        'device_id' => $deviceToken->device_id,
                        'response' => $response['response']
                    ]);
                }
            } else {
                $failedCount++;
                \Log::warning('Push notification failed - empty response:', [
                    'user_id' => $deviceToken->user_id,
                    'device_id' => $deviceToken->device_id
                ]);
            }
        }
        
        \Log::info('Push notification batch completed:', [
            'notification_id' => $notification->id,
            'total_users' => count($userIds),
            'device_tokens_found' => $deviceTokens->count(),
            'sent_successfully' => $sentCount,
            'failed' => $failedCount
        ]);
        
        return ['sent' => $sentCount, 'failed' => $failedCount];
        
    } catch (\Exception $e) {
        \Log::error('Failed to send push notifications: ' . $e->getMessage());
        return ['sent' => 0, 'failed' => 0];
    }
}
    /**
     * Send push notification
     * This is a basic implementation - you'll need to implement your specific push service
     */
    
    public function show($enuserid = null)
    {
        $notification_id = '';
        if (!empty($enuserid)) {
            $notification_id = base64_decode($enuserid);
        } else {
            return redirect()->route($this->model . ".index");
        }
        
        $notificationDetails = UserNotification::with('user')->where('id', $notification_id)->first();
        return View("admin.$this->model.show", compact('notificationDetails'));
    }
    
    /**
     * Get user's device ID by user ID (AJAX)
     */
    public function getUserDeviceId(Request $request)
    {
        $userId = $request->input('user_id');
        $user = User::find($userId);
        
        if ($user && $user->device_id) {
            return response()->json([
                'success' => true,
                'device_id' => $user->device_id
            ]);
        }
        
        return response()->json([
            'success' => false,
            'device_id' => null
        ]);
    }
}