<?php
// From profile when not logged in
if(isset($_GET['e']))
    if($_GET['e'] == 'log')
        echo 'Connectez-vous pour accéder à votre profil';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>test</title>
</head>

<body>
    <h1>Login</h1>
    <form action="" method="POST" style="display: flex; width: 300px; flex-direction:column; padding:10px">
        <label for="emailUser">Mail</label>
        <input type="email" name="emailUser" id="emailUser">
        <label for="passwordUser">Mot de passe</label>
        <input type="password" name="passwordUser" id="passwordUser">
        <button type="submit" name="login" id="btnLogin">Se connecter</button>
    </form>
    <p id="message"></p>
</body>

</html>
<style>
    * {
        padding: 5px;
        margin: 5px;
    }
</style>
<script>
    window.onload = () => {
        let btn = document.querySelector('#btnLogin');

        btn.addEventListener('click', function(e) {
            e.preventDefault();

            let email = document.querySelector('#emailUser').value;
            let password = document.querySelector('#passwordUser').value;

            login(email, password);
        });
    }

    function login(email, password) {
        fetch(`${window.location.origin}/public/api/v1/login`, {
            method: 'post',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                emailUser: email,
                passwordUser: password
            }),
        }).then(function(data) {
            return data.json();
        }).then(function(data) {
            let msg = document.querySelector('#message');

            if (data.status) {
                if (data.status == 'success') {
                    sessionStorage.token = data.token;
                    window.location.href = 'profile';
                } else if (data.status == 'failed') {
                    msg.style.backgroundColor = 'red'
                    msg.textContent = data.status;
                }
            } else
                msg.textContent = data.status;
        })
    }
</script>