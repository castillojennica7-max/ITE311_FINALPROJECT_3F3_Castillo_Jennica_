<!DOCTYPE html>
<html>
<head>
    <title>Services - SalonSync</title>
</head>
<body>
    <h1>Services</h1>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <a href="{{ route('services.create') }}">+ Add Service</a>
    <br><br>

    <table border="1" cellpadding="8">
        <tr>
            <th>Service</th>
            <th>Price</th>
            <th>Duration (minutes)</th>
        </tr>
        @foreach($services as $service)
            <tr>
                <td>{{ $service->name }}</td>
                <td>₱{{ number_format($service->price, 2) }}</td>
                <td>{{ $service->duration_minutes }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>