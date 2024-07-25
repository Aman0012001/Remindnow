<?php $i = 1; ?>

<?php $__env->startSection('content'); ?>
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
        <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                        Edit  <?php echo e(Config('constants.FAQ.FAQ_TITLE')); ?> </h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route('dashboard')); ?>" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route($model.'.index')); ?>" class="text-muted"> <?php echo e(Config('constants.FAQ.FAQS_TITLE')); ?></a>
                        </li>
                    </ul>
                </div>
            </div>
            <?php echo $__env->make("admin.elements.quick_links", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
    <div class="d-flex flex-column-fluid">
        <div class="container">
            <form action="<?php echo e(route($model.'.update',base64_encode($faqDetails->id))); ?>" method="post" class="mws-form" autocomplete="off" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="card card-custom gutter-b">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <label name="faq_order">Faq Order</label><span class="text-danger"> * </span>
                                    <input type="text" name="faq_order" class="form-control form-control-solid form-control-lg  <?php $__errorArgs = ['faq_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($faqDetails->faq_order ?? old('faq_order')); ?>">
                                    <?php if($errors->has('faq_order')): ?>
                                    <div class="invalid-feedback">
                                        <?php echo e($errors->first('faq_order')); ?>

                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                            <div class="tab-pane fade <?php echo e(($i==$language_code)?'show active':''); ?>" id="<?php echo e($language->title); ?>" role="tabpanel" aria-labelledby="<?php echo e($language->title); ?>">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="row">
                                            <div class="col-xl-6">
                                                <div class="form-group">
                                                    <?php if($i == 1): ?>
                                                    <label for="<?php echo e($language->id); ?>.question">Question</label><span class="text-danger"> * </span>
                                                    <input type="text" name="data[<?php echo e($language->id); ?>][question]" class="form-control form-control-solid form-control-lg <?php $__errorArgs = ['question'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($multiLanguage[$language->id]['question'] ?? old('question')); ?>">
                                                    <?php if($errors->has('question')): ?>
                                                    <div class="invalid-feedback">
                                                        <?php echo e($errors->first('question')); ?>

                                                    </div>
                                                    <?php endif; ?>
                                                    <?php else: ?>
                                                    <label for="<?php echo e($language->id); ?>.question">Question</label><span class="text-danger"> </span>
                                                    <input type="text" name="data[<?php echo e($language->id); ?>][question]" class="form-control form-control-solid form-control-lg" value="<?php echo e($multiLanguage[$language->id]['question'] ?? old('question')); ?>">
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="col-xl-12">
                                                <div class="form-group">
                                                    <div id="kt-ckeditor-1-toolbar<?php echo e($language->id); ?>"></div>
                                                    <?php if($i == 1): ?>
                                                    <label>Answer </label><span class="text-danger"> * </span>
                                                    <textarea id="body_<?php echo e($language->id); ?>" name="data[<?php echo e($language->id); ?>][answer]" class="form-control form-control-solid form-control-lg  <?php $__errorArgs = ['answer'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('answer')); ?>">
                                                    <?php echo e($multiLanguage[$language->id]['answer'] ?? old('answer')); ?> </textarea>
                                                    <?php if($errors->has('answer')): ?>
                                                    <div class="alert invalid-feedback admin_login_alert">
                                                        <?php echo e($errors->first('answer')); ?>

                                                    </div>
                                                    <?php endif; ?>
                                                    <?php else: ?>
                                                    <label>Answer </label>
                                                    <textarea name="data[<?php echo e($language->id); ?>][answer]" id="body_<?php echo e($language->id); ?>" class="form-control form-control-solid form-control-lg">
                                                    <?php echo e($multiLanguage[$language->id]['answer'] ?? old('answer')); ?></textarea>
                                                    <?php endif; ?>
                                                </div>
                                                <script src="<?php echo e(asset('/js/ckeditor/ckeditor.js')); ?>"></script>
                                                <script>
                                                    CKEDITOR.replace(<?php echo 'body_' . $language->id; ?>, {
                                                        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
                                                        removeButtons:'New Page,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteWord,Save,PasteFromWord,Undo,Redo,Find,Replace,SelectAll,Form,Checkbox,RadioButton,HiddenField,Strike,Subscript,Superscript,Language,Link,Unlink,Anchor,ShowBlocks',
                                                        enterMode: CKEDITOR.ENTER_BR
                                                    });
                                                    CKEDITOR.config.allowedContent = true;
                                                    CKEDITOR.config.removePlugins = 'scayt';
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php $i++; ?>
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
<?php echo $__env->make('admin.layouts.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/melosync/web/melosyncpanel.stage04.obdemo.com/public_html/resources/views/admin/faqs/edit.blade.php ENDPATH**/ ?>