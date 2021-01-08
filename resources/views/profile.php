<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
</head>

<body>
    <h1>PROFIL</h1>
    <p>Vous êtes connecté.e !!!</p>
    <button id="logoutBtn">Se déconnecter</button>
</body>

</html>
<script>
console.log(sessionStorage.token)
    let logoutBtn = document.querySelector('#logoutBtn');
    logoutBtn.addEventListener('click', function() {
        fetch(`${window.location.origin}/api/v1/logout`, {
            method: 'post',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `bearer ${sessionStorage.token}`
            },
        }).then(() => {
            delete sessionStorage.token;
            window.location.href = window.location.origin + '?logout=1';
        });
    });


    let tokenUser = {}

    // If Logged in
    if (sessionStorage.token)
        tokenUser = parseJwt(sessionStorage.token);
    else {
        window.location.href = `${window.location.origin}/?e=log`;
    }

    // If token exists
    if (Object.keys(tokenUser).length !== 0) {
        console.log(tokenUser)
        fetch(`${window.location.origin}/api/v1/roles/${tokenUser.idRoleUser}`, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': 'bearer ' + sessionStorage.token
            },
        }).then((response) => {
            return response.json();
        }).then((data) => {
            console.log(data.role.roleName);
        });
    } else
        console.log('pas ok')

    function parseJwt(token) {
        var base64Url = token.split('.')[1];
        var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
        var jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
            return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
        }).join(''));

        return JSON.parse(jsonPayload);
    };
</script>