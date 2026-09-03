<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kontrak {{ $kontrak->no_kontrak }}</title>
    <style>
        @page {
            margin: 25mm 20mm 20mm 25mm;
        }
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.5;
        }
        .title {
            text-align: center;
            text-transform: uppercase;
        }
        .title h2, .title h4 {
            margin: 0;
        }
        .content {
            margin-top: 25px;
        }
        .section-table {
            width: 100%;
            border-collapse: collapse;
        }
        .section-table td {
            vertical-align: top;
            padding: 2px 0;
        }
        .section-table .label {
            width: 160px;
        }
        .section-table .colon {
            width: 10px;
        }
        .paragraph {
            text-align: justify;
        }
        .pasal-title {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            margin-bottom: 5px;
        }
        ul {
            padding-left: 18px;
        }
        .page-break {
            page-break-before: always;
        }
        table.comp-table {
            width: 70%;
            border-collapse: separate;
            margin-top: 10px;
            margin-bottom: 15px;
        }
        table.comp-table td {
            padding: 6px 10px;
            border: none;
        }
        table.comp-table td.label {
            width: 55%;
        }
        table.comp-table td.value {
            text-align: right;
        }
    </style>
</head>
<body>
    {!! $konten !!}
</body>
</html>
