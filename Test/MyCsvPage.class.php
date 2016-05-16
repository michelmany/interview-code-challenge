<?php
namespace Test;
class MyCsvPage
{
    protected function displayPage()
    {
        /* echo out csv upload form and exit */
    }
    protected function process_page()
    {
        $file = $_FILES['average'];
        if ($file['error'] != 0) {
            $this->displayPage();
        }
       
        // Read file 
        $file = $file['tmp_name'];
        $fp = fopen($file, 'r');
        if (!$fp)
            throw new \Exception('could not open file');
        
        // Calculate averages per row and append
        $csvInArray = [];
        while (($row = fgetcsv($fp)) !== FALSE) {
            $csvInArray[] = $row;
        }
        foreach ($csvInArray as $key=>$line) {
            $csvInArray[$key][] = $this->createCsvLine($key, $line);
        }

        // Write new file
        $fp = fopen($file, 'w');
        foreach ($csvInArray as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
	header('Content-Type: application/csv');
	header('Content-Disposition: attachment; filename=example.csv');
	header('Pragma: no-cache');
	readfile($file);
    }
    protected function createCsvLine($key, $line)
    {
        if ($key == 0) {
            return 'Average';
        }
        return $this->average($line);
    }
    protected function average($row)
    {
        $average = [];
        $total = count($row)-1;
        for ($i = 0; $i <= $total; $i++) {
            $average[] = $row[$i];
        }
        //var_dump($average);exit;
        return array_sum($average) / count($row);
    }
    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([new MyCsvPage, $name], $arguments);
    }
}
\Test\MyCsvPage::process_page();