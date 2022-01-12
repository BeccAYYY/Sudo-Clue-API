@echo off
title Sudo-Clue Test cURLS

pause

echo:
echo No Action Set
curl -X GET 127.0.0.1/API/API/core.php -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}
echo:
echo Expected: 400
pause

echo:
echo Invalid Action Set
curl -X GET 127.0.0.1/API/API/core.php?action=foobar -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}
echo:
echo Expected: 400
pause

echo:
echo Login Check while Logged Out
curl -X GET 127.0.0.1/API/API/core.php?action=login_check -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}
echo:
echo Expected: 401
pause

echo:
echo Get User Details while Logged Out
curl -X GET 127.0.0.1/API/API/core.php?action=get_user_details -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}
echo:
echo Expected: 200
pause

echo:
echo Log In with Invalid Password
curl -X POST 127.0.0.1/API/API/core.php?action=login -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code} -d "{\"username\": \"Username\", \"password\": \"Password1\"}" -H "Content-Type: application/json"
echo:
echo Expected: 400
pause

echo:
echo Log In with Invalid Username
curl -X POST 127.0.0.1/API/API/core.php?action=login -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}  -H "Content-Type: application/json"  -d "{   \"username\": \"Username+\",   \"password\": \"Password1!\"}"
echo:
echo Expected: 400
pause

echo:
echo Log In with Incorrect Details
curl -X POST 127.0.0.1/API/API/core.php?action=login -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}  -H "Content-Type: application/json"  -d "{   \"username\": \"Username\",   \"password\": \"Password1@\"}"
echo:
echo Expected: 400
pause

echo:
echo Log In with Correct Details
curl -X POST 127.0.0.1/API/API/core.php?action=login -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}  -H "Content-Type: application/json"  -d "{   \"username\": \"Username\",   \"password\": \"Password1!\"}"
echo:
echo Expected: 200
pause

echo:
echo Login Check while Logged In
curl -X GET 127.0.0.1/API/API/core.php?action=login_check -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}
echo:
echo Expected: 200
pause

echo:
echo Get User Details
curl -X GET 127.0.0.1/API/API/core.php?action=get_user_details -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}
echo:
echo Expected: 200
pause

echo:
echo Log Out
curl -X GET 127.0.0.1/API/API/core.php?action=logout -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}
echo:
echo Expected: 200
pause

echo:
echo Check if Existing Username Exists
curl -X GET "127.0.0.1/API/API/core.php?action=username_exists&username=Username" -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}
echo:
echo Expected: 200
pause

echo:
echo Check if Non-Existant Username Exists
curl -X GET "127.0.0.1/API/API/core.php?action=username_exists&username=Username5" -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}
echo:
echo Expected: 404
pause

echo:
echo Register with Empty Post
curl -X POST 127.0.0.1/API/API/core.php?action=register -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}
echo:
echo Expected: 400
pause

echo:
echo Register when Username Exists
curl -X POST 127.0.0.1/API/API/core.php?action=register -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}  -H "Content-Type: application/json"  -d "{   \"username\": \"Username\",   \"password\": \"Password1!\",   \"password2\": \"Password1!\"}"
echo:
echo Expected: 403
pause

echo:
echo Register when Username is Invalid
curl -X POST 127.0.0.1/API/API/core.php?action=register -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}  -H "Content-Type: application/json"  -d "{   \"username\": \"Username+\",   \"password\": \"Password1!\",   \"password2\": \"Password1!\"}"
echo:
echo Expected: 400
pause

echo:
echo Register when Password is Invalid
curl -X POST 127.0.0.1/API/API/core.php?action=register -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}  -H "Content-Type: application/json"  -d "{   \"username\": \"Username5\",   \"password\": \"Password1\",   \"password2\": \"Password1\"}"
echo:
echo Expected: 400
pause

echo:
echo Register when Passwords Do Not Match
curl -X POST 127.0.0.1/API/API/core.php?action=register -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}  -H "Content-Type: application/json"  -d "{   \"username\": \"Username5\",   \"password\": \"Password1!\",   \"password2\": \"Password1@\"}"
echo:
echo Expected: 400
pause

echo:
echo Register with Valid Details
curl -X POST 127.0.0.1/API/API/core.php?action=register -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}  -H "Content-Type: application/json"  -d "{   \"username\": \"Username5\",   \"password\": \"Password1!\",   \"password2\": \"Password1!\"}"
echo:
echo Expected: 200
pause

echo:
echo Login Check After Registering
curl -X GET 127.0.0.1/API/API/core.php?action=login_check -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}
echo:
echo Expected: 200
pause

echo:
echo Update Username with Incorrect Password
curl -X POST 127.0.0.1/API/API/core.php?action=update_username -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}  -H "Content-Type: application/json"  -d "{   \"username\": \"Username4\",   \"password\": \"Password1\"}"
echo:
echo Expected: 400
pause

echo:
echo Update Username where New Username Exists
curl -X POST 127.0.0.1/API/API/core.php?action=update_username -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}  -H "Content-Type: application/json"  -d "{   \"username\": \"Username\",   \"password\": \"Password1!\"}"
echo:
echo Expected: 400
pause

echo:
echo Update Username with Invalid Username
curl -X POST 127.0.0.1/API/API/core.php?action=update_username -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}  -H "Content-Type: application/json"  -d "{   \"username\": \"Username+\",   \"password\": \"Password1!\"}"
echo:
echo Expected: 400
pause

echo:
echo Update Username with Correct Details
curl -X POST 127.0.0.1/API/API/core.php?action=update_username -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}  -H "Content-Type: application/json"  -d "{   \"username\": \"Username4\",   \"password\": \"Password1!\"}"
echo:
echo Expected: 200
pause

echo:
echo Update Password with Incorrect Password
curl -X POST 127.0.0.1/API/API/core.php?action=update_password -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}  -H "Content-Type: application/json"  -d "{   \"old-password\": \"Password2!\",   \"password\": \"Password2!\",   \"password2\": \"Password2!\"}"
echo:
echo Expected: 400
pause

echo:
echo Update Password with Invalid Password
curl -X POST 127.0.0.1/API/API/core.php?action=update_password -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}  -H "Content-Type: application/json"  -d "{   \"old-password\": \"Password1!\",   \"password\": \"Password2\",   \"password2\": \"Password2\"}"
echo:
echo Expected: 400
pause

echo:
echo Update Password with Non-Matching Passwords
curl -X POST 127.0.0.1/API/API/core.php?action=update_password -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}  -H "Content-Type: application/json"  -d "{   \"old-password\": \"Password1!\",   \"password\": \"Password2!\",   \"password2\": \"Password3!\"}"
echo:
echo Expected: 400
pause

echo:
echo Update Password with Correct Details
curl -X POST 127.0.0.1/API/API/core.php?action=update_password -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}  -H "Content-Type: application/json"  -d "{   \"old-password\": \"Password1!\",   \"password\": \"Password2!\",   \"password2\": \"Password2!\"}"
echo:
echo Expected: 200
pause

echo:
echo Delete Account with Incorrect Password
curl -X POST 127.0.0.1/API/API/core.php?action=delete_account -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}  -H "Content-Type: application/json"  -d "{   \"username\": \"Username4\",   \"password\": \"Password3!\"}"
echo:
echo Expected: 400
pause

echo:
echo Delete Account with Correct Details
curl -X POST 127.0.0.1/API/API/core.php?action=delete_account -b PHPSESSID=rok1vqs83cjs58u7agclki7djh -w %%{http_code}  -H "Content-Type: application/json"  -d "{   \"username\": \"Username4\",   \"password\": \"Password2!\"}"
echo:
echo Expected: 200
pause
