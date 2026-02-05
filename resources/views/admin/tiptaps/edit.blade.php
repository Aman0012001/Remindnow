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
    .current-image {
        margin-top: 10px;
    }
    .current-image img {
        max-width: 200px;
        max-height: 200px;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px;
    }
</style>

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                        Edit Tiptap
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
            <form action="{{ route("$model.update", base64_encode($tiptapDetails->id)) }}" method="POST" enctype="multipart/form-data" id="tiptapForm">
                @csrf
                @method('PUT')
                <div class="card card-custom gutter-b">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <label>Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $tiptapDetails->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <label>Image</label>
                                    <div class="custom-file">
                                        <input type="file" name="image" class="custom-file-input @error('image') is-invalid @enderror" id="imageInput" accept="image/*">
                                        <label class="custom-file-label" for="imageInput">
                                            {{ $tiptapDetails->image ? 'Change file' : 'Choose file' }}
                                        </label>
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Current Image -->
                                    @if($tiptapDetails->image)
                                        <div class="current-image mt-3">
                                            <p class="text-muted mb-2">Current Image:</p>
                                            <div class="preview-wrapper">
                                                <a class="fancybox-buttons" data-fancybox-group="button" href="{{ Config::get('constants.TIPTAP_IMAGE_PATH').$tiptapDetails->image }}">
                                                    <img src="{{ Config::get('constants.TIPTAP_IMAGE_PATH').$tiptapDetails->image }}" alt="{{ $tiptapDetails->title }}" width="200" style="object-fit: cover;">
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- New Image Preview -->
                                    <div class="image-preview-container mt-3" id="newPreviewContainer" style="display: none;">
                                        <p class="text-muted mb-2">New Image Preview:</p>
                                        <div class="preview-wrapper">
                                            <img id="imagePreview" class="image-preview" alt="New Image Preview">
                                            <div class="remove-preview" id="removePreview">×</div>
                                        </div>
                                        <small class="form-text text-muted">
                                            Preview of the new image you're uploading
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <label class="checkbox">
                                        <input type="checkbox" name="is_active" value="1" {{ $tiptapDetails->is_active ? 'checked' : '' }}>
                                        <span></span>
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between border-top mt-5 pt-10">
                            <button type="submit" class="btn btn-primary">Update</button>
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
            customFileLabel.textContent = '{{ $tiptapDetails->image ? "Change file" : "Choose file" }}';
        });

        // Form validation
        $('#tiptapForm').on('submit', function() {
            const fileInput = $('#imageInput');
            const file = fileInput[0].files[0];
            
            // Only validate if a file is selected (image is optional in edit)
            if (file) {
                // Check file size (2MB = 2 * 1024 * 1024 bytes)
                const maxSize = 2 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('File size must be less than 2MB.');
                    return false;
                }

                // Check file type
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    alert('Please select a valid image file (JPEG, PNG, JPG, GIF).');
                    return false;
                }
            }

            return true;
        });
    });
</script>
@endsection
@endsection