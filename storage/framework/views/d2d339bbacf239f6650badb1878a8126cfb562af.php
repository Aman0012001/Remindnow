<?php $__env->startSection('content'); ?>
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
		<div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
			<div class="d-flex align-items-center flex-wrap mr-1">
				<div class="d-flex align-items-baseline flex-wrap mr-5">
					<h5 class="text-dark font-weight-bold my-1 mr-5">
						Edit <?php echo e(Config('constants.DESIGNATION.DESIGNATION_TITLE')); ?> </h5>
					<ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
						<li class="breadcrumb-item">
							<a href="<?php echo e(route('dashboard')); ?>" class="text-muted">Dashboard</a>
						</li>
						<li class="breadcrumb-item">
							<a href="<?php echo e(route('designations.index')); ?>" class="text-muted"><?php echo e(Config('constants.DESIGNATION.DESIGNATIONS_TITLE')); ?></a>
						</li>
					</ul>
				</div>
			</div>
			<?php echo $__env->make("admin.elements.quick_links", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		</div>
	</div>
	<div class="d-flex flex-column-fluid">
		<div class=" container ">
			<form action="" method="post" class=mws-form files=true autocomplete="off">
				<?php echo csrf_field(); ?>
				<div class="card">
					<div class="card-body">
						<div class="row">
							<div class="col-xl-1"></div>
							<div class="col-xl-10">
								<h3 class="mb-10 font-weight-bold text-dark">
									<?php echo e(Config('constants.DESIGNATION.DESIGNATION_TITLE')); ?> Information
								</h3>
								<div class="row">
									<div class="col-xl-6">
										<div class="form-group">
											<label for="name">Name</label><span class="text-danger"> * </span>
											<input type="text" name="name" class="form-control form-control-solid form-control-lg   <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($modell->name ?? ''); ?>">
											<?php if($errors->has('name')): ?>
											<div class=" invalid-feedback">
												<?php echo e($errors->first('name')); ?>

											</div>
											<?php endif; ?>
										</div>
									</div>
								</div>
								<?php if(!empty($aclModules)): ?>
								<h3 class="mt-8 mb-8">Designation Permissions</h3>
								<label class="font-size-lg font-weight-bold checkbox mb-5">
									<input type="checkbox" class="checkAll" />
									<span class="mr-2"></span>
									Check All
								</label>
								<div id="accordion" role="tablist" class="accordion accordion-toggle-arrow">
                                            <?php
                                            $counter    =    0;
                                            foreach ($aclModules as $aclModule) {
                                            ?>
                                                <div class="card mb-5 border-bottom">
                                                    <div class="card-header d-flex align-items-center" role="tab">
                                                        <div class="ml-5">
                                                            <label class="checkbox">
                                                                <input type="checkbox" name="data[<?php echo e($counter); ?>][value]" value=1 class="parent" id="<?php echo e($aclModule->id); ?>" <?php echo e(($aclModule->active == 1) ? 'checked' : ''); ?>>
                                                                <input type="hidden" name="data[<?php echo e($counter); ?>][designation_id]" value="<?php echo e($aclModule->id); ?>">
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
                                                                    $module_counter        =    0;
                                                                    foreach ($aclModule['sub_module'] as $subModule) {
                                                                    ?>
                                                                        <div class="font-size-lg font-weight-bold mb-3"><?php echo e(!empty($subModule->title)?strtoupper($subModule->title):''); ?></div>
                                                                        <div class="row">
                                                                            <?php
                                                                            $count    =    0;
                                                                            if (!$subModule['module']->isEmpty()) {
                                                                                foreach ($subModule['module'] as $module) {
                                                                                    $count++;
                                                                            ?>
                                                                                    <div class="col-auto mb-5">
                                                                                        <label class="checkbox">
                                                                                            <input type="checkbox" id="<?php echo e($aclModule->id); ?>" name="data[<?php echo e($counter); ?>][module][<?php echo e($module_counter); ?>][value]" value=1 class="children child<?php echo e($aclModule->id); ?>" <?php echo e(($module->active == 1) ? 'checked' : ''); ?>>
                                                                                            <input type="hidden" name="data[<?php echo e($counter); ?>][module][<?php echo e($module_counter); ?>][id]" value="<?php echo e($module->id); ?>">
                                                                                            <input type="hidden" name="data[<?php echo e($counter); ?>][module][<?php echo e($module_counter); ?>][designation_module_id]" value="<?php echo e($subModule->id); ?>">
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
                                                                        $count    =    0;
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
                                                                                                <label class="checkbox">
                                                                                                    <input type="checkbox" id="<?php echo e($aclModule->id); ?>" name="data[<?php echo e($counter); ?>][module][<?php echo e($module_counter); ?>][value]" value=1 class="children child<?php echo e($aclModule->id); ?>" <?php echo e(($module->active == 1) ?  'checked' : ''); ?>>

                                                                                                    <input type="hidden"  name="data[<?php echo e($counter); ?>][module][<?php echo e($module_counter); ?>][id]" value="<?php echo e($module->id); ?>">

                                                                                                    <input type="hidden" name="data[<?php echo e($counter); ?>][module][<?php echo e($module_counter); ?>][designation_module_id]" value="<?php echo e($subModule->id); ?>">
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
                                                                                <input type="checkbox" id="<?php echo e($aclModule->id); ?>" name="data[<?php echo e($counter); ?>][module][<?php echo e($module_counter); ?>][value]" value=1 class="children child<?php echo e($aclModule->id); ?>" <?php echo e(($parentModule->active == 1) ? 'checked' : ''); ?>>

                                                                                <input type="hidden" name="data[<?php echo e($counter); ?>][module][<?php echo e($module_counter); ?>][id]" value="<?php echo e($parentModule->id); ?>">
                                                                                <input type="hidden" name="data[<?php echo e($counter); ?>][module][<?php echo e($module_counter); ?>][designation_module_id]" value="<?php echo e($aclModule->id); ?>">
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
								<?php endif; ?>
								<div class="d-flex justify-content-between border-top mt-5 pt-10">
									<div>
										<button button type="button" onclick="submit_form();" class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
											Submit
										</button>
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

<script type="text/javascript">
    $(document).ready(function() {
        if ($(".parent:input").val() == 1) {
            var parentid = $(".parent:input:checked").attr('id');
            $('.child' + parentid).attr('checked', true);
        }
        $(".checkAll").click(function() {
            $(".parent:input").not(this).prop('checked', this.checked);
            $(".children:input").not(this).prop('checked', this.checked);
        });
        $(".parent:input").click(function() {
            var parentid = $(this).attr('id');
            var isChecked = $("#"+parentid).prop("checked");
           
            $('.child' + parentid).not(this).prop('checked', this.checked);
        });
        
        $(".children").click(function() {
            var childid = $(this).attr('id');
            // $('#' + childid).not(this).prop('checked', this.checked);
        });

    });
</script>
<script>
	function submit_form() {
		$(".mws-form").submit();
	}
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/gettransfer/web/gettransfer.stage02.obdemo.com/public_html/resources/views/admin/designations/edit.blade.php ENDPATH**/ ?>