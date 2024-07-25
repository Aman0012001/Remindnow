<!doctype html>
<html class="no-js" lang="en">
  <head>
    <?php echo $__env->make('frontend.elements.seo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('frontend.elements.commoncss', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  </head>
  <body data-mobile-nav-style="full-screen-menu" data-mobile-nav-bg-color="#252840">
    <?php echo $__env->make('frontend.elements.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->yieldContent('content'); ?>
    <?php echo $__env->make('frontend.elements.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('frontend.elements.commonjs', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  </body>
</html><?php /**PATH C:\xampp\htdocs\crafto\resources\views/frontend/layout/app.blade.php ENDPATH**/ ?>