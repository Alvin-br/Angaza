
/* --- 1. INITIALIZE EMAILJS --- */
// It is best practice to initialize with your Public Key as soon as the script loads
(function() {
    emailjs.init("GoR9pxQN_36Cg2t3X"); // Your confirmed Public Key
})();

document.addEventListener('DOMContentLoaded', () => {
    
    /* --- 2. SOOTHING SCROLL ANIMATIONS --- */
    const observerOptions = {
        threshold: 0.1, 
        rootMargin: "0px 0px -50px 0px" 
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, observerOptions);

    const elementsToAnimate = document.querySelectorAll(
        '.news-card, .gallery-item, .top-hero-left, .scripture-container, .top-hero-right, .registration-section'
    );
    
    elementsToAnimate.forEach(el => {
        el.classList.add('reveal'); 
        observer.observe(el);       
    });

    /* --- 3. REGISTRATION & EMAILJS LOGIC --- */
    const regForm = document.getElementById('registrationForm');
    const submitBtn = document.getElementById('submitBtn');
    const verifyBtn = document.getElementById('verifyBtn');
    const modal = document.getElementById('welcomeModal');
    const closeModal = document.getElementById('closeModal');

    if (regForm) {
        regForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // SECURITY CHECK: Ensure reCAPTCHA is completed
            const captchaResponse = grecaptcha.getResponse();
            if (captchaResponse.length === 0) {
                alert("Please complete the reCAPTCHA to shine with us.");
                return;
            }
            
            // UI Visual Switch
            submitBtn.style.display = 'none';
            verifyBtn.style.display = 'block';
            verifyBtn.innerHTML = "Confirm Membership ✨";
        });
    }

    if (verifyBtn) {
        verifyBtn.addEventListener('click', () => {
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const userEmail = document.getElementById('userEmail').value;

            // PREPARE DATA: These variables must match your EmailJS Dashboard exactly
            const templateParams = {
                from_name: firstName + " " + lastName, // Stores full name
                to_email: userEmail,                   // Matches {{to_email}} in dashboard
                message: "Welcome to the Angaza family! We are thrilled to have you join our mission of unity and action."
            };

            // SEND EMAIL: Using your verified IDs
            emailjs.send('service_4wlvopl', 'template_050602d', templateParams)
                .then(function(response) {
                   console.log('SUCCESS!', response.status, response.text);
                   
                   // REDIRECT: Send user to your new Success Page
                   window.location.href = "success.html"; 
                   
                }, function(error) {
                   console.log('FAILED...', error);
                   alert("Registration failed. Please check your internet connection and try again.");
                   // Reset buttons so they can try again
                   submitBtn.style.display = 'block';
                   verifyBtn.style.display = 'none';
                });
        });
    }

    // Modal Close Logic (as a backup if they aren't redirected)
    if (closeModal) {
        closeModal.addEventListener('click', () => {
            modal.classList.remove('active');
        });
    }
});

