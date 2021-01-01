<?php

/**
 * This file holds things for processing CSV data
*/

/**
 * Writes data to a CSV file
 *
 * @param $filename - CSV file name
 * @param $mode - file writing mode (append to or rewrite the file)
 * @param $data - array of data to write
 */

function writeDataToCSV ($filename, $mode, $data) {
    $file = fopen($filename,$mode);
    foreach ($data as $line)
    fputcsv($file, $line);
    fclose($file);
}

/**
 * Gets info about visitors from CSV data file
 *
 * @param $csv_file - CSV data file name
 * @return array csv_result
 */

function getDataFromCSV($csv_file) {
    $csv_result = [];
    if(!file_exists($csv_file)) return $csv_result;
    if (($file = fopen($csv_file, "r")) !== false) {
        while (($data = fgetcsv($file)) !== FALSE) {
            $csv_result[] = $data;
        }
        fclose($file);
    }
    return $csv_result;
}