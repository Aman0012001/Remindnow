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
use App\Models\Festival;  
use App\Models\Temple;  
use App\Models\FestivalTemple;
use App\Models\Language;
use App\Models\FestivalDescription;
use Carbon\Carbon;
use Redirect,Session;

class FestivalController extends Controller
{
    public $model      =   'festivals';
    public $sectionNameSingular      =   'festivals';
    public function __construct(Request $request)
    {   
        parent::__construct();
        View()->share('model', $this->model);
        View()->share('sectionNameSingular', $this->sectionNameSingular);
        $this->request = $request;
    }
    public function index(Request $request)
    {
        $DB					=	Festival::query();
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
            
 if (!empty($searchData['state_id'])) {
            $stateId = $searchData['state_id'];
            $DB->whereJsonContains('states', $stateId);
        }
            if ((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))) {
                $dateS = date("Y-m-d",strtotime($searchData['date_from']));
                $dateE =  date("Y-m-d",strtotime($searchData['date_to']));
                $DB->whereBetween('festivals.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('festivals.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('festivals.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("temples.name", 'like', '%' . $fieldValue . '%');
                    }
                    
                }
                $searchVariable	=	array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }

        $DB->where("festivals.is_deleted", 0);
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'festivals.created_at';
        $order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
        $records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
        $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        $complete_string		=	$request->query();
        unset($complete_string["sortBy"]);
        unset($complete_string["order"]);
        $query_string			=	http_build_query($complete_string);
        $results->appends($inputGet)->render();
        $resultcount = $results->count();
            $allStates = \App\Models\State::get();
        return  View("admin.$this->model.index", compact('resultcount', 'results', 'searchVariable', 'sortBy', 'order', 'query_string','allStates'));
    }
    public function create(Request $request)
    {       
        $temples    = Temple::where('is_deleted',0)->get();
        $states     = \App\Models\State::get();
        $languages = Language::where('is_active', 1)->get();
        $language_code = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
        return View("admin.$this->model.add", compact('temples','states','languages', 'language_code'));
        
    }


    public function markPopular(Request $request)
{
    try {
        // Validate request
        $validator = Validator::make($request->all(), [
            'festival_id' => 'required|exists:festivals,id',
            'is_popular' => 'required|in:0,1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request data'
            ], 422);
        }
        $festival = Festival::find($request->festival_id);

        if (!$festival) {
            return response()->json([
                'success' => false,
                'message' => 'Festival not found'
            ], 404);
        }

        // Check if user has permission (optional)
        // if (!auth()->user()->can('update-festival')) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'You do not have permission to update this festival'
        //     ], 403);
        // }

        // Update festival status
        $oldStatus = $festival->is_popular;
        $festival->is_popular = $request->is_popular;
        $saved = $festival->save();

        if ($saved) {
            // Optional: Log the activity
            // activity()
            //     ->performedOn($festival)
            //     ->causedBy(auth()->user())
            //     ->log('marked festival as ' . ($request->is_popular ? 'popular' : 'not popular'));
            
            return response()->json([
                'success' => true,
                'message' => $request->is_popular 
                    ? 'Festival has been marked as popular' 
                    : 'Festival has been marked as not popular',
                'data' => [
                    'is_popular' => $festival->is_popular,
                    'status_text' => $festival->is_popular ? 'Popular' : 'Not Popular'
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update festival status. Please try again.'
            ], 500);
        }

    } catch (\Exception $e) {
        \Log::error('Error marking festival as popular: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while updating the festival status'
        ], 500);
    }
}
    
    
    // public function Save(Request $request){
    //     if ($request->isMethod('POST')) {
    //         $thisData = $request->all();
    //         $default_language           =    Config('constants.DEFAULT_LANGUAGE.FOLDER_CODE');
    //         $language_code              =    Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
    //         $dafaultLanguageArray       =    $thisData['data'][$language_code];
           
            
    //         $validator                    =   Validator::make(
                
    //             array(
    //                 'name'              => $dafaultLanguageArray['name'],
    //                 'image'             => 'required',
                    
    //             ),
    //             array(
    //                 'name'             => 'required',
    //             )
    //         );
            
    //         if ($validator->fails()) {
    //             return Redirect::back()->withErrors($validator)->withInput();
    //         }else{
    //             $festival                               =   new Festival;
    //             $festival->date                         =   $request->input('date');
    //             $festival->name                         =   $dafaultLanguageArray['name'] ?? '';
    //             $festival->description                  =   $dafaultLanguageArray['long_dec'] ?? '';
    //             $festival->temple_id                    =   $request->input('temple_id');
    //             $festival->short_dec                    =   $dafaultLanguageArray['short_dec'] ?? '';
    //             $festival->long_dec                     =   $dafaultLanguageArray['long_dec'] ?? '';
    //             $festival->regional_names                     =   $dafaultLanguageArray['regional_names'] ?? '';
                
    //             if ($request->hasFile('image')) {
    //                 $extension = $request->file('image')->getClientOriginalExtension();
    //                 $fileName = time() . '-image.' . $extension;
    //                 $folderName = strtoupper(date('M') . date('Y')) . "/";
    //                 $folderPath = Config('constants.FESTIVAL_IMAGE_ROOT_PATH') . $folderName;
                    
    //                 if ($request->file('image')->move($folderPath, $fileName)) {
    //                     $festival->image = $folderName . $fileName;
    //                 }
    //             }
    //             $SavedResponse = $festival->save();
    //             $lastId = $festival->id;
    //             if (!empty($thisData)) {
    //                 foreach ($thisData['data'] as $language_id => $value) {
    //                     $subObj                 = new FestivalDescription();
    //                     $subObj->language_id    = $language_id;
    //                     $subObj->parent_id      = $lastId;
    //                     $subObj->name           = $value['name'];
    //                     $subObj->description    = $value['short_dec'] ?? '';
    //                     $subObj->long_description = $value['long_dec'] ?? '';
    //                     $subObj->regional_names = $value['regional_names'] ?? '';
    //                     $subObj->save();
    //                 }
    //             }

                
    //             if (!$SavedResponse) {
    //                 Session()->flash('error', trans("Something went wrong."));
    //                 return Redirect()->back()->withInput();
    //             } else {
    //                 Session()->flash('success', ucfirst(Config('constants.FESTIVAL.FESTIVAL_TITLE')." has been added successfully"));
    //                 return Redirect()->route($this->model . ".index");
    //             }
    //         }
    //     } 
    // }
    
    
    
    
    public function Save(Request $request){
        if ($request->isMethod('POST')) {
            $thisData = $request->all();
    
            $default_language     = Config('constants.DEFAULT_LANGUAGE.FOLDER_CODE');
            $language_code        = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
            $dafaultLanguageArray = $thisData['data'][$language_code];
    
            // ✅ Validation
            $validator = Validator::make(
                [
                    'name'  => $dafaultLanguageArray['name'] ?? '',
                    'image' => $request->file('image'),
                ],
                [
                    'name'  => 'required',
                    'image' => 'required|mimes:jpeg,png,jpg,gif|max:2048',
                ]
            );
    
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            } else {
                $festival = new Festival;
    
                // ✅ Base (non-language) fields
                $festival->date             = $request->input('date');
                $festival->name             = $dafaultLanguageArray['name'] ?? '';
                $festival->short_dec        = $dafaultLanguageArray['short_dec'] ?? '';
                $festival->long_dec         = $dafaultLanguageArray['long_dec'] ?? '';
                $festival->regional_names   = $dafaultLanguageArray['regional_names'] ?? '';
                $festival->temple_id        = $request->input('temple_id'); // if applicable
                $festival->description      = $dafaultLanguageArray['long_dec'] ?? '';
                  $festival->states           = $request->input('states');
    
                // ✅ Optional base-level columns (add these columns in DB if needed)
                $festival->states_celebrated  = $dafaultLanguageArray['states_celebrated'] ?? '';
                $festival->duration           = $dafaultLanguageArray['duration'] ?? '';
                $festival->daily_significance = $dafaultLanguageArray['daily_significance'] ?? '';
                $festival->history            = $dafaultLanguageArray['history'] ?? '';
                $festival->temples_to_visit   = $dafaultLanguageArray['temples_to_visit'] ?? '';
                $festival->other_info         = $dafaultLanguageArray['other_info'] ?? '';
    
                // ✅ Image upload
                if ($request->hasFile('image')) {
                    $extension  = $request->file('image')->getClientOriginalExtension();
                    $fileName   = time() . '-image.' . $extension;
                    $folderName = strtoupper(date('M') . date('Y')) . "/";
                    $folderPath = Config('constants.FESTIVAL_IMAGE_ROOT_PATH') . $folderName;
    
                    if (!file_exists($folderPath)) {
                        mkdir($folderPath, 0777, true);
                    }
    
                    if ($request->file('image')->move($folderPath, $fileName)) {
                        $festival->image = $folderName . $fileName;
                    }
                }
    
                // ✅ Save festival
                $SavedResponse = $festival->save();
                $lastId = $festival->id;
    
                // ✅ Save multilingual descriptions
                if (!empty($thisData['data'])) {
                    foreach ($thisData['data'] as $language_id => $value) {
                        $subObj = new FestivalDescription();
                        $subObj->language_id       = $language_id;
                        $subObj->parent_id         = $lastId;
                        $subObj->name              = $value['name'] ?? '';
                        $subObj->description       = $value['short_dec'] ?? '';
                        $subObj->long_description  = $value['long_dec'] ?? '';
                        $subObj->regional_names    = $value['regional_names'] ?? '';
                        $subObj->states_celebrated = $value['states_celebrated'] ?? '';
                        $subObj->duration          = $value['duration'] ?? '';
                        $subObj->daily_significance = $value['daily_significance'] ?? '';
                        $subObj->history           = $value['history'] ?? '';
                        $subObj->temples_to_visit  = $value['temples_to_visit'] ?? '';
                        $subObj->other_info        = $value['other_info'] ?? '';
                        $subObj->save();
                    }
                }
    
                // ✅ Final response
                if (!$SavedResponse) {
                    Session()->flash('error', trans("Something went wrong."));
                    return Redirect()->back()->withInput();
                } else {
                    Session()->flash('success', ucfirst(Config('constants.FESTIVAL.FESTIVAL_TITLE') . " has been added successfully"));
                    return Redirect()->route($this->model . ".index");
                }
            }
        }
    }
    
    

    // public function edit(Request $request,  $enuserid = null)
    // { 
    //     $festival_id = '';
    //     $multiLanguage =    array();
    //     if (!empty($enuserid)) {
    //         $festival_id = base64_decode($enuserid);
    //         $festivalDetails         =   Festival::find($festival_id);
    //         $cmsdescriptiondetl = FestivalDescription::where('parent_id', $festival_id)->get();
    //         if (!empty($cmsdescriptiondetl)) {
    //             foreach ($cmsdescriptiondetl as $description) {
    //                 $multiLanguage[$description->language_id]['name']    =    $description->name;
    //                 $multiLanguage[$description->language_id]['short_desc']    =    $description->description;
    //                 $multiLanguage[$description->language_id]['long_desc']    =    $description->long_description;
    //             }
    //         }
    //         $languages = Language::where('is_active', 1)->get();
    //         $temples    = Temple::where('is_deleted',0)->get();
    //         $language_code = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
    //         return View("admin.$this->model.edit", compact('multiLanguage', 'cmsdescriptiondetl', 'festivalDetails', 'languages', 'language_code','temples'));

    //     }else{
    //         return Redirect()->route($this->model . ".index");
    //     }
        
     
    // }
    
    
    public function edit(Request $request, $enuserid = null)
{
    $festival_id = '';
    $multiLanguage = [];

    if (!empty($enuserid)) {
        $festival_id = base64_decode($enuserid);
        $festivalDetails = Festival::find($festival_id);

        $cmsdescriptiondetl = FestivalDescription::where('parent_id', $festival_id)->get();

        if (!empty($cmsdescriptiondetl)) {
            foreach ($cmsdescriptiondetl as $description) {
                $multiLanguage[$description->language_id]['name']               = $description->name;
                $multiLanguage[$description->language_id]['short_desc']         = $description->description;
                $multiLanguage[$description->language_id]['long_desc']          = $description->long_description;
                $multiLanguage[$description->language_id]['regional_names']     = $description->regional_names ?? '';
                $multiLanguage[$description->language_id]['states_celebrated']  = $description->states_celebrated ?? '';
                $multiLanguage[$description->language_id]['duration']           = $description->duration ?? '';
                $multiLanguage[$description->language_id]['daily_significance'] = $description->daily_significance ?? '';
                $multiLanguage[$description->language_id]['history']            = $description->history ?? '';
                $multiLanguage[$description->language_id]['temples_to_visit']   = $description->temples_to_visit ?? '';
                $multiLanguage[$description->language_id]['other_info']         = $description->other_info ?? '';
            }
        }

        $languages = Language::where('is_active', 1)->get();
        $temples = Temple::where('is_deleted', 0)->get();
        $language_code = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
        $states = \App\Models\State::get(); // Add this

        return view("admin.$this->model.edit", compact(
            'multiLanguage',
            'cmsdescriptiondetl',
            'festivalDetails',
            'languages',
            'language_code',
            'temples',
            'states'
        ));
    } else {
        return redirect()->route($this->model . ".index");
    }
}

    
    // public function update(Request $request,  $enuserid = null){
    //     $festival_id = '';
    //     $multiLanguage =    array();
    //     if (!empty($enuserid)) {
    //       $festival_id = base64_decode($enuserid);
    //         $thisData                    =    $request->all();
            
    //         $default_language            =    Config('constants.DEFAULT_LANGUAGE.FOLDER_CODE');
    //         $language_code                 =   Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
    //         $dafaultLanguageArray        =    $thisData['data'][$language_code];
            
    //           $validator = Validator::make(
    //             array(
    //               'name'              => $dafaultLanguageArray['name'],
    //             ),
    //             array(
    //               'name'             => 'required',
    //             )
    //         );
    //         if ($validator->fails()) {
    //             return redirect()->back()->withErrors($validator)->withInput();
    //         }else{
    //              $festival                               =   Festival::where("id",$festival_id)->first();
    //             $festival->name                         =   $dafaultLanguageArray['name'] ?? '';
    //             $festival->date                         =   $request->date;
    //              $festival->description                 =   $dafaultLanguageArray['long_dec'] ?? '';
    //             if(!empty($request->temple_id)){
    //                  $festival->temple_id   =   json_encode($request->input('temple_id'));
    //             }
    //              $festival->short_dec                 =   $dafaultLanguageArray['short_dec'] ?? '';
    //              $festival->long_dec                 =   $dafaultLanguageArray['long_dec'] ?? '';
                 
    //             if ($request->hasFile('image')) {
    //                 $extension = $request->file('image')->getClientOriginalExtension();
    //                 $fileName = time() . '-image.' . $extension;
    //                 $folderName = strtoupper(date('M') . date('Y')) . "/";
    //                 $folderPath = Config('constants.FESTIVAL_IMAGE_ROOT_PATH') . $folderName;
                    
    //                 if ($request->file('image')->move($folderPath, $fileName)) {
    //                     $festival->image = $folderName . $fileName;
    //                 }
    //             }
    //             $SavedResponse = $festival->save();
    //             $lastId = $festival->id;
    //              FestivalDescription::where("parent_id", $lastId)->delete();
    //             if (!empty($thisData)) {
    //                 foreach ($thisData['data'] as $language_id => $value) {
    //                     $subObj                =    new FestivalDescription();
    //                     $subObj->language_id    = $language_id;
    //                     $subObj->parent_id      = $lastId;
    //                     $subObj->name           = $value['name'];
    //                     $subObj->description    = $value['short_desc'] ?? '';
    //                     $subObj->long_description = $value['long_desc'] ?? '';
    //                     $subObj->save();
    //                 }
    //             }
    //              if (!$SavedResponse) {
    //                 Session()->flash('error', trans("Something went wrong."));
    //                 return Redirect()->back()->withInput();
    //             } else {
    //                 Session()->flash('success', ucfirst(Config('constants.FESTIVAL.FESTIVAL_TITLE')." has been updated successfully"));
    //                 return Redirect()->route($this->model . ".index");
    //             }
    //         }
            
    //     }
       
    // }
    public function update(Request $request, $enuserid = null)
{
    if (empty($enuserid)) {
        Session()->flash('error', trans("Invalid festival ID."));
        return Redirect()->back();
    }

    $festival_id = base64_decode($enuserid);
    $thisData = $request->all();

    $default_language = Config('constants.DEFAULT_LANGUAGE.FOLDER_CODE');
    $language_code = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
    $dafaultLanguageArray = $thisData['data'][$language_code];

    // ✅ Validation — only name required
    $validator = Validator::make(
        [
            'name'  => $dafaultLanguageArray['name'] ?? '',
        ],
        [
            'name'  => 'required',
        ]
    );

    if ($validator->fails()) {
        return Redirect::back()->withErrors($validator)->withInput();
    }

    // ✅ Find existing record
    $festival = Festival::where("id", $festival_id)->first();
    if (!$festival) {
        Session()->flash('error', trans("Festival not found."));
        return Redirect()->back();
    }

    // ✅ Update base (non-language) fields
    $festival->date                = $request->input('date');
    $festival->name                = $dafaultLanguageArray['name'] ?? '';
    $festival->regional_names      = $dafaultLanguageArray['regional_names'] ?? '';
      $festival->description                 =   $dafaultLanguageArray['long_dec'] ?? '';
        $festival->states              = $request->input('states'); 
                if(!empty($request->temple_id)){
                     $festival->temple_id   =   json_encode($request->input('temple_id'));
                }
                 $festival->short_dec                 =   $dafaultLanguageArray['short_dec'] ?? '';
                 $festival->long_dec                 =   $dafaultLanguageArray['long_dec'] ?? '';
    $festival->description         = $dafaultLanguageArray['long_dec'] ?? '';
    $festival->states_celebrated   = $dafaultLanguageArray['states_celebrated'] ?? '';
    $festival->duration            = $dafaultLanguageArray['duration'] ?? '';
    $festival->daily_significance  = $dafaultLanguageArray['daily_significance'] ?? '';
    $festival->history             = $dafaultLanguageArray['history'] ?? '';
    $festival->temples_to_visit    = $dafaultLanguageArray['temples_to_visit'] ?? '';
    $festival->other_info          = $dafaultLanguageArray['other_info'] ?? '';

    // ✅ Handle image upload
    if ($request->hasFile('image')) {
        $extension  = $request->file('image')->getClientOriginalExtension();
        $fileName   = time() . '-image.' . $extension;
        $folderName = strtoupper(date('M') . date('Y')) . "/";
        $folderPath = Config('constants.FESTIVAL_IMAGE_ROOT_PATH') . $folderName;

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        if ($request->file('image')->move($folderPath, $fileName)) {
            $festival->image = $folderName . $fileName;
        }
    }

    // ✅ Save main table
    $SavedResponse = $festival->save();
    $lastId = $festival->id;

    // ✅ Update multilingual table
    FestivalDescription::where("parent_id", $lastId)->delete();

    if (!empty($thisData['data'])) {
        foreach ($thisData['data'] as $language_id => $value) {
            $subObj = new FestivalDescription();
            $subObj->language_id        = $language_id;
            $subObj->parent_id          = $lastId;
            $subObj->name               = $value['name'] ?? '';
            $subObj->description    = $value['short_desc'] ?? '';
            $subObj->long_description = $value['long_desc'] ?? '';
            $subObj->regional_names     = $value['regional_names'] ?? '';
            $subObj->states_celebrated  = $value['states_celebrated'] ?? '';
            $subObj->duration           = $value['duration'] ?? '';
            $subObj->daily_significance = $value['daily_significance'] ?? '';
            $subObj->history            = $value['history'] ?? '';
            $subObj->temples_to_visit   = $value['temples_to_visit'] ?? '';
            $subObj->other_info         = $value['other_info'] ?? '';
            $subObj->save();
        }
    }

    // ✅ Final response
    if (!$SavedResponse) {
        Session()->flash('error', trans("Something went wrong."));
        return Redirect()->back()->withInput();
    }

    Session()->flash('success', ucfirst(Config('constants.FESTIVAL.FESTIVAL_TITLE') . " has been updated successfully"));
    return Redirect()->route($this->model . ".index");
}

    
    public function destroy( $enuserid)
    {
        $festival_id = '';
        if (!empty($enuserid)) {
            $festival_id = base64_decode($enuserid);
        }
        $userDetails   =   Festival::find($festival_id);
        if (empty($userDetails)) {
            return Redirect()->route($this->model . '.index');
        }
        if ($festival_id) {

            Festival::where('id', $festival_id)->update(array(
                'is_deleted'    => 1, 
            ));

            Session()->flash('flash_notice', trans(ucfirst( "Festival has been removed successfully")));
        }
        return back();
    }
    public function changeStatus($modelId = 0, $status = 0)
    {
        if ($status == 1) {
            $statusMessage   =   trans(Config('constants.TEMPLE.TEMPLE_TITLE'). " has been deactivated successfully");
        } else {
            $statusMessage   =   trans(Config('constants.TEMPLE.TEMPLE_TITLE'). " has been activated successfully");
        }
        $temple = Temple::find($modelId);
        if ($temple) {
            $currentStatus = $temple->is_active;
            if (isset($currentStatus) && $currentStatus == 0) {
                $NewStatus = 1;
            } else {
                $NewStatus = 0;
            }
            $temple->is_active = $NewStatus;
            $ResponseStatus = $temple->save();
        }
        Session()->flash('flash_notice', $statusMessage);
        return back();
    }
   
    public function view($enuserid = null)
    {
        $festival_id = '';
        if (!empty($enuserid)) {
            $festival_id = base64_decode($enuserid);
        } else {
            return redirect()->route($this->model . ".index");
        }
        $festivalDetails    =    Festival::where('id', $festival_id)->first();
        return  View("admin.$this->model.view", compact('festivalDetails'));
    }
       

      public function festivalTemples(Request $request,$festival_id)
    {
        $festival_id        =   base64_decode($festival_id);
        $DB					=	FestivalTemple::query();
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
                $DB->whereBetween('festivals_temple.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('festivals_temple.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('festivals_temple.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                   
                    
                }
                $searchVariable	=	array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }

        $DB->where('festival_id',$festival_id);
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'festivals_temple.created_at';
        $order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
        $records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
        $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        $complete_string		=	$request->query();
        unset($complete_string["sortBy"]);
        unset($complete_string["order"]);
        $query_string			=	http_build_query($complete_string);
        // dd($results);
        $results->appends($inputGet)->render();
        $resultcount = $results->count();
        return  View("admin.$this->model.festival_temples", compact('resultcount', 'results', 'searchVariable', 'sortBy', 'order', 'query_string','festival_id'));
    }

    public function festivalTempleCreate($festival_id)
    {       
        $temples    = Temple::where('is_deleted',0)->get();
        $festival_id = base64_decode($festival_id);
        return  View("admin.$this->model.temple_add",compact('temples','festival_id'));
    }
    public function festivalTempleSave(Request $request,$festival_id){
        $festival_id = base64_decode($festival_id);
        if ($request->isMethod('POST')) {
            $thisData = $request->all();
            $validator                    =   Validator::make(
                $request->all(), 
                array(
                    'temple_id'              => "required",
                ),
                array(
                    "temple_id.required"     => trans("please select the temple."),
                )
            );
            
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            }else{
                $festival                               =   new FestivalTemple;
                $festival->festival_id                         =   $festival_id;
                $festival->temple_id                         =   $request->input('temple_id');
                $SavedResponse = $festival->save();
                
                if (!$SavedResponse) {
                    Session()->flash('error', trans("Something went wrong."));
                    return Redirect()->back()->withInput();
                } else {
                    Session()->flash('success', ucfirst("Temple has been added successfully"));
                    return Redirect()->route("festivals.festivalTemples",base64_encode($festival_id));
                }
            }
        } 
    }
    public function festivalTempleDestroy( $enuserid)
    {
        $festival_id = '';
        if (!empty($enuserid)) {
            $festival_id = base64_decode($enuserid);
        }
        $userDetails   =   FestivalTemple::find($festival_id);
        if (empty($userDetails)) {
            return Redirect()->route($this->model . '.index');
        }
        if ($festival_id) {

            FestivalTemple::where('id', $festival_id)->delete();
            Session()->flash('flash_notice', trans(ucfirst( "Festival Temple has been removed successfully")));
        }
        return back();
    }
}
