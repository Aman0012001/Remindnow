@extends('admin.layouts.layout')
@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                        User Notifications
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
                                <a href="{{ route("$model.create") }}" class="btn btn-primary">Add New Notification</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample">
                                <div id="collapseSearch" class="collapse" data-parent="#accordionExample">
                                    <div class="row mb-6">
                                        <div class="col-lg-3 mb-lg-5 mb-6">
                                            <label>Title</label>
                                            <input type="text" class="form-control" name="title" placeholder="Search by title" value="{{ $searchVariable['title'] ?? '' }}">
                                        </div>
                                      
                                        <div class="col-lg-3 mb-lg-5 mb-6">
                                            <label>User</label>
                                            <select name="user_id" class="form-control select2">
                                                <option value="">Select User</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}" {{ isset($searchVariable['user_id']) && $searchVariable['user_id'] == $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-6">
                                        <div class="col-lg-3 mb-lg-5 mb-6">
                                            <label>Date From</label>
                                            <input type="text" class="form-control datepicker" name="date_from" placeholder="Date From" value="{{ $searchVariable['date_from'] ?? '' }}" readonly>
                                        </div>
                                        <div class="col-lg-3 mb-lg-5 mb-6">
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
                                            <th>Description</th>
                                            <th>User</th>
                                            <th class="text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!$results->isEmpty())
                                            @foreach($results as $result)
                                            <tr>
                                                <td>
                                                    @if(!empty($result->image))
                                                        <a class="fancybox-buttons" data-fancybox-group="button" href="{{ Config::get('constants.NOTIFICATION_IMAGE_PATH').$result->image }}">
                                                            <img src="{{ Config::get('constants.NOTIFICATION_IMAGE_PATH').$result->image }}" width="50" height="50">
                                                        </a>
                                                    @else
                                                        <span class="text-muted">No Image</span>
                                                    @endif
                                                </td>
                                                <td>{{ $result->title }}</td>
                                                <td>{{ Str::limit($result->description, 50) }}</td>
                                                <td>
                                                    @if($result->user)
                                                        {{ $result->user->name }}
                                                    @elseif($result->user_ids)
                                                        <a href="javascript:void(0);" 
                                                           class="text-info view-all-users" 
                                                           data-user-ids="{{ $result->user_ids }}"
                                                           data-notification-title="{{ $result->title }}"
                                                           data-toggle="modal" 
                                                           data-target="#usersModal">
                                                            All Users ({{ count(explode(',', $result->user_ids)) }})
                                                        </a>
                                                    @else
                                                        <span class="text-info">All Users</span>
                                                    @endif
                                                </td>
                                              
                                                <td class="text-right">
                                                 <a href="{{ route("$model.sendNow", base64_encode($result->id)) }}" class="btn btn-icon btn-light btn-hover-info btn-sm" title="Send Now">
                                                            <i class="la la-paper-plane"></i>
                                                        </a>
                                                    <a href="{{ route("$model.delete", base64_encode($result->id)) }}" class="btn btn-icon btn-light btn-hover-danger btn-sm confirmDelete" title="Delete">
                                                        <i class="la la-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        @else
                                        <tr>
                                            <td colspan="9" class="text-center">No notifications found.</td>
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

<!-- Modal -->
<div class="modal fade" id="usersModal" tabindex="-1" role="dialog" aria-labelledby="usersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="usersModalLabel">Users List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-custom alert-light-primary mb-4" role="alert">
                    <div class="alert-icon"><i class="flaticon-users"></i></div>
                    <div class="alert-text">
                        <span id="notificationTitle"></span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="usersTable">
                        <thead>
                            <tr>
                                <th width="50">#</th>
                                <th>User Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <!-- Users will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@section('js')
<script>
    $(document).ready(function() {
        // Initialize datepicker
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
        
        // Initialize Select2
        $('.select2').select2({
            placeholder: "Select User",
            allowClear: true
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
                text: title + " this notification?",
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
        
        // Send now confirmation
        $('a[title="Send Now"]').on('click', function(e) {
            e.preventDefault();
            var href = $(this).attr('href');
            
            Swal.fire({
                title: 'Send Notification?',
                text: "This will send the notification immediately to the user.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });

        // Handle View All Users Click
        $('.view-all-users').on('click', function(e) {
            e.preventDefault();
            var userIds = $(this).data('user-ids');
            var notificationTitle = $(this).data('notification-title');
            
            // Set modal title
            $('#notificationTitle').html('<strong>Notification:</strong> ' + notificationTitle);
            
            // Clear previous data
            $('#usersTableBody').empty();
            
            // Split user IDs
            var userIdArray = userIds.split(',');
            
            // Add loading indicator
            $('#usersTableBody').html('<tr><td colspan="4" class="text-center"><div class="spinner spinner-primary spinner-center"></div></td></tr>');
            
            // Make AJAX request to get user details
            $.ajax({
                url: '{{ route("admin.getUsersByIds") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    user_ids: userIds
                },
                success: function(response) {
                    $('#usersTableBody').empty();
                    
                    if(response.success && response.users.length > 0) {
                        var index = 1;
                        $.each(response.users, function(key, user) {
                            var row = '<tr>' +
                                '<td>' + index + '</td>' +
                                '<td>' + (user.name || 'N/A') + '</td>' +
                                '<td>' + (user.email || 'N/A') + '</td>' +
                                '<td>' + (user.phone_number || 'N/A') + '</td>' +
                                '</tr>';
                            $('#usersTableBody').append(row);
                            index++;
                        });
                    } else {
                        $('#usersTableBody').html('<tr><td colspan="4" class="text-center text-muted">No users found.</td></tr>');
                    }
                },
                error: function() {
                    $('#usersTableBody').html('<tr><td colspan="4" class="text-center text-danger">Error loading users.</td></tr>');
                }
            });
        });
    });
</script>
@endsection
@endsection

