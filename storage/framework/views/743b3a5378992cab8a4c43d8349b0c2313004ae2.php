<tr class="panel panel-default delete_add_more_accor_<?php echo e($counter); ?>" rel="<?php echo e($counter); ?>">
	<td>
		<div class="mws-form-item">
            <input type="text" name="data[<?php echo e($counter); ?>][name]"data-rel="<?php echo e($counter); ?>" class="form-control">

		</div>
	</td>
	<td>
		<div class="mws-form-item">
        <input type="text" name="data[<?php echo e($counter); ?>][function_name]"data-rel="<?php echo e($counter); ?>" class="form-control">
		
		</div>
	</td>
	<td> 
		<a href="javascript:void(0);" onclick="del_row('<?php echo e($counter); ?>');" class="btn btn-danger btn-small" >
		<i class="fa fa-trash"></i>
		</a>
	</td>
</tr><?php /**PATH /home/gettransfer/web/gettransfer.dev.obdemo.com/public_html/resources/views/admin/acl/add_more.blade.php ENDPATH**/ ?>