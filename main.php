<?php

include 'header.php';

// Initialize notification variables
$notification = '';
$notificationType = '';

// Check for login messages
if (isset($_SESSION['login_message'])) {
    $notification = $_SESSION['login_message'];
    $notificationType = 'success';
    unset($_SESSION['login_message']); // Clear the message after displaying
}

// Check for login errors
if (isset($_SESSION['login_error'])) {
    $notification = $_SESSION['login_error'];
    $notificationType = 'error';
    unset($_SESSION['login_error']); // Clear the error after displaying
}



?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>QPAL</title>
	<link rel="stylesheet" type="text/css" href="./style.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	
	<!--for arrow-->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />



  <!--AOS-->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

	<!-- font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">


	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

	<style>
	.burger-icon span {
		background: black !important;
	}
	/* Responsive adjustments */
@media (max-width: 768px) {
  .hero-text {
   	  	max-width: 100% !important; /* Adjust the size of the text image on smaller screens */  
    	position: relative;
		margin-bottom: 15rem !important;

	  }

  .hero-vape {
      width: 60% !important; /* Adjust the vape image size for mobile */
  }
}

.hero h1 {
    display: none; /* Default is hidden */
}

@media screen and (max-width: 768px) {
    .hero h1 {
        display: block !important; /* Display when the screen width is 768px or less */
    }
}

	</style>
<body style="  background-color: #edf1f8 !important;">



	



	

	<!-- Hero Section ----------------------------------------------------------------------------------------------------------------------->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center " >
                <div class="col-md-6 order-md-last mb-4 mb-md-0 pt-4" data-aos="fade-left" data-aos-offset="500" data-aos-duration="1500">
                    <img src="./home-img.svg" alt="Quick Puff Product" class="hero-image">
                </div>
                <div class="col-md-6" data-aos="fade-right" data-aos-anchor="#example-anchor" data-aos-offset="500" data-aos-duration="1500">
                    <h1>QPAL Flona Max Pro</h1>
                    <p>Experience Vaping like never before</p>
                  <a href=""> Explore Products</a>
                    <div class="social-icons">
                        <i class="fa-brands fa-facebook"></i>
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

<div style="height: 70px;"></div>


  <!-- ABOUT the qpal vape SECTION ----------------------------------------------------------------------------------------------------------------------->
    <!-- HTML Structure -->
    <div class="container py-1" data-aos="fade-up" data-aos-duration="1500">
        <div class="flona rounded shadow overflow-hidden" style="border-radius: 30px!important;">
            <div class="row g-0">
                <div class="col-lg-6 text-center text-lg-start flona-text">
                    <h1>Qpal Flona V1</h1>
                    <p style="text-align: justify;">
                        Experience next-level vaping with the Qpal Flona V1, a device designed for flavor enthusiasts and smooth
                        cloud chasers alike. Built with advanced technology, the Flona V1 offers reliable performance,
                        customizable settings, and a sleek, portable design.
                        <br><br>
                        Its innovative coil system ensures pure flavor with each puff, while the durable battery keeps you powered
                        all day. The Qpal Flona V1 is perfect for those seeking a modern, high-quality vape that elevates your
                        experience.
                    </p>
                </div>
                <div class="col-lg-6">
                    <img src="./aboutqpal.webp" alt="Qpal Flona V1" class="img-fluid w-100 h-100 object-fit-cover">
                </div>
            </div>
        </div>
    </div>

<div style="height: 50px;"></div>
	<!--SPECS page -->


	<main class="spec-container">
        <div class="spec-row">
        <section class="spec-col-lg-6" style="padding-top: 12rem;"  data-aos="fade-right" data-aos-duration="1500">
                <img src="./specs.svg" alt="" style="width: 100%;">
            </section>
            <section class="spec-col-lg-6" style="margin-top: 7rem;" data-aos="fade-left" data-aos-duration="1500">
                <h2 class="spec-title">Specifications</h2>

                <div class="spec-row spec-text-center spec-mt-5">
                    <div class="spec-col-4 spec-mb-5">
                        <img src="./s1.svg" alt="Puffs icon" class="spec-img">
                        <h3>Up to 20 000</h3>
                        <p>Puffs</p>
                    </div>
                    <div class="spec-col-4 spec-mb-5 spec-border-right spec-border-left">
                        <img src="./s2.svg" alt="Flavours icon" class="spec-img">
                        <h3>12</h3>
                        <p>Flavours</p>
                    </div>
                    <div class="spec-col-4 spec-mb-5">
                        <img src="./s3.svg" alt="Battery icon" class="spec-img">
                        <h3>650 mAh</h3>
                        <p>Battery</p>
                    </div>
                    <div class="spec-col-4 spec-mt-5">
                        <img src="./s4.svg" alt="Screen icon" class="spec-img">
                        <h3>LED</h3>
                        <p>Screen</p>
                    </div>
                    <div class="spec-col-4 spec-mt-5 spec-border-right spec-border-left">
                        <img src="./s5.svg" alt="Connector icon" class="spec-img">
                        <h3>USB-C</h3>
                        <p>Connector</p>
                    </div>
                    <div class="spec-col-4 spec-mt-5">
                        <img src="./s6.svg" alt="Coil icon" class="spec-img">
                        <h3>Dual Mesh</h3>
                        <p>Coil</p>
                    </div>
                </div>
            </section>
            
        </div>
    </main>

                  <div style="height: 150px;"></div>

    <!----Showcase product -->
    <div class="showcase " data-aos="fade-up" data-aos-duration="1500">
      <h1>Product Showcase</h1>
    <div class="video-container">
        <video id="showcase-video" autoplay muted loop>
            <source src="./flona-video1.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="play-pause-overlay" id="playPauseOverlay">
            <button class="play-pause-btn" aria-label="Play/Pause"></button>
        </div>
    </div>
    </div>



    <!--WHY CHOOSE US PAGE -->


<section class="bestsellers container" style="margin-top: 5rem !important;">
  <h2 >WHY CHOOSE US</h2>
  <div class="container" >
    <div class="card1" data-aos="fade-up-right" data-aos-duration="1500">
      <div class="card-inner" style="--clr:#fff;">
        <div class="box">
          <div class="imgBox">
            <img src="./about/1.png" alt="Trust & Co.">
          </div>
          <div class="icon">
            <a href="./product.php" class="iconBox"> <span class="material-symbols-outlined">
                arrow_outward
              </span></a>
          </div>
        </div>
      </div>
      <div class="content">
        <h3>OUR BRAND</h3>
        <p>QPAL stands for Quick Puff All Love. Our mission is to create a premium vaping experience that blends creativity and expertise. We’re passionate about crafting a brand you can trust and enjoy, no matter the occasion.</p>
        <ul>
       
       
        </ul>
      </div>
    </div>
    <div class="card1" data-aos="fade-up" data-aos-duration="1500">
      <div class="card-inner" style="--clr:#fff;">
        <div class="box">
          <div class="imgBox">
            <img src="./about/2.png" alt="Tonic">
          </div>
          <div class="icon">
            <a href="./product.php" class="iconBox"> <span class="material-symbols-outlined">
                arrow_outward
              </span></a>
          </div>
        </div>
      </div>
      <div class="content">
        <h3>OUR TEAM</h3>
        <p>Meet the vibrant minds behind QPAL! Our dedicated team brings passion and expertise, working together to deliver bold, exciting flavors. Together, we’re redefining the vape experience with innovation and commitment to quality.</p>
        <ul>

         
        </ul>
      </div>
    </div>
    <div class="card1" data-aos="fade-up-left" data-aos-duration="1500">
      <div class="card-inner" style="--clr:#fff;">
        <div class="box">
          <div class="imgBox">
            <img src="./about/3.png" alt="Shower Gel">
          </div>
          <div class="icon">
            <a href="./product.php" class="iconBox"> <span class="material-symbols-outlined">
                arrow_outward
              </span></a>
          </div>
        </div>
      </div>
      <div class="content">
        <h3>WHAT WE DO</h3>
        <p>At QPAL, we're all about flavor that lasts. From brainstorming bold new blends to perfecting the classics, we’re dedicated to bringing you an unforgettable vaping journey. Explore our rich flavours experience and see what makes us stand out.</p>
        <ul>
     
     
        </ul>
      </div>
    </div>
  </div>
</section>
	

<div style="height: 50px;"></div>














	


<!---FOOOTERR---->
	<footer class="site-footer">
		<div class="container">
			<div class="footer-content">
				<div class="footer-section about">
					<h3 class="logo-text">QPAL</h3>
					<p style="color: white;">
						QPAL is dedicated to providing high-quality vaping products with a focus on compatibility and user experience.
					</p>
					<div class="contact" style="color: #edf1f8;">
						<span><i class="fas fa-phone"></i> &nbsp; 09935367760</span>
						<span><i class="fas fa-envelope"></i> &nbsp; qpal@gmail.com</span>
					</div>
				</div>
				<div class="footer-section links">
					<h3>Quick Links</h3>
					<ul>
						<li><a href="./main.php">Home</a></li>
						<li><a href="./product.php">Products</a></li>
						<li><a href="./aboutpage.php">About Us</a></li>						
						
					</ul>
				</div>
				<div class="footer-section follow-us">
					<h3>Follow Us</h3>
					<div class="socials">
						<a href="#" style="margin-right: 15px; color:aqua;"><i class="fab fa-facebook fa-3x"></i></a>
						<a href="#" style="color:aqua;"><i class="fab fa-instagram fa-3x"></i></a>
						
					</div>
				</div>
			</div>
			<div class="footer-bottom" style="color: #edf1f8;">
			<a href="http://sunnaj.wuaze.com/?i=1" style="color:white;">	&copy; 2024 QPAL | Designed by Sunnaj | All rights reserved</a>
			</div>
		</div>
	</footer>

	

	
	

                <!-----VIDEO SECTION-------->
	<script>
        const videoContainer = document.querySelector('.video-container');
        const video = document.getElementById('showcase-video');
        const overlay = document.getElementById('playPauseOverlay');

        overlay.addEventListener('click', () => {
            if (video.paused) {
                video.play();
                videoContainer.classList.remove('paused');
            } else {
                video.pause();
                videoContainer.classList.add('paused');
            }
        });

        video.addEventListener('play', () => {
            videoContainer.classList.remove('paused');
        });

        video.addEventListener('pause', () => {
            videoContainer.classList.add('paused');
        });
    </script>

	<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
	<script src="./main.js"></script>
	<script src="./restrict.js"></script>
	<script>
		AOS.init();
	  </script>
</body>
</html>
