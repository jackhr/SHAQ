<?php

include_once 'helpers.php';

if (!!$order_request) {
    $apply_discount = false;
    $days = getDifferenceInDays($order_request['pick_up'], $order_request['drop_off']);
    $price_day = (int)$vehicle['price_day_USD'];
    if ((int)$vehicle['uses_discount'] === 1 && $days >= 2) {
        $apply_discount = true;
        $price_day = (int)$vehicle['price_day_low_USD'];
    }
    $vehicle['imgSrc'] = "/assets/images/vehicles/{$vehicle['slug']}.jpg";
    $vehicle_name = $vehicle['name'];
    $vehicle_type = $vehicle['type'];
    $vehicle_img_src = $vehicle['imgSrc'];
    $render_change_btn = false;
    $rate = [
        'days' => $days,
        'rate' => makePriceString($price_day),
        'sub_total' => makePriceString($price_day, $days)
    ];

    if ($apply_discount) {
        $rate['rate'] .= '<div class="discount-tool-tip">i<div><span>Fixed price:</span><span><b>2</b> days or more: <b>' . $rate['rate'] . '</b></span></div></div>';
    }

    $estimated_total = ($price_day * $days) + array_sum(array_column($add_ons, 'cost'));
    $estimated_total = makePriceString($estimated_total);
} else if (isset($_SESSION['reservation'])) {
    $render_change_btn = true;
    $reservation = $_SESSION['reservation'];
    if (isset($reservation['vehicle'])) {
        $days = 1;
        $apply_discount = false;
        $price_day = (int)$vehicle['price_day_USD'];
        if (isset($reservation['itinerary'])) {
            $days = getDifferenceInDays($itinerary['pickUpDate']['date'], $itinerary['returnDate']['date']);
            if ((int)$vehicle['uses_discount'] === 1 && $days >= 2) {
                $apply_discount = true;
                $price_day = (int)$vehicle['price_day_low_USD'];
            }
        }
        $vehicle = $reservation['vehicle'];
        $vehicle_name = $vehicle['name'];
        $vehicle_type = $vehicle['type'];
        $vehicle_img_src = $vehicle['imgSrc'];
        $rate = [
            'days' => $days,
            'rate' => makePriceString($price_day),
            'sub_total' => makePriceString($price_day)
        ];
        if (isset($reservation['itinerary'])) {
            $itinerary = $reservation['itinerary'];
            $rate['days'] = $days;
            $rate['sub_total'] = makePriceString($price_day, $days);
        }
        $estimated_total = $price_day * $days;

        if ($apply_discount) {
            $rate['rate'] .= '<div class="discount-tool-tip">i<div><span>Fixed price:</span><span><b>2</b> days or more: <b>' . $rate['rate'] . '</b></span></div></div>';
        }
    }
    if (isset($reservation['add_ons']) && count($reservation['add_ons']) > 0) {
        $add_ons = $reservation['add_ons'];
        $estimated_total = isset($estimated_total) ? $estimated_total + getAddOnsSubTotal($reservation) : getAddOnsSubTotal($reservation);
    }
    $estimated_total = isset($estimated_total) ? makePriceString($estimated_total) : "--";
} else {
    $vehicle_name = "Car";
    $vehicle_type = "Please select your vehicle";
}

?>

<div id="reservation-summary">

    <?php if ($render_change_btn) { ?>
        <span class="change-car-btn continue-btn">Change?</span>
    <?php } ?>

    <h5><?php echo $vehicle_name; ?></h5>
    <h6><?php echo $vehicle_type; ?></h6>

    <div class="car summary">
        <?php if (isset($vehicle)) { ?>
            <img src="<?php echo $vehicle['imgSrc']; ?>" alt="">
        <?php } else { ?>
            <div>+</div>
            <div>
                <span>Find a vehicle</span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                    <path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z" />
                </svg>
            </div>
        <?php } ?>
    </div>

    <div class="rate summary">
        <h6>Rate</h6>
        <?php if (isset($rate)) { ?>
            <table>
                <thead>
                    <tr>
                        <th>Day(s)</th>
                        <th>Rate</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $rate['days']; ?></td>
                        <td><?php echo $rate['rate']; ?></td>
                        <td><?php echo $rate['sub_total']; ?></td>
                    </tr>
                    <tr>
                        <td colspan="2">Rental Subtotal</td>
                        <td><?php echo $rate['sub_total']; ?></td>
                    </tr>
                </tbody>
            </table>
        <?php } else { ?>
            <div>
                <span>--</span>
                <span>--</span>
                <span>--</span>
            </div>
        <?php } ?>
    </div>

    <div class="add-ons summary">
        <h6>Add-ons</h6>
        <?php if (isset($add_ons)) { ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Rate</th>
                        <th>subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($add_ons as $id => $add_on) { ?>
                        <tr data-id="<?php echo $id; ?>">
                            <td><?php echo getNameTdStr($add_on, $days); ?></td>
                            <td><?php echo makePriceString($add_on['cost']); ?></td>
                            <td><?php echo makePriceString(getAddOnCostForTotalDays($add_on, $days)); ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td>Add-ons Subtotal</td>
                        <td></td>
                        <td><?php echo makePriceString(getAddOnsSubTotal($reservation)); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php } else { ?>
            <div>
                <span>--</span>
                <span>--</span>
                <span>--</span>
            </div>
        <?php } ?>
    </div>

    <div class="estimated-total">
        <span>Estimated Total</span>
        <span><?php echo $estimated_total; ?></span>
    </div>

</div>