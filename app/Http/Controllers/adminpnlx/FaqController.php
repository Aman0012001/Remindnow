<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Language;
use App\Models\Festival;
use App\Models\FestivalFaq;
use App\Models\Faq;
use App\Models\Faq_description;

class FaqController extends Controller
{
    public $model = 'faqs';

    public function __construct(Request $request)
    {
        parent::__construct();
        view()->share('model', $this->model);
        $this->request = $request;
    }

    public function index(Request $request)
    {
        $DB = Faq::query();
        $searchVariable = [];
        $inputGet = $request->all();

        if ($request->all()) {
            $searchData = $request->all();
            unset($searchData['display'], $searchData['_token']);

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
                    if ($fieldName == "question") {
                        $DB->where("faqs.question", 'like', '%' . $fieldValue . '%');
                    }

                    if ($fieldName == "answer") {
                        $DB->where("faqs.answer", 'like', '%' . $fieldValue . '%');
                    }
                }

                $searchVariable = array_merge($searchVariable, [$fieldName => $fieldValue]);
            }
        }
        if(!empty($request->id)){
            $DB->where('is_festival',base64_decode($request->id));
        }
        $sortBy = $request->input('sortBy') ?? 'created_at';
        $order = $request->input('order') ?? 'DESC';
        $records_per_page = $request->input('per_page') ?? config("Reading.records_per_page");

        $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);

        $complete_string = $request->query();
        unset($complete_string["sortBy"], $complete_string["order"]);

        $query_string = http_build_query($complete_string);

        $results->appends($inputGet)->render();
        
        return view("admin.$this->model.index", compact('results', 'searchVariable', 'sortBy', 'order', 'query_string','request'));
    }
    
    public function festivalIndex(Request $request)
    {
        $DB = Faq::query();
        $searchVariable = [];
        $inputGet = $request->all();

        if ($request->all()) {
            $searchData = $request->all();
            unset($searchData['display'], $searchData['_token']);

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
                    if ($fieldName == "question") {
                        $DB->where("faqs.question", 'like', '%' . $fieldValue . '%');
                    }

                    if ($fieldName == "answer") {
                        $DB->where("faqs.answer", 'like', '%' . $fieldValue . '%');
                    }
                }

                $searchVariable = array_merge($searchVariable, [$fieldName => $fieldValue]);
            }
        }
        if(!empty($request->id)){
            $DB->where('is_festival',base64_decode($request->id));
        }
        $sortBy = $request->input('sortBy') ?? 'created_at';
        $order = $request->input('order') ?? 'DESC';
        $records_per_page = $request->input('per_page') ?? config("Reading.records_per_page");

        $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);

        $complete_string = $request->query();
        unset($complete_string["sortBy"], $complete_string["order"]);

        $query_string = http_build_query($complete_string);

        $results->appends($inputGet)->render();
        
        return view("admin.$this->model.festival_index", compact('results', 'searchVariable', 'sortBy', 'order', 'query_string','request'));
    }

    public function create(Request $request)
    {
        $languages = Language::where('is_active', 1)->get();
        $language_code = config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
        $festival_id = !empty($request->id) ? base64_decode($request->id) : null;
        return view("admin.$this->model.add", compact('languages', 'language_code','festival_id'));
    }

    public function store(Request $request)
    {
        $thisData = $request->all();
        $language_code = config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
        $dafaultLanguageArray = $thisData['data'][$language_code];

        $validator = Validator::make([
            'faq_order' => $request->input('faq_order'),
            'question'  => $dafaultLanguageArray['question'],
            'answer'    => $dafaultLanguageArray['answer'],
        ], [
            'faq_order' => 'required|numeric',
            'question'  => 'required',
            'answer'    => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $obj = new Faq;
        $obj->faq_order = $request->input('faq_order');
        $obj->question  = $dafaultLanguageArray['question'];
        $obj->answer    = $dafaultLanguageArray['answer'];
        $obj->is_active = 1;
        $obj->is_festival   = $request->id;
        $obj->save();

        $lastId = $obj->id;

        if (!empty($thisData)) {
            foreach ($thisData['data'] as $language_id => $value) {
                $subObj = new Faq_description();
                $subObj->language_id = $language_id;
                $subObj->parent_id = $lastId;
                $subObj->question = $value['question'];
                $subObj->answer = $value['answer'];
                $subObj->save();
            }
        }

        session()->flash('success', config('constants.FAQ.FAQ_TITLE') . " has been added successfully");

        if(!empty($request->id)){
            return redirect()->route($this->model . ".festivalIndex",'id='.base64_encode($obj->is_festival));    
        }
        return redirect()->route($this->model . ".index");
    }

    public function show($encmsid)
    {
        if (empty($encmsid)) {
            return redirect()->route($this->model . ".index");
        }

        $cms_id = base64_decode($encmsid);
        $FaqDetails = Faq::find($cms_id);

        return view("admin.$this->model.view", compact('FaqDetails'));
    }

    public function edit($enfaqid)
    {
        if (empty($enfaqid)) {
            return redirect()->route($this->model . ".index");
        }

        $faq_id = base64_decode($enfaqid);
        $faqDetails = Faq::find($faq_id);
        $Faq_descriptiondetl = Faq_description::where('parent_id', $faq_id)->get();
        $multiLanguage = [];

        foreach ($Faq_descriptiondetl as $description) {
            $multiLanguage[$description->language_id] = [
                'question' => $description->question,
                'answer'   => $description->answer,
            ];
        }

        $languages = Language::where('is_active', 1)->get();
        $language_code = config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');

        return view("admin.$this->model.edit", compact('multiLanguage', 'Faq_descriptiondetl', 'faqDetails', 'languages', 'language_code'));
    }

    public function update(Request $request, $enfaqid)
    {
        if (empty($enfaqid)) {
            return redirect()->route($this->model . ".index");
        }

        $faq_id = base64_decode($enfaqid);
        $thisData = $request->all();
        $language_code = config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
        $dafaultLanguageArray = $thisData['data'][$language_code];

        $validator = Validator::make([
            'faq_order' => $request->input('faq_order'),
            'question'  => $dafaultLanguageArray['question'],
            'answer'    => $dafaultLanguageArray['answer'],
        ], [
            'faq_order' => 'required|numeric',
            'question'  => 'required',
            'answer'    => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $obj = Faq::find($faq_id);
        $obj->faq_order = $request->input('faq_order');
        $obj->question = $dafaultLanguageArray['question'];
        $obj->answer   = $dafaultLanguageArray['answer'];
        $obj->is_active = 1;
        $obj->save();

        Faq_description::where("parent_id", $obj->id)->delete();

        foreach ($thisData['data'] as $language_id => $value) {
            $subObj = new Faq_description();
            $subObj->language_id = $language_id;
            $subObj->parent_id = $obj->id;
            $subObj->question = $value['question'];
            $subObj->answer = $value['answer'];
            $subObj->save();
        }

        session()->flash('success', config('constants.FAQ.FAQ_TITLE') . " has been updated successfully");
        if(!empty($obj->is_festival)){
            return redirect()->route($this->model . ".festivalIndex",'id='.base64_encode($obj->is_festival));    
        }
        return redirect()->route($this->model . ".index");
    }

    public function destroy($enfaqid)
    {
        if (empty($enfaqid)) {
            return redirect()->route($this->model . ".index");
        }

        $faq_id = base64_decode($enfaqid);
        Faq::find($faq_id)->delete();
        Faq_description::where("parent_id", $faq_id)->delete();

        session()->flash('flash_notice', config('constants.FAQ.FAQ_TITLE') . " has been removed successfully");

        return back();
    }

    // === New Festival FAQ Methods ===

    public function festivalFaqs(Request $request, $festival_id = null)
    {
        if ($request->isMethod('post')) {
            if (!empty($request->question)) {
                foreach ($request->question as $index => $question) {
                    $faq = new FestivalFaq();
                    $faq->festival_id = $festival_id;
                    $faq->question = $question;
                    $faq->answer = $request->answer[$index];
                    $faq->save();
                }

                return redirect()->back()->with('success', 'FAQs Created Successfully');
            }
        }

        $festival = Festival::where('id', $festival_id)->first();
        $festivalFqs = FestivalFaq::where('festival_id', $festival_id)->orderBy('id')->get();

        return view('admin.festival_faqs.festival-faqs', compact('festivalFqs', 'festival'));
    }


}
