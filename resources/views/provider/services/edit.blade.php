<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Service</title>
</head>
<body>
    <h1>Edit Service</h1>

    <form action="{{ route('provider.services.update', $service) }}" method="POST">
        @csrf
        @method('PUT')

        @include('provider.services._form', ['service' => $service])

        <p>
            <button type="submit">Save</button>
            <a href="{{ route('provider.services.index') }}">Cancel</a>
        </p>
    </form>
</body>
</html>

