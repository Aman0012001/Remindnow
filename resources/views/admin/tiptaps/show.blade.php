@extends('admin.layouts.layout')
@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                        View Tiptap
                    </h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route($model.'.index') }}" class="text-muted">Tiptaps</a>
                        </li>
                    </ul>
                </div>
            </div>
            @include("admin.elements.quick_links")
        </div>
    </div>

    <div class="d-flex flex-column-fluid">
        <div class="container">
            <div class="card card-custom gutter-b">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-4 col-form-label">Title:</label>
                                <div class="col-8">
                                    <span class="form-control-plaintext font-weight-bolder">{{ $tiptapDetails->title }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-4 col-form-label">Image:</label>
                                <div class="col-8">
                                    @if(!empty($tiptapDetails->image))
                                        <a class="fancybox-buttons" data-fancybox-group="button" href="{{ Config::get('constants.TIPTAP_IMAGE_PATH').$tiptapDetails->image }}">
                                            <img src="{{ Config::get('constants.TIPTAP_IMAGE_PATH').$tiptapDetails->image }}" width="100" height="100">
                                        </a>
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-4 col-form-label">Status:</label>
                                <div class="col-8">
                                    @if($tiptapDetails->is_active == 1)
                                        <span class="label label-lg label-light-success label-inline">Active</span>
                                    @else
                                        <span class="label label-lg label-light-danger label-inline">Inactive</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-4 col-form-label">Created At:</label>
                                <div class="col-8">
                                    <span class="form-control-plaintext font-weight-bolder">{{ date('Y-m-d H:i:s', strtotime($tiptapDetails->created_at)) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between border-top mt-5 pt-10">
                        <a href="{{ route("$model.index") }}" class="btn btn-light">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection