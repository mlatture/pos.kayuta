<section class="kayuta-lake-sec">
    <div class="container">

        <div class="row">
            <div class="col-lg-6">
                <div class="site-detail-wrapper">
                    <h3><?php echo e($siteDetail->sitename); ?></h3>
                    <?php if($uscid != $uscod && $siteclass != 'Amenity' && isset($siteDetail) && isset($rateTier)): ?>
                        <?php if($lengthofStay < $minimumstay): ?>
                            <p class="card-text mb-0">
                                Site <?php echo e($siteid); ?> is not available for your selected dates as there is a longer minimum stay required.<br>
                                please adjust dates below.<br>
                            </p>
                        <?php else: ?>
                            <p class="card-text mb-0">
                                Site <?php echo e($siteid); ?> is available for your selected dates.<br>
                                <?php echo e(date('l, F jS Y', strtotime($uscid))); ?> - To -
                                <?php echo e(date('l, F jS Y', strtotime($uscod))); ?><br>
                                For a <?php echo e($lengthofStay); ?> night stay the price is
                                <?php echo e(\App\CPU\Helpers::format_currency_usd($workingtotal)); ?> The average nightly rate
                                is:
                                <?php echo e(\App\CPU\Helpers::format_currency_usd($avgnightlyrate)); ?><br>
                                <br>
                            </p>
                        <?php endif; ?>

                        <p>
                            <?php if($siteLock == 'On'): ?>
                                <?php echo e($siteLockMessage); ?>

                            <?php endif; ?>
                        </p>
                        <p>
                        </p>

                        <?php if($thissiteisavailable): ?>
                            <?php
                                $eventname = filter_var($eventname, FILTER_SANITIZE_SPECIAL_CHARS);
                            ?>
                            <form id="reservationCartForm" action="<?php echo e(route('reservations.add-to-cart')); ?>" method="post">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="cartid" value="<?php echo e(uniqid()); ?>">
                                <input type="hidden" name="cid" value="<?php echo e($uscid); ?>">
                                <input type="hidden" name="bookingId" value="<?php echo e($bookingId); ?>">
                                <input type="hidden" name="cod" value="<?php echo e($uscod); ?>">
                                <input type="hidden" name="base" value="<?php echo e($base); ?>">
                                <input type="hidden" name="rateadjustment" value="<?php echo e($rateadjustment); ?>">
                                <input type="hidden" name="extracharge" value="<?php echo e($extracharge); ?>">
                                <input type="hidden" name="riglength" value="<?php echo e($riglength); ?>">
                                <input type="hidden" name="siteid" value="<?php echo e($siteid); ?>">
                                <input type="hidden" name="siteclass" value="<?php echo e($siteclass); ?>">
                                <input type="hidden" name="taxrate" value="<?php echo e($taxrate); ?>">
                                <input type="hidden" name="nights" value="<?php echo e($lengthofStay); ?>">
                                <input type="hidden" name="description"
                                       value="<?php echo e($lengthofStay); ?> nights in site <?php echo e($siteid); ?>">
                                <input type="hidden" name="email" value="">
                                <input type="hidden" name="subtotal" value="<?php echo e($workingtotal); ?>">
                                <input type="hidden" name="rid" value="uc">
                                <input type="hidden" name="events" value="<?php echo e($eventname); ?>">
                                <input type="checkbox" name="sitelock" checked=""> Site Lock fee
                                <?php echo e(\App\CPU\Helpers::format_currency_usd($siteLockFee)); ?>

                                <div class="btn-wrapper mt-3">
                                    <button type="button" onclick="addSiteToCart(this)" class="btn btn-primary">Add to Cart</button>


                                </div>
                            </form>
                        <?php else: ?>
                                Site not available in this dates, Please change the date from the start.


                        <?php endif; ?>
                    <?php else: ?>
                        <p class="card-text">
                            <?php if($uscid != ''): ?>
                                Site <?php echo e($siteid); ?> is not available for your selected dates.<br>
                                <?php echo e($uscid); ?> -
                                <?php echo e($uscod); ?><br>
                            <?php else: ?>
                                Check availability for this site<br>
                    <?php endif; ?>

                    
                </div>
                <br><br></b>
                </p>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-6">
            
        </div>
        <div class="col-lg-12 mt-4">
            <div class="slide-wrapper">
                <?php if($siteDetail->images && count($siteDetail->images) > 0): ?>
                    <?php $__currentLoopData = $siteDetail->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="single-slide-wrapper">
                            <figure>
                                <img src="<?php echo e(asset('storage/sites/' . $image)); ?>" class="w-100 img-fluid"
                                     alt="">
                            </figure>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="single-slide-wrapper">
                        <figure>
                            <img src="<?php echo e(asset('assets/front/img/(D07).jpg')); ?>" class="w-100 img-fluid"
                                 alt="">
                        </figure>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="site-detail-wrapper">

                <div class="attr-wrapper my-2">
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex align-items-center">
                            <span class="property fw-bold">Amenities : </span>
                            <span class="value text-primary fw-bold ms-3">
                                    <?php echo e((is_array($siteDetail->amenities) ? str_replace('_', ' ', implode(',', $siteDetail->amenities)) : 'N/A') ?? 'N/A'); ?></span>
                        </li>
                    </ul>
                </div>

                <div class="attr-wrapper my-2">
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex align-items-center">
                            <span class="property fw-bold">Attributes : </span>
                            <span class="value text-primary fw-bold ms-3">
                                    <?php echo $siteDetail->attributes ?? 'N/A'; ?></span>
                        </li>
                    </ul>
                </div>
                <?php if($uscid != $uscod && $siteclass != 'Amenity' && isset($siteDetail) && isset($rateTier)): ?>
                    <p class="card-text">
                        
                        <?php echo e($bookingmessage); ?>

                        <br>
                    </p>

                    <p>
                    </p>
                    <div>
                        <div class="policy-wrapper py-5">
                            <h5 class="text-uppercase">Policies</h5>
                            <div class="txt-wrap mt-4">
                                <h5>CANCELLATIONS / DATE CHANGES / REFUNDS:</h5>
                                <p>
                                    Cancellations 10 Days Or More Prior To Arrival Date Are Subject To A 15%
                                    Cancellation Fee.
                                    There Are No Refunds For Cancellations Within 10 Days Prior To The
                                    Arrival Date. There Are
                                    No Date Changes Allowed Within 10 Days Prior To The Arrival Date. There
                                    Are No Refunds In
                                    The Event Of Forced Closures Due To COVID, Other Diseases, Disasters, Or
                                    Due To Other
                                    Reasons. For Stays That Qualify, Only Rain Checks Will Be Issued In The
                                    Event Of Forced
                                    Closure. There Are No Refunds Or Discounts If An Amenity, Activity, Or
                                    Event Is Not
                                    Available, Closed, Or Canceled.
                                </p>
                                <p>
                                    YOUR ENTRY INTO THE PARK INDICATES YOUR ACCEPTANCE OF KAYUTA LAKE'S
                                    POLICIES, TERMS, AND
                                    CONDITIONS.
                                </p>
                            </div>
                        </div>
                        
                    </div>
                    <p></p>
                <?php else: ?>
                    
                <?php endif; ?>
            </div>
        </div>

    </div>


    
    </div>

</section>
<?php /**PATH C:\Users\THOMAS JON\OneDrive\Desktop\pos.kayuta\resources\views\reservations\site-details.blade.php ENDPATH**/ ?>