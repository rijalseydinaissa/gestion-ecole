<!DOCTYPE html>
<html>
<head>
    <title>Liste des utilisateurs</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 5px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Liste des utilisateurs</h1>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Téléphone</th>
                <th>Statut</th>
                <th>Adresse</th>
                <th>Photo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user['nom'] ?? 'N/A' }}</td>
                    <td>{{ $user['prenom'] ?? 'N/A' }}</td>
                    <td>{{ $user['email'] ?? 'N/A' }}</td>
                    <td>{{ $user['role'] ?? 'N/A' }}</td>
                    <td>{{ $user['telephone'] ?? 'N/A' }}</td>
                    <td>{{ $user['statut'] ?? 'N/A' }}</td>
                    <td>{{ $user['adresse'] ?? 'N/A' }}</td>
                    <td>{{ $user['photo'] ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>