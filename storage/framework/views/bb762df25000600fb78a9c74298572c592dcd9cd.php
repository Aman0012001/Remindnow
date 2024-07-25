<?php $__env->startSection('content'); ?>
<!--begin::Content-->
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
	<!--begin::Subheader-->
	<div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
		<div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
			<div class="d-flex align-items-center flex-wrap mr-1">
				<div class="d-flex align-items-baseline flex-wrap mr-5">
					<h5 class="text-dark font-weight-bold my-1 mr-5">
						Add <?php echo e(Config('constants.EMAIL_TEMPLATES_TITLE.EMAIL_TEMPLATE')); ?> </h5>
					<ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
						<li class="breadcrumb-item">
							<a href="<?php echo e(route('dashboard')); ?>" class="text-muted">Dashboard</a>
						</li>
						<li class="breadcrumb-item">
							<a href="<?php echo e(route($model.'.index')); ?>" class="text-muted"><?php echo e(Config('constants.EMAIL_TEMPLATES_TITLE.EMAIL_TEMPLATES')); ?></a>
						</li>
					</ul>
				</div>
			</div>
			<?php echo $__env->make("admin.elements.quick_links", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		</div>
	</div>

	<!--begin::Entry-->
	<div class="d-flex flex-column-fluid">
		<div class=" container ">
			<form action="<?php echo e(route($model.'.store')); ?>" method="post" class="mws-form">
				<?php echo csrf_field(); ?>
				<div class="card card-custom gutter-b">
					<?php if(count($languages) > 1): ?>
					<div class="card-header card-header-tabs-line">
						<div class="card-toolbar border-top">
							<ul class="nav nav-tabs nav-bold nav-tabs-line">
								<?php if(!empty($languages)): ?>
								<?php $i = 1; ?>
								<?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<li class="nav-item">
									<a class="nav-link <?php echo e(($i==$language_code )?'active':''); ?>" data-toggle="tab" href="#<?php echo e($language->title); ?>">
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
					<?php endif; ?>
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
												<!--begin::Input-->
												<div class="form-group">
													<?php if($i == 1): ?>
													<lable name="<?php echo e($language->id); ?>.'.name'">Name</lable><span class="text-danger"> * </span>
													<input type="text" name="data[<?php echo e($language->id); ?>][name]" class="form-control form-control-solid form-control-lg <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('name')); ?>">
													<?php if($errors->has('name')): ?>
													<div class="invalid-feedback">
														<?php echo e($errors->first('name')); ?>

													</div>
													<?php endif; ?>
													<?php else: ?>
													<lable name="<?php echo e($language->id); ?>.'.name'">Name</lable><span class="text-danger"> </span>
													<input type="text" name="data[<?php echo e($language->id); ?>][name]" class="form-control form-control-solid form-control-lg" value="<?php echo e(old('name')); ?>">
													<?php endif; ?>
												</div>
											</div>
											<div class="col-xl-6">
												<div class="form-group">
													<?php if($i == 1): ?>
													<lable name="<?php echo e($language->id); ?>.'.subject'">Subject</lable><span class="text-danger"> * </span>
													<input type="text" name="data[<?php echo e($language->id); ?>][subject]" class="form-control form-control-solid form-control-lg <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('subject')); ?>">
													<?php if($errors->has('subject')): ?>
													<div class="invalid-feedback">
														<?php echo e($errors->first('subject')); ?>

													</div>
													<?php endif; ?>
													<?php else: ?>
													<lable name="<?php echo e($language->id); ?>.'.subject'">Subject</lable><span class="text-danger"> </span>
													<input type="text" name="data[<?php echo e($language->id); ?>][subject]" class="form-control form-control-solid form-control-lg" value="<?php echo e(old('subject')); ?>">
													<?php endif; ?>
												</div>
											</div>
											<div class="col-xl-6">
												<div class="form-group">
													<?php if($i == 1): ?>
													<lable for="action">Action</lable><span class="text-danger"> * </span>
													<select name="data[<?php echo e($language->id); ?>][action]" id="action" class="form-control form-control-solid form-control-lg <?php $__errorArgs = ['action'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
														<option value="">Select Action</option>
														<?php $__currentLoopData = $Action_options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
														<option value="<?php echo e($action->action); ?>"><?php echo e($action->action); ?></option>
														<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													</select>
													<?php if($errors->has('action')): ?>
													<div class="invalid-feedback">
														<?php echo e($errors->first('action')); ?>

													</div>
													<?php endif; ?>
													<?php else: ?>
													<lable for="action">Action</lable><span class="text-danger"> </span>
													<select name="data[<?php echo e($language->id); ?>][action]" id="action" class="form-control form-control-solid form-control-lg">
														<option value="">Select Action</option>
														<?php $__currentLoopData = $Action_options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
														<option value="<?php echo e($action->action); ?>"><?php echo e($action->action); ?></option>
														<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													</select>
													<?php endif; ?>
												</div>
											</div>
									

										<div class="col-xl-6">
											<div class="form-group">
												<lable for="Constants">Constants</lable><span class="text-danger"> * </span>
												<select name="constants" id="constants" class="form-control form-control-solid form-control-lg">
												</select>

												<a onclick="return InsertHTML(<?php echo e($i); ?>)" href="javascript:void(0)" class="btn btn-lg btn-success no-ajax pull-right"><i class="icon-white "></i><?php echo e(trans("Insert Variable")); ?> </a>
											</div>

										</div>

										<div class="col-xl-12">
											<!--begin::Input-->
											<div class="form-group">
												<div id="kt-ckeditor-1-toolbar<?php echo e($language->id); ?>"></div>
												<?php if($i == 1): ?>
												<lable for="<?php echo e($language->id); ?>.'.body'">Email Body </lable><span class="text-danger"> * </span>
												<textarea id="body_<?php echo e($language->id); ?>" name="data[<?php echo e($language->id); ?>][body]" class="form-control form-control-solid form-control-lg  <?php $__errorArgs = ['body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('body')); ?>">
                                                     </textarea>
												<?php if($errors->has('body')): ?>
												<div class="invalid-feedback">
													<?php echo e($errors->first	('body')); ?>

												</div>
												<?php endif; ?>
												<?php else: ?>
												<lable for="<?php echo e($language->id); ?>.'.body'">Email Body </lable><span class="text-danger"> </span>
												<textarea id="body_<?php echo e($language->id); ?>" name="data[<?php echo e($language->id); ?>][body]" class="form-control form-control-solid form-control-lg " value="<?php echo e(old('body')); ?>">
                                                    </textarea>

												<?php endif; ?>
											</div>
											<script src="<?php echo e(asset('/public/js/ckeditor/ckeditor.js')); ?>"></script>
											<script>
												/* CKEDITOR for Email Body */
												CKEDITOR.replace(<?php echo 'body_' . $language->id; ?>, {
													filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
													enterMode: CKEDITOR.ENTER_BR
												});
												CKEDITOR.config.allowedContent = true;
											</script>
											<!--end::Input-->
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
	</div>
</div>
</form>
</div>
<?php $constant = ''; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script type='text/javascript'>
	var myText = '<?php echo $constant; ?>';
	$(function() {
		constant();
	});


	function InsertHTML() {

		var strUser = document.getElementById("constants").value;
		if (strUser != '') {
			var newStr = '{' + strUser + '}';
			var oEditor = CKEDITOR.instances["body"];
			oEditor.insertHtml(newStr);
		}
	}
	/* this function used for get constant,define in email template*/
	$('#action').change(function() {
		var constant = document.getElementById("action").value;
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: "<?php echo URL()->to('adminpnlx/email-templates/get-constant') ?>",
			type: "POST",
			data: {
				constant: constant
			},
			dataType: 'json',
			success: function(r) {
				$('#constants').empty();
				$('#constants').append('<option value="">-- Select One --</option>');
				$.each(r, function(val, text) {
					var sel = '';
					if (myText == text) {
						sel = 'selected="selected"';
					}

					$('#constants').append('<option value="' + text + '" ' + sel + '>' + text + '</option>');
				});
			}
		});
		return false;
	});
</script>
<style>
	.table>thead>tr>th,
	.table>tbody>tr>th,
	.table>tfoot>tr>th,
	.table>thead>tr>td,
	.table>tbody>tr>td,
	.table>tfoot>tr>td {
		font-size: 14px !important;
		padding: 0px !important;
	}

	.table>thead>tr>th,
	.table>tbody>tr>th,
	.table>tfoot>tr>th,
	.table>thead>tr>td,
	.table>tbody>tr>td,
	.table>tfoot>tr>td {
		vertical-align: top !important;
	}

	.table-bordered>thead>tr>th,
	.table-bordered>tbody>tr>th,
	.table-bordered>tfoot>tr>th,
	.table-bordered>thead>tr>td,
	.table-bordered>tbody>tr>td,
	.table-bordered>tfoot>tr>td {
		border: 0px !important;
	}

	.table>thead>tr>th,
	.table>tbody>tr>th,
	.table>tfoot>tr>th,
	.table>thead>tr>td,
	.table>tbody>tr>td,
	.table>tfoot>tr>td {
		border-top: 0px !important;
		padding: 0px !important;
	}

	.table-bordered {
		border: 0px !important;
	}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/gettransfer/web/gettransfer.dev.obdemo.com/public_html/resources/views/admin/email-templates/add.blade.php ENDPATH**/ ?>