<!DOCTYPE html>
<html>
<head>
    <title>Liste des Promotions</title>
</head>
<body>
    <h1>Liste des Promotions</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Libelle</th>
                <th>Date de début</th>
                <th>Date de fin</th>
                <th>État</th>
            </tr>
        </thead>
        <tbody>
            @foreach($promotions as $promotion)
            <tr>
                <!-- <td>{{ $promotion['id'] }}</td> -->
                <td>{{ $promotion['libelle'] }}</td>
                <td>{{ $promotion['date_debut'] }}</td>
                <td>{{ $promotion['date_fin'] }}</td>
                <td>{{ $promotion['duree'] }}</td>
                <td>{{ $promotion['photo'] }}</td>
                <td>{{ $promotion['etat'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
