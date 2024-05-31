<?php include 'config.php';
session_start();
if(isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voice Login</title>
    <link rel="stylesheet" href="path/to/bootstrap.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <div class="row">
            <div class="col-md-offset-3 col-md-6">
                <!-- Form -->
                <form id="loginUser" method="POST">
                    <div class="customer_login">
                        <br>
                        <h2>Login here</h2>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" id="username" class="form-control username" placeholder="Username" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" id="password" class="form-control password" placeholder="Password" autocomplete="off" required>
                        </div>
                        <input type="submit" name="login" class="btn" value="Login"/>
                        <span>Don't Have an Account <a href="register.php">Register</a></span>
                        <br>
                        <div id="message"></div>
                    </div>
                </form>
                <!-- /Form -->

                <!-- Display recognized username and password -->
                <div id="recognizedCredentials" style="display:none;">
                    <p><strong>Recognized Username:</strong> <span id="recognizedUsername"></span></p>
                    <p><strong>Recognized Password:</strong> <span id="recognizedPassword"></span></p>
                </div>
            </div>
        </div>
    </div>
    <script>
        const synth = window.speechSynthesis;

function setFemaleVoice(utterance) {
    const voices = synth.getVoices();
    const femaleVoice = voices.find(voice =>
        voice.name.includes("Google US English") ||
        voice.name.includes("Microsoft Zira") ||
        voice.name.includes("Microsoft Catherine") ||
        voice.name.includes("Google UK English Female")
    );

    if (femaleVoice) {
        utterance.voice = femaleVoice;
    }
}

const utterance = new SpeechSynthesisUtterance("Please log in first to continue. If you don't have an account, press '0' to register.");

if (synth.getVoices().length !== 0) {
    setFemaleVoice(utterance);
    synth.speak(utterance);
} else {
    synth.onvoiceschanged = () => {
        setFemaleVoice(utterance);
        synth.speak(utterance);
    };
}


// Function to handle keydown event
function handleKeyDown(event) {
    if (event.key === '1') {
        // Speak login prompt
        speakLoginPrompt();
    } else if (event.key === '0') {
        // Redirect to register page
        window.location.href = 'http://localhost/supermart/register.php';
    }
}

// Add event listener for keydown event
document.addEventListener('keydown', handleKeyDown);





        let recognition;
        let step = 0;

        function startVoiceRecognition() {
            recognition = new webkitSpeechRecognition(); // for Chrome
            recognition.lang = 'en-US';
            recognition.continuous = false;
            recognition.interimResults = false;

            // Start recognition
            recognition.start();

            recognition.onresult = function(event) {
                let transcript = event.results[0][0].transcript.trim();
                if (step === 0) {
                    transcript = transcript.replace(/\s/g, '').toLowerCase(); // Remove spaces and convert to lowercase
                    document.getElementById('username').value = transcript;
                    document.getElementById('recognizedUsername').innerText = transcript;
                    step++;
                    speak('Username received. Please press S again to say your password.');
                    recognition.stop();
                } else if (step === 1) {
                    document.getElementById('password').value = transcript;
                    document.getElementById('recognizedPassword').innerText = transcript;
                    step++;
                    speak('Password received. To submit, please press Enter.');
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
                    speak('Please say your username.');
                    startVoiceRecognition();
                } else if (step === 1) {
                    speak('Please say your password.');
                    startVoiceRecognition();
                }
            } else if (event.key === 'Enter' && step === 2) {
                // Manually trigger the submit event handler
                $('#loginUser').submit();
            }
        });

        document.getElementById('startVoiceBtn').addEventListener('click', function() {
            step = 0; // Reset step for new session
            speak('Please press S to say your username.');
        });

        $(document).ready(function(){
            $('#loginUser').submit(function(e){
                e.preventDefault();
                var username = $('#username').val();
                var password = $('#password').val();
                if(username === '' || password === ''){
                    $('#message').html('<div class="alert alert-danger">Please Fill All The Fields.</div>');
                } else {
                    $.ajax({
                        url: 'php_files/user.php',
                        method: 'POST',
                        data: {login: '1', username: username, password: password},
                        dataType: 'json',
                        success: function(response){
                            $('#message').html('');
                            if(response.success){
                                $('#message').html('<div class="alert alert-success">Logged In Successfully.</div>');
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
                }
            });
        });
    </script>
    <?php include 'footer.php'; ?>
</body>
</html>
