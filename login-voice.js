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

const utterance = new SpeechSynthesisUtterance("Please log in first to continue. If you don't have an account, press 'shift' to register.");

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
    if (event.key === 'l') {
        // Speak login prompt
        speakLoginPrompt();
    } else if (event.key === 'shift') {
        // Redirect to register page
        window.location.href = 'http://74.225.249.224/register.php';
    }
}

// Add event listener for keydown event
document.addEventListener('keydown', handleKeyDown);


