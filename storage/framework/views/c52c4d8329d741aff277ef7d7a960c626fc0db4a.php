<<<<<<< HEAD
=======
<div class="tab-content mt-2" id="myTabContent">
    <div class="tab-pane fade show active" id="quick" role="tabpanel" aria-labelledby="quick-tab">
        <div class="order-product product-section">
            <div class="row product-list" id="product-list">
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($product->quantity != 0): ?>
                        <div class="col-md-3" style="cursor: pointer">
                            <div class="card product-item" data-barcode="<?php echo e($product->barcode); ?>"
                                data-id="<?php echo e($product->id); ?>" data-bs-toggle="tooltip" data-bs-placement="top"
                                data-bs-html="true" title="Product Name: <?php echo e($product->name); ?>">
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo e($product->quantity < 0 ? '*' : $product->quantity); ?>


                                </span>
                                <?php
                                    $imagePath = 'images/products/' . $product->image;
                                    $fallbackImageUrl = asset('images/product-thumbnail.jpg');

                                    if (!empty($product->image) && file_exists(public_path($imagePath))) {
                                        $imageUrl = asset($imagePath);
                                    } else {
                                        $imageUrl = $fallbackImageUrl;
                                    }
                                ?>
                                               

                                <img    src="<?php echo e($product->image && Storage::disk('public')->exists('products/' . $product->image) ? Storage::url('products/' . $product->image) : Storage::url('product-thumbnail.jpg')); ?>"  class="rounded mx-auto d-block img-fluid"
                                    alt="Product Image">



                                <div class="card-body">
                                    <div class="btn-products-container">
                                        <p class="card-text t"><?php echo e(Str::limit($product->name, 10)); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="catgories" role="tabpanel" aria-labelledby="catgories-tab">
        <div class="order-product category-section">
            <div class="row">
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-3" style="cursor: pointer">
                        <div class="card category-item" data-id="<?php echo e($category->id); ?>" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="<?php echo e($category->name); ?>">
                            <img src="<?php echo e(asset('images/product-thumbnail.jpg')); ?>"
                                class="rounded mx-auto d-block img-fluid" alt="Product Image">


                            <div class="card-body">
                                <div class="btn-products-container">
                                    <p class="card-text t"><?php echo e(Str::limit($category->name, 10)); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>



    
</div>
<?php /**PATH C:\Users\THOMAS JON\OneDrive\Desktop\pos.kayuta\resources\views/cart/tabpanel.blade.php ENDPATH**/ ?>
>>>>>>> main
