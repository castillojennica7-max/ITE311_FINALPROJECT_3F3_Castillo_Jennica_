<!DOCTYPE html>
<html>
<head>
    <title>Add Service - SalonSync</title>
</head>
<body>
    <h1>Add Service</h1>

    @if($errors->any())
        <ul style="color: red;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('services.store') }}">
        @csrf
        <input type="text" name="name" placeholder="Service name (e.g. Haircut)" required><br><br>
        <input type="number" step="0.01" name="price" placeholder="Price" required><br><br>
        <input type="number" name="duration_minutes" placeholder="Duration in minutes" required><br><br>
        <button type="submit">Save</button>
    </form>

    <br>
    <a href="{{ route('services.index') }}">Back to list</a>
</body>
</html>