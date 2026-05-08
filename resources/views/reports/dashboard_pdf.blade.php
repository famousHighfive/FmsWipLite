<!DOCTYPE html>
<html>
<head>
    <title>Rapport Décisionnel</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .stats-grid { display: table; width: 100%; margin-bottom: 30px; }
        .stats-item { display: table-cell; padding: 15px; background: #f9fafb; border: 1px solid #e5e7eb; text-align: center; }
        .stats-item h3 { margin: 0; color: #6b7280; font-size: 12px; text-transform: uppercase; }
        .stats-item p { margin: 5px 0 0; font-size: 24px; font-weight: bold; color: #111827; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #4f46e5; color: white; padding: 10px; text-align: left; font-size: 14px; }
        td { padding: 10px; border-bottom: 1px solid #e5e7eb; font-size: 13px; }
        .footer { margin-top: 50px; text-align: right; font-size: 10px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport Décisionnel wipLite</h1>
        <p>Généré le {{ $date }}</p>
    </div>

    <div class="stats-grid">
        <div class="stats-item">
            <h3>Total Employés</h3>
            <p>{{ $stats['employees'] }}</p>
        </div>
        <div class="stats-item">
            <h3>Actifs</h3>
            <p>{{ $stats['active'] }}</p>
        </div>
        <div class="stats-item">
            <h3>Campagnes</h3>
            <p>{{ $stats['campaigns'] }}</p>
        </div>
        <div class="stats-item">
            <h3>Heures Totales</h3>
            <p>{{ round($stats['workedHours']) }}h</p>
        </div>
    </div>

    <h2>Performance des Campagnes</h2>
    <table>
        <thead>
            <tr>
                <th>Nom de la Campagne</th>
                <th>Statut</th>
                <th>Nombre d'Agents</th>
            </tr>
        </thead>
        <tbody>
            @foreach($campaigns as $campaign)
            <tr>
                <td>{{ $campaign->name }}</td>
                <td>{{ $campaign->status }}</td>
                <td>{{ $campaign->assignments_count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Document généré automatiquement par le système wipLite.
    </div>
</body>
</html>
