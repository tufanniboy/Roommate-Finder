// Form validation functions

// Signup form validation
function validateSignupForm() {
    let isValid = true;
    
    // Reset all error messages
    const errorElements = document.querySelectorAll('.error');
    errorElements.forEach(element => {
        element.textContent = '';
    });
    
    // Validate name
    const name = document.getElementById('name');
    if (name && name.value.trim() === '') {
        document.getElementById('nameError').textContent = 'Name is required';
        isValid = false;
    }
    
    // Validate email
    const email = document.getElementById('email');
    if (email) {
        if (email.value.trim() === '') {
            document.getElementById('emailError').textContent = 'Email is required';
            isValid = false;
        } else if (!isValidEmail(email.value)) {
            document.getElementById('emailError').textContent = 'Please enter a valid email address';
            isValid = false;
        }
    }
    
    // Validate phone
    const phone = document.getElementById('phone');
    if (phone) {
        if (phone.value.trim() === '') {
            document.getElementById('phoneError').textContent = 'Phone number is required';
            isValid = false;
        } else if (!isValidPhone(phone.value)) {
            document.getElementById('phoneError').textContent = 'Please enter a valid 10-digit phone number';
            isValid = false;
        }
    }
    
    // Validate address
    const address = document.getElementById('address');
    if (address && address.value.trim() === '') {
        document.getElementById('addressError').textContent = 'Address is required';
        isValid = false;
    }
    
    // Validate age
    const age = document.getElementById('age');
    if (age) {
        if (age.value.trim() === '') {
            document.getElementById('ageError').textContent = 'Age is required';
            isValid = false;
        } else if (isNaN(age.value) || parseInt(age.value) < 18) {
            document.getElementById('ageError').textContent = 'Age must be at least 18';
            isValid = false;
        }
    }
    
    // Validate password
    const password = document.getElementById('password');
    if (password) {
        if (password.value.trim() === '') {
            document.getElementById('passwordError').textContent = 'Password is required';
            isValid = false;
        } else if (password.value.length < 8) {
            document.getElementById('passwordError').textContent = 'Password must be at least 8 characters';
            isValid = false;
        }
    }
    
    // Validate confirm password
    const confirmPassword = document.getElementById('confirmPassword');
    if (confirmPassword && password) {
        if (confirmPassword.value.trim() === '') {
            document.getElementById('confirmPasswordError').textContent = 'Please confirm your password';
            isValid = false;
        } else if (confirmPassword.value !== password.value) {
            document.getElementById('confirmPasswordError').textContent = 'Passwords do not match';
            isValid = false;
        }
    }
    
    return isValid;
}

// Login form validation
function validateLoginForm() {
    let isValid = true;
    
    // Reset all error messages
    const errorElements = document.querySelectorAll('.error');
    errorElements.forEach(element => {
        element.textContent = '';
    });
    
    // Validate email
    const email = document.getElementById('email');
    if (email) {
        if (email.value.trim() === '') {
            document.getElementById('emailError').textContent = 'Email is required';
            isValid = false;
        } else if (!isValidEmail(email.value)) {
            document.getElementById('emailError').textContent = 'Please enter a valid email address';
            isValid = false;
        }
    }
    
    // Validate password
    const password = document.getElementById('password');
    if (password && password.value.trim() === '') {
        document.getElementById('passwordError').textContent = 'Password is required';
        isValid = false;
    }
    
    return isValid;
}

// Questionnaire form validation
function validateQuestionnaireForm() {
    let isValid = true;
    
    // Reset all error messages
    const errorElements = document.querySelectorAll('.error');
    errorElements.forEach(element => {
        element.textContent = '';
    });
    
    // Validate location
    const location = document.getElementById('location');
    if (location && location.value === '') {
        document.getElementById('locationError').textContent = 'Please select a location';
        isValid = false;
    }
    
    // Validate budget (radio buttons)
    const budgetSelected = document.querySelector('input[name="budget"]:checked');
    if (!budgetSelected) {
        document.getElementById('budgetError').textContent = 'Please select a budget range';
        isValid = false;
    }
    
    // Validate roommates (radio buttons)
    const roommatesSelected = document.querySelector('input[name="roommates"]:checked');
    if (!roommatesSelected) {
        document.getElementById('roommatesError').textContent = 'Please select number of roommates';
        isValid = false;
    }
    
    // Validate lifestyle (radio buttons)
    const lifestyleSelected = document.querySelector('input[name="lifestyle"]:checked');
    if (!lifestyleSelected) {
        document.getElementById('lifestyleError').textContent = 'Please select your lifestyle preference';
        isValid = false;
    }
    
    return isValid;
}

// Helper functions
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidPhone(phone) {
    const phoneRegex = /^\d{10}$/;
    return phoneRegex.test(phone);
}