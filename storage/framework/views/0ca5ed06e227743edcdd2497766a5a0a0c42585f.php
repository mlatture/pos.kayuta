

<?php $__env->startSection('title', 'Select Site'); ?>

<?php $__env->startSection('content-header', 'Select Site'); ?>

<?php $__env->startPush('css'); ?>

<style>
    #tooltip {
        background: green;
        border: 1px solid black;
        border-radius: 5px;
        padding: 5px;
    }
    .single-slide-wrapper img {
        height: 500px;
        object-fit: cover;
    }

    * {
        box-sizing: border-box
    }

    .mySlides {
        display: none
    }

    img {
        vertical-align: middle;
    }

    /* Slideshow container */
    .slideshow-container {
        max-width: 1000px;
        position: relative;
        margin: auto;
    }

    /* Next & previous buttons */
    .prev,
    .next {
        cursor: pointer;
        position: absolute;
        top: 50%;
        width: auto;
        padding: 16px;
        margin-top: -22px;
        color: white;
        font-weight: bold;
        font-size: 18px;
        transition: 0.6s ease;
        border-radius: 0 3px 3px 0;
        user-select: none;
    }

    /* Position the "next button" to the right */
    .next {
        right: 0;
        border-radius: 3px 0 0 3px;
    }

    /* On hover, add a black background color with a little bit see-through */
    .prev:hover,
    .next:hover {
        background-color: rgba(0, 0, 0, 0.8);
    }

    /* Caption text */
    .text {
        color: #f2f2f2;
        font-size: 15px;
        padding: 8px 12px;
        position: absolute;
        bottom: 8px;
        width: 100%;
        text-align: center;
    }

    /* Number text (1/3 etc) */
    .numbertext {
        color: #f2f2f2;
        font-size: 12px;
        padding: 8px 12px;
        position: absolute;
        top: 0;
    }

    /* The dots/bullets/indicators */
    .dot {
        cursor: pointer;
        height: 15px;
        width: 15px;
        margin: 0 2px;
        background-color: #bbb;
        border-radius: 50%;
        display: inline-block;
        transition: background-color 0.6s ease;
    }

    .active,
    .dot:hover {
        background-color: #717171;
    }

    /* Fading animation */
    .fade {
        animation-name: fade;
        animation-duration: 1.5s;
    }

    @keyframes fade {
        from {
            opacity: .4
        }

        to {
            opacity: 1
        }
    }

    /* On smaller screens, decrease text size */
    @media only screen and (max-width: 300px) {

        .prev,
        .next,
        .text {
            font-size: 11px
        }
    }

    .kayuta-lake-sec {
        padding: 124px 0px 100px 0px !important;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div id="tooltip" display="none" style="position: absolute; display: none;"></div>
    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 700 933">
        <image width="100%" height="933" xlink:href="<?php echo e(asset('assets/front/img/mapforwebsite.jpg')); ?>"></image>
        <!--<image href="<?php echo e(asset('assets/front/img/transparent.png')); ?>" x="395" y="50"></image>-->
        <text x="440" y="180" font-family="Verdana" font-size="15" fill="black">
            Click on a Green or Orange site </text>
        <text x="460" y="200" font-family="Verdana" font-size="15" fill="black">
            to see pricing or to book</text>
        <text x="505" y="232" font-family="Verdana" font-size="10" fill="black">
            Green sites are available
        </text>
        <ellipse id="legendgreen" cx="490" cy="228" rx="11" ry="6" fill="#66FF66"></ellipse>
        <text x="505" y="250" font-family="Verdana" font-size="10" fill="black">
            Orange sites are available but do
        </text>
        <text x="505" y="260" font-family="Verdana" font-size="10" fill="black">
            not have the selected amenities
        </text>
        <ellipse id="legendorange" cx="490" cy="252" rx="11" ry="6" fill="orange"></ellipse>
        <text x="505" y="280" font-family="Verdana" font-size="10" fill="black">
            Red sites are not available.
        </text>
        <ellipse id="legendred" cx="490" cy="276" rx="11" ry="6" fill="red"></ellipse>
        <text x="505" y="300" font-family="Verdana" font-size="10" fill="black">
            Blue sites have a booking in
        </text>
        <text x="505" y="310" font-family="Verdana" font-size="10" fill="black">
            progress. Check back later.
        </text>
        <ellipse id="legendblue" cx="490" cy="300" rx="11" ry="6" fill="blue"></ellipse>
        <a xlink:href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
            <image href="/buttons/booknow1.png" x="425" y="110" width='80'
                   onmousemove="showTooltip(evt, 'Click to Search');" onmouseout="hideTooltip();" />
        </a>

        <?php $__currentLoopData = $sites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currentsite): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                // vars for current site start with c
                $lengthdetails = '';
                $allowbooking = true;
                $stddetails = '';
                $filltext = '';
                $fillcolor = '#66FF66'; //green?
                $csite = trim($currentsite['siteid']);
                $chookup = $currentsite['hookup'];
                $csiteclass = $currentsite['class'];
                $crigtype = $currentsite['rigtypes'];
                $booking = Session::get('booking');
            ?>

            <?php if($currentsite['status'] == 'JustShowMap'): ?>
                <a xlink:href='<?php echo e(route('reservations.site-details', ['id' => $csite])); ?>'>
                    <<?php echo $currentsite['coordinates']; ?> stroke='blue' stroke-opacity='1.0' opacity='0.7' fill='white'
                    onmousemove="showTooltip(evt, '<?php echo e($currentsite['sitename']); ?> (<?php echo e($chookup); ?> Max Length <?php echo e($currentsite['maxlength']); ?> feet. Enter dates to check availability.)');"
                    onmouseout="hideTooltip();" />
                </a>
            <?php else: ?>
                <?php if($csiteclass == 'Amenity'): ?>
                    <a xlink:href='<?php echo e(route('reservations.site-details', ['id' => $csite])); ?>'>
                        <<?php echo $currentsite['coordinates']; ?> stroke='black' stroke-opacity='1.0' opacity='0.7' fill='white'
                        onmousemove="showTooltip(evt, '<?php echo e($currentsite['sitename'] . ' (Click for more information about this amenity.)'); ?>');"
                        onmouseout="hideTooltip();" />
                    </a>
                <?php else: ?>
                    <?php if($hookup != ''): ?>
                        <?php if($chookup != $hookup): ?>
                            <?php
                                $filltext .= 'This site does not have your selected hookup. (' . $hookup . ')';
                                $fillcolor = 'orange';
                                $allowbooking = true;
                            ?>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if($currentsite['maxlength'] > 2): ?>
                        <?php
                            $lengthdetails = '(' . $chookup . ' Max Length is ' . $currentsite['maxlength'] . ' feet.) ';
                        ?>
                    <?php endif; ?>

                    <?php if($riglength != ''): ?>
                        <?php
                            // Rig length check
                            if ($riglength < $currentsite['minlength'] || $riglength > $currentsite['maxlength']) {
                                $allowbooking = false;
                                $filltext .= 'Your rig will not fit. ';
                                $fillcolor = 'red';
                            } else {
                                $filltext .= 'Your rig will fit. ';
                            }
                        ?>
                    <?php endif; ?>

                    <?php if(strpos($csiteclass, $siteclass) === false): ?>
                        <?php
                            // The site class was not a match
                            $filltext .= 'This is not the type of site you are looking for. ';
                            $allowbooking = false;
                            $fillcolor = 'red';
                        ?>
                    <?php endif; ?>

                    <?php if($allowbooking): ?>
                        <?php if($currentsite['incart'] != 'Available' && $currentsite['reserved'] == 'Available'): ?>
                            <?php
                                $fillcolor = 'blue';
                            ?>
                            <?php if(in_array($currentsite['cartid'], $cartIds)): ?>
                                <?php
                                    $allowbooking = false;
                                    $filltext .= 'This site is in your cart. ';
                                ?>
                            <?php else: ?>
                                
                                <?php
                                    $allowbooking = false;
                                    $filltext .= 'This site is locked in another cart - check back later. ';
                                ?>
                                
                            <?php endif; ?>
                        <?php elseif(trim($currentsite['reserved']) != 'Available'): ?>
                            <?php
                                $allowbooking = false;
                                $fillcolor = 'red';
                                $filltext = 'This site is already reserved. ';
                            ?>
                        <?php endif; ?>
                    <?php endif; ?>

                    

                    <?php if($allowbooking): ?>
                        <a xlink:href='javascript:void(0)' onclick="showSiteDetail(this, '<?php echo e(route('reservations.site.detail', [$csite, $bookingId])); ?>')">
                            <?php endif; ?>

                            <<?php echo $currentsite['coordinates']; ?> <?php echo e($stddetails); ?> stroke='black' stroke-opacity='1.0' opacity='0.8'
                            fill='<?php echo e($fillcolor); ?>'
                            onmousemove="showTooltip(evt, '<?php echo e($currentsite['sitename']); ?> <?php echo e($lengthdetails); ?> <br><?php echo e($filltext); ?>');"
                            onmouseout="hideTooltip();" /> </a>
                    <?php endif; ?>
                <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </svg>
    <a href="<?php echo e(route('reservations.checkout', [$bookingId])); ?>" class="btn btn-primary mt-3 mb-3 mr-2 float-right checkout--btn <?php echo e((!empty($items) &&  count($items) > 0) ? '' : 'd-none'); ?>">Proceed To Checkout</a>
    <div class="modal fade bd-example-modal-lg cartModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Site Detail</h5>
                    <button type="button" class="close" onclick="closeModal()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
                </div>
            </div>
        </div>
    </div>


<?php $__env->stopSection(); ?>


<?php $__env->startPush('js'); ?>
    <script>
        function showTooltip(evt, text) {
            let tooltip = document.getElementById("tooltip");
            tooltip.innerHTML = text;
            tooltip.style.display = "inline-block";
            tooltip.style.left = evt.pageX + 10 + 'px';
            tooltip.style.top = evt.pageY + 10 + 'px';
        }

        function hideTooltip() {
            var tooltip = document.getElementById("tooltip");
            tooltip.style.display = "none";
        }

        function showSiteDetail(input, route){
            $.get(route, function(res){
               if(res.status == "error"){
                   alert(res.message);
               }else{
                   $('.cartModal .modal-body').empty().html(res.content);
                   $('.cartModal').modal('toggle');
               }
            });
        }

        function closeModal(){
            $('.cartModal .modal-body').empty();
            $('.cartModal').modal('hide');
        }

        function addSiteToCart(input){
            $(input).attr('disabled', true);
            $.post($('#reservationCartForm').attr('action'), $('#reservationCartForm').serialize(), function(res){
                if(res.status == "success"){
                    $('.cartModal .modal-body').empty();
                    $('.cartModal').modal('hide');
                    $('.checkout--btn').removeClass('d-none');
                }
                $(input).attr('disabled', false);
            });
        }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\THOMAS JON\OneDrive\Desktop\pos.kayuta\resources\views\reservations\booking.blade.php ENDPATH**/ ?>