<?php
namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use App\Models\CaseStudy;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Illuminate\Http\Request;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Redirect,Response,Session,URL,View,Validator;
/**
* CaseStudyController Controller
*
* Add your methods in the class below
*
* This file will render views from views/adminpnlxSeoPage
*/
class CaseStudyController extends Controller {

	public $model      =   'CaseStudy';
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
	$DB							=	CaseStudy::query();
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
                        $DB->where("case_studies.title", 'like', '%' . $fieldValue . '%');
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
	return  View::make('admin.CaseStudy.index',compact('results','searchVariable','sortBy','order','query_string'));
	}// end listDoc()
/**
* Function for display page  for add new seo
*
* @param null
*
* @return view page. 
*/
public function add(){
    	
	
	return  View::make('admin.CaseStudy.add');
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
		$obj 					=  new CaseStudy;
		$obj->title   			= $request->input('title');
		$obj->save();

		Session::flash('flash_notice', trans("Case Study added successfully")); 
		return Redirect::to('adminpnlx/case-studies');
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
	$docs				=	CaseStudy::where('id',$ids)->first();
	if(empty($docs)) {
		return Redirect::to('adminpnlx/case-studies');
	}
	 return  View::make('admin.CaseStudy.edit',array('doc'=>$docs));
	}// end editBlock()
/**
* Function for update seo 
*
* @param $Id ad id of seo 
*
* @return redirect page. 
*/
function update($Id,Request $request){
$docs				=	CaseStudy::find($Id);
	if(empty($docs)) {
		return Redirect::to('adminpnlx/case-studies');
	}
	$request->replace($this->arrayStripTags($request->all()));
	$this_data				=	$request->all();
	$doc 					= 	Service:: find($Id);
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

		$Seo_response		=	CaseStudy::where('id', $Id)->update(array(
			'title' 			=>  $request->input('title'),
		));

			Session::flash('flash_notice',  trans("Case Study updated successfully")); 
			return Redirect::intended('adminpnlx/case-studies');
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
		$delete_item = CaseStudy::where('id',$ids)->update(array('is_deleted' => 1,));
      Session::flash('flash_notice', trans("Case Study has been removed successfully"));
	  return Redirect::to('adminpnlx/case-studies');
	}// end deleteSeoPage()
/**
* Function for delete multiple seo
*
* @param null
*
* @return view page. 
*/



	
}// end BlockController	