@extends('admin.layouts.layout')
@section('content')
<style>
    .image-preview-container {
        margin-top: 10px;
    }
    .image-preview {
        max-width: 200px;
        max-height: 200px;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px;
    }
    .preview-wrapper {
        position: relative;
        display: inline-block;
    }
    .remove-preview {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(0,0,0,0.7);
        color: white;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 14px;
    }
    .user-type-section {
        margin-bottom: 20px;
        padding: 15px;
        border: 1px solid #e4e6ef;
        border-radius: 6px;
    }
    .device-info {
        font-size: 12px;
        color: #6c757d;
        margin-left: 5px;
    }
    .no-device {
        color: #f64e60;
    }
    .has-device {
        color: #1bc5bd;
    }
</style>

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                        Create User Notification
                    </h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route($model.'.index') }}" class="text-muted">User Notifications</a>
                        </li>
                    </ul>
                </div>
            </div>
            @include("admin.elements.quick_links")
        </div>
    </div>

    <div class="d-flex flex-column-fluid">
        <div class="container">
            <form action="{{ route("$model.save") }}" method="POST" enctype="multipart/form-data" id="notificationForm">
                @csrf
                
                <div class="card card-custom gutter-b">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <label>Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="user-type-section">
                            <h6 class="text-dark font-weight-bold mb-4">Select Users for Notification</h6>
                            
                            <!-- Simple Select All Checkbox -->
                            <div class="form-group mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="select_all_users" value="1" class="form-check-input" id="selectAllCheckbox" {{ old('select_all_users') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold" for="selectAllCheckbox">
                                        Select All Users
                                    </label>
                                    <small class="form-text text-muted d-block mt-1">
                                        Check this box to send notification to all users
                                    </small>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Select Users</label>
                                        <select name="user_ids[]" id="user_ids" class="form-control select3 @error('user_ids') is-invalid @enderror" multiple="multiple">
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" 
                                                    data-device-count="{{ $user->deviceTokens->count() }}"
                                                    {{ (old('user_ids') && in_array($user->id, old('user_ids'))) ? 'selected' : '' }}>
                                                    {{ $user->name }} ({{ $user->email }})
                                                    @if($user->deviceTokens->count() > 0)
                                                        <span class="device-info has-device">
                                                            ✓ {{ $user->deviceTokens->count() }} device(s)
                                                        </span>
                                                    @else
                                                        {{-- <span class="device-info no-device">
                                                            ✗ No device
                                                        </span> --}}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('user_ids')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted" id="selectionStatus">
                                            Select individual users or check "Select All Users" above
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <label>Image (Optional)</label>
                                    <div class="custom-file">
                                        <input type="file" name="image" class="custom-file-input @error('image') is-invalid @enderror" id="imageInput" accept="image/*">
                                        <label class="custom-file-label" for="imageInput">
                                            Choose file
                                        </label>
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Image Preview -->
                                    <div class="image-preview-container mt-3" id="newPreviewContainer" style="display: none;">
                                        <p class="text-muted mb-2">Image Preview:</p>
                                        <div class="preview-wrapper">
                                            <img id="imagePreview" class="image-preview" alt="Image Preview">
                                            <div class="remove-preview" id="removePreview">×</div>
                                        </div>
                                        <small class="form-text text-muted">
                                            Preview of the image you're uploading (Max: 2MB)
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between border-top mt-5 pt-10">
                            <button type="submit" class="btn btn-primary">Save Notification</button>
                            <a href="{{ route("$model.index") }}" class="btn btn-light">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2 for multiple selection
        $('#user_ids').select2({
            placeholder: "Select Users",
            allowClear: true,
            closeOnSelect: false
        });
        
        // Initial setup based on checkbox state
        if ($('#selectAllCheckbox').is(':checked')) {
            disableUserSelection();
            $('#selectionStatus').html('<span class="text-success">✓ Notification will be sent to ALL users</span>');
        }
        
        // Select All Checkbox functionality
        $('#selectAllCheckbox').on('change', function() {
            if ($(this).is(':checked')) {
                disableUserSelection();
                $('#selectionStatus').html('<span class="text-success">✓ Notification will be sent to ALL users</span>');
            } else {
                enableUserSelection();
                $('#selectionStatus').text('Select individual users or check "Select All Users" above');
            }
        });
        
        // Function to disable user selection
        function disableUserSelection() {
            $('#user_ids').prop('disabled', true);
            $('#user_ids').val(null).trigger('change');
            $('#user_ids').select2({
                disabled: true
            });
        }
        
        // Function to enable user selection
        function enableUserSelection() {
            $('#user_ids').prop('disabled', false);
            $('#user_ids').select2({
                disabled: false
            });
        }
        
        // When user manually selects from dropdown
        $('#user_ids').on('change', function() {
            if ($('#selectAllCheckbox').is(':checked')) {
                return; // Don't do anything if "Select All" is checked
            }
            
            const selectedOptions = $('#user_ids').val();
            if (selectedOptions && selectedOptions.length > 0) {
                // Update status message
                $('#selectionStatus').html('<span class="text-info">✓ ' + selectedOptions.length + ' user(s) selected</span>');
            } else {
                // Update status message
                $('#selectionStatus').text('Select individual users or check "Select All Users" above');
            }
        });
        
        // Image preview functionality
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');
        const removePreview = document.getElementById('removePreview');
        const customFileLabel = document.querySelector('.custom-file-label');
        const newPreviewContainer = document.getElementById('newPreviewContainer');

        // Update file input label
        imageInput.addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'Choose file';
            customFileLabel.textContent = fileName;
            
            // Show preview if file selected
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    newPreviewContainer.style.display = 'block';
                    removePreview.style.display = 'flex';
                }
                
                reader.readAsDataURL(e.target.files[0]);
            } else {
                newPreviewContainer.style.display = 'none';
            }
        });

        // Remove preview functionality
        removePreview.addEventListener('click', function() {
            imageInput.value = '';
            newPreviewContainer.style.display = 'none';
            customFileLabel.textContent = 'Choose file';
        });

        // Form validation
        $('#notificationForm').on('submit', function(e) {
            const title = $('input[name="title"]').val();
            const selectAllChecked = $('#selectAllCheckbox').is(':checked');
            const userIds = $('#user_ids').val();
            
            // Validate required fields
            if (!title) {
                alert('Please enter notification title.');
                e.preventDefault();
                return false;
            }
            
            // Check if at least one user is selected (either all or specific)
            if (!selectAllChecked && (!userIds || userIds.length === 0)) {
                alert('Please select at least one user for the notification or check "Select All Users".');
                e.preventDefault();
                return false;
            }
            
            // Validate image if selected
            const file = imageInput.files[0];
            if (file) {
                // Check file size (2MB = 2 * 1024 * 1024 bytes)
                const maxSize = 2 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('File size must be less than 2MB.');
                    e.preventDefault();
                    return false;
                }

                // Check file type
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    alert('Please select a valid image file (JPEG, PNG, JPG, GIF).');
                    e.preventDefault();
                    return false;
                }
            }
            
            return true;
        });
    });


</script>


@endsection
@endsection