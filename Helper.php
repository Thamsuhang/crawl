<?php

class Helper {
    public static function CreateCsv($title, $newArray) {
        $file = fopen('data.csv', 'w');
        ftruncate($file, 0);
        fputcsv($file, $title);
        foreach ($newArray as $row) {
            fputcsv($file, $row);
        }
        // Close the file
        fclose($file);
        self::downloadFile($newArray);
    }

    public static function downloadFile($newArray) {
        $file = ("data.csv");
        $filetype = filetype($file);
        $filename = basename($file);
        header("Content-Type: " . $filetype);
        header("Content-Length: " . filesize($file));
        header("Content-Disposition: attachment; filename=" . $filename);
        readfile($file);
        header("Refresh:0");
    }

    public static function addAddress($pq, $country_list,$codes,$states) {
        $AddresNode = $pq->find('p:nth-child(3)')->text();
        $newNode = explode('Funding Scheme(s):', $AddresNode);
        $addressString = preg_replace('/^(?<=)\s*/', '', $newNode[0]); //remove any extra space before a string
        $eachLineArray = explode(PHP_EOL, $addressString);
        if (!empty($eachLineArray)) {
            $newData = isset($eachLineArray[1]) && $eachLineArray[1] !== '' ? $eachLineArray[1] : [];
            $a['street'] = (isset($eachLineArray[0]) && $eachLineArray[0] !== '' ? $eachLineArray[0] : '');
            $country = isset($eachLineArray[2]) && $eachLineArray[2] !== '' ? trim(preg_replace('/\s+/', ' ', $eachLineArray[2])) : '';
            $a['country'] = (in_array(ucfirst(strtolower($country)), $country_list)) ? $country : 'N/A';
            if (strpos($a['street'], ',')) {
                $newData = $a['street'];
                $a['street']='';
            }
            if (isset($newData) && !empty($newData)) {
                $others = explode(',', $newData);
                foreach ($others as $k => $o) {
                    $o = preg_replace('/^(?<=)\s*/', '', $o);
                    switch ($k) {
                        case 0:
                            $a['city'] = $o;
                            break;
                        case 1:
                            $a['state'] =  (in_array(ucwords(strtolower($o)), $states) || in_array(ucwords(strtolower($o)), $codes)  ) ? $o : 'N/A';;;
                            break;
                        case 2:
                            $a['postCode'] = $o;
                            break;
                    }

                }
            }
        }
        return $a;
    }

    public static function makeRequest($post) {

        $url = 'https://www.otaus.com.au/search/membersearchdistance';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($ch, CURLOPT_POST, 1);
        $result = (curl_exec($ch));
        curl_close($ch);
        $result = json_decode($result, true);
        return $result['mainlist'];
    }

    public static function getContacts($datum,$limit) {
        $newArray = phpQuery::newDocumentHTML();
      $datum = array_splice($datum, 0, $limit);
        $id='ids=';
        if(count($datum) > 45) $datum=array_chunk($datum,45);
        foreach ($datum as $k => $data) {
                foreach ($data as $key => $d) {
                    $id .= ($key == 0) ? $d : '&ids=' . $d;
                }
               
                $url = 'https://www.otaus.com.au/search/getcontacts?' . $id;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $result = (curl_exec($ch));
                curl_close($ch);
                $newArray->append($result);
            $id='ids=';
        }
       return $newArray;
    }

    public static function checkPracticeRepetition($array, $key, $val) {
        foreach ($array as $item)
            if (isset($item[$key]) && $item[$key] == $val)
                return true;
        return false;
    }
}