<?php

session_start();


if ($_GET['reset-data'] == 'true') {
    session_destroy();
    header('Location: /book-now.php');
}

$title_suffix = "Reservation";
$page = "reservation";
$description = "Book a car rental with Shaquan's Car Rental. Choose from a variety of vehicles and rental options. Reserve your car today.";

include_once 'includes/header.php';

$vehicles_arr = [];

$vehicles_query = "SELECT * FROM vehicles";
$vehicles_result = mysqli_query($con, $vehicles_query);
while ($row = mysqli_fetch_assoc($vehicles_result)) $vehicles_arr[] = $row;

$add_ons_query = "SELECT * FROM add_ons";
$add_ons_result = mysqli_query($con, $add_ons_query);
while ($row = mysqli_fetch_assoc($add_ons_result)) $add_ons_arr[] = $row;


$reservation = $_SESSION['reservation'];

$reservation['step'] = isset($reservation['step']) ? $reservation['step'] : ($_GET['step'] ?? 1);

if (isset($reservation['itinerary'])) {
    $itinerary = $reservation['itinerary'];
    $pick_up_val = $itinerary['pickUpDate']['value'];
    $return_val = $itinerary['returnDate']['value'];
    $pick_up_step_val = "{$itinerary['pickUpLocation']} - {$itinerary['pickUpDate']['altValue']}";
    $return_step_val = "{$itinerary['returnLocation']} - {$itinerary['returnDate']['altValue']}";
    $pick_up_location = $itinerary['pickUpLocation'];
    $return_location = $itinerary['returnLocation'];
} else {
    $pick_up_step_val = "--";
    $return_step_val = "--";
    $pick_up_val = "";
    $return_val = "";
}

if (isset($reservation['vehicle'])) {
    $vehicle = $reservation['vehicle'];
    $vehicle_name = $vehicle['name'];
    $vehicle_type = $vehicle['type'];
    $vehicle_img_src = $vehicle['imgSrc'];
} else {
    $vehicle_name = "Type";
    $vehicle_type = "--";
}

$session_add_ons = [];
if (isset($reservation['add_ons']) && count($reservation['add_ons']) > 0) {
    $session_add_ons = $reservation['add_ons'];
}
$session_add_ons = count($session_add_ons) ? $session_add_ons : ["--"];

echo "<pre>";
print_r($_SESSION);
echo "</pre>";

?>

<section class="general-header">
    <h1>Reservation</h1>
</section>

<section id="reservation-steps">
    <div class="inner">
        <div class="reservation-step itinerary <?php echo $reservation['step'] == 1 ? "active" : ""; ?>" data-step="1">
            <div class="header">
                <span>1</span>
                <h2>Your Itinerary</h2>
            </div>
            <div class="body">
                <div>
                    <h6>Pick Up</h6>
                    <p><?php echo $pick_up_step_val; ?></p>
                </div>
                <div>
                    <h6>Drop Off</h6>
                    <p><?php echo $return_step_val; ?></p>
                </div>
            </div>
        </div>
        <div class="reservation-step vehicle-add-on <?php echo $reservation['step'] == 2 ? "active" : ""; ?>" data-step="2">
            <div class="header">
                <span>2</span>
                <h2>Select Vehicle/Add-ons</h2>
            </div>
            <div class="body">
                <div>
                    <h6><?php echo $vehicle_name; ?></h6>
                    <p><?php echo $vehicle_type; ?></p>
                </div>
                <div>
                    <h6>Add-ons</h6>
                    <p>
                        <?php
                        $counter = 0;
                        foreach ($session_add_ons as $add_on) {
                            $prefix = $counter > 0 ? ", " : "";
                            $counter++;
                            echo "<span data-id=\"{$add_on['id']}\">{$prefix}{$add_on['abbr']}</span>";
                        } ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="reservation-step reservation <?php echo $reservation['step'] == 3 ? "active" : ""; ?>" data-step="3">
            <div class="header">
                <span>3</span>
                <h2>Reserve Your Vehicle</h2>
            </div>
            <div class="body">
                <div>
                    <h6>Your Information</h6>
                    <p>--</p>
                </div>
                <div>
                    <h6>Payment Information</h6>
                    <p>--</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="itinerary-section" data-step="1" <?php if ($reservation['step'] != 1) echo 'style="display:none;"'; ?>>
    <div class="inner">
        <h1>Reserve Your Vehicle</h1>
        <div class="reservation-flow-container">
            <div class="left">
                <div>
                    <h2>Pick Up</h2>
                    <div class="main-itinerary-box">
                        <div>
                            <h6>Place to pick up the Car<sup>*</sup></h6>
                            <div class="custom-select pick-up form-input">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                    <path d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z" />
                                </svg>
                                <span><?php echo isset($pick_up_location) ? $pick_up_location : "Choose Office"; ?></span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                    <path d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z" />
                                </svg>
                                <div class="custom-select-options">
                                    <span <?php echo $pick_up_location === "Choose Office" ? 'class="selected"' : "" ?>>Choose Office</span>
                                    <span <?php echo $pick_up_location === "Airport" ? 'class="selected"' : "" ?>>Airport</span>
                                    <span <?php echo $pick_up_location === "Main Office" ? 'class="selected"' : "" ?>>Main Office</span>
                                </div>
                            </div>
                        </div>
                        <div class="checkbox-container">
                            <?php if (isset($itinerary)) { ?>
                                <input id="return-to-same-location" type="checkbox" class="hidden-checkbox" hidden <?php if ($itinerary['returnToSameLocation']['value'] === 'on') echo 'checked aria-checked="true"'; ?> />
                            <?php } else { ?>
                                <input id="return-to-same-location" type="checkbox" class="hidden-checkbox" hidden checked aria-checked="true" />
                            <?php } ?>
                            <div class="custom-checkbox"></div>
                            <label class="custom-checkbox-label">Return to the same location</label>
                        </div>
                        <div>
                            <h6>Pick-up Date/Time<sup>*</sup></h6>
                            <input type="text" id="pick-up-flatpickr" class="form-input flatpickr-input" placeholder="Pickup Date" value="<?php echo $pick_up_val; ?>">
                        </div>
                    </div>
                    <h2>Return</h2>
                    <div class="main-itinerary-box">
                        <div <?php if ($itinerary == null || $itinerary['returnToSameLocation']['value'] === 'on') echo 'style="display: none;"'; ?>>
                            <h6>Place to drop the Car<sup>*</sup></h6>
                            <div class="custom-select return form-input">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                    <path d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z" />
                                </svg>
                                <span><?php echo isset($return_location) ? $return_location : "Choose Office"; ?></span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                    <path d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z" />
                                </svg>
                                <div class="custom-select-options">
                                    <span <?php echo $return_location === "Choose Office" ? 'class="selected"' : "" ?>>Choose Office</span>
                                    <span <?php echo $return_location === "Airport" ? 'class="selected"' : "" ?>>Airport</span>
                                    <span <?php echo $return_location === "Main Office" ? 'class="selected"' : "" ?>>Main Office</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h6>Drop Date/Time<sup>*</sup></h6>
                            <input type="text" id="return-flatpickr" class="form-input flatpickr-input" placeholder="Return Date" value="<?php echo $return_val; ?>">
                        </div>
                    </div>
                    <div class="continue-btn">Continue Reservation</div>
                    <?php if (isset($itinerary)) { ?>
                        <div class="reset-data">
                            <span>reset data</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-refresh-cw" stroke-width="3" width="24" height="24" viewBox="0 0 24 24">
                                <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"></path>
                                <path d="M21 3v5h-5"></path>
                                <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"></path>
                                <path d="M8 16H3v5"></path>
                            </svg>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php include 'includes/reservation-summary.php'; ?>
        </div>
    </div>
</section>

<section id="vehicle-selection-section" data-step="2" <?php echo $reservation['step'] != 2 ? 'style="display:none;"' : (isset($vehicle) ? 'style="display:none;"' : ""); ?>>
    <div class="inner">
        <h1>Select Vehicle</h1>
        <div id="vehicles">
            <?php foreach ($vehicles_arr as $v) {
                $active_vehicle = $vehicle['id'] == $v['id'];
            ?>
                <div class="vehicle-container <?php echo $active_vehicle ? "active" : ""; ?>" data-vehicle-id="<?php echo $v['id']; ?>">
                    <img src="/assets/images/vehicles/<?php echo $v['slug']; ?>.jpg" alt="Car thumbnail">
                    <div class="center">
                        <div>
                            <h1 class="vehicle-name"><?php echo $v['name']; ?></h1>
                            <span class="vehicle-type"><?php echo $v['type']; ?></span>
                        </div>
                        <div>
                            <div>
                                <svg height="800px" width="800px" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 240.235 240.235" xml:space="preserve">
                                    <path d="M211.744,6.089C208.081,2.163,203.03,0,197.52,0h-15.143c-11.16,0-21.811,8.942-23.74,19.934l-0.955,5.436  c-0.96,5.47,0.332,10.651,3.639,14.589c3.307,3.938,8.186,6.106,13.74,6.106h19.561c2.714,0,5.339-0.542,7.778-1.504l-2.079,17.761  c-2.001-0.841-4.198-1.289-6.507-1.289h-22.318c-9.561,0-18.952,7.609-20.936,16.961l-19.732,93.027l-93.099-6.69  c-5.031-0.36-9.231,1.345-11.835,4.693c-2.439,3.136-3.152,7.343-2.009,11.847l10.824,42.618  c2.345,9.233,12.004,16.746,21.53,16.746h78.049h1.191h39.729c9.653,0,18.336-7.811,19.354-17.411l15.272-143.981  c0.087-0.823,0.097-1.634,0.069-2.437l5.227-44.648c0.738-1.923,1.207-3.967,1.354-6.087l0.346-4.97  C217.214,15.205,215.407,10.016,211.744,6.089z" />
                                </svg>
                                <span><?php echo $v['people']; ?> Seats</span>
                            </div>
                            <div>
                                <svg width="800px" height="800px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <mask id="doorMask">
                                            <rect width="100%" height="100%" fill="white" />
                                            <path d="M18,14H15a1,1,0,0,1,0-2h3a1,1,0,0,1,0,2Z" fill="black" />
                                        </mask>
                                    </defs>
                                    <path d="M19,2H12.41A2,2,0,0,0,11,2.59l-7.71,7.7A1,1,0,0,0,3,11v4.13a2,2,0,0,0,1.72,2l2.06.3A5.11,5.11,0,0,1,11,21.24,1,1,0,0,0,12,22h7a2,2,0,0,0,2-2V4A2,2,0,0,0,19,2Zm0,8H6.41l6-6H19Z" mask="url(#doorMask)"></path>
                                </svg>
                                <span><?php echo $v['doors']; ?> Doors</span>
                            </div>
                            <?php if ($v['ac'] == 1) { ?>
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                        <path d="M224 0c13.3 0 24 10.7 24 24V70.1l23-23c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-57 57v76.5l66.2-38.2 20.9-77.8c3.4-12.8 16.6-20.4 29.4-17s20.4 16.6 17 29.4L373 142.2l37.1-21.4c11.5-6.6 26.2-2.7 32.8 8.8s2.7 26.2-8.8 32.8L397 183.8l31.5 8.4c12.8 3.4 20.4 16.6 17 29.4s-16.6 20.4-29.4 17l-77.8-20.9L272 256l66.2 38.2 77.8-20.9c12.8-3.4 26 4.2 29.4 17s-4.2 26-17 29.4L397 328.2l37.1 21.4c11.5 6.6 15.4 21.3 8.8 32.8s-21.3 15.4-32.8 8.8L373 369.8l8.4 31.5c3.4 12.8-4.2 26-17 29.4s-26-4.2-29.4-17l-20.9-77.8L248 297.6v76.5l57 57c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-23-23V488c0 13.3-10.7 24-24 24s-24-10.7-24-24V441.9l-23 23c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l57-57V297.6l-66.2 38.2-20.9 77.8c-3.4 12.8-16.6 20.4-29.4 17s-20.4-16.6-17-29.4L75 369.8 37.9 391.2c-11.5 6.6-26.2 2.7-32.8-8.8s-2.7-26.2 8.8-32.8L51 328.2l-31.5-8.4c-12.8-3.4-20.4-16.6-17-29.4s16.6-20.4 29.4-17l77.8 20.9L176 256l-66.2-38.2L31.9 238.6c-12.8 3.4-26-4.2-29.4-17s4.2-26 17-29.4L51 183.8 13.9 162.4c-11.5-6.6-15.4-21.3-8.8-32.8s21.3-15.4 32.8-8.8L75 142.2l-8.4-31.5c-3.4-12.8 4.2-26 17-29.4s26 4.2 29.4 17l20.9 77.8L200 214.4V137.9L143 81c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l23 23V24c0-13.3 10.7-24 24-24z" />
                                    </svg>
                                    <span>A/C</span>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="right">
                        <div>
                            <span>USD$<?php echo $v['price_day']; ?></span>
                            <span>/Total</span>
                        </div>
                        <span>1 Days / 0 Hours</span>
                        <div class="continue-btn"><?php echo $active_vehicle ? "CONTINUE" : "BOOK NOW"; ?></div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

<section id="vehicle-add-ons" data-step="2" <?php echo $reservation['step'] != 2 ? 'style="display:none;"' : (isset($vehicle) ? "" : 'style="display:none;"'); ?>>
    <div class="inner">
        <h1>Vehicle Add-ons</h1>
        <div class="reservation-flow-container">
            <div class="left">
                <div id="add-ons">
                    <?php foreach ($add_ons_arr as $add_on) { ?>
                        <div class="add-on-container" data-id="<?php echo $add_on['id']; ?>">
                            <div class="top">
                                <div class="left">
                                    <h2><?php echo $add_on['name']; ?></h2>
                                    <div class="more-add-on-info">
                                        <span>More Information</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down">
                                            <path d="m6 9 6 6 6-6" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="add-on-btn <?php echo isset($session_add_ons[$add_on['id']]) ? "added" : ""; ?>"></div>
                            </div>
                            <p><?php echo $add_on['description']; ?></p>
                        </div>
                    <?php } ?>
                </div>
                <div class="continue-btn">Continue Reservation</div>
            </div>
            <?php include 'includes/reservation-summary.php'; ?>
        </div>
    </div>
</section>

<section id="final-section" data-step="3" <?php if ($reservation['step'] != 3) echo 'style="display:none;"'; ?>>
    <div class="inner">
        <h1>Final Details</h1>
        <div class="reservation-flow-container">
            <div class="left">
                <div id="final-details-form">
                    <h2>Contact Info</h2>
                    <div class="mutiple-input-container">
                        <div class="input-container">
                            <h6>First Name<sup>*</sup></h6>
                            <input type="text">
                        </div>
                        <div class="input-container">
                            <h6>Last Name<sup>*</sup></h6>
                            <input type="text">
                        </div>
                    </div>
                    <div class="mutiple-input-container">
                        <div class="input-container">
                            <h6>Driver's License (Optional)</h6>
                            <input type="text">
                        </div>
                        <div class="input-container">
                            <h6>Country / Region (Optional)</h6>
                            <input type="text">
                        </div>
                    </div>
                    <div class="input-container">
                        <h6>Street address (optional)</h6>
                        <input type="text">
                    </div>
                    <div class="input-container">
                        <h6>Town / City (optional)</h6>
                        <input type="text">
                    </div>
                    <div class="input-container">
                        <h6>State / County (optional)</h6>
                        <input type="text">
                    </div>
                    <div class="input-container">
                        <h6>Phone <sup>*</sup></h6>
                        <input type="text">
                    </div>
                    <div class="input-container">
                        <h6>Email address <sup>*</sup></h6>
                        <input type="text">
                    </div>
                    <div class="continue-btn">Send Request</div>
                </div>
            </div>
            <?php include 'includes/reservation-summary.php'; ?>
        </div>
</section>


<?php include_once 'includes/footer.php'; ?>