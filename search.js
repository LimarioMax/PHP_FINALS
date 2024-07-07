// Function to perform AJAX request and update search results
function searchVideos() {
    // Get the search term from the input field
    var searchTerm = document.getElementById('search').value.trim();

    // Create a new XMLHttpRequest object
    var xhr = new XMLHttpRequest();

    // Configure the AJAX request
    xhr.open('GET', 'search.php?search=' + encodeURIComponent(searchTerm), true);

    // Define what happens on successful data submission
    xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 400) {
            // On success, update the search results section
            document.getElementById('search-results').innerHTML = xhr.responseText;
        } else {
            console.error('Request failed: ' + xhr.status);
        }
    };

    // Handle network errors
    xhr.onerror = function () {
        console.error('Request failed');
    };

    // Send the request to the server
    xhr.send();
}
