

fetch("../API/core.php?action=login-check")
.then(response => response.json())
.then(data => {
    test.innerHTML = data.Message;
})