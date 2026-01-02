<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home | GSU CS Discussion Forum</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <style>
    /* GSU THEME COLORS */
    :root {
      --gsu-primary-blue: #003366;
      --gsu-secondary-yellow: #ffc107;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #000;
      background-color: #f8f9fa;
    }

    /* Navbar: GSU Blue */
    .navbar {
      padding: 15px 0;
      background-color: var(--gsu-primary-blue) !important;
    }

    /* Hero Section */
    .hero {
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      padding: 20px;
      color: var(--gsu-primary-blue);
      /* background-color: #e9ecef; /* Light background for visibility */
    }
    .hero h1 {
      font-size: 3rem;
      font-weight: bold;
      margin-bottom: 20px; 
    }
    .hero p {
      font-size: 1.2rem;
      margin-bottom: 30px;
    }
    
    /* CTA Buttons: GSU Yellow */
    .btn-gsu-primary {
      background-color: var(--gsu-primary-blue);
      border-color: var(--gsu-primary-blue);
      color: #ffffff;
      padding: 12px 30px;
      border-radius: 5px;
      margin: 10px;
      transition: 0.3s;
    }
    .btn-gsu-primary:hover {
      background-color: #002244;
      border-color: #002244;
      transform: scale(1.05);
      color: #ffffff;
    }
    .btn-gsu-secondary {
      background-color: var(--gsu-secondary-yellow);
      border-color: var(--gsu-secondary-yellow);
      color: var(--gsu-primary-blue); /* Dark text on light yellow */
      padding: 12px 30px;
      border-radius: 5px;
      margin: 10px;
      transition: 0.3s;
    }
    .btn-gsu-secondary:hover {
      background-color: #e0a800;
      border-color: #e0a800;
      transform: scale(1.05);
      color: var(--gsu-primary-blue);
    }
    .text-gsu-primary {
      color: var(--gsu-primary-blue) !important;
    }

/* How It Works Custom Style */
.how-card {
  background: #ffffff;
  border-left: 5px solid var(--gsu-primary-blue); /* GSU Blue Stripe */
  transition: all 0.3s ease-in-out;
  width: 100%;
  max-width: 300px;
}

.how-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 8px 20px rgba(0, 51, 102, 0.15);
}

.icon-wrapper {
  width: 80px;
  height: 80px;
  margin: 0 auto;
  background: rgba(0, 51, 102, 0.1); /* Light GSU Blue background */
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
}
.icon-wrapper i {
    color: var(--gsu-primary-blue) !important;
}

.arrow {
  display: flex;
  align-items: center;
  justify-content: center;
}

/* For mobile view, stack vertically and show downward arrows */
@media (max-width: 767px) {
  .how-it-works {
    flex-direction: column;
  }

  .arrow i {
    transform: rotate(90deg);
    margin: 10px 0;
  }
}

    /* Cards hover */
    .card {
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }

    /* Left blue stripe for cards */
    .card-left-blue {
      border-left: 5px solid var(--gsu-primary-blue);
    }

    /* Scroll fade-up animation */
    .fade-up {
      opacity: 0;
      transform: translateY(30px);
      transition: all 0.8s ease-out;
    }
    .fade-up.show {
      opacity: 1;
      transform: translateY(0);
    }

    /* Footer */
    footer {
      background-color: var(--gsu-primary-blue);
      padding: 20px;
      text-align: center;
      color: #ccc;
    }
    a {
      text-decoration: none;
    }

    /* Form input focus highlight: GSU Blue */
    form input:focus, form textarea:focus {
      border-color: var(--gsu-primary-blue);
      box-shadow: 0 0 5px rgba(0, 51, 102, 0.3);
    }

    /* Contact Info Icons: GSU Blue */
    .info-box i {
        font-size: 35px;
        color: var(--gsu-primary-blue);
        background: rgba(0, 51, 102, 0.1);
        padding: 15px;
        border-radius: 50%;
    }

    .info-box h4 {
        margin: 0;
        color: var(--gsu-primary-blue);
        font-size: 18px;
        font-weight: 600;
    }

    /* Contact Form Send Button: GSU Blue */
    .send-btn {
      background: var(--gsu-primary-blue);
      color: white;
      border: none;
      padding: 12px 25px;
      font-size: 16px;
      border-radius: 6px;
      cursor: pointer;
      transition: 0.3s;
      width: 100%;
    }

    .send-btn:hover {
      background: #002244;
    }
    
    .section-title::after {
        content: "";
        width: 80px;
        height: 3px;
        background: var(--gsu-primary-blue);
        display: block;
        margin: 10px auto 0;
        border-radius: 2px;
    }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow">
    <div class="container">
      <a class="navbar-brand fw-bold text-white" href="index2.html"><i class="fas fa-graduation-cap me-1"></i> GSU CS Discussion Forum</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
              data-bs-target="#navbarNav" aria-controls="navbarNav" 
              aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link active text-white" href="index2.html"><i class="fas fa-home me-1"></i>Home</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="#"><i class="fas fa-info-circle me-1"></i>About</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="#"><i class="fas fa-envelope me-1"></i>Contact</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="login.php"><i class="fas fa-sign-in-alt me-1"></i>Login</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="hero container">
    <div>
      <marquee behavior="scroll" direction="left" scrollamount="5">
        <h1><i class="fas fa-graduation-cap text-gsu-primary me-2"></i>Welcome to GSU CS Discussion Forum</h1>
      </marquee>
      <p>The <strong>official online community</strong> for GSU Computer Science students, lecturers, and mentors. Learn, discuss, and collaborate efficiently.</p>
      <a href="login.php" class="btn btn-gsu-primary m-3"><i class="fas fa-sign-in-alt me-1"></i>Login</a>
      <a href="register.php" class="btn btn-gsu-secondary m-3"><i class="fas fa-user-plus me-1"></i>Register</a>
    </div>
  </div>
  <hr class="my-4">

  <section class="container my-5 fade-up">
    <h2 class="text-center mb-4 text-gsu-primary"><i class="fas fa-bell me-2"></i>Forum Notifications & Updates</h2>
    <div class="row g-3">
      <div class="col-md-4">
        <div class="card shadow-sm h-100 card-left-blue">
          <div class="card-body d-flex align-items-start">
            <i class="fas fa-user-plus me-2 fs-4 text-gsu-primary"></i>
            <div>
              <h6 class="card-title mb-1">New</h6>
              <p class="card-text mb-0">Mentor enrollment for the new session opens on <strong>15th October</strong></p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm h-100 card-left-blue">
          <div class="card-body d-flex align-items-start">
            <i class="fas fa-tools me-2 fs-4 text-gsu-primary"></i>
            <div>
              <h6 class="card-title mb-1">System Maintenance</h6>
              <p class="card-text mb-0">Scheduled for stability fixes on <strong>20th October</strong></p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm h-100 card-left-blue">
          <div class="card-body d-flex align-items-start">
            <i class="fas fa-history me-2 fs-4 text-gsu-primary"></i>
            <div>
              <h6 class="card-title mb-1">Archive Update</h6>
              <p class="card-text mb-0">Last session's threads moved to archive on <strong>30th October</strong></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  

  <section class="container my-5 fade-up">
    <h2 class="text-center mb-4 text-gsu-primary"><i class="fas fa-cog me-2"></i>Core Forum Features</h2>
    <div class="row text-center">
      <div class="col-md-4 mb-3">
        <div class="card shadow-sm p-3 h-100 card-left-blue">
          <h5 class="card-title"><i class="fas fa-users me-2 text-gsu-primary"></i>Peer-to-Peer Support</h5>
          <p class="card-text">Connect with fellow students to solve complex programming challenges together.</p>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="card shadow-sm p-3 h-100 card-left-blue">
          <h5 class="card-title"><i class="fas fa-check-circle me-2 text-gsu-primary"></i>Verified Lecturer Help</h5>
          <p class="card-text">Receive guidance and answers directly from GSU CS faculty members.</p>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="card shadow-sm p-3 h-100 card-left-blue">
          <h5 class="card-title"><i class="fas fa-chart-line me-2 text-gsu-primary"></i>Career Mentorship</h5>
          <p class="card-text">Connect with alumni mentors for career advice and industry insights.</p>
        </div>
      </div>
    </div>
  </section>
  
<section class="container my-5 fade-up">
  <h2 class="text-center mb-5 text-gsu-primary fw-bold fade-up">
    <i class="fas fa-lightbulb me-2"></i>How It Works
  </h2>

  <div class="how-it-works d-flex flex-column flex-md-row justify-content-center align-items-center gap-4">
    <div class="how-card text-center p-4 shadow-sm rounded fade-up card-left-blue">
      <div class="icon-wrapper mb-3">
        <i class="fas fa-user-plus fs-1"></i>
      </div>
      <h5 class="fw-bold text-gsu-primary">Step 1: Register</h5>
      <p class="text-muted">Create your GSU verified account as a Student, Lecturer, or Mentor.</p>
    </div>

    <div class="arrow d-none d-md-block">
      <i class="fas fa-arrow-right fs-2 text-gsu-primary"></i>
    </div>

    <div class="how-card text-center p-4 shadow-sm rounded fade-up card-left-blue">
      <div class="icon-wrapper mb-3">
        <i class="fas fa-book-reader fs-1"></i>
      </div>
      <h5 class="fw-bold text-gsu-primary">Step 2: Post & Discuss</h5>
      <p class="text-muted">Post your programming questions, project issues, or course-related queries.</p>
    </div>

    <div class="arrow d-none d-md-block">
      <i class="fas fa-arrow-right fs-2 text-gsu-primary"></i>
    </div>

    <div class="how-card text-center p-4 shadow-sm rounded fade-up card-left-blue">
      <div class="icon-wrapper mb-3">
        <i class="fas fa-chart-bar fs-1"></i>
      </div>
      <h5 class="fw-bold text-gsu-primary">Step 3: Resolve & Learn</h5>
      <p class="text-muted">Receive quick, quality answers from peers and faculty to resolve your issues faster.</p>
    </div>
  </div>
</section>


  <section class="container my-5 fade-up">
    <h2 class="text-center mb-4 text-gsu-primary"><i class="fas fa-quote-right me-2"></i>What People Say</h2>
    <div class="row text-center">
      <div class="col-md-4 mb-3">
        <div class="card shadow-sm p-4 h-100 card-left-blue">
          <i class="fas fa-user-circle text-gsu-primary fs-1 mb-3"></i>
          <p class="text-muted">"This platform is essential for Computer Science. The response time from lecturers is amazing and helpful!"</p>
          <h6 class="fw-bold text-gsu-primary">Mr. Friday Ahmed</h6>
          <small>Course Lecturer</small>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="card shadow-sm p-4 h-100 card-left-blue">
          <i class="fas fa-user-circle text-gsu-primary fs-1 mb-3"></i>
          <p class="text-muted">"I found answers to my C++ project bug in minutes. It's much faster than email or office hours."</p>
          <h6 class="fw-bold text-gsu-primary">Aisha Bature</h6>
          <small>300-Level Student</small>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="card shadow-sm p-4 h-100 card-left-blue">
          <i class="fas fa-user-circle text-gsu-primary fs-1 mb-3"></i>
          <p class="text-muted">"Mentoring students here is rewarding. The interface makes it easy to spot and answer difficult technical questions."</p>
          <h6 class="fw-bold text-gsu-primary">Engr. Tijjani Sani</h6>
          <small>Alumni Mentor</small>
        </div>
      </div>
    </div>
  </section>
  <section class="container my-5 fade-up text-center">
    <h2 class="mb-4 text-gsu-primary"><i class="fas fa-chart-area me-2"></i>Forum Impact</h2>
    <div class="row">
      <div class="col-md-3 col-6 mb-4">
        <h3 class="text-gsu-primary fw-bold">5,000+</h3>
        <p>Total Posts</p>
      </div>
      <div class="col-md-3 col-6 mb-4">
        <h3 class="text-gsu-primary fw-bold">150+</h3>
        <p>Verified Mentors</p>
      </div>
      <div class="col-md-3 col-6 mb-4">
        <h3 class="text-gsu-primary fw-bold">100+</h3>
        <p>Daily Active Users</p>
      </div>
      <div class="col-md-3 col-6 mb-4">
        <h3 class="text-gsu-primary fw-bold">95%</h3>
        <p>Response Rate</p>
      </div>
    </div>
  </section>


  <section class="contact-section">
    <h2 class="section-title text-gsu-primary">Contact the Admin Team</h2>

    <div class="contact-container text-gsu-primary">

      <div class="contact-info">
        <div class="info-box">
          <i class="fas fa-phone-alt"></i>
          <div>
            <h4>Call Us</h4>
            <p>+234 900-GSU-HELP</p>
          </div>
        </div>

        <div class="info-box">
          <i class="fas fa-envelope"></i>
          <div>
            <h4>Email Us</h4>
            <p>support@gsucsforum.com</p>
          </div>
        </div>

        <div class="info-box">
          <i class="fas fa-map-marker-alt"></i>
          <div>
            <h4>Visit Us</h4>
            <p>GSU Computer Science Department Office</p>
            <p>Gombe, Gombe State, Nigeria</p>
          </div>
        </div>
      </div>

      <div class="contact-form">
        <form method="POST" action="contact_process.php">
          <div class="form-row">
            <div class="form-group">
              <label>Full Name *</label>
              <input name="name" type="text" placeholder="Enter your full name" required>
            </div>
            <div class="form-group">
              <label>Email Address *</label>
              <input name="email" type="email" placeholder="Enter your GSU email" required>
            </div>
          </div>

          <div class="form-group">
            <label>Message *</label>
            <textarea name="message" rows="5" placeholder="Your inquiry or feedback" required></textarea>
          </div>

          <button type="submit" class="send-btn btn">Send Message</button>
        </form>
      </div>

    </div>
  </section>


  <footer>
    <p><i class="fas fa-copyright me-1"></i>&copy; <?php echo date("Y"); ?> Computer Science Department. All rights reserved.</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
  function fadeUpOnScroll() {
    const elements = document.querySelectorAll('.fade-up');
    const windowBottom = window.innerHeight;
    elements.forEach(el => {
      const elementTop = el.getBoundingClientRect().top;
      if (elementTop < windowBottom - 100) {
        el.classList.add('show');
      }
    });
  }
  window.addEventListener('scroll', fadeUpOnScroll);
  window.addEventListener('load', fadeUpOnScroll);
  </script>

</body>
</html>