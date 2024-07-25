<?php $__env->startSection('content'); ?>
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
   <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
      <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
         <div class="d-flex align-items-center flex-wrap mr-1">
            <div class="d-flex align-items-baseline flex-wrap mr-5">
               <h5 class="text-dark font-weight-bold my-1 mr-5">
               View <?php echo e(Config('constants.STAFF.STAFF_TITLE')); ?> </h5>
               <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                  <li class="breadcrumb-item">
                     <a href="<?php echo e(route('dashboard')); ?>" class="text-muted">Dashboard</a>
                  </li>
                  <li class="breadcrumb-item">
                     <a href="<?php echo e(route($model.'.index')); ?>" class="text-muted"> <?php echo e(Config('constants.STAFF.STAFF_TITLE')); ?></a>
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
                        <a class="nav-link active" data-toggle="tab" href="#kt_apps_contacts_view_tab_1">
                           <span class="nav-text">
                              Staff Management Information
                           </span>
                        </a>
                     </li>

                  </ul>
               </div>
            </div>
            <div class="card-body px-0">
               <div class="tab-content px-10">
                  <div class="tab-pane active" id="kt_apps_contacts_view_tab_1" role="tabpanel">
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Name:</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder"><?php echo e($modell->name ??''); ?></span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Email:</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder"><?php echo e($modell->email ?? ''); ?></span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Phone Number:</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder"> <?php echo e(@$modell->phone_number_prefix ? '+'.$modell->phone_number_prefix : ''); ?><?php echo e($modell->phone_number ?? ''); ?></span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Department:</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder"><?php echo e(DepartmentbyName($modell->department_id) ?? ''); ?></span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Designations:</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder"><?php echo e(DesignationbyName($modell->designation_id )??''); ?></span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Registered On:</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder"><?php echo e(date(config("Reading.date_format"),strtotime($modell->created_at))); ?></span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Status:</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">
                              <?php if($modell->is_active == 1): ?>
                              <span class="label label-lg label-light-success label-inline">Activated</span>
                              <?php else: ?>
                              <span class="label label-lg label-light-danger label-inline">Deactivated</span>
                              <?php endif; ?>
                           </span>
                        </div>
                     </div>
                  </div>
                  <h3 class="mt-8 mb-8">Staff management Permissions</h3>
                  <label class="font-size-lg font-weight-bold checkbox mb-5">
                     <input type="checkbox" class="checkAll" />

                     <div id="accordion" role="tablist" class="accordion accordion-toggle-arrow">
                        <?php
                        $counter   =   0;
                        foreach ($aclModules as $aclModule) {
                        ?>
                           <div class="card mb-5 border-bottom">
                              <div class="card-header d-flex align-items-center" role="tab">
                                 <div class="ml-5">
                                    <label class="checkbox">
                                       <input type="text" name="data[<?php echo e($counter); ?>][value]" value=1  <?php echo e(($aclModule->active == 1) ?  'checked' : ''); ?>>
                                       <input type="checkbox" name="data[<?php echo e($counter); ?>][value]" value=1  id ="<?php echo e($aclModule->id); ?>" class ="parent" Readonly <?php echo e(($aclModule->active == 1) ?  'checked' : ''); ?>>
                                       <input type="hidden" name="data[<?php echo e($counter); ?>][department_id]" value="<?php echo e($aclModule->id); ?>">
                                       <span class="mr-2"></span>
                                    </label>
                                 </div>
                                 <a class="text-dark px-2 py-4 w-100" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo e($counter); ?>" aria-expanded="true" aria-controls="collapse<?php echo e($counter); ?>">
                                    <i class="more-less glyphicon glyphicon-plus"></i>
                                    <?php echo e(strtoupper($aclModule->title ?? '')); ?>

                                 </a>
                              </div>
                              <div id="collapse<?php echo e($counter); ?>" class="collapse" data-parent="#accordion">
                                 <?php
                                 if (!empty($aclModule['sub_module'])) {
                                 ?>
                                    <div class="card-body ">
                                       <div class="">
                                          <?php
                                          $module_counter      =   0;
                                          foreach ($aclModule['sub_module'] as $subModule) {
                                          ?>
                                             <div class="font-size-lg font-weight-bold mb-3"><?php echo e(!empty($subModule->title)?strtoupper($subModule->title):''); ?></div>
                                             <div class="row">
                                                <?php
                                                $count   =   0;
                                                if (!$subModule['module']->isEmpty()) {
                                                   foreach ($subModule['module'] as $module) {
                                                      $count++;
                                                ?>
                                                      <div class="col-auto mb-5">
                                                         <label class="checkbox">
                                                         <input type="text" name="data[<?php echo e($counter); ?>][module][<?php echo e($module_counter); ?>][value]" value=1 class="children child.<?php echo e($aclModule->id); ?>" <?php echo e(($module->active == 1) ? 'checked' : ''); ?>>
                                                            <input type="hidden" name="data[<?php echo e($counter); ?>][id][<?php echo e($module_counter); ?>][id]">
                                                            <input type="hidden" name="data[<?php echo e($counter); ?>][department_module_id][<?php echo e($module_counter); ?>][department_module_id]" value="<?php echo e($subModule->id); ?>">
                                                            <span class="mr-2"></span>
                                                            <?php echo e($module->name); ?>

                                                         </label>
                                                      </div>
                                                   <?php
                                                      $module_counter++;
                                                   }
                                                   ?>
                                                   <td colspan="6-<?php echo e($count); ?>"></td>
                                                <?php
                                                } else {
                                                ?>
                                                   <td colspan="6"></td>
                                                <?php
                                                }
                                                ?>
                                             </div>
                                             <?php
                                          }
                                          if (!empty($aclModule['extModule'])) {
                                             $count   =   0;
                                             foreach ($aclModule['extModule'] as $subModule) {
                                                $count++;
                                             ?>
                                                <div class="font-size-lg font-weight-bold mb-3">
                                                <?php echo e(strtoupper($subModule->title ?? '')); ?>

                                                </div>
                                                <div class="row">
                                                   <?php
                                                   if (!$subModule['module']->isEmpty()) {
                                                      foreach ($subModule['module'] as $module) {
                                                   ?>
                                                         <div class="col-auto mb-5">
                                                            <label class="checkbox">
                                                            <input type="text" name="data[<?php echo e($counter); ?>][module][<?php echo e($module_counter); ?>][value]" value=1 class="children child.<?php echo e($aclModule->id); ?>" <?php echo e(($module->active == 1) ?  'checked' : ''); ?>>
                                                                <input type="hidden" name="data[<?php echo e($counter); ?>][module][<?php echo e($module_counter); ?>][id]" value="<?php echo e($module->id); ?>">
                                                                <input type="hidden" name="data[<?php echo e($counter); ?>][module][<?php echo e($module_counter); ?>][department_module_id]" value="<?php echo e($subModule->id); ?>">
                                                               <span class="mr-2"></span>
                                                               <?php echo e($module->name); ?>

                                                            </label>
                                                         </div>
                                                      <?php
                                                         $module_counter++;
                                                      }
                                                      ?>
                                                      <td colspan="6-<?php echo e($count); ?>"></td>
                                                   <?php
                                                   } else {
                                                   ?>
                                                      <td colspan="5"></td>
                                                   <?php
                                                   }
                                                   ?>
                                                </div>
                                          <?php
                                             }
                                          }
                                          ?>
                                       </div>
                                    <?php
                                 }
                                    ?>
                                    <?php
                                    if (isset($aclModule['parent_module_action'])  && (!$aclModule['parent_module_action']->isEmpty())) {
                                    ?>
                                       <div class="font-size-lg font-weight-bold mb-3"> <?php echo e($aclModule->title); ?> </div>
                                       <div class="row">
                                          <?php
                                          foreach ($aclModule['parent_module_action'] as $parentModule) {
                                          ?>
                                             <div class="col-auto mb-5">
                                                <label class="checkbox">
                                                <input type="text" name="data[<?php echo e($counter); ?>][module][<?php echo e($module_counter); ?>][value]" value=1 class="children child.<?php echo e($aclModule->id); ?>" <?php echo e(($parentModule->active == 1) ?  'checked' : ''); ?>>
                                                <input type="checkbox" name="data[<?php echo e($counter); ?>][module][<?php echo e($module_counter); ?>][value]" value=1 class="children child.<?php echo e($aclModule->id); ?>" <?php echo e(($parentModule->active == 1) ?  'checked' : ''); ?>>
                                                <input type="hidden" name="data[<?php echo e($counter); ?>][module][<?php echo e($module_counter); ?>][id]" value="<?php echo e($parentModule->id); ?>">
                                                <input type="hidden" name="data[<?php echo e($counter); ?>][module][<?php echo e($module_counter); ?>][department_module_id]" value="<?php echo e($aclModule->id); ?>">
                                                <span class="mr-2"></span>
                                                   <?php echo e($parentModule->name); ?>

                                                </label>
                                             </div>
                                          <?php
                                             $counter++;
                                          }
                                          ?>
                                       </div>
                                    <?php
                                    }
                                    ?>
                                    </div>
                              </div>
                           </div>
                        <?php
                           $counter++;
                        }
                        ?>
                     </div>
                     <!--end::Tab Content-->
               </div>
            </div>
            <!--end::Body-->
         </div>

      </div>
      <!--end::Container-->
   </div>
   <!--end::Entry-->
</div>
<!--end::Content-->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/gettransfer/web/gettransfer.stage02.obdemo.com/public_html/resources/views/admin/staff/view.blade.php ENDPATH**/ ?>