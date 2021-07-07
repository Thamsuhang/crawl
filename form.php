<?php
include('Helper.php');
include('phpQuery-onefile.php');
include('params.php');
if ($_POST !== '') {

    $thisArray = Helper::makeRequest($_POST['post']);
    $all = Helper::getContacts($thisArray, $_POST['count']);
}
$document = $all;
$newArray = [];

foreach (pq('div.content__row') as $k => $li) {
    $pq = pq($li);
    //for name and email//
    $name = $pq->find('div.title__tag')->text();
    $check= Helper::checkPracticeRepetition($newArray,'name',$name); //check repetition of practice name uncomment if necessary
    if($check == false) {
        $contactName = $pq->find('strong.name')->text();
        $string = $pq->find('.contact-heading')->siblings('a')->text();
        preg_match_all('/\d{8,10}/', $string, $phone);
        //    preg_match_all('/\w+(\.\w+)?@\w+(\.\w+)?\.\w+(\.\w+)?/', $string, $email,);
        $newArray[$k]['name'] = ($name !== '') ? $name : 'N/A';
        $newArray[$k]['contactName'] = ($contactName !== '') ? $contactName : 'N/A';
        $newArray[$k]['phone'] = (isset($phone[0][0]) && $phone[0][0] !== '') ? $phone[0][0] : 'N/A';
        //    $newArray[$k]['email'] = (isset($email[0][0]) && $email[0][0] !== '') ? $email[0][0] : 'N/A';
        //name and email end//

        //for address //
        $addressArray = Helper::addAddress($pq,$country_list);
        $newArray[$k]['street'] = (isset($addressArray['street']) && $addressArray['street'] !== '') ? $addressArray['street'] : 'N/A';
        $newArray[$k]['city'] = (isset($addressArray['city']) && $addressArray['city'] !== '') ? $addressArray['city'] : 'N/A';
        $newArray[$k]['state'] = (isset($addressArray['state']) && $addressArray['state'] !== '') ? $addressArray['state'] : 'N/A';
        $newArray[$k]['country'] = (isset($addressArray['country']) && $addressArray['country'] !== '') ? $addressArray['country'] : 'N/A';
        $newArray[$k]['postCode'] = (isset($addressArray['postCode']) && $addressArray['postCode'] !== '') ? $addressArray['postCode'] : 'N/A';
        //for address end//


        //for funding and area of practice//
        $lastCol = $pq->find('.content__col:last-child')->text();
        $fundingString = preg_replace('/\s+/', '', $lastCol);
        $words = preg_replace('/(?<!\ )Area\(s\)/', ' $0', $fundingString);
        $fundingAndArea = explode(' ', $words);
        $onlyFundingString = (isset($fundingAndArea[0]) && !empty($fundingAndArea[0])) ? explode('FundingScheme(s):', $fundingAndArea[0]) : '';
        $onlyAreaOfPractice = (isset($fundingAndArea[1]) && !empty($fundingAndArea[1])) ? explode('Area(s)ofPractice:', $fundingAndArea[1]) : '';
        $newArray[$k]['fundingScheme(s)'] = (isset($onlyFundingString[1]) && $onlyFundingString[1] !== '') ? $onlyFundingString[1] : 'N/A';
        $newArray[$k]['AreaOfPractice(s'] = (isset($onlyAreaOfPractice[1]) && $onlyAreaOfPractice[1] !== '') ? $onlyAreaOfPractice[1] : 'N/A';
        //funding and area of practice end//
    }
}

//save csv
Helper::CreateCsv($title, $newArray);








