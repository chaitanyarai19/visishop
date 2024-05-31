var cururl = window.location.href;
console.log(cururl);

if (cururl === 'http://localhost/visishop/index.php' || cururl === 'http://localhost/visishop/') {
   // window.addEventListener('keydown', (event) => {
       
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

            const utterance = new SpeechSynthesisUtterance("Welcome to our e-commerce website. Press 1 for men's category, press 2 for women's category, press 3 for kids' category.");

            if (synth.getVoices().length !== 0) {
                setFemaleVoice(utterance);
                synth.speak(utterance);
            } else {
                synth.onvoiceschanged = () => {
                    setFemaleVoice(utterance);
                    synth.speak(utterance);
                };
            }

            window.addEventListener('keydown', (event) => {
                const navigate = (url) => {
                    window.location.href = `http://localhost/visishop/category.php?cat=${url}`;
                };
                switch (event.key) {
                    case '1':
                        navigate('29');
                        break;
                    case '2':
                        navigate('30');
                        break;
                    case '3':
                        navigate('31');
                        break;
                    default:
                        const invalidKeyUtterance = new SpeechSynthesisUtterance('Invalid choice, please press 1, 2, or 3.');
                        setFemaleVoice(invalidKeyUtterance);
                        synth.speak(invalidKeyUtterance);
                        break;
                }
            });
    
   // });
} else if (cururl.startsWith('http://localhost/visishop/category.php?cat=')) {


    
let categoryName = "";
if (cururl.includes('cat=29')) {
    categoryName = "Mens";
} else if (cururl.includes('cat=30')) {
    categoryName = "Womens";
} else if (cururl.includes('cat=31')) {
    categoryName = "Kids";
}
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


    const utterance = new SpeechSynthesisUtterance(`Now you are in ${categoryName} Category page.`);

    if (synth.getVoices().length !== 0) {
        setFemaleVoice(utterance);
        synth.speak(utterance);
    } else {
        synth.onvoiceschanged = () => {
            setFemaleVoice(utterance);
            synth.speak(utterance);
        };
    }

    window.addEventListener('keydown', (event) => {
        if (event.key === "Enter") {
            window.location.href = 'http://localhost/visishop/cart.php';
        } else if (event.key === "Backspace") {
            window.history.back();
        }
    });
} else if (cururl === 'http://localhost/visishop/cart.php') {
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

    const cartTable = document.querySelector('table.table');
    let cartDetails = 'Your cart contains: ';

    if (cartTable) {
        const rows = cartTable.querySelectorAll('.item-row');
        rows.forEach(row => {
            const productName = row.querySelector('td:nth-child(2)').innerText;
            const productPrice = row.querySelector('.product-price').innerText;
            const productQuantity = row.querySelector('.item-qty').value;
            cartDetails += `${productQuantity} of ${productName} priced at ${productPrice} each, `;
        });
        cartDetails = cartDetails.slice(0, -2); // Remove trailing comma and space
        cartDetails += ". For checkout press Enter.";
    } else {
        cartDetails = 'Your cart is currently empty.';
    }

    const utterance = new SpeechSynthesisUtterance(cartDetails);

    if (synth.getVoices().length !== 0) {
        setFemaleVoice(utterance);
        synth.speak(utterance);
    } else {
        synth.onvoiceschanged = () => {
            setFemaleVoice(utterance);
            synth.speak(utterance);
        };
    }

    let currentItemIndex = -1;
    let itemRows = cartTable.querySelectorAll('.item-row');

    function updateQuantity(up) {
        if (currentItemIndex >= 0 && currentItemIndex < itemRows.length) {
            const qtyInput = itemRows[currentItemIndex].querySelector('.item-qty');
            let currentQty = parseInt(qtyInput.value, 10);
            if (up) {
                currentQty += 1;
            } else {
                currentQty -= 1;
            }
            qtyInput.value = Math.max(1, currentQty); // Ensure quantity does not go below 1
            qtyInput.dispatchEvent(new Event('change')); // Trigger change event to update subtotal and total
        }
    }

    function handleShift(event) {
        if (event.key === "ArrowUp") {
            updateQuantity(true);
        } else if (event.key === "ArrowDown") {
            updateQuantity(false);
        }
    }

    window.addEventListener('keydown', (event) => {
        if (event.key === "Enter") {
            document.querySelector('.checkout-form').submit();
        } else if (event.key === "Backspace") {
            window.history.back();
        } else if (event.key === "Shift") {
            window.addEventListener('keydown', handleShift);
            window.addEventListener('keyup', () => {
                window.removeEventListener('keydown', handleShift);
            }, { once: true });
        }
    });
} else if (cururl === 'http://localhost/visishop/success.php') {
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

    const clapSound = new Audio('path/to/clap.mp3'); // Ensure this path is correct
    const utterance = new SpeechSynthesisUtterance("Congratulations! Your order is confirmed with us. Press Enter to go to the home page.");

    clapSound.play();

    clapSound.onended = () => {
        if (synth.getVoices().length !== 0) {
            setFemaleVoice(utterance);
            synth.speak(utterance);
        } else {
            synth.onvoiceschanged = () => {
                setFemaleVoice(utterance);
                synth.speak(utterance);
            };
        }
    };

    window.addEventListener('keydown', (event) => {
        if (event.key === "Enter") {
            window.location.href = 'http://localhost/visishop/';
        }
    });
} else {
    console.error('Speech synthesis not supported');
}
