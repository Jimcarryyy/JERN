document.getElementById('submitButton').addEventListener('click', function(event) {
    var loader = document.getElementById('loader');
    var buttonText = document.getElementById('buttonText');

    // Show the loader and hide the text
    loader.style.display = 'inline-block';
    buttonText.style.display = 'none';

    // Optionally, disable the button to prevent multiple submissions
    event.target.disabled = true;

    // Simulate form submission delay
    setTimeout(function() {
        event.target.disabled = false;
        loader.style.display = 'none';
        buttonText.style.display = 'inline-block';
    }, 2000); 
});

