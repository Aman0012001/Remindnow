<?php $__env->startSection('content'); ?>
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
        <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                    Edit <?php echo e(Config('constants.PRODUCT.PRODUCT_TITLE')); ?></h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route('dashboard')); ?>" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route($model.'.index')); ?>" class="text-muted"> <?php echo e(Config('constants.PRODUCT.PRODUCT_TITLES')); ?></a>
                        </li>
                    </ul>
                </div>
            </div>
            <?php echo $__env->make("admin.elements.quick_links", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
    <div class="d-flex flex-column-fluid">
        <div class=" container ">
            <form action="<?php echo e(route($model.'.update',base64_encode($proDetails->id))); ?>" method="POST" class="mws-form" autocomplete="off" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="card card-custom gutter-b">
                    <div class="card-header card-header-tabs-line">
                        <div class="card-toolbar border-top">
                            <ul class="nav nav-tabs nav-bold nav-tabs-line">
                                <?php if(!empty($languages)): ?>
                                <?php $i = 1; ?>
                                <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo e(($i==$language_code)?'active':''); ?>" data-toggle="tab" href="#<?php echo e($language->title); ?>">
                                        <span class="symbol symbol-20 mr-3">
                                            <img src="<?php echo e(url (Config::get('constants.LANGUAGE_IMAGE_PATH').$language->image)); ?>" alt="">
                                        </span>
                                        <span class="nav-text"><?php echo e($language->title); ?></span>
                                    </a>
                                </li>
                                <?php $i++; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <?php if(!empty($languages)): ?>
                            <?php $i = 1; ?>
                            <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="tab-pane fade <?php echo e(($i ==  $language_code )?'show active':''); ?>" id="<?php echo e($language->title); ?>" role="tabpanel" aria-labelledby="<?php echo e($language->title); ?>">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="row">
                                            <div class="col-xl-6">
                                                <div class="form-group">
                                                    <?php if($i == 1): ?>
                                                    <label for="<?php echo e($language->id); ?>.name">Name</label><span class="text-danger"> * </span>
                                                    <input type="text" name="data[<?php echo e($language->id); ?>][name]" class="form-control form-control-solid form-control-lg <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($multiLanguage[$language->id]['name']); ?>">
                                                    <?php if($errors->has('name')): ?>
                                                    <div class="invalid-feedback">
                                                        <?php echo e($errors->first('name')); ?>

                                                    </div>
                                                    <?php endif; ?>
                                                    <?php else: ?>
                                                    <label for="<?php echo e($language->id); ?>.name">Name</label><span class="text-danger"> </span>
                                                    <input type="text" name="data[<?php echo e($language->id); ?>][name]" class="form-control form-control-solid form-control-lg" value="<?php echo e($multiLanguage[$language->id]['name']); ?>">
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="col-xl-12">
                                                <div class="form-group">
                                                    <?php if($i == 1): ?>
                                                    <label for="<?php echo e($language->id); ?>.descriptions">Description</label><span class="text-danger"> * </span>
                                                    <textarea id="body_<?php echo e($language->id); ?>.descriptions" name="data[<?php echo e($language->id); ?>][descriptions]" class="form-control form-control-solid form-control-lg  <?php $__errorArgs = ['descriptions'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="6"><?php echo e($multiLanguage[$language->id]['descriptions']); ?></textarea>
                                                    <?php if($errors->has('descriptions')): ?>
                                                    <div class="invalid-feedback">
                                                        <?php echo e($errors->first('descriptions')); ?>

                                                    </div>
                                                    <?php endif; ?>
                                                    <?php else: ?>
                                                    <label for="<?php echo e($language->id); ?>.descriptions">Description</label><span class="text-danger"> </span>
                                                    <textarea id="body_<?php echo e($language->id); ?>.descriptions" name="data[<?php echo e($language->id); ?>][descriptions]" class="form-control form-control-solid form-control-lg "  rows="6"><?php echo e($multiLanguage[$language->id]['descriptions']); ?></textarea>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php $i++; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </div>
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <lable for="category_id" >Category<span class="text-danger"> * </span></lable>
                                    <select name="category_id"  id="category-dropdown" class="form-control form-control-solid form-control-lg  <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <option value="">Select Category</option>
                                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option  value="<?php echo e($cat->id); ?>"  <?php echo e($proDetails->category_id == $cat->id ? 'selected' : ''); ?>  ><?php echo e($cat->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                                     
                                    </select>
                                    <?php if($errors->has('category_id')): ?>
                                    <div class="invalid-feedback">
                                        <?php echo e($errors->first('category_id')); ?>

                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <lable for="subcategory_id" id="subcategory_id">Sub Category</lable><span
                                    class="text-danger"> * </span>
                                    <select name="subcategory_id" id="subcatdata"class="form-control form-control-solid form-control-lg <?php $__errorArgs = ['subcategory_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <option  id="disable_varient" value="">Select Sub Category</option>
                                        <?php $__currentLoopData = $subcatData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option  value="<?php echo e($subcat->id); ?>"  <?php echo e($proDetails->sub_category_id == $subcat->id ? 'selected' : ''); ?>  ><?php echo e($subcat->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>    
                                    </select>
                                    <?php if($errors->has('subcategory_id')): ?>
                                    <div class="invalid-feedback">
                                        <?php echo e($errors->first('subcategory_id')); ?>

                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <lable for="price" >Price<span class="text-danger"> * </span></lable>
                                    <input type="text" name="price" class="form-control form-control-solid form-control-lg <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"  value="<?php echo e($proDetails->price); ?>" accept="price/*" placeholder="₱ Price">
                                    <?php if($errors->has('price')): ?>
                                    <div class="invalid-feedback">
                                        <?php echo e($errors->first('price')); ?>

                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <lable for="in_stock" >Out Of Stock<span class="text-danger">  </span></lable>
                                    <input type="radio" name="in_stock"  <?php echo e($proDetails->in_stock ==0 ? 'checked' : ''); ?> value="0">
                                    <lable for="in_stock" >In Stock<span class="text-danger">  </span></lable>
                                    <input type="radio" name="in_stock" <?php echo e($proDetails->in_stock ==1 ? 'checked' : ''); ?>  value="1">
                                    <?php if($errors->has('in_stock')): ?>
                                    <div class="invalid-feedback">
                                        <?php echo e($errors->first('in_stock')); ?>

                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12">
                                 <?php $product_images =  $ProductImages ?? []; ?>
                                <label id="image_select-error" class="error" for="image_select"></label>
                                <label id="fetched_image-error" class="error" for="fetched_image"></label>
                                <label for="<?php echo e($language->id); ?>.name">Product Images</label><span class="text-danger"> * </span>
                                <input style="visibility:hidden; padding: 0; position: absolute;" class="fetched_image_select <?php $__errorArgs = ['cover_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="cover_image" value="<?php echo e($proDetails->cover_image ?? ''); ?>">
                                <?php if($errors->has('cover_image')): ?>
                                <div class="invalid-feedback">
                                    <?php echo e($errors->first('cover_image')); ?>

                                </div>
                                <?php endif; ?>
                                <div class="form-group" id="dropBox">
                                    <input type="file" id="imgUpload" multiple accept="image/*" onchange="handleFiles_images(this.files)">
                                    <progress hidden="" id="progress" max=100 value=0></progress>
                                    <label class="button" for="imgUpload">...or Upload From Your Computer</label>
                                    <div id="gallery">
                                        <?php if(count($product_images) > 0): ?>
                                        <?php $__currentLoopData = $product_images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($image->thumbnail_image == $proDetails->cover_image): ?>
                                        <input style="visibility:hidden; padding: 0; position: absolute;" class="fetched_image_select" name="cover_image" value="<?php echo e($image->thumbnail_image); ?>">
                                        <?php endif; ?>
                                        <div class="imageBox">
                                        <h3 class="cover_image_add <?php echo e($image->thumbnail_image == $proDetails->cover_image ? 'image_coverdesgin' : ''); ?>"><?php echo e($image->thumbnail_image == $proDetails->cover_image ? 'Cover Photo' : ''); ?></h3>
                                        <input hidden class="image_name_of" name="images[]" value="<?php echo e($image->thumbnail_image); ?>">
                                        <img src="<?php echo e(Config('constants.PRODUCT_IMAGE').$image->thumbnail_image); ?>">
                                        <div class="gallery-img-edit">
                                                    <a href="javascript:void(0)" data-image="<?php echo e($image->thumbnail_image); ?>" class="delete_image" data-type-image="session">
                                                        <svg width="12" height="14" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M11.5714 2.14286H9.36482L8.45411 0.622768C8.19911 0.236437 7.80536 0 7.35536 0H4.64464C4.19464 0 3.77679 0.236437 3.54643 0.622768L2.63518 2.14286H0.428571C0.190848 2.14286 0 2.33384 0 2.57143V3C0 3.23839 0.190848 3.42857 0.428571 3.42857H0.857143V12C0.857143 12.9469 1.62455 13.7143 2.57143 13.7143H9.42857C10.3754 13.7143 11.1429 12.9469 11.1429 12V3.42857H11.5714C11.8098 3.42857 12 3.23839 12 3V2.57143C12 2.33384 11.8098 2.14286 11.5714 2.14286ZM4.60446 1.36286C4.63125 1.31598 4.68482 1.28571 4.74107 1.28571H7.25893C7.31585 1.28571 7.36942 1.31585 7.39621 1.36272L7.86429 2.14286H4.13571L4.60446 1.36286ZM9.42857 12.4286H2.57143C2.33472 12.4286 2.14286 12.2367 2.14286 12V3.42857H9.85714V12C9.85714 12.2357 9.66429 12.4286 9.42857 12.4286ZM6 11.1429C6.23689 11.1429 6.42857 10.9512 6.42857 10.7143V5.14286C6.42857 4.90596 6.23689 4.71429 6 4.71429C5.76311 4.71429 5.57143 4.90714 5.57143 5.14286V10.7143C5.57143 10.95 5.76429 11.1429 6 11.1429ZM3.85714 11.1429C4.09286 11.1429 4.28571 10.95 4.28571 10.7143V5.14286C4.28571 4.90596 4.09404 4.71429 3.85714 4.71429C3.62025 4.71429 3.42857 4.90714 3.42857 5.14286V10.7143C3.42857 10.95 3.62143 11.1429 3.85714 11.1429ZM8.14286 11.1429C8.37975 11.1429 8.57143 10.9512 8.57143 10.7143V5.14286C8.57143 4.90596 8.37975 4.71429 8.14286 4.71429C7.90596 4.71429 7.71429 4.90714 7.71429 5.14286V10.7143C7.71429 10.95 7.90714 11.1429 8.14286 11.1429Z" fill="white"/>
                                                        </svg>
                                                    </a>
                                                    <div class="dropdown">
                                                        <a id="custom-toggle-button" class="custom-toggle-button">
                                                            <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="none"><path d="M0 256a56 56 0 1 1 112 0A56 56 0 1 1 0 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z" fill="white"/></svg>
                                                        </a>
                                                        <ul id="custom-dropdown-menu" class="dropdown-menu custom-dropdown-menu" aria-labelledby="custom-toggle-button">
                                                            <li><a class="dropdown-item select_cover_image_by_photo">Make cover photo</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                        </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                 <?php if($errors->has('images')): ?>
                                <div class="invalid-feedback">
                                    <?php echo e($errors->first('images')); ?>

                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="span pt-5" id="addMoreData">
                            <?php if(count($ProductSize) > 0): ?>
                                <?php $__currentLoopData = $ProductSize; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $modellDetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="row align-items-center">
                                    <div class="col-xl-4">
                                        <div class="form-group">
                                            <?php $html = ''; ?>
                                                <?php $html .='<option value="">Select Size</option>';  ?>
                                                    <?php $__currentLoopData = $size; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sizes): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    
                                                        <?php $html .= '<option value="<?php echo $sizes->id ?> ">  <?php echo $sizes->name ?> </option>'; ?>
                                                    
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <select name="product_sizes[<?php echo e($key); ?>][size_id]" class="form-control form-control-solid form-control-lg  <?php $__errorArgs = ['size_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                <option value="">Select Size</option>
                                                <?php $__currentLoopData = $size; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sizes1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option  value="<?php echo e($sizes1->id); ?>"  <?php echo e($modellDetail->size_id == $sizes1->id ? 'selected' : ''); ?>  ><?php echo e($sizes1->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                                     
                                            </select> 
                                        </div>
                                       

                                    </div>
                                    <div class="col-xl-4">
                                        <div class="form-group">
                                            <label for="price">Price</label><span class="text-danger"> </span>
                                            <input type="text" name="product_sizes[<?php echo e($key); ?>][price]" class="form-control form-control-solid form-control-lg " value="<?php echo e($modellDetail->price); ?>" placeholder="₱ Price">
                                            <?php if($errors->has('price')): ?>
                                            <div class=" invalid-feedback">
                                                <?php echo e($errors->first('price')); ?>

                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-xl-3">
                                        <div class="form-group">
                                            <lable for="out_stock" >Out Of Stock<span class="text-danger">  </span></lable>
                                            <input type="radio" name="product_sizes[<?php echo e($key); ?>][stock]" <?php echo e($modellDetail->in_stock==0 ? 'checked' : ''); ?> id="out_stock" class="" value="0">
                                            <lable for="stock" >In Stock<span class="text-danger">  </span></lable>
                                            <input type="radio" name="product_sizes[<?php echo e($key); ?>][stock]"  <?php echo e($modellDetail->in_stock==1 ? 'checked' : ''); ?> id="stock" value="1" >
                                        </div>
                                    </div>
                                    <?php if($key == 0): ?>
                                    <div class="col-xl-1">
                                        <label></label> <br><br>
                                        <a href="javascript:void(0)" id="addMore"><i class="fa fa-plus text-success" aria-hidden="true"></i></a>
                                     </div>
                                     <?php else: ?>
                                     <div class="col-xl-1">
                                        <label></label> <br><br>
                                        <a href="javascript:void(0)" class="removeMore"> <i class="fa fa-trash text-danger" aria-hidden="true"></i> </a>
                                     </div>
                                     <?php endif; ?>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
     let dropAreaa = document.getElementById("dropBox");
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropAreaa.addEventListener(eventName, preventDefaults, false)
        document.body.addEventListener(eventName, preventDefaults, false)
    });
    ['dragenter', 'dragover'].forEach(eventName => {
        dropAreaa.addEventListener(eventName, highlight, false)
    });
    ['dragleave', 'drop'].forEach(eventName => {
        dropAreaa.addEventListener(eventName, unhighlight, false)
    })

    let uploadProgress = []
    let progressBar = document.getElementById('progress')
    dropAreaa.addEventListener('drop', handleDrops, false)

    function preventDefaults(e) {
        e.preventDefault()
        e.stopPropagation()
    }

    function highlight(e) {
        dropAreaa.classList.add('highlight')
    }

    function unhighlight(e) {
        dropAreaa.classList.remove('active')
    }

    function initializeProgress(numFiles) {
        progressBar.value = 0
        uploadProgress = []

        for (let i = numFiles; i > 0; i--) {
            uploadProgress.push(0)
        }
    }
    function updateProgress(fileNumber, percent) {
        uploadProgress[fileNumber] = percent
        let total = uploadProgress.reduce((tot, curr) => tot + curr, 0) / uploadProgress.length
        progressBar.value = total
    }
    var priviewFileLen = 0;

    function handleFiles(files) {
        files = [...files]
        initializeProgress(files.length)
        priviewFileLen = files.length;
        files.forEach(previewFile)
    }

    function handleDrops(e) {
        var dt = e.dataTransfer
        var files = dt.files
        const input = document.getElementById('imgUpload')
        for (let i = 0; i < files.length; i++) {
            const file = files[i]
            dt.items.add(file);
        }
        input.files = dt.files
        handleFiles_images(files)
    }


    function handleFiles_images(files) {
        files = [...files]
        initializeProgress(files.length)
        var form_data = new FormData();
        var totalfiles = document.getElementById('imgUpload').files.length;
        for (var index = 0; index < totalfiles; index++) {
            form_data.append("image[]", document.getElementById('imgUpload').files[index]);
        }
        form_data.append("_token", '<?php echo e(csrf_token()); ?>');
        form_data.append("path", 'PRODUCT_IMAGE_ROOT_PATH');
        $('.loader-wrapper').show();
        $.ajax({
            type: 'POST',
            url: "<?php echo e(route('product.productPhotosUploads')); ?>",
            data: form_data,
            contentType: false,
            processData: false,
            success: function(data) {
                $.each(data, function(key, val) {
                    var result = val.type.split('/');
                    var fetched_imageCheck = $('.fetched_image_select').val();
                    if (result[0] == 'image') {
                        if(fetched_imageCheck == ''){
                            $('.fetched_image_select').val(val.image);
                        }

                        console.log('vinpof'+fetched_imageCheck+'vinpof');
                        var html = `<div class="imageBox">
                        <h3 class="cover_image_add ${fetched_imageCheck == '' ? 'image_coverdesgin' : ''}">${fetched_imageCheck == '' ? 'Cover Photo' : ''}</h3>
                        <input hidden class="image_name_of" name="images[]" value="${val.image}">
                        <img src="<?php echo e(Config('constants.PRODUCT_IMAGE')); ?>/${val.image}">
                        <div class="gallery-img-edit">
                                    <a href="javascript:void(0)" data-image="${val.image}" class="delete_image" data-type-image="session">
                                        <svg width="12" height="14" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M11.5714 2.14286H9.36482L8.45411 0.622768C8.19911 0.236437 7.80536 0 7.35536 0H4.64464C4.19464 0 3.77679 0.236437 3.54643 0.622768L2.63518 2.14286H0.428571C0.190848 2.14286 0 2.33384 0 2.57143V3C0 3.23839 0.190848 3.42857 0.428571 3.42857H0.857143V12C0.857143 12.9469 1.62455 13.7143 2.57143 13.7143H9.42857C10.3754 13.7143 11.1429 12.9469 11.1429 12V3.42857H11.5714C11.8098 3.42857 12 3.23839 12 3V2.57143C12 2.33384 11.8098 2.14286 11.5714 2.14286ZM4.60446 1.36286C4.63125 1.31598 4.68482 1.28571 4.74107 1.28571H7.25893C7.31585 1.28571 7.36942 1.31585 7.39621 1.36272L7.86429 2.14286H4.13571L4.60446 1.36286ZM9.42857 12.4286H2.57143C2.33472 12.4286 2.14286 12.2367 2.14286 12V3.42857H9.85714V12C9.85714 12.2357 9.66429 12.4286 9.42857 12.4286ZM6 11.1429C6.23689 11.1429 6.42857 10.9512 6.42857 10.7143V5.14286C6.42857 4.90596 6.23689 4.71429 6 4.71429C5.76311 4.71429 5.57143 4.90714 5.57143 5.14286V10.7143C5.57143 10.95 5.76429 11.1429 6 11.1429ZM3.85714 11.1429C4.09286 11.1429 4.28571 10.95 4.28571 10.7143V5.14286C4.28571 4.90596 4.09404 4.71429 3.85714 4.71429C3.62025 4.71429 3.42857 4.90714 3.42857 5.14286V10.7143C3.42857 10.95 3.62143 11.1429 3.85714 11.1429ZM8.14286 11.1429C8.37975 11.1429 8.57143 10.9512 8.57143 10.7143V5.14286C8.57143 4.90596 8.37975 4.71429 8.14286 4.71429C7.90596 4.71429 7.71429 4.90714 7.71429 5.14286V10.7143C7.71429 10.95 7.90714 11.1429 8.14286 11.1429Z" fill="white"/>
                                        </svg>
                                    </a>
                                    <div class="dropdown">
                                        <a id="custom-toggle-button" class="custom-toggle-button">
                                            <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="none"><!--! Font Awesome Pro 6.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M0 256a56 56 0 1 1 112 0A56 56 0 1 1 0 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z" fill="white"/></svg>
                                        </a>
                                        <ul id="custom-dropdown-menu" class="dropdown-menu custom-dropdown-menu" aria-labelledby="custom-toggle-button">
                                            <li><a class="dropdown-item select_cover_image_by_photo">Make cover photo</a></li>
                                        </ul>
                                    </div>
                                </div>
                        </div>`;
                        $('#gallery').append(html);
                    } else {
                        toastr.error('Browse to upload a valid File with image extension');
                    }
                    $('.loader-wrapper').hide();
                });
            }
        });
    }
    $('body').on('click','.custom-toggle-button' ,function(e) {
             e.preventDefault();
            $('.custom-dropdown-menu').toggleClass('show'); // Toggle the "show" class
        });
    $(document).on('mouseleave', function(e) {
        console.log(e.target);
        if (!$(e.target).closest('.dropdown').length) {
            $('.custom-dropdown-menu').removeClass('show');
        }
    });
    $('body').on('click', '.select_cover_image_by_photo', function() {
        $('.cover_image_add').html('');
        $('.cover_image_add').removeClass('image_coverdesgin');
        $(this).parents().children('.cover_image_add').html('Cover Photo');
        $(this).parents().children('.cover_image_add').addClass('image_coverdesgin');
        $('.fetched_image_select').val($(this).parents().children('.image_name_of').val());
    });


     $('body').on('click', '.delete_image', function() {
        var removeElement = $(this).parents('.imageBox');
        var image_delete  = $(this).data("image");
        var type_image  = $(this).data("type-image");
        var fetched_image = $('.fetched_image_select');
        var path          = 'PRODUCT_IMAGE';
        
        var form_data = new FormData();
        form_data.append("_token", '<?php echo e(csrf_token()); ?>');
        form_data.append("path", 'PRODUCT_IMAGE');
        form_data.append("type_image", type_image);

        Swal.fire({
            title: "Are you sure?",
            text: "Want to delete this ?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it",
            cancelButtonText: "No, cancel",
            reverseButtons: true
        }).then(function(result) {
            console.log(result);
            if (result.value) {  
            //     $('.loader-wrapper').show();
            //     $('.overlay').show();
                $.ajax({
                    type: "POST",
                    url: "<?php echo e(route('product.productPhotosDelete')); ?>",
                    data: form_data,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if(response == 'success'){
                            if(image_delete == fetched_image.val()){
                                $('.fetched_image_select').val('');
                            }
                            removeElement.remove();
                            if($('.gallery').children('.imageBox').length == 0){
                                document.getElementById('image_select').value = '';                                
                            }
                        }
                        $('.loader-wrapper').hide();
                        $('.overlay').hide();
                    }
                }); 
            } else if (result.dismiss === "cancel") {
                Swal.fire(
                    "Cancelled",
                    "Your imaginary file is safe :)",
                    "error"
                )
            }
        });
    });
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $('#category-dropdown').on('change', function() {
            var cat_id = this.value;
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>"
                },
                type: "POST",
                url: "<?php echo e(route('product.subcategory')); ?>",

                data: {
                    cat_id: cat_id,
                },
                dataType: 'json',
                success: function(response) {
                    $("#subcatdata").html(response.data.result)

                }
            });
        });
        var html = '<?php echo $html ?>';
        var i = $('#addMoreData').children('.row').length-1;
        $('#addMore').on("click", function(){
         i++;
         $('#addMoreData').append('<div class="row removeMoreData">  <div class="col-xl-4"> <div class="form-group">'+
            '<label for="size">Size</label><span class="text-danger"> </span>'+
            '<select name="product_sizes['+i+'][size_id]" class="form-control form-control-solid form-control-lg  <?php $__errorArgs = ["size"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">'+html+
            '</select>' +
            '</div>   </div>' +
            ' <div class="col-xl-4">  <div class="form-group">'+
            '<label for="price"> Price </label><span class="text-danger"> </span>'+
            ' <input type="text" name="product_sizes['+i+'][price]" class="form-control form-control-solid form-control-lg  <?php $__errorArgs = ["price"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old("price")); ?>" placeholder="₱ Price">'+
            '</div>  </div> '+
            '<div class="col-xl-3">'+
                                '<div class="form-group">'+
                                    '<lable for="out_stock" >Out Of Stock<span class="text-danger"> * </span></lable>'+
                                    '<input type="radio" name="product_sizes['+i+'][stock]" id="out_stock" class="" value="0">'+
                                    '<lable for="stock" >In Stock<span class="text-danger">  </span></lable>'+
                                    '<input type="radio" name="product_sizes['+i+'][stock]" id="stock"  value="1">'+
                                '</div>'+
                            '</div>'+
            '<div class="col-xl-1">'+
            '<label></label> <br><br>'+
            '<a href="javascript:void(0)" class="removeMore"><i class="fa fa-trash text-danger" aria-hidden="true"></i></a>'+                
            '</div>  </div>');

     });
    });
    $(document).on('click', '.removeMore', function() {
        $(this).parent().parent().remove()
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/zaz/web/zaz.stage02.obdemo.com/public_html/resources/views/admin/product/edit.blade.php ENDPATH**/ ?>