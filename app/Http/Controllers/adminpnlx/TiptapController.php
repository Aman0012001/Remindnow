<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use App\Models\Tiptap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\App;
use Config;
use Carbon\Carbon;
use Redirect, Session;

class TiptapController extends Controller
{
    public $model = 'tiptaps';
    public $sectionNameSingular = 'tiptaps';
    
    public function __construct(Request $request)
    {   
        parent::__construct();
        View()->share('model', $this->model);
        View()->share('sectionNameSingular', $this->sectionNameSingular);
        $this->request = $request;
    }
    
    public function index(Request $request)
    {
        $DB = Tiptap::query();
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
                $DB->whereBetween('tiptaps.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('tiptaps.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('tiptaps.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "title") {
                        $DB->where("tiptaps.title", 'like', '%' . $fieldValue . '%');
                    }
                }
                $searchVariable = array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }

        $DB->where("tiptaps.is_deleted", 0);
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'tiptaps.created_at';
        $order = ($request->input('order')) ? $request->input('order') : 'DESC';
        $records_per_page = ($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
        $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        $complete_string = $request->query();
        unset($complete_string["sortBy"]);
        unset($complete_string["order"]);
        $query_string = http_build_query($complete_string);
        $results->appends($inputGet)->render();
        $resultcount = $results->count();
        
        return View("admin.$this->model.index", compact('resultcount', 'results', 'searchVariable', 'sortBy', 'order', 'query_string'));
    }
    
    public function create(Request $request)
    {
        return View("admin.$this->model.create");
    }
    
    public function store(Request $request)
    {
        if ($request->isMethod('POST')) {
            $validator = Validator::make(
                array(
                    'title' => $request->input('title'),
                    'image' => $request->hasFile('image') ? $request->file('image') : '',
                ),
                array(
                    'title' => 'required|string|max:255',
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                )
            );
            
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            } else {
                $tiptap = new Tiptap;
                $tiptap->title = $request->input('title');
                $tiptap->is_active = $request->has('is_active') ? 1 : 0;
                
                if ($request->hasFile('image')) {
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $fileName = time() . '-image.' . $extension;
                    $folderName = strtoupper(date('M') . date('Y')) . "/";
                    $folderPath = Config::get('constants.TIPTAP_IMAGE_ROOT_PATH') . $folderName;
                    
                    // Create directory if it doesn't exist
                    if (!File::exists($folderPath)) {
                        File::makeDirectory($folderPath, 0777, true, true);
                    }
                    
                    if ($request->file('image')->move($folderPath, $fileName)) {
                        $tiptap->image = $folderName . $fileName;
                    }
                }
                
                $SavedResponse = $tiptap->save();
                
                if (!$SavedResponse) {
                    Session()->flash('error', trans("Something went wrong."));
                    return Redirect()->back()->withInput();
                } else {
                    Session()->flash('success', ucfirst(Config::get('constants.TIPTAP.TIPTAP_TITLE') . " has been added successfully"));
                    return Redirect()->route($this->model . ".index");
                }
            }
        }
    }
    
    public function edit(Request $request, $enuserid = null)
    {
        $tiptap_id = '';
        if (!empty($enuserid)) {
            $tiptap_id = base64_decode($enuserid);
            $tiptapDetails = Tiptap::find($tiptap_id);
            
            if ($tiptapDetails) {
                return View("admin.$this->model.edit", compact('tiptapDetails'));
            }
        }
        
        return Redirect()->route($this->model . ".index");
    }
    
    public function update(Request $request, $enuserid = null)
    {
        $tiptap_id = '';
        if (!empty($enuserid)) {
            $tiptap_id = base64_decode($enuserid);
            
            $validator = Validator::make(
                array(
                    'title' => $request->input('title'),
                ),
                array(
                    'title' => 'required|string|max:255',
                )
            );
            
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            } else {
                $tiptap = Tiptap::where("id", $tiptap_id)->first();
                $tiptap->title = $request->input('title');
                $tiptap->is_active = $request->has('is_active') ? 1 : 0;
                
                if ($request->hasFile('image')) {
                    // Delete old image if exists
                    if (!empty($tiptap->image) && File::exists(Config::get('constants.TIPTAP_IMAGE_ROOT_PATH') . $tiptap->image)) {
                        File::delete(Config::get('constants.TIPTAP_IMAGE_ROOT_PATH') . $tiptap->image);
                    }
                    
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $fileName = time() . '-image.' . $extension;
                    $folderName = strtoupper(date('M') . date('Y')) . "/";
                    $folderPath = Config::get('constants.TIPTAP_IMAGE_ROOT_PATH') . $folderName;
                    
                    // Create directory if it doesn't exist
                    if (!File::exists($folderPath)) {
                        File::makeDirectory($folderPath, 0777, true, true);
                    }
                    
                    if ($request->file('image')->move($folderPath, $fileName)) {
                        $tiptap->image = $folderName . $fileName;
                    }
                }
                
                $SavedResponse = $tiptap->save();
                
                if (!$SavedResponse) {
                    Session()->flash('error', trans("Something went wrong."));
                    return Redirect()->back()->withInput();
                } else {
                    Session()->flash('success', ucfirst(Config::get('constants.TIPTAP.TIPTAP_TITLE') . " has been updated successfully"));
                    return Redirect()->route($this->model . ".index");
                }
            }
        }
    }
    
    public function destroy($enuserid)
    {
        $tiptap_id = '';
        if (!empty($enuserid)) {
            $tiptap_id = base64_decode($enuserid);
        }
        
        $tiptapDetails = Tiptap::find($tiptap_id);
        if (empty($tiptapDetails)) {
            return Redirect()->route($this->model . '.index');
        }
        
        if ($tiptap_id) {
            // Delete image if exists
            if (!empty($tiptapDetails->image) && File::exists(Config::get('constants.TIPTAP_IMAGE_ROOT_PATH') . $tiptapDetails->image)) {
                File::delete(Config::get('constants.TIPTAP_IMAGE_ROOT_PATH') . $tiptapDetails->image);
            }
            
            Tiptap::where('id', $tiptap_id)->update(array(
                'is_deleted' => 1,
            ));
            
            Session()->flash('flash_notice', trans(ucfirst(Config::get('constants.TIPTAP.TIPTAP_TITLE') . " has been removed successfully")));
        }
        return back();
    }
    
   public function changeStatus($modelId = 0, $status = 0)
{
    if ($status == 1) {
        $statusMessage = trans(Config::get('constants.TIPTAP.TIPTAP_TITLE') . " has been activated successfully");
    } else {
        $statusMessage = trans(Config::get('constants.TIPTAP.TIPTAP_TITLE') . " has been deactivated successfully");
    }
    
    $tiptap = Tiptap::find($modelId);
    if ($tiptap) {
        $currentStatus = $tiptap->is_active;
        if (isset($currentStatus) && $currentStatus == 0) {
            $NewStatus = 1;
        } else {
            $NewStatus = 0;
        }
        $tiptap->is_active = $NewStatus;
        $ResponseStatus = $tiptap->save();
    }
    Session()->flash('flash_notice', $statusMessage);
    return back();
}
    
    public function show($enuserid = null)
    {
        $tiptap_id = '';
        if (!empty($enuserid)) {
            $tiptap_id = base64_decode($enuserid);
        } else {
            return redirect()->route($this->model . ".index");
        }
        
        $tiptapDetails = Tiptap::where('id', $tiptap_id)->first();
        return View("admin.$this->model.show", compact('tiptapDetails'));
    }
}