<!DOCTYPE html>
<html>
<head>
    <title>Vos informations d'inscription</title>
</head>
<body>
    <h1>Bienvenue {{ $user->prenom }} {{ $user->nom }}</h1>
    <p>Votre compte a été créé avec succès. Voici vos informations d'authentification :</p>
    <ul>
        <li>Login : {{ $login }}</li>
        <li>Mot de passe : {{ $password }}</li>
    </ul>
    <p>Pour vous connecter, suivez ce lien : <a href="{{ $auth_link }}">Connexion</a></p>
</body>
</html>
