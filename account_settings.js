// Initialize address selector on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the Philippine address selector
    if (typeof window.initializeAddressSelector === 'function') {
        window.initializeAddressSelector();
    }
});

// Function to set text value to hidden field
function setText(nameSel, hiddenId) {
    const opt = document.querySelector(nameSel + " option:checked");
    document.getElementById(hiddenId).value = opt ? opt.text : "";
}

// Tab Switching Functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button and corresponding content
            this.classList.add('active');
            document.getElementById(tabName + '-tab').classList.add('active');
        });
    });

    // Initialize Philippines Address Selector for Address tab
    if (typeof $ !== 'undefined' && $.fn.ph_locations) {
        // $('#region').ph_locations({
        //     'location_type': 'regions'
        // });

        // $('#region').on('change', function() {
        //     var selectedRegion = $(this).val();
        //     $('#province').prop('disabled', false);
        //     $('#province').ph_locations({
        //         'location_type': 'provinces',
        //         'region_code': selectedRegion
        //     });
        //     // Reset dependent dropdowns
        //     $('#city').prop('disabled', true).html('<option value="">Select city/municipality</option>');
        //     $('#barangay').prop('disabled', true).html('<option value="">Select barangay</option>');
            
        //     // Update hidden field
        //     setText('#region', 'User_Region_Name');
        // });

        // $('#province').on('change', function() {
        //     var selectedProvince = $(this).val();
        //     $('#city').prop('disabled', false);
        //     $('#city').ph_locations({
        //         'location_type': 'cities',
        //         'province_code': selectedProvince
        //     });
        //     // Reset dependent dropdown
        //     $('#barangay').prop('disabled', true).html('<option value="">Select barangay</option>');
            
        //     // Update hidden field
        //     setText('#province', 'User_Province_Name');
        // });

        // $('#city').on('change', function() {
        //     var selectedCity = $(this).val();
        //     $('#barangay').prop('disabled', false);
        //     $('#barangay').ph_locations({
        //         'location_type': 'barangays',
        //         'city_code': selectedCity
        //     });
            
        //     // Update hidden field
        //     setText('#city', 'User_City_Name');
        // });

        // $('#barangay').on('change', function() {
        //     // Update hidden field
        //     setText('#barangay', 'User_Barangay_Name');
        // });
    }

    // Initialize hidden fields with current values on page load
    ["#region", "#province", "#city", "#barangay"].forEach((sel, i) => {
        const ids = ["User_Region_Name", "User_Province_Name", "User_City_Name", "User_Barangay_Name"];
        setText(sel, ids[i]);
    });
});

// Password Toggle Functionality
function togglePassword(inputId, iconId) {
    const passwordInput = document.getElementById(inputId);
    const eyeIcon = document.getElementById(iconId);
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
    } else {
        passwordInput.type = 'password';
        eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
    }
}

// Personal Information Form Submission
document.getElementById('personalInfoForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const firstName = document.getElementById('firstName').value.trim();
    const lastName = document.getElementById('lastName').value.trim();
    const email = document.getElementById('email').value.trim();
    
    if (!firstName || !lastName || !email) {
        alert('Please fill in all required fields');
        return;
    }
    
    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('Please enter a valid email address');
        return;
    }
    
    // In real application, this would send data to backend
    alert('Personal information updated successfully!');
    console.log('Personal Info Updated:', {
        firstName,
        lastName,
        email,
        phone: document.getElementById('phone').value
    });
});

// Address Form Submission
document.getElementById('addressForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const region = document.getElementById('region').value;
    const province = document.getElementById('province').value;
    const city = document.getElementById('city').value;
    const barangay = document.getElementById('barangay').value;
    const street = document.getElementById('street').value.trim();
    
    if (!region || !province || !city || !barangay || !street) {
        alert('Please fill in all required address fields');
        return;
    }
    
    // In real application, this would send data to backend
    alert('Address information updated successfully!');
    console.log('Address Updated:', {
        region,
        province,
        city,
        barangay,
        street,
        houseNumber: document.getElementById('houseNumber').value,
        zipCode: document.getElementById('zipCode').value
    });
});

// Password Form Submission
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (!currentPassword || !newPassword || !confirmPassword) {
        alert('Please fill in all password fields');
        return;
    }
    
    if (newPassword !== confirmPassword) {
        alert('New passwords do not match');
        return;
    }
    
    if (newPassword.length < 6) {
        alert('Password must be at least 6 characters long');
        return;
    }
    
    // In real application, this would send data to backend
    alert('Password changed successfully!');
    
    // Reset form
    document.getElementById('passwordForm').reset();
    console.log('Password Changed');
});

// Add smooth scrolling for better UX
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
