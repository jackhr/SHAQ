<?php

$title_suffix = "Contact";
$page = "contact";
$description = "Contact Shaquan's Car Rental for more information about our services, vehicles, and more.";

include_once 'includes/header.php';

?>

<section class="general-header">
    <h1>contact</h1>
</section>

<section id="contact-card-section">
    <div class="inner">
        <h1>Shaquan's Car Rental</h1>

        <div class="contact-brief-info">
            <span>Belle Vue Estate</span>
            <span>St John’s</span>
            <span>Antigua, WI</span>
        </div>

        <div>
            <div class="contact-link">
                <span>Phone:</span>
                <a href="tel:+1 (268) 771-1891">+1 (268) 771-1891</a>
            </div>
            <div class="contact-link">
                <span>Email:</span>
                <a href="mailto:info@shaquanscarrentalantigua.com">info@shaquanscarrentalantigua.com</a>
            </div>
        </div>

        <div class="contact-brief-info">
            <span>Service Hours</span>
            <span>Monday to Sunday</span>
            <span>8:00 am to 6:00 pm</span>
        </div>
    </div>
</section>

<section id="contact-form-section">
    <div class="inner">
        <h1>SEND US AN EMAIL</h1>
        <form action="">
            <div class="left">
                <div class="input-container">
                    <label for="contact-message">Your Message</label>
                    <textarea name="message" id="contact-message" cols="30" rows="10" placeholder="Enter your message..."></textarea>
                </div>
            </div>
            <div class="right">
                <div class="input-container">
                    <label for="contact-name">Name *</label>
                    <input id="contact-name" name="name" type="text" placeholder="Enter your name">
                </div>
                <div class="input-container">
                    <label for="contact-email">Email *</label>
                    <input id="contact-email" name="email" type="text" placeholder="email@domain.com">
                </div>
                <button type="submit">SUBMIT</button>
            </div>
        </form>
    </div>
</section>


<?php include_once 'includes/footer.php'; ?>