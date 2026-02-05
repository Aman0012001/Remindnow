@extends('admin.layouts.layout')
@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
@php $counter = 0; @endphp
<style>
    .invalid-feedback {
        display: inline;
    }
    .AClass {
        right:10px;
        position: absolute;
    }
</style>
<style>
    .invalid-feedback {
        display: inline;
    }
    /* Fixed Select2 height and overflow handling */
    .select2-container--default .select2-selection--multiple {
        min-height: 48px;
        height: auto !important;
        border: 1px solid #e4e6ef;
        border-radius: 0.42rem;
        overflow: hidden;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        display: block !important;
        padding: 5px 5px 0 5px !important;
        line-height: 1.5 !important;
        min-height: 38px;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        display: inline-flex;
        align-items: center;
        background-color: #3699ff;
        border: 1px solid #3699ff;
        color: #fff;
        border-radius: 0.25rem;
        padding: 0.25rem 0.5rem;
        margin: 0 5px 5px 0;
        max-width: 100%;
        word-break: break-word;
        line-height: 1.3;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: rgba(255, 255, 255, 0.7);
        margin-right: 5px;
        order: -1;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #fff;
    }
    
    /* Ensure the input field stays in line */
    .select2-container--default .select2-search--inline {
        display: inline-block;
        margin-bottom: 5px;
    }
    
    .select2-container--default .select2-search--inline .select2-search__field {
        margin-top: 0 !important;
        height: 26px;
        min-width: 150px;
    }
    
    /* Container height grows with content */
    .select2-container .select2-selection--multiple {
        min-height: 48px;
        height: auto;
    }
    
    /* Fix for the form group to contain the growing select */
    .form-group {
        position: relative;
    }
    
    /* Clear any fixed heights that might cause issues */
    .select2-container {
        height: auto !important;
    }
</style>
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                        Edit {{ Config('constants.FESTIVAL.FESTIVAL_TITLE') }}
                    </h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard')}}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route($model.'.index')}}" class="text-muted">{{ Config('constants.FESTIVAL.FESTIVAL_TITLES') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
            @include("admin.elements.quick_links")
        </div>
    </div>

    <div class="d-flex flex-column-fluid">
        <div class="container">
            <form action="{{route($model.'.update',base64_encode($festivalDetails->id))}}" method="POST" class="mws-form" autocomplete="off" enctype="multipart/form-data">
                @csrf
                @method('POST')

                <!-- ===================== DATE & IMAGE ===================== -->
                {{-- <div class="card card-custom gutter-b">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <label for="date">Date</label><span class="text-danger">*</span>
                                    <input type="text" name="date" class="form-control" id="myDates" placeholder="Select multiple dates" value="{{ $festivalDetails->date ?? '' }}">
                                    @if ($errors->has('date'))
                                        <div class="invalid-feedback">{{ $errors->first('date') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-xl-6">
                                <div class="form-group m-0">
                                    <label for="image">Image</label><span class="text-danger">*</span>
                                    <input type="file" name="image" class="form-control form-control-solid form-control-lg @error('image') is-invalid @enderror">
                                    @if ($errors->has('image'))
                                        <div class="invalid-feedback">{{ $errors->first('image') }}</div>
                                    @endif
                                </div>

                                @if(!empty($festivalDetails->image))
                                    <a class="fancybox-buttons" data-fancybox-group="button" href="{{ Config('constants.FESTIVAL_IMAGE_PATH').$festivalDetails->image }}">
                                        <img src="{{ Config('constants.FESTIVAL_IMAGE_PATH').$festivalDetails->image }}" width="50" height="50">
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div> --}}

                <!-- ===================== DATE, STATES & IMAGE ===================== -->
<div class="card card-custom gutter-b">
    <div class="card-body">
        <div class="row">
            <div class="col-xl-4">
                <div class="form-group">
                    <label for="date">Date</label><span class="text-danger">*</span>
                    <input type="text" name="date" class="form-control"  id="myDates" placeholder="Select multiple dates" value="{{ $festivalDetails->date ?? '' }}">
                    @if ($errors->has('date'))
                        <div class="invalid-feedback">{{ $errors->first('date') }}</div>
                    @endif
                </div>
            </div>

            <!-- 🆕 States Selection -->
            <div class="col-xl-4">
                <div class="form-group">
                    <label for="states">States</label>
                    <select name="states[]" id="states" class="form-control form-control-solid form-control-lg select2" multiple="multiple" style="width: 100%;">
                        @if(!empty($states))
                            @foreach($states as $state)
                                <option value="{{ $state->id }}" 
                                    @if(!empty($festivalDetails->states) && in_array($state->id, $festivalDetails->states))
                                        selected
                                    @endif
                                >
                                    {{ $state->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @if ($errors->has('states'))
                        <div class="invalid-feedback">{{ $errors->first('states') }}</div>
                    @endif
                    <small class="form-text text-muted">Select multiple states where this festival is celebrated</small>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="form-group m-0">
                    <label for="image">Image</label><span class="text-danger">*</span>
                    <input type="file" name="image" class="form-control form-control-solid form-control-lg @error('image') is-invalid @enderror">
                    @if ($errors->has('image'))
                        <div class="invalid-feedback">{{ $errors->first('image') }}</div>
                    @endif
                </div>

                @if(!empty($festivalDetails->image))
                    <a class="fancybox-buttons" data-fancybox-group="button" href="{{ Config('constants.FESTIVAL_IMAGE_PATH').$festivalDetails->image }}">
                        <img src="{{ Config('constants.FESTIVAL_IMAGE_PATH').$festivalDetails->image }}" width="50" height="50">
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

                <!-- ===================== LANGUAGE TABS ===================== -->
                <div class="card card-custom gutter-b">
                    <div class="card-header card-header-tabs-line">
                        <div class="card-toolbar border-top">
                            <ul class="nav nav-tabs nav-bold nav-tabs-line">
                                @if(!empty($languages))
                                <?php $i = 1; ?>
                                @foreach($languages as $language)
                                    <li class="nav-item">
                                        <a class="nav-link {{($i==$language_code)?'active':'' }}" data-toggle="tab" href="#{{$language->title}}">
                                            <span class="symbol symbol-20 mr-3">
                                                <img src="{{url(Config::get('constants.LANGUAGE_IMAGE_PATH').$language->image)}}" alt="">
                                            </span>
                                            <span class="nav-text">{{$language->title}}</span>
                                        </a>
                                    </li>
                                <?php $i++; ?>
                                @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">
                            @if(!empty($languages))
                            <?php $i = 1; ?>
                            @foreach($languages as $language)
                            <div class="tab-pane fade {{($i==$language_code)?'show active':'' }}" id="{{$language->title}}" role="tabpanel" aria-labelledby="{{$language->title}}">
                                <div class="row">

                                    <!-- ✅ Required: Name -->
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="{{$language->id}}.name">Name</label><span class="text-danger">*</span>
                                            <input type="text" name="data[{{$language->id}}][name]" class="form-control form-control-solid form-control-lg @error('data.'.$language->id.'.name') is-invalid @enderror" value="{{$multiLanguage[$language->id]['name'] ?? old('data.'.$language->id.'.name')}}">
                                            @if ($errors->has('data.'.$language->id.'.name'))
                                                <div class="invalid-feedback">{{ $errors->first('data.'.$language->id.'.name') }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- 📝 Optional: Short Description -->
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <label>Short Description</label>
                                            <textarea id="short_desc_{{$language->id}}" name="data[{{$language->id}}][short_desc]" class="form-control form-control-solid form-control-lg">{{$multiLanguage[$language->id]['short_desc'] ?? old('data.'.$language->id.'.short_desc')}}</textarea>
                                        </div>
                                    </div>

                                    <!-- 📝 Optional: Long Description -->
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <label>Long Description</label>
                                            <textarea id="long_desc_{{$language->id}}" name="data[{{$language->id}}][long_desc]" class="form-control form-control-solid form-control-lg">{{$multiLanguage[$language->id]['long_desc'] ?? old('data.'.$language->id.'.long_desc')}}</textarea>
                                        </div>
                                    </div>

                                    <!-- 🆕 Optional: Regional Names -->
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label>Regional Names</label>
                                            <input type="text" name="data[{{$language->id}}][regional_names]" class="form-control form-control-solid form-control-lg" value="{{$multiLanguage[$language->id]['regional_names'] ?? old('data.'.$language->id.'.regional_names')}}" placeholder="Comma separated names">
                                        </div>
                                    </div>

                                    <!-- 🆕 Optional: States Celebrated -->
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label>States Celebrated</label>
                                            <input type="text" name="data[{{$language->id}}][states_celebrated]" class="form-control form-control-solid form-control-lg" value="{{$multiLanguage[$language->id]['states_celebrated'] ?? old('data.'.$language->id.'.states_celebrated')}}" placeholder="Comma separated states">
                                        </div>
                                    </div>

                                    <!-- 🆕 Optional: Duration -->
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label>Duration of the Festival</label>
                                            <input type="text" name="data[{{$language->id}}][duration]" class="form-control form-control-solid form-control-lg" value="{{$multiLanguage[$language->id]['duration'] ?? old('data.'.$language->id.'.duration')}}" placeholder="e.g. 3 Days, 1 Week">
                                        </div>
                                    </div>

                                    <!-- 🆕 Optional: Daily Significance -->
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label>Daily Significance</label>
                                            <textarea name="data[{{$language->id}}][daily_significance]" class="form-control form-control-solid form-control-lg" rows="3" placeholder="Explain daily rituals">{{$multiLanguage[$language->id]['daily_significance'] ?? old('data.'.$language->id.'.daily_significance')}}</textarea>
                                        </div>
                                    </div>

                                    <!-- 🆕 Optional: History -->
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <label>History of the Festival</label>
                                            <textarea name="data[{{$language->id}}][history]" class="form-control form-control-solid form-control-lg" rows="4" placeholder="Describe historical background">{{$multiLanguage[$language->id]['history'] ?? old('data.'.$language->id.'.history')}}</textarea>
                                        </div>
                                    </div>

                                    <!-- 🆕 Optional: Temples to Visit -->
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <label>Temples to Visit</label>
                                            <textarea name="data[{{$language->id}}][temples_to_visit]" class="form-control form-control-solid form-control-lg" rows="3" placeholder="List important temples">{{$multiLanguage[$language->id]['temples_to_visit'] ?? old('data.'.$language->id.'.temples_to_visit')}}</textarea>
                                        </div>
                                    </div>

                                    <!-- 🆕 Optional: Other Information -->
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <label>Other Information</label>
                                            <textarea name="data[{{$language->id}}][other_info]" class="form-control form-control-solid form-control-lg" rows="3" placeholder="Any additional details">{{$multiLanguage[$language->id]['other_info'] ?? old('data.'.$language->id.'.other_info')}}</textarea>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <?php $i++; ?>
                            @endforeach
                            @endif
                        </div>

                        <div class="d-flex justify-content-between border-top mt-5 pt-10">
                            <div>
                                <button type="submit" class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
                                    Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.select2').select2({ width: '100%' });
        $('.form-control[multiple]').select2({ placeholder: 'Select', width: '100%' });
    });
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
{{-- <script>
flatpickr("#myDates", {
    mode: "multiple",
    dateFormat: "Y-m-d"
});
</script> --}}
<script>
    // Simple - past dates are not shown in the calendar
    flatpickr("#myDates", {
        minDate: "today", // Past dates won't be selectable
            mode: "multiple",
        enable: [
            // Only enable dates from today forward
            function(date) {
                return date >= new Date().setHours(0,0,0,0);
            }
        ],
        dateFormat: "Y-m-d"
    });
</script>
@stop
