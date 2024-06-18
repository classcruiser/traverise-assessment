<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Database importer</title>
</head>
<body>
    <form action="{{ route('database.restore') }}" method="post">
        <h3>Database importer</h3>
        <p>Select your database backup here:</p>
        
        @if (session('success'))
            <br>
            <p style="color: green">{{ session('success') }}</p>
            <br>
        @endif

        @if (session('error'))
            <br>
            <p style="color: red">{{ session('error') }}</p>
            <br>
        @endif

        <select name="file">
            @foreach ($files as $file)
                <option value="{{ $file }}">{{ $file }}</option>
            @endforeach
        </select>
        <br><br>
        <button type="submit">Restore</button>
        @csrf
    </form>
</body>
</html>