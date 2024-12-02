<?php
include 'header.php';   
require_once 'db_connection.php';


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products from the database
$sql = "SELECT PRODUCT_ID, PRODUCT_NAME, image_path, PRICE, STOCK, FLAVOURS, EXP_DATE FROM products";
$result = $conn->query($sql);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QPAL - About us</title>

    <link rel="stylesheet" type="text/css" href="./style.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	
	<!--for arrow-->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
        <!--fONT AWESOME-->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

  <!--AOS-->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

	<!-- font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>


<style>
  		.brand-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 2rem 0;
        }
        
        .brand-logo {
            max-width: 100%;
            height: auto;
            filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));
            transition: all 0.3s ease;
        }
        
        .explore-btn {
            background-color: #1a1a1a;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            border: none;
        }
        
        .explore-btn:hover {
            background-color: #333;
            color: white;
            transform: translateY(-2px);
        }

        .brand-text {
            color: #333;
            line-height: 1.8;
			text-align: justify;
        }

        @media (min-width: 992px) {
            .brand-logo {
                max-width: 400px;
            }
        }

        @media (min-width: 768px) and (max-width: 991.98px) {
            .brand-logo {
                max-width: 300px;
            }
        }

        @media (max-width: 767.98px) {
            .brand-section {
                text-align: center;
            }
            
            .brand-logo {
                max-width: 250px;
                margin-bottom: 2rem;
            }

            h1 {
                font-size: 2.5rem;
            }

            .brand-text {
                font-size: 0.9rem;
            }

            .explore-btn {
                padding: 0.6rem 1.5rem;
                font-size: 0.9rem;
            }
        }
		
        .what-we-do {
            padding: 6rem 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .section-title {
            font-size: 3.5rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 4rem;
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .section-title.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .info-card {
            background: linear-gradient(145deg, #1a1a1a, #2a2a2a);
            border-radius: 20px;
            padding: 2.5rem;
            height: 100%;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(50px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
		
        }

        .info-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .info-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .info-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1));
            opacity: 0;
            transition: all 0.3s ease;
        }

        .info-card:hover::before {
            opacity: 1;
        }

        .card-title {
            color: #fff;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            position: relative;
            display: inline-block;
        }
		.card-text p {
			text-align: justify;
		}

        .card-title::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 3px;
            background: #fff;
            transition: width 0.3s ease;
        }

        .info-card:hover .card-title::after {
            width: 100%;
        }

        .card-text {
            color: #f1f1f1;
            line-height: 1.8;
            font-size: 1.1rem;
            margin: 0;
        }

        @media (max-width: 991.98px) {
            .section-title {
                font-size: 2.5rem;
                margin-bottom: 3rem;
            }

            .info-card {
                margin-bottom: 2rem;
            }

            .card-title {
                font-size: 1.75rem;
            }

            .card-text {
                font-size: 1rem;
            }
        }

        @media (max-width: 767.98px) {
            .what-we-do {
                padding: 4rem 0;
            }

            .section-title {
                font-size: 2rem;
            }
        }
</style>
<body>
  		

	<!-- Spacer to prevent content from being hidden under fixed elements -->
	<div style="height: 20px;"></div>




     <!-- Team Section -->
<section class="team-section" id="about">
    <h2 class="team-title" style="color: black;">About Us</h2>
    <p class="team-subtitle">Meet the creators of the QPAL website and innovative vape products.</p>
    
    <div class="team-grid">
        <!-- Team member cards will be added here dynamically -->
        <div class="team-member"  data-aos="fade-right" data-aos-duration="1000">
            <div class="team-member-image" style="background-image: url('jean2.jpg')"></div>
            <div class="team-member-info">
                <h3 class="team-member-name">Jean Acel Padilla</h3>
                <p class="team-member-position">Front-end</p>
                <p class="team-member-bio">The front end designer is responsioble for the visual representation of our website. the front end designer is also responsible for the product design. The front end designer makes sure that the visual representation of our website is appealing in the eyes of our user.
                </p>
            </div>
        </div>

        <div class="team-member" data-aos="fade-right" data-aos-duration="1000">
            <div class="team-member-image" style="background-image: url('DJ.jpg')"></div>
            <div class="team-member-info">
                <h3 class="team-member-name">Dharryl James Valeriano</h3>
                <p class="team-member-position">BPM</p>
                <p class="team-member-bio">The BPM is responsible for giving commands and how our website system works.  He is responsible for the commands within the system. He is also the primary checker of the system if his commands for  the flow of the system were right.              
                </p>
            </div>
        </div>

        <div class="team-member" data-aos="fade-left" data-aos-duration="1000">
            <div class="team-member-image" style="background-image: url('SUNNAJ.png')"></div>
            <div class="team-member-info">
                <h3 class="team-member-name">Christian J. Sabolo</h3>
                <p class="team-member-position">Backend</p>
                <p class="team-member-bio">The backend programmer is responsible for the php coding and the database for the user dashboard. he is responsible for the storing of data and its segregation in the database.
They are also responsible for the process our system. For example the the process of placing of order by the user.</p>
            </div>
        </div>

        <div class="team-member" data-aos="fade-left" data-aos-duration="1000">
            <div class="team-member-image" style="background-image: url('Uriel.jpg')"></div>
            <div class="team-member-info">
                <h3 class="team-member-name">Uriel Mayor</h3>
                <p class="team-member-position">Admin Programmer</p>
                <p class="team-member-bio">Ofcourse if there is a back end programmer for the user interface there is also a admin dashboard programmer. Admins and the user can't have the same interface because it might cause malfunction in the system. The admin dashboard is also used for managing the orders and it's status.</p>
            </div>
        </div>
    </div>
</section>


<!-- Brand Section -->
<section class="brand-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-5 mb-4 mb-md-0">
                    <img src="./abou-brand.png" alt="QPAL Logo" class="brand-logo mx-auto d-block">
                </div>
                <div class="col-lg-6 col-md-7">
                    <h1 class="display-4 fw-bold mb-4">Our Brand</h1>
                    <div class="brand-text">
                        <p class="mb-4">
                            At QPAL, we believe that every puff should be a blend of quality, flavor, and care. Created with the idea of Quick Puff, All Love, our brand is committed to delivering a premium vaping experience that's easy, enjoyable, and full of character. Our mission is to bring convenience and flavor together in a way that resonates with the modern vape enthusiast.
                        </p>
                        <p class="mb-5">
                            This website is more than just a showcase of our products; it's a culmination of dedication and passion for our craft. Developed as part of our final project, QPAL represents our journey and aspirations as students, blending innovation with a focus on user satisfaction. Each product is crafted with the idea of spreading joy, making QPAL a brand built on love, for every puff and every customer.
                        </p>
                    </div>
                    <button class="explore-btn">
                        Explore Products
                    </button>
                </div>
            </div>
        </div>
    </section>



	<!-- What We Do Section -->
	<section class="what-we-do">
        <div class="container">
            <h2 class="section-title">What We Do</h2>
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="info-card">
                        <h3 class="card-title">Vision</h3>
                        <p class="card-text">
                            Vision Our company is aiming to strive success in the future. In order to accomplish this so called success, we the team here in QPAL are exerting effort and time for the sake of accomplishing the success that we wanted. We are aiming for our company to known internationally. For us to accomplish this goal we are experimenting new types of advertisements for our product. Our team also thrives to improve our services so that we can give our customers a premium quality and treatment.
                        </p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="info-card">
                        <h3 class="card-title">Mission</h3>
                        <p class="card-text">
                            Our mission at QPAL is to deliver exceptional quality in every product we offer. We are dedicated to meeting the needs of our customers by crafting reliable and enjoyable vaping experiences. Our team continuously explores innovative ways to enhance product design and functionality, keeping up with industry trends to ensure premium satisfaction. We focus on creating a strong connection with our customers by providing attentive service and support, all while maintaining a commitment to safety and quality in everything we do.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>




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

	

	
	

	
	<script>
        // Intersection Observer for scroll animations
        const observerOptions = {
            threshold: 0.2
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        // Observe title and cards
        document.querySelector('.section-title').classList.add('visible');
        document.querySelectorAll('.info-card').forEach(card => {
            observer.observe(card);
        });

        // Add hover animation delay for cards
        document.querySelectorAll('.info-card').forEach((card, index) => {
            card.style.transitionDelay = `${index * 0.1}s`;
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