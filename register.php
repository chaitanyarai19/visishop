<?php include 'config.php';
session_start();
if(isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}


include 'header.php'; ?>
<div class="container">
    <div class="row">
        <div class="col-md-offset-3 col-md-6">
           
            <!-- Form -->
            <form id="register_sign_up" class="signup_form" method ="POST" autocomplete="off">
                <h2>register here</h2>
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" id="first_name" name="f_name" class="form-control first_name" placeholder="First Name" autocomplete="off" requried />
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" id="last_name" name="l_name" class="form-control last_name" placeholder="Last Name" autocomplete="off" requried />
                </div>
                <div class="form-group">
                    <label>Username / Email</label>
                    <input type="email" id="username" name="username" class="form-control user_name" placeholder="Email Address" autocomplete="off" requried />
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="password" name="password" class="form-control pass_word" placeholder="Password" autocomplete="off" requried />
                </div>
                <div class="form-group">
                    <label>Mobile</label>
                    <input type="phone" id="mobile" name="mobile" class="form-control mobile" placeholder="Mobile" autocomplete="off" requried />
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" id="address" name="address" class="form-control address" placeholder="Address" autocomplete="off" requried>
                </div>
                <div class="form-group">
                    <label>City</label>
                    <input type="text" id="city" name="city" class="form-control city" placeholder="City" autocomplete="off" requried>
                </div>
                <input type="submit" id="signup" name="signup" class="btn" value="sign up"/>
            </form>
            <!-- /Form -->
        </div>
    </div>
</div>
   
    <script>
    let recognition;
    let step = 0;

    function startVoiceRecognition() {
        recognition = new webkitSpeechRecognition();
        recognition.lang = 'en-US';
        recognition.continuous = false;
        recognition.interimResults = false;

        // Start recognition
        recognition.start();

        recognition.onresult = function(event) {
            let transcript = event.results[0][0].transcript.trim();
            if (step === 0) {
                document.getElementById('first_name').value = transcript;
                step++;
                speak('First name received. Please press S again to say your last name.');
                recognition.stop();
            } else if (step === 1) {
                document.getElementById('last_name').value = transcript;
                step++;
                speak('Last name received. Please press S again to say your email address.');
                recognition.stop();
            } else if (step === 2) {
                transcript = transcript.replace(/\s/g, '').toLowerCase();
                document.getElementById('username').value = transcript;
                step++;
                speak('Email address received. Please press S again to say your password.');
                recognition.stop();
            } else if (step === 3) {
                document.getElementById('password').value = transcript;
                step++;
                speak('Password received. Please press S again to say your mobile number.');
                recognition.stop();
            } else if (step === 4) {
                document.getElementById('mobile').value = transcript;
                step++;
                speak('Mobile number received. Please press S again to say your address.');
                recognition.stop();
            } else if (step === 5) {
                document.getElementById('address').value = transcript;
                step++;
                speak('Address received. Please press S again to say your city.');
                recognition.stop();
            } else if (step === 6) {
                document.getElementById('city').value = transcript;
                step++;
                speak('City received. To submit, please press Enter.');
                recognition.stop();
            }
        };

        recognition.onerror = function(event) {
            console.error('Speech recognition error detected: ' + event.error);
            recognition.stop();
        };
    }

    function speak(text) {
        const msg = new SpeechSynthesisUtterance();
        msg.text = text;
        window.speechSynthesis.speak(msg);
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 's' || event.key === 'S') {
            if (step === 0) {
                speak('Please say your first name.');
                startVoiceRecognition();
            } else if (step === 1) {
                speak('Please say your last name.');
                startVoiceRecognition();
            } else if (step === 2) {
                speak('Please say your email address.');
                startVoiceRecognition();
            } else if (step === 3) {
                speak('Please say your password.');
                startVoiceRecognition();
            } else if (step === 4) {
                speak('Please say your mobile number.');
                startVoiceRecognition();
            } else if (step === 5) {
                speak('Please say your address.');
                startVoiceRecognition();
            } else if (step === 6) {
                speak('Please say your city.');
                startVoiceRecognition();
            }
        } else if (event.key === 'Enter' && step === 7) {
            document.getElementById('registerUser').submit();
        }
    });

    document.getElementById('startVoiceBtn').addEventListener('click', function() {
        step = 0;
        speak('Please press S to start registration.');
    });

    $(document).ready(function(){
    $('#registerUser').submit(function(e){
        e.preventDefault(); // Prevent the default form submission
        var formData = $(this).serialize(); // Serialize the form data

        // Send AJAX request
        $.ajax({
            url: './php_files/user.php',
            method: 'POST',
            data: formData, // Send serialized form data
            dataType: 'json',
            success: function(response){
                // Handle the response
                if(response.success){
                    $('#message').html('<div class="alert alert-success">'+response.success+'</div>');
                    setTimeout(function(){ 
                        window.location.href = 'index.php';
                    }, 1000);
                } else if(response.error){
                    $('#message').html('<div class="alert alert-danger">'+response.error+'</div>');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#message').html('<div class="alert alert-danger">An error occurred: ' + textStatus + '</div>');
            }
        });
    });
});

</script>
<?php include 'footer.php'; ?>