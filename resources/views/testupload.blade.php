<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Document</title>
</head>

<body>
    <form action="{{route('store.ikw')}}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('POST')
        <input type="file" name="file" id="">
        <button type="submit">Save</button>
    </form>
    {{-- <form action="{{route('update')}}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('POST')
        <input type="file" name="file" id="">
        <button type="submit">Save</button>
    </form> --}}
    {{-- <form action="{{route('export')}}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('GET')
        <button type="submit">Download</button>
    </form> --}}
</body>

</html>