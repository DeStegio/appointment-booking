<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Service</title>
</head>
<body>
    <h1>Create Service</h1>

    <form action="{{ route('provider.services.store') }}" method="POST">
        @csrf

        @include('provider.services._form')

        <p>
            <button type="submit">Save</button>
            <a href="{{ route('provider.services.index') }}">Cancel</a>
        </p>
    </form>
</body>
</html>

