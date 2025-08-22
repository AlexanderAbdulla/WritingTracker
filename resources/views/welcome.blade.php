<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Writing Sessions</title>
    </head>
    <body>

        <h1>All Writing Sessions</h1>

        <div style="height: 320px; margin-bottom: 1rem;">
            <canvas id="wordsChart"></canvas>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div style="color: green;">
                {{ session('success') }}
            </div>
        @endif

        {{-- Error Messages --}}
        @if ($errors->any())
            <div style="color: red;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form to Add Session --}}
        <h2>Create New Session</h2>
        <form method="POST" action="/sessions">
            @csrf

            <label>Project Name:</label><br>
            <input type="text" name="project_name" value="{{ old('project_name') }}"><br><br>

            <label>Wordcount:</label><br>
            <input type="number" name="wordcount" value="{{ old('wordcount') }}"><br><br>

            <label>Minutes Spent:</label><br>
            <input type="number" name="minutes_spent" value="{{ old('minutes_spent') }}"><br><br>

            <label>Time Finished:</label><br>
            <input type="datetime-local" name="time_finished" value="{{ old('time_finished') }}"><br><br>

            <label>User ID:</label><br>
            <input type="text" name="user_id" value="{{ old('user_id') }}"><br><br>

            <button type="submit">Save Session</button>
        </form>

        <hr>

        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>Project</th>
                    <th>Wordcount</th>
                    <th>Minutes</th>
                    <th>Finished</th>
                    <th>User</th>
                </tr>
            </thead>
            <tbody>
                @foreach($writingSessions as $writingSession)
                    <tr>
                        <td>{{ $writingSession->project_name }}</td>
                        <td>{{ $writingSession->wordcount }}</td>
                        <td>{{ $writingSession->minutes_spent }}</td>
                        <td>{{ $writingSession->time_finished }}</td>
                        <td>{{ $writingSession->user_id }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        const labels  = @json($daily->pluck('day'));
        const words   = @json($daily->pluck('words'));
        const minutes = @json($daily->pluck('minutes'));

        // Chart.js expects numeric arrays
        const ctx = document.getElementById('wordsChart');
        new Chart(ctx, {
            type: 'bar', // 👈 change chart type
            data: {
            labels,
            datasets: [
                {
                label: 'Words per day',
                data: words,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                },
                {
                label: 'Minutes per day',
                data: minutes,
                backgroundColor: 'rgba(255, 99, 132, 0.7)',
                yAxisID: 'y1',
                }
            ]
            },
            options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'bottom' } },
            scales: {
                x: { stacked: false, title: { display: true, text: 'Date' } },
                y: { beginAtZero: true, title: { display: true, text: 'Words' } },
                y1: {
                position: 'right',
                beginAtZero: true,
                grid: { drawOnChartArea: false },
                title: { display: true, text: 'Minutes' }
                }
            }
            }
        });
        </script>
    </body>
</html>
