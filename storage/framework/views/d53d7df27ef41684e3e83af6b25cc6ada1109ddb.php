<?php $__env->startSection('content'); ?> 

<!--begin::Content-->
	<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
		<!--begin::Subheader-->
		<div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
			<div
				class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
				<!--begin::Info-->
				<div class="d-flex align-items-center flex-wrap mr-1">
					<!--begin::Page Heading-->
					<div class="d-flex align-items-baseline flex-wrap mr-5">
						<!--begin::Page Title-->
						<h5 class="text-dark font-weight-bold my-1 mr-5">
							My Account </h5>
						<!--end::Page Title-->

						<!--begin::Breadcrumb-->
						<ul
							class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
							<li class="breadcrumb-item">
								<a href="<?php echo e(route('dashboard')); ?>" class="text-muted">Dashboard</a>
							</li>
						</ul>
						<!--end::Breadcrumb-->
					</div>
					<!--end::Page Heading-->
				</div>
				<!--end::Info-->

				<?php echo $__env->make("admin.elements.quick_links", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			</div>
		</div>
		<!--end::Subheader-->

		<!--begin::Entry-->
		<div class="d-flex flex-column-fluid">
			<!--begin::Container-->
			<div class=" container ">
                <form action="<?php echo e(route('myaccount')); ?>" class="mws-form" method="post">
                 <?php echo csrf_field(); ?>
				<div class="card">
					<div class="card-body">
						<div class="row">
							<div class="col-xl-1"></div>
							<div class="col-xl-10">
								<h3 class="mb-10 font-weight-bold text-dark"></h3>
								<div class="row">
									<div class="col-xl-6">
										<!--begin::Input-->
										<div class="form-group">
                                        <label> Name</label><span class="text-danger"> * </span>
												<input type="text" name="name" class="form-control form-control-solid form-control-lg  <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($userInfo->name ?? old('name')); ?>">
												<?php if($errors->has('name')): ?>
												<div class="alert invalid-feedback admin_login_alert">
													<?php echo e($errors->first('name')); ?>

												</div>
												<?php endif; ?>
										</div>
										<!--end::Input-->
									</div>
								</div>
								<div class="row">
									<div class="col-xl-6">
										<!--begin::Input-->
										<div class="form-group">
                                        <label> Email</label><span class="text-danger"> * </span>
												<input type="text" name="email" class="form-control form-control-solid form-control-lg  <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e($userInfo->email ?? old('email')); ?>">
												<?php if($errors->has('email')): ?>
												<div class="alert invalid-feedback admin_login_alert">
													<?php echo e($errors->first('email')); ?>

												</div>
												<?php endif; ?>
										</div>
										<!--end::Input-->
									</div>
								</div>
								
								<div class="d-flex justify-content-between border-top mt-5 pt-10">
									
									<div>
										<button	button type="submit" class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
											Submit
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</ Form>
        	</div>
		</div>
	</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/gettransfer/web/gettransfer.dev.obdemo.com/public_html/resources/views/admin/dashboard/myaccount.blade.php ENDPATH**/ ?>