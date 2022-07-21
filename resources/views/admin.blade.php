<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <link href="{{ mix('/css/admin.css') }}?{{ time() }}" rel="stylesheet" />
    <script src="{{ mix('/js/admin.js') }}?{{ time() }}" defer></script>
    @inertiaHead
</head>
<body>
@inertia
</body>
</html>
