<?php $__env->startSection('content'); ?>
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
        <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                    Add new <?php echo e(Config('constants.STAFF.STAFF_TITLE')); ?> </h5>
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
            <form action="<?php echo e(route($model.'.store')); ?>" method="post" class="mws-form" autocomplete="off" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-1"></div>
                            <div class="col-xl-10">
                                <h3 class="mb-10 font-weight-bold text-dark">
                                Manager Information
                                </h3>
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="name"> Name</label><span class="text-danger"> * </span>
                                            <input type="text" name="name" class="form-control alfa form-control-solid form-control-lg  <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('name')); ?>">
                                            <?php if($errors->has('name')): ?>
                                            <div class=" invalid-feedback">
                                                <?php echo e($errors->first('name')); ?>

                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="email"> Email</label><span class="text-danger"> * </span>
                                            <input type="text" name="email" class="form-control form-control-solid form-control-lg  <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('email')); ?>">
                                            <?php if($errors->has('email')): ?>
                                            <div class=" invalid-feedback">
                                                <?php echo e($errors->first('email')); ?>

                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                  
                                  
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="phone_number">Password</label><span class="text-danger"> * </span>
                                            <input type="password" name="password" class="form-control form-control-solid form-control-lg  <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('password')); ?>">
                                            <?php if($errors->has('password')): ?>
                                            <div class=" invalid-feedback">
                                                <?php echo e($errors->first('password')); ?>

                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="phone_number">Confirm Password</label><span class="text-danger"> * </span>
                                            <input type="password" name="confirm_password" class="form-control form-control-solid form-control-lg  <?php $__errorArgs = ['confirm_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('confirm_password')); ?>">
                                            <?php if($errors->has('confirm_password')): ?>
                                            <div class=" invalid-feedback">
                                                <?php echo e($errors->first('confirm_password')); ?>

                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <h3 class="mb-10 font-weight-bold text-dark">
                                    Staff Management Department Information</h3>
                                <div class="row">
                                   
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                        
                                            <label for="designation_id">Role</label><span class="text-danger"> * </span>
                                            <select name="designation_id" id="designation_id" class=" form-control form-control-solid form-control-lg chosenselect_designation_id <?php $__errorArgs = ['designation_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                <option value="">Select Role</option>
                                                <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $departments): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($departments->id); ?>"> <?php echo e($departments->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <?php if($errors->has('designation_id')): ?>
                                            <div class=" invalid-feedback">
                                                <?php echo e($errors->first('designation_id')); ?>

                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="staffPermission"></div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between border-top mt-5 pt-10">
                                    <div>
                                        <button button type="button" onclick="submit_form();" class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
                                            SUBMIT
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
            $('.child' + parentid).not(this).prop('checked', this.checked);
        });

        $(".children").click(function() {
            var childid = $(this).attr('id');
            // $('#' + childid).not(this).prop('checked', this.checked);
        });
    });
</script>
<script>
    function DepartmentList() {
        departmentid = ($(".DepartmentList").val() != "") ? $(".DepartmentList").val() : 0;
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>"
            },
            url: '<?php echo e(route("$model.getDesignations")); ?>',
            type: "POST",
            data: {
                'departmentid': departmentid,
                "selctedid": "0"
            },
            success: function(response) {
                $(".designation_iddrop").html(response);
            }
        });
    }
    $(function() {
        $(".DepartmentList").change(function() {
            DepartmentList();
        });
        DepartmentList();
    });

    $('#designation_id').on('change', function() {
        var id = $(this).val();
        $.ajax({
            url: '<?php echo e(route("$model.getStaffPermission")); ?>',
            type: 'POST',
            data: {
                'designation_id': id
            }, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(r) {
                if (r != '') {
                    $('.staffPermission').html(r);
                } else {
                    show_message(r.message, 'error');
                }

            }
        });
    });

    function submit_form() {
        $(".mws-form").submit();
    }
</script>


<script >
    $(document).ready(function(){
        $("#phone_number_country").intlTelInput({
                                    allowDropdown:true,
                                    preferredCountries:[],
                                    initialCountry: 'IN',
                                    separateDialCode:true
                                });
    $("#phone_number_country").on('countrychange', function (e, countryData) {

        var data    = $(".iti__selected-dial-code").html();
        var data1   = $("#phone_number_country").intlTelInput("getSelectedCountryData").iso2;
     
        $("#dial_code").val(data);
        $("#country_code").val(data1);

       
    });
    })
   
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/zaz/web/zaz.dev.obdemo.com/public_html/resources/views/admin/staff/add.blade.php ENDPATH**/ ?>