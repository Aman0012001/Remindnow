@extends('admin.layouts.layout')
@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
@php $counter = 0; @endphp
<style>
    .invalid-feedback {
        display: inline;
    }
    .AClass{
        right:10px;
        position: absolute;
    }
</style>

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                        Edit  {{ Config('constants.TEMPLE.TEMPLE_TITLE') }}
                    </h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard')}}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route($model.'.index')}}" class="text-muted"> {{ Config('constants.TEMPLE.TEMPLE_TITLE') }} </a>
                        </li>
                    </ul>
                </div>
            </div>
            @include("admin.elements.quick_links")
        </div>
    </div>

    <div class="d-flex flex-column-fluid">
        <div class="container">
              <form action="{{route($model.'.update',base64_encode($templeDetails->id))}}" method="POST" class="mws-form" autocomplete="off" enctype="multipart/form-data">
                @csrf
                @method('POST')
                <div class="card card-custom gutter-b">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <label for="url">url</label><span class="text-danger"> * </span>
                                     <input type="text" name="url" class="form-control"  placeholder="Enter Url" value="{{ $templeDetails->url ?? '' }}">
                                    @if ($errors->has('url'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('url') }}
                                        </div>
                                    @endif
                                </div>
                            </div>  
                            

                            <div class="col-xl-6">
                                <div class="form-group m-0">
                                    <label for="image">Image</label><span class="text-danger">*</span>
                                    <input type="file" name="image" class="form-control form-control-solid form-control-lg @error('image') is-invalid @enderror" value="{{ old('image') }}">
                                    @if ($errors->has('image'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('image') }}
                                        </div>
                                    @endif
                                </div>

                                @if(!empty($templeDetails->image))
                                    <a class="fancybox-buttons" data-fancybox-group="button" href="{{ Config('constants.TEMPLE_IMAGE_PATH').$templeDetails->image }}">
                                        <img src="{{ Config('constants.TEMPLE_IMAGE_PATH').$templeDetails->image }}" width="50" height="50">
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
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
                                            <img src="{{url (Config::get('constants.LANGUAGE_IMAGE_PATH').$language->image)}}" alt="">
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
                                    <div class="col-xl-12">
                                        <div class="row">
                                            <div class="col-xl-6">
                                                <div class="form-group">
                                                    @if($i == 1)
                                                    <label for="{{$language->id}}.name">Name</label><span class="text-danger"> * </span>
                                                    <input type="text" name="data[{{$language->id}}][name]" class="form-control form-control-solid form-control-lg @error('name') is-invalid @enderror" value="{{$multiLanguage[$language->id]['name'] ?? old('name')}}">
                                                    @if ($errors->has('name'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('name') }}
                                                    </div>
                                                    @endif
                                                    @else
                                                    <label for="{{$language->id}}.name">Name</label><span class="text-danger"> </span>
                                                    <input type="text" name="data[{{$language->id}}][name]" class="form-control form-control-solid form-control-lg" value="{{$multiLanguage[$language->id]['name'] ?? old('name')}}">
                                                    @endif
                                                </div>
                                            </div>
                                           
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
                                <button button type="submit" class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.select2').select2({
            width: '100%'
        });
        $('.form-control[multiple]').select2({
            placeholder: 'Select',
            width: '100%'
        });
    });
</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
flatpickr("#myDates", {
    mode: "multiple",   // allow multiple dates
    dateFormat: "Y-m-d" // output format
});
</script>
@stop
