<?php if($catData): ?>
<select name="subcategory_id" id="category-dropdown" class="form-control form-control-solid form-control-lg <?php $__errorArgs = ['subcategory_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
    <option value="">Select Sub Category</option>
    <?php $__currentLoopData = $catData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <option value="<?php echo e($subcat->id); ?>" <?php echo e($subcat->id == $subcategory_id ? 'selected' : ''); ?>>  <?php echo e($subcat->name); ?> </option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>
<?php endif; ?>
<?php /**PATH /home/zaz/web/zaz.stage02.obdemo.com/public_html/resources/views/admin/product/sub-category.blade.php ENDPATH**/ ?>