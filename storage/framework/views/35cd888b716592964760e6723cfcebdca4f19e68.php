<?php $__env->startSection('content'); ?>
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
        <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                        Add <?php echo e(Config('constants.ACL.ACL_TITLE')); ?> </h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route('dashboard')); ?>" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route($model.'.index')); ?>" class="text-muted"><?php echo e(Config('constants.ACL.ACL_TITLE')); ?></a>
                        </li>
                    </ul>
                </div>
            </div>
            <?php echo $__env->make("admin.elements.quick_links", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
    <div class="d-flex flex-column-fluid">
        <div class=" container ">
            <form action="<?php echo e(route($model.'.store')); ?>" method="post" class="mws-form" autocomplete="off" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-1"></div>
                            <div class="col-xl-10">
                                <h3 class="mb-10 font-weight-bold text-dark">
                                    <?php echo e(Config('constants.ACL.ACL_TITLE')); ?> Information
                                </h3>
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="parent_id">Select Parent</label><span class="text-danger"> </span>
                                            <select class="form-control form-control-solid form-control-lg" name="parent_id" value="<?php echo e(old('parent_id')); ?>">
                                                <option value="<?php echo e(old('parent_id')); ?>">Select Parent </option>
                                                <?php $__currentLoopData = $parent_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($list->id); ?>"><?php echo e($list->title); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="title">Title </label><span class="text-danger"> * </span>
                                            <input type="text" name="title" class="form-control form-control-solid form-control-lg  <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('title')); ?>">
                                            <?php if($errors->has('title')): ?>
                                            <div class="invalid-feedback">
                                                <?php echo e($errors->first('title')); ?>

                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="path">Path </label><span class="text-danger"> * </span>
                                            <input type="text" name="path" class="form-control form-control-solid form-control-lg  <?php $__errorArgs = ['path'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('path')); ?>">
                                            <span>Without Plugin URL: javascript::void();</span>
                                            <?php if($errors->has('path')): ?>
                                            <div class="invalid-feedback">
                                                <?php echo e($errors->first('path')); ?>

                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="module_order">Order </label><span class="text-danger"> * </span>
                                            <input type="text" name="module_order" class="form-control form-control-solid form-control-lg  <?php $__errorArgs = ['module_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('module_order')); ?>">
                                            <?php if($errors->has('module_order')): ?>
                                            <div class="invalid-feedback">
                                                <?php echo e($errors->first('module_order')); ?>

                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <label for="icon">Icon </label><span class="text-danger"> </span>
                                            <textarea name="icon" class="form-control form-control-solid form-control-lg"><?php echo e(old('icon')); ?> </textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between border-top mt-5 pt-10">
                                    <div>
                                        <button button type="submit" class="btn btn-success font-weight-bold text-uppercase px-9 py-4">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function submit_form() {
        $(".mws-form").submit();
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/zaz/web/zaz.stage02.obdemo.com/public_html/resources/views/admin/acl/add.blade.php ENDPATH**/ ?>