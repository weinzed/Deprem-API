<?php
error_reporting();
header('Content-type: application/json');

$content = file_get_contents("http://www.koeri.boun.edu.tr/scripts/lst0.asp");

preg_match_all("/<pre>(.*?)<\/pre>/s", $content, $pre);

$rows = explode("\n", str_replace(["<pre>", "</pre>"], "", $pre[0][0]));
for ($i = 0; $i < 7; $i++) array_shift($rows);

$rows = array_filter(array_map(function ($row) {

    $parts = explode('  ', $row);

    $parts = array_filter($parts, function ($row) {
        return strlen($row) > 1;
    });

    $parts = array_map(function ($part) {
        if ($part === '-.-') return null;
        return strip_tags(htmlspecialchars(str_replace(["\t", "\s", "\w", "\r", "\n"],'',trim($part))));
    }, $parts);

    return array_values($parts);

}, $rows), function ($row) {

    return count($row) > 2;
});

$arr = [];


foreach ($rows as $row) {

    $arr[] = [
        'tarih' => $row[0],
        'kordinatlar' => sprintf("%s,%s", $row[1], $row[2]),
        'derinlik' => $row[3],
        'ml' => $row[5],
        'lokasyon' => $row[7],
        'precision' => $row[8]
    ];
}

function validate($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data); 
    return $data;
}

if(isset($_GET['limit']))
{
    $limit = array_slice($arr, 0, validate($_GET['limit']));

    die(json_encode(array(
        "status" => true,
        "fetch" => "http://www.koeri.boun.edu.tr",
        'data' => $limit
    )));
}
else{
    die(json_encode(array(
        "status" => true,
        "fetch" => "http://www.koeri.boun.edu.tr",
        'data' => $arr
    )));
}

?>
