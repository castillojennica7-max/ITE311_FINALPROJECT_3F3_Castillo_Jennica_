<!DOCTYPE html>
<html>
<head>
    <title>Staff - SalonSync</title>
</head>
<body>
    <h1>Staff</h1>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <a href="{{ route('staff.create') }}">+ Add Staff</a>
    <br><br>

    <table border="1" cellpadding="8">
        <tr>
            <th>Name</th>
            <th>Role</th>
            <th>Phone</th>
        </tr>
        @foreach($staffMembers as $member)
            <tr>
                <td>{{ $member->name }}</td>
                <td>{{ $member->role }}</td>
                <td>{{ $member->phone }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>