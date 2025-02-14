<?php

function loadCSV($filename) {
    $data = [];
    if (($handle = fopen($filename, "r")) !== FALSE) {
        while (($row = fgetcsv($handle, 1000, "\t")) !== FALSE) { // Using tab delimiter
            if (count($row) >= 2) { // Ensure at least two columns
                $x = (int)trim($row[0]);
                $y = (int)trim($row[1]);
                $data[] = [$x, $y];
            }
        }
        fclose($handle);
    }
    return $data;
}

function createScatterPlot($data, $width = 800, $height = 600) {
    $image = imagecreatetruecolor($width, $height);
    $backgroundColor = imagecolorallocate($image, 255, 255, 255);
    $pointColor = imagecolorallocate($image, 0, 0, 255);
    $axisColor = imagecolorallocate($image, 0, 0, 0);

    imagefill($image, 0, 0, $backgroundColor);

    // Draw X and Y axes
    imageline($image, 50, $height - 50, $width - 50, $height - 50, $axisColor);
    imageline($image, 50, 50, 50, $height - 50, $axisColor);

    // Normalize data for plotting
    $xValues = array_column($data, 0);
    $yValues = array_column($data, 1);
    $xMin = min($xValues);
    $xMax = max($xValues);
    $yMin = min($yValues);
    $yMax = max($yValues);

    foreach ($data as $point) {
        [$x, $y] = $point;
        $plotX = 50 + ($x - $xMin) / ($xMax - $xMin) * ($width - 100);
        $plotY = $height - 50 - ($y - $yMin) / ($yMax - $yMin) * ($height - 100);
        imagesetpixel($image, (int)$plotX, (int)$plotY, $pointColor);
    }

    // Output image
    header("Content-Type: image/png");
    imagepng($image);
    imagedestroy($image);
}

// Load the data and plot
$data = loadCSV("data.csv"); // Ensure you have saved the data as "data.csv"
createScatterPlot($data);

?>