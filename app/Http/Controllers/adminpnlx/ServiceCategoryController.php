<?php
namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Illuminate\Http\Request;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Redirect,Response,Session,URL,View,Validator;
/**
* ServiceCategoryController Controller
*
* Add your methods in the class below
*
* This file will render views from views/adminpnlxSeoPage
*/
class ServiceCategoryController extends Controller {

	public $model      =   'ServiceCategory';
    public function __construct(Request $request)
    {   
        parent::__construct();
        View()->share('model', $this->model);
        $this->request = $request;
    }
/**
* Function for display all Document 
*
* @param null
*
* @return view page. 
*/
public function index(Request $request){	
	$DB							=	ServiceCategory::query();
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
            
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "title") {
                        $DB->where("services_categories.title", 'like', '%' . $fieldValue . '%');
                    }
                    
                }
                $searchVariable = array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }
        $DB->where('is_deleted',0);
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
        $order = ($request->input('order')) ? $request->input('order') : 'DESC';
        $records_per_page = ($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
        $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);       
        $complete_string = $request->query();
        unset($complete_string["sortBy"]);
        unset($complete_string["order"]);
        $query_string = http_build_query($complete_string);
        $results->appends($inputGet)->render();
	return  View::make('admin.ServiceCategory.index',compact('results','searchVariable','sortBy','order','query_string'));
	}// end listDoc()
/**
* Function for display page  for add new seo
*
* @param null
*
* @return view page. 
*/
public function add(){
    	
	
	return  View::make('admin.ServiceCategory.add');
	} //end add()
/**
* Function for save document
*
* @param null
*
* @return redirect page. 
*/
function save(Request $request){
	$request->replace($this->arrayStripTags($request->all()));
	$thisData					=	$request->all();


   if (!empty($thisData)) {
	$validator = Validator::make(
         $request->all(),
		array(
			'title' 			=> 'required',
			
		),
		array(
			"title.required"				=>	trans("The title field is required."),				
		)
	);
    if($validator->fails()){
  
        return Redirect::back()->withErrors($validator)->withInput();
      }else{
		$obj 					=  new ServiceCategory;
		$obj->title   			= $request->input('title');
		$obj->save();

		Session::flash('flash_notice', trans("Service Category page added successfully")); 
		return Redirect::to('adminpnlx/service-categories');
		}
      }
	}//end saveBlock()
/**
* Function for display page  for edit seo
*
* @param $Id ad id 
*
* @return view page. 
*/	
public function edit($Id){
	$ids	= base64_decode($Id); 
	$docs				=	ServiceCategory::where('id',$ids)->first();
	if(empty($docs)) {
		return Redirect::to('adminpnlx/service-categories');
	}
	 return  View::make('admin.ServiceCategory.edit',array('doc'=>$docs));
	}// end editBlock()
/**
* Function for update seo 
*
* @param $Id ad id of seo 
*
* @return redirect page. 
*/
function update($Id,Request $request){
$docs				=	ServiceCategory::find($Id);
	if(empty($docs)) {
		return Redirect::to('adminpnlx/service-categories');
	}
	$request->replace($this->arrayStripTags($request->all()));
	$this_data				=	$request->all();
	$doc 					= 	ServiceCategory:: find($Id);
	$validator = Validator::make(
        $request->all(),
		array(
			'title' 			=> 'required',
			),
		array(
			"title.required"				=>	trans("The title field is required."),				
		)
	);
	if ($validator->fails()){	
		return Redirect::back()
		->withErrors($validator)->withInput();
	}else{

		$Seo_response		=	ServiceCategory::where('id', $Id)->update(array(
			'title' 			=>  $request->input('title'),
		));

			Session::flash('flash_notice',  trans("Service Category updated successfully")); 
			return Redirect::intended('adminpnlx/service-categories');
      }
	}// end updateSeoPage()
/**
* Function for update seo  status
*
* @param $Id as id of seo 
* @param $Status as status of seo 
*
* @return redirect page. 
*/	
public function delete($modelId, Request $request){
	
	$ids	= base64_decode($modelId);
		$delete_item = ServiceCategory::where('id',$ids)->update(array('is_deleted' => 1,));
      Session::flash('flash_notice', trans("Service Category has been removed successfully"));
	  return Redirect::to('adminpnlx/service-categories');
	}// end deleteSeoPage()
/**
* Function for delete multiple seo
*
* @param null
*
* @return view page. 
*/



	
}// end BlockController	