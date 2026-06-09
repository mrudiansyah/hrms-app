<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 1cm;
        }

        body {
            font-family: sans-serif;
            font-size: 7px;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: auto;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 2px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        th {
            background-color: #f4b084;
            text-align: center;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .pull-left {
            float: left;
        }

        .pull-right {
            float: right;
        }

        .bg-primary {
            background-color: #3c8dbc;
            color: #fff;
        }

        .bg-grey {
            background-color: #eee;
        }

        .bg-dark-grey {
            background-color: #d2d6de;
        }

        .font-bold {
            font-weight: bold;
        }

        .box-header {
            display: none;
        }
    </style>
</head>

<body>
    @yield('Contents')
</body>

</html>