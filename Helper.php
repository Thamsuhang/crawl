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
        self::downloadFile();
    }

    public static function downloadFile() {
        $file = ("data.csv");
        $filetype = filetype($file);
        $filename = basename($file);
        header("Content-Type: " . $filetype);
        header("Content-Length: " . filesize($file));
        header("Content-Disposition: attachment; filename=" . $filename);
        readfile($file);
        header("Refresh:0");
    }

    public static function addAddress($pq, $country_list) {
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
                            $a['state'] = $o;
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

    public static function getContacts($data,$limit) {
        $newArray = phpQuery::newDocumentHTML();
        $data = array_splice($data, 0, $limit);
        foreach ($data as $k => $d) {
            $url = 'https://www.otaus.com.au/search/getcontacts?ids=' . $d;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = (curl_exec($ch));
            curl_close($ch);
            $newArray->append($result);
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