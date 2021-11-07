

/*
fetch("../API/core.php?action=login_check")
.then(response => response.json())
.then(data => {
    test.innerHTML = data.Message;
})*/


fetch("../API/core.php?action=fuckyou&username=test*")
.then(response => response.json())
.then(data => {
    test.innerHTML = data.Message;
})

