<?php $__env->startSection('content'); ?>
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
    <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
      <div class="d-flex align-items-center flex-wrap mr-1">
        <div class="d-flex align-items-baseline flex-wrap mr-5">
          <h5 class="text-dark font-weight-bold my-1 mr-5">
            View    <?php echo e(Config('constants.PRODUCT.PRODUCT_TITLE')); ?>

          </h5>
          <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
            <li class="breadcrumb-item">
              <a href="<?php echo e(route('dashboard')); ?>" class="text-muted">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
              <a href="<?php echo e(route($model.'.index')); ?>" class="text-muted">  <?php echo e(Config('constants.PRODUCT.PRODUCT_TITLES')); ?></a>
            </li>
          </ul>
        </div>
      </div>
      <?php echo $__env->make("admin.elements.quick_links", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
  </div>
  <div class="d-flex flex-column-fluid">
    <div class=" container ">
      <div class="card card-custom gutter-b">
        <div class="card-header card-header-tabs-line">
          <div class="card-toolbar">
            <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-bold nav-tabs-line-3x" role="tablist">
              <li class="nav-item">
                <a class="nav-link active hide_me" data-toggle="tab" href="#kt_apps_contacts_view_tab_1">
                  <span class="nav-text">
                    <?php echo e(Config('constants.PRODUCT.PRODUCT_TITLE')); ?> Information
                  </span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link  hide_me" data-toggle="tab" href="#kt_apps_contacts_view_tab_2">
                  <span class="nav-text">
                    <?php echo e(Config('constants.PRODUCT.PRODUCT_TITLE')); ?> Image
                  </span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link  hide_me" data-toggle="tab" href="#kt_apps_contacts_view_tab_3">
                  <span class="nav-text">
                    <?php echo e(Config('constants.PRODUCT.PRODUCT_TITLE')); ?> Size
                  </span>
                </a>
              </li>
            </div>
          </div>
          <div class="card-body px-0">
            <div class="tab-content px-10">
              <div class="tab-pane active" id="kt_apps_contacts_view_tab_1" role="tabpanel">
                <div class="form-group row my-2">
                  <label class="col-4 col-form-label">  Name:</label>
                  <div class="col-8">
                    <span class="form-control-plaintext font-weight-bolder"><?php echo e(ucwords($ProductDetails->name ?? '')); ?></span>
                  </div>
                </div>                     
                <div class="form-group row my-2">
                  <label class="col-4 col-form-label">Price:</label>
                  <div class="col-8">
                    <span class="form-control-plaintext font-weight-bolder"> ₱ <?php echo e($ProductDetails->price ?? ''); ?></span>
                  </div>
                </div>
                <div class="form-group row my-2">
                  <label class="col-4 col-form-label">Category Name:</label>
                  <div class="col-8">
                    <span class="form-control-plaintext font-weight-bolder"> <?php echo e(@$ProductDetails->productcat->name ?? ''); ?></span>
                  </div>
                </div>
                <div class="form-group row my-2">
                  <label class="col-4 col-form-label">Sub Category Name:</label>
                  <div class="col-8">
                    <span class="form-control-plaintext font-weight-bolder"> <?php echo e(@$ProductDetails->productsubcat->name ?? ''); ?></span>
                  </div>
                </div>

                <div class="form-group row my-2">
                  <label class="col-4 col-form-label">Status:</label>
                  <div class="col-8">
                    <span class="form-control-plaintext font-weight-bolder">
                      <?php if($ProductDetails->is_active == 1): ?>
                      <span class="label label-lg label-light-success label-inline">Activated</span>
                      <?php else: ?>
                      <span class="label label-lg label-light-danger label-inline">Deactivated</span>
                      <?php endif; ?>
                    </span>
                  </div>
                </div>
                <div class="form-group row my-2">
                  <label class="col-4 col-form-label">Stock:</label>
                  <div class="col-8">
                    <span class="form-control-plaintext font-weight-bolder">
                      <?php if($ProductDetails->in_stock == 1): ?>
                      <span class="label label-lg label-light-success label-inline">In Stock</span>
                      <?php else: ?>
                      <span class="label label-lg label-light-danger label-inline">Out Of Stock</span>
                      <?php endif; ?>
                    </span>
                  </div>
                </div>
                <div class="form-group row my-2">
                  <label class="col-4 col-form-label">Cover Image:</label>
                  <div class="col-3">
                    <span class="form-control-plaintext font-weight-bolder"> <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo e(url(Config::get('constants.PRODUCT_IMAGE').$ProductDetails->cover_image  ?? '')); ?>"> <img height="70" width="70" src="<?php echo e(url(Config::get('constants.PRODUCT_IMAGE').$ProductDetails->cover_image  ?? '')); ?>" /></a></span>
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="kt_apps_contacts_view_tab_2" role="tabpanel">
                <h3>Images</h3>
                <?php if(count($ProductImages) > 0): ?>
                <div class="form-group row my-2">
                  <?php $__currentLoopData = $ProductImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $Img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <div class="col-3">
                    <span class="form-control-plaintext font-weight-bolder"> <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo e(url(Config::get('constants.PRODUCT_IMAGE').@$Img->thumbnail_image  ?? '')); ?>"> <img height="70" width="70" src="<?php echo e(url(Config::get('constants.PRODUCT_IMAGE').@$Img->thumbnail_image  ?? '')); ?>" /></a></span>
                  </div>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div> 
                <?php else: ?>
                <div colspan="6" style="text-align:center;"><?php echo e(trans("Image not found.")); ?></div>
                <?php endif; ?>
              </div>
              <div class="tab-pane" id="kt_apps_contacts_view_tab_3" role="tabpanel">
                <h3>Size's </h3>
                <?php if(count($ProductSize) > 0): ?>
                <?php $__currentLoopData = $ProductSize; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $Size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <div class="form-group row my-2">
                    <label class="col-4 col-form-label">  Size:</label>
                    <div class="col-8">
                      <span class="form-control-plaintext font-weight-bolder"><?php echo e(ucwords(@$Size->sizeName->name ?? '')); ?></span>
                    </div>
                  </div>                     
                  <div class="form-group row my-2">
                    <label class="col-4 col-form-label">Price:</label>
                    <div class="col-8">
                      <span class="form-control-plaintext font-weight-bolder"> ₱ <?php echo e($Size->price ?? ''); ?></span>
                    </div>
                  </div>
                  <div class="form-group row my-2">
                    <label class="col-4 col-form-label">Stock:</label>
                    <div class="col-8">
                      <span class="form-control-plaintext font-weight-bolder">
                        <?php if($Size->in_stock == 1): ?>
                        <span class="label label-lg label-light-success label-inline">In Stock</span>
                        <?php else: ?>
                        <span class="label label-lg label-light-danger label-inline">Out Of Stock</span>
                        <?php endif; ?>
                      </span>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                <div colspan="6" style="text-align:center;"><?php echo e(trans("Image not found.")); ?></div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/zaz/web/zaz.stage02.obdemo.com/public_html/resources/views/admin/product/view.blade.php ENDPATH**/ ?>