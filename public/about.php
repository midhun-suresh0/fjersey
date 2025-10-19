<?php
$page_title = "About Us";
require_once '../includes/header.php';
?>

<!-- About Banner -->
<section class="section" style="padding-top: 30px; padding-bottom: 30px; background-color: #f8f9fa;">
    <div class="container">
        <h1 style="margin-bottom: 0;">About Us</h1>
    </div>
</section>

<!-- About Content -->
<section class="section">
    <div class="container">
        <div class="about-content">
            <div class="about-image">
                <img src="<?php echo SITE_URL; ?>assets/images/about-store.jpg" alt="Football Jersey Store" style="max-width: 100%; height: auto; border-radius: 8px;">
            </div>
            
            <div class="about-text">
                <h2>Our Story</h2>
                <p>Welcome to Football Jersey Store, your premier destination for authentic football jerseys from around the world. Founded in 2023, we've quickly established ourselves as a trusted retailer for football enthusiasts and collectors alike.</p>
                
                <p>Our journey began with a simple passion for the beautiful game and a desire to provide fans with high-quality, authentic jerseys that represent their favorite teams and players. What started as a small online shop has grown into a comprehensive store offering jerseys from all major leagues and national teams.</p>
                
                <h2>Our Mission</h2>
                <p>At Football Jersey Store, our mission is to connect fans with their passion through authentic football merchandise. We believe that wearing your team's colors is more than just fashion—it's an expression of loyalty, identity, and belonging to a global community of football lovers.</p>
                
                <h2>Quality & Authenticity</h2>
                <p>We take pride in offering only authentic, officially licensed products. Each jersey in our collection is carefully sourced to ensure the highest quality and authenticity. We work directly with manufacturers and authorized distributors to guarantee that what you receive is genuine.</p>
                
                <h2>Customer Experience</h2>
                <p>We're committed to providing an exceptional shopping experience. From our user-friendly website to our responsive customer service team, we strive to make your journey with us seamless and enjoyable. We value your trust and work hard to earn it with every order.</p>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="section" style="background-color: #f0f0f0;">
    <div class="container">
        <div class="section-title">
            <h2>Our Team</h2>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; text-align: center;">
            <div class="team-member">
                <div style="background-color: #ddd; width: 150px; height: 150px; border-radius: 50%; margin: 0 auto 20px; overflow: hidden;">
                    <!-- Team member photo placeholder -->
                </div>
                <h3>John Smith</h3>
                <p>Founder & CEO</p>
            </div>
            
            <div class="team-member">
                <div style="background-color: #ddd; width: 150px; height: 150px; border-radius: 50%; margin: 0 auto 20px; overflow: hidden;">
                    <!-- Team member photo placeholder -->
                </div>
                <h3>Sarah Johnson</h3>
                <p>Operations Manager</p>
            </div>
            
            <div class="team-member">
                <div style="background-color: #ddd; width: 150px; height: 150px; border-radius: 50%; margin: 0 auto 20px; overflow: hidden;">
                    <!-- Team member photo placeholder -->
                </div>
                <h3>Michael Brown</h3>
                <p>Customer Service Lead</p>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>