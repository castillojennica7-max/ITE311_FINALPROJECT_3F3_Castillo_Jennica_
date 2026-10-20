<!DOCTYPE html>
<html>
<head>
    <title>Add Staff - SalonSync</title>
</head>
<body>
    <h1>Add Staff</h1>

    @if($errors->any())
        <ul style="color: red;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('staff.store') }}">
        @csrf
        <input type="text" name="name" placeholder="Name" required><br><br>
        <input type="text" name="role" placeholder="Role (e.g. Hair Stylist)" required><br><br>
        <input type="text" name="phone" placeholder="Phone"><br><br>
        <button type="submit">Save</button>
    </form>

    <br>
    <a href="{{ route('staff.index') }}">Back to list</a>
</body>
</html>