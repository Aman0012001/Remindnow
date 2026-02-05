@extends('admin.layouts.layout')
@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                        Tiptaps
                    </h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}" class="text-muted">Dashboard</a>
                        </li>
                    </ul>
                </div>
            </div>
            @include("admin.elements.quick_links")
        </div>
    </div>

    <div class="d-flex flex-column-fluid">
        <div class="container">
            {{ Form::open(['method' => 'get', 'route' => "$model.index", 'class' => 'kt-form kt-form--fit mb-0', 'autocomplete' => 'off']) }}
            <div class="row">
                <div class="col-12">
                    <div class="card card-custom card-stretch card-shadowless">
                        <div class="card-header">
                            <div class="card-title"></div>
                            <div class="card-toolbar">
                                <a href="javascript:void(0);" class="btn btn-primary dropdown-toggle mr-2" data-toggle="collapse" data-target="#collapseSearch">
                                    Search
                                </a>
                                <a href="{{ route("$model.create") }}" class="btn btn-primary">Add New Tiptap</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample">
                                <div id="collapseSearch" class="collapse" data-parent="#accordionExample">
                                    <div class="row mb-6">
                                        <div class="col-lg-4 mb-lg-5 mb-6">
                                            <label>Title</label>
                                            <input type="text" class="form-control" name="title" placeholder="Search by title" value="{{ $searchVariable['title'] ?? '' }}">
                                        </div>
                                        <div class="col-lg-4 mb-lg-5 mb-6">
                                            <label>Date From</label>
                                            <input type="text" class="form-control datepicker" name="date_from" placeholder="Date From" value="{{ $searchVariable['date_from'] ?? '' }}" readonly>
                                        </div>
                                        <div class="col-lg-4 mb-lg-5 mb-6">
                                            <label>Date To</label>
                                            <input type="text" class="form-control datepicker" name="date_to" placeholder="Date To" value="{{ $searchVariable['date_to'] ?? '' }}" readonly>
                                        </div>
                                    </div>
                                    <div class="row mt-8">
                                        <div class="col-lg-12">
                                            <button class="btn btn-primary btn-primary--icon" id="kt_search">
                                                <span>
                                                    <i class="la la-search"></i>
                                                    <span>Search</span>
                                                </span>
                                            </button>
                                            &nbsp;&nbsp;
                                            <a href="{{ route("$model.index") }}" class="btn btn-secondary btn-secondary--icon">
                                                <span>
                                                    <i class="la la-close"></i>
                                                    <span>Clear</span>
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table dataTable table-head-custom table-head-bg table-borderless table-vertical-center">
                                    <thead>
                                        <tr class="text-uppercase">
                                            <th>Image</th>
                                            <th>Title</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th class="text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!$results->isEmpty())
                                            @foreach($results as $result)
                                            <tr>
                                                <td>
                                                    @if(!empty($result->image))
                                                        <a class="fancybox-buttons" data-fancybox-group="button" href="{{ Config::get('constants.TIPTAP_IMAGE_PATH').$result->image }}">
                                                            <img src="{{ Config::get('constants.TIPTAP_IMAGE_PATH').$result->image }}" width="50" height="50">
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>{{ $result->title }}</td>
                                                <td>
                                                    @if($result->is_active == 1)
                                                        <span class="label label-lg label-light-success label-inline">Active</span>
                                                    @else
                                                        <span class="label label-lg label-light-danger label-inline">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>{{ date('Y-m-d', strtotime($result->created_at)) }}</td>
                                                <td class="text-right">
                                                    <a href="{{ route("$model.edit", base64_encode($result->id)) }}" class="btn btn-icon btn-light btn-hover-primary btn-sm" title="Edit">
                                                        <i class="la la-edit"></i>
                                                    </a>
                                                    <a href="{{ route("$model.show", base64_encode($result->id)) }}" class="btn btn-icon btn-light btn-hover-info btn-sm" title="View">
                                                        <i class="la la-eye"></i>
                                                    </a>
                                                    <a href="{{ route("$model.delete", base64_encode($result->id)) }}" class="btn btn-icon btn-light btn-hover-danger btn-sm confirmDelete" title="Delete">
                                                        <i class="la la-trash"></i>
                                                    </a>
                                                    
                                                    <!-- Status Change Button -->
                                                    @if($result->is_active == 1)
                                                        <a title="Click To Deactivate" href='{{ route("$model.changeStatus", [$result->id, 0]) }}' class="btn btn-icon btn-light btn-hover-danger btn-sm status_any_item" data-toggle="tooltip" data-placement="top"
                                                            data-container="body" data-boundary="window"
                                                            data-original-title="Deactivate">
                                                            <span class="svg-icon svg-icon-md svg-icon-danger">
                                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                        <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
                                                                            <rect x="0" y="7" width="16" height="2" rx="1"/>
                                                                            <rect opacity="0.3" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000) " x="0" y="7" width="16" height="2" rx="1"/>
                                                                        </g>
                                                                    </g>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                    @else
                                                        <a title="Click To Activate" href='{{ route("$model.changeStatus", [$result->id, 1]) }}' class="btn btn-icon btn-light btn-hover-success btn-sm status_any_item" data-toggle="tooltip" data-placement="top"
                                                            data-container="body" data-boundary="window"
                                                            data-original-title="Activate">
                                                            <span class="svg-icon svg-icon-md svg-icon-success">
                                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                        <polygon points="0 0 24 0 24 24 0 24"/>
                                                                        <path d="M9.26193932,16.6476484 C8.90425297,17.0684559 8.27315905,17.1196257 7.85235158,16.7619393 C7.43154411,16.404253 7.38037434,15.773159 7.73806068,15.3523516 L16.2380607,5.35235158 C16.6013618,4.92493855 17.2451015,4.87991302 17.6643638,5.25259068 L22.1643638,9.25259068 C22.5771466,9.6195087 22.6143273,10.2515811 22.2474093,10.6643638 C21.8804913,11.0771466 21.2484189,11.1143273 20.8356362,10.7474093 L17.0997854,7.42665306 L9.26193932,16.6476484 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(14.999995, 11.000002) rotate(-180.000000) translate(-14.999995, -11.000002) "/>
                                                                        <path d="M4.26193932,17.6476484 C3.90425297,18.0684559 3.27315905,18.1196257 2.85235158,17.7619393 C2.43154411,17.404253 2.38037434,16.773159 2.73806068,16.3523516 L11.2380607,6.35235158 C11.6013618,5.92493855 12.2451015,5.87991302 12.6643638,6.25259068 L17.1643638,10.2525907 C17.5771466,10.6195087 17.6143273,11.2515811 17.2474093,11.6643638 C16.8804913,12.0771466 16.2484189,12.1143273 15.8356362,11.7474093 L12.0997854,8.42665306 L4.26193932,17.6476484 Z" fill="#000000" fill-rule="nonzero" transform="translate(9.999995, 12.000002) rotate(-180.000000) translate(-9.999995, -12.000002) "/>
                                                                    </g>
                                                                </svg>
                                                            </span>
                                                        </a> 
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        @else
                                        <tr>
                                            <td colspan="5" class="text-center">No records found.</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                                @include('pagination.default', ['results' => $results])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize datepicker
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Status change confirmation
        $('.status_any_item').on('click', function(e) {
            e.preventDefault();
            var href = $(this).attr('href');
            var title = $(this).attr('title');
            
            Swal.fire({
                title: 'Are you sure?',
                text: title + " this tiptap?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, ' + title.toLowerCase() + ' it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });

        // Delete confirmation
        $('.confirmDelete').on('click', function(e) {
            e.preventDefault();
            var href = $(this).attr('href');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });
    });
</script>
@endsection