<!DOCTYPE html>
<html>
<head>
    <title>Customers - SalonSync</title>
</head>
<body>
    <h1>Customers</h1>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <a href="{{ route('customers.create') }}">+ Add Customer</a>
    <br><br>

    <table border="1" cellpadding="8">
        <tr>
            <th>Name</th>
            <th>Phone</th>
            <th>Email</th>
        </tr>
        @foreach($customers as $customer)
            <tr>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->phone }}</td>
                <td>{{ $customer->email }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>