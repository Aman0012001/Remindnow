
<?php $__env->startSection('content'); ?>
<?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('counter', [])->html();
} elseif ($_instance->childHasBeenRendered('ZCQgvtP')) {
    $componentId = $_instance->getRenderedChildComponentId('ZCQgvtP');
    $componentTag = $_instance->getRenderedChildComponentTagName('ZCQgvtP');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('ZCQgvtP');
} else {
    $response = \Livewire\Livewire::mount('counter', []);
    $html = $response->html();
    $_instance->logRenderedChild('ZCQgvtP', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?> 

<?php echo \Livewire\Livewire::scripts(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layout.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\crafto\resources\views/frontend/pages/index.blade.php ENDPATH**/ ?>