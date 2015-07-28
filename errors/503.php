<?php
    header('HTTP/1.1 503 Service Temporarily Unavailable');
    header('Status: 503 Service Temporarily Unavailable');
    header('Retry-After: 300');//300 seconds
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upgrades to Server</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
</head>
<body>
    <h1>503 Server Error</h1>
    <p>
        This is most likely a mysql error. This error report needs more updating
    </p>
</body>
</html>
