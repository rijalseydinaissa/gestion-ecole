<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Apprenants</title>
</head>
<body>
    <h1>Liste des Apprenants</h1>
    <table border="1" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($apprenants as $apprenant)
                <tr>
                    <td>{{ $apprenant['nom'] ?? 'N/A' }}</td>
                    <td>{{ $apprenant['prenom'] ?? 'N/A' }}</td>
                    <td>{{ $apprenant['email'] ?? 'N/A' }}</td>
                    <td>{{ $apprenant['statut'] ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
