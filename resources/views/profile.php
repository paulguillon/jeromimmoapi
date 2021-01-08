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
    <a href="logout"><button>Se déconnecter</button></a>
</body>

</html>
<script>
    let tokenUser = {}

    // If Logged in
    if (sessionStorage.token)
        tokenUser = parseJwt(sessionStorage.token);
    else {
        window.location.href = `${window.location.origin}/public/?e=log`;
    }

    // If token exists
    if (Object.keys(tokenUser).length !== 0) {
        console.log(tokenUser)
        fetch(`${window.location.origin}/public/api/v1/roles/${tokenUser.idRoleUser}`).then((response) => {
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