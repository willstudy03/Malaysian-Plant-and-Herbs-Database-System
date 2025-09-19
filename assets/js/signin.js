const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');
const registerForm = document.getElementById('regForm');
const loginForm = document.getElementById('loginForm');

registerBtn.addEventListener('click', function(){
    container.classList.add("active");
});

loginBtn.addEventListener('click', function(){
    container.classList.remove("active");
});

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('regForm').addEventListener('submit', function(event) {
        event.preventDefault();
        
        const formData = new FormData(this);
        fetch('assets/php/validation.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect to another page if registration is successful
                window.location.href = 'home.php'; // Replace with your desired page
            } else {
                // Display error messages
                document.getElementById('nameError').textContent = data.nameError || '';
                document.getElementById('emailError').textContent = data.emailError || '';
                document.getElementById('phoneError').textContent = data.phoneError || '';
                document.getElementById('passwordError').textContent = data.passwordError || '';
                document.getElementById('confirmPasswordError').textContent = data.confirmPasswordError || '';
            }
        })
        .catch(error => console.error('Error:', error));
    });

    document.getElementById('loginForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);
        fetch('assets/php/validation.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect to another page if login is successful
                window.location.href = 'home.php'; // Replace with your desired page
            } else {
                const errorBlock = document.getElementById('errorblock');
                const errorHTML = `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong>${data.message}</strong>
                                        <a href="#" id="alertbtn" type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></a>
                                    </div>`;
                errorBlock.innerHTML = errorHTML;
            }
        })
        .catch(error => console.error('Error:', error));
    });
});
