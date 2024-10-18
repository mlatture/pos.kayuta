<?php if(Session::has('success')): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo e(Session::get('success')); ?>

<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<?php endif; ?><?php /**PATH C:\Users\THOMAS JON\OneDrive\Desktop\pos.kayuta\resources\views\layouts\partials\alert\success.blade.php ENDPATH**/ ?>