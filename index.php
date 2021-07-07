<?php


include('phpQuery-onefile.php');
include('Helper.php');
include('params.php');
$newArray = [];
//for calling curl to fetch data
function CurlInit() {
    $url = 'https://www.otaus.com.au/find-an-ot';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = (curl_exec($ch));
    curl_close($ch);
    return $result;
}

//end of curl
$result = CurlInit();

//calling curl function
$document = phpQuery::newDocument($result); //creating new phpquery
$selectArea = pq('#memberSearch_AreaOfPracticeId option');
$selectFunding = pq('#memberSearch_FundingSchemeId option');
$areaOfPracticeIds = [];
$fundingIds = [];

function makeSelectArray($data) {
    $type = [];
    foreach ($data as $k => $val) {
        $val = pq($val);
        $type[$k]['id'] = $val->val();
        $type[$k]['title'] = $val->text();
    }
    return $type;
}

$areaOfPracticeIds = makeSelectArray($selectArea);
array_shift($areaOfPracticeIds); //removes the top empty option
$fundingIds = makeSelectArray($selectFunding);
array_shift($fundingIds); //removes the top empty option
?>
<html lang = "en">
<head>
   <meta charset = "UTF-8">
   <meta name = "viewport" content = "width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
   <meta http-equiv = "X-UA-Compatible" content = "ie=edge">
   <title>Crawl</title>
   <link rel = "stylesheet" href = "https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
   <style>
       body{
           background: lightgrey;
       }
       #container {
           display: flex;
          justify-content: center;
           align-items: center;
           height: 100vh;
           padding: 50px;
       }

       .btn-primary {
           color: #fff;
           background-color: #007bff;
           border-color: #007bff;
           padding: 10px;
           border-radius: 5px;
           cursor: pointer;
       }
       /* //loader using pure css loader */
       .lds-spinner div {
           transform-origin: 40px 40px;
           animation: lds-spinner 1.2s linear infinite;
       }
       .lds-spinner div:after {
           content: " ";
           display: block;
           position: absolute;
           top: 3px;
           left: 37px;
           width: 6px;
           height: 18px;
           border-radius: 20%;
           background: #fff;
       }
       .lds-spinner div:nth-child(1) {
           transform: rotate(0deg);
           animation-delay: -1.1s;
       }
       .lds-spinner div:nth-child(2) {
           transform: rotate(30deg);
           animation-delay: -1s;
       }
       .lds-spinner div:nth-child(3) {
           transform: rotate(60deg);
           animation-delay: -0.9s;
       }
       .lds-spinner div:nth-child(4) {
           transform: rotate(90deg);
           animation-delay: -0.8s;
       }
       .lds-spinner div:nth-child(5) {
           transform: rotate(120deg);
           animation-delay: -0.7s;
       }
       .lds-spinner div:nth-child(6) {
           transform: rotate(150deg);
           animation-delay: -0.6s;
       }
       .lds-spinner div:nth-child(7) {
           transform: rotate(180deg);
           animation-delay: -0.5s;
       }
       .lds-spinner div:nth-child(8) {
           transform: rotate(210deg);
           animation-delay: -0.4s;
       }
       .lds-spinner div:nth-child(9) {
           transform: rotate(240deg);
           animation-delay: -0.3s;
       }
       .lds-spinner div:nth-child(10) {
           transform: rotate(270deg);
           animation-delay: -0.2s;
       }
       .lds-spinner div:nth-child(11) {
           transform: rotate(300deg);
           animation-delay: -0.1s;
       }
       .lds-spinner div:nth-child(12) {
           transform: rotate(330deg);
           animation-delay: 0s;
       }
       @keyframes lds-spinner {
           0% {
               opacity: 1;
           }
           100% {
               opacity: 0;
           }
       }
       .loader{
           position:absolute;
           left:45%;
           top:40%;
       }

   </style>
</head>
<body>
<div id = "container">
   <div class="row">
      <div class="col-12">
         <h3 class = "text-center">Crawl occupational Therapy Australia</h3>
      </div>
      <div class="col-12">
         <form method = "post" action = "form.php" id="form">
            <input type = "text" value = "2" name = "post[ServiceType]" hidden>
            <input type = "text" value = "state" name = "post[State]" hidden>
            <input type = "text" value = "" name = "post[Name]" hidden>
            <input type = "text" value = "" name = "post[PracticeName]" hidden>
            <input type = "text" value = "" name = "post[Location]" hidden>
            <input type = "number" value = "0" name = "post[Distance]" hidden>

            <div class = "form-group">
               <label for = "AreaOfPracticeId">Area Of Practice</label>
               <select name = "post[AreaOfPracticeId]" class = "form-control" id = "AreaOfPracticeId" required>
                  <option value = ""> Select One</option>
                   <?php foreach ($areaOfPracticeIds as $k => $i): ?>
                      <option value = "<?= isset($i['id']) && $i['id'] !== '' ? $i['id'] : '' ?>"><?= isset($i['title']) && $i['title'] !== '' ? $i['title'] : '' ?></option>
                   <?php endforeach; ?>
               </select>
            </div>
            <div class = "form-group">
               <label for = "FundingSchemeId">Funding Scheme</label>
               <select name = "post[FundingSchemeId]" class = "form-control" id = "FundingSchemeId">
                  <option value = ""> Select One</option>
                   <?php foreach ($fundingIds as $k => $i): ?>
                      <option value = "<?= isset($i['id']) && $i['id'] !== '' ? $i['id'] : '' ?>"><?= isset($i['title']) && $i['title'] !== '' ? $i['title'] : '' ?></option>
                   <?php endforeach; ?>
               </select>
            </div>
            <div class = "form-group">
               <label for = "count" class = "control-label">How Many data do you wnat?</label>
               <select name = "count" id = "count" class = "form-control">
                  <option value = "45">50</option>
                  <option value = "100">100</option>
                  <option value = "150">150</option>
                  <option value = "200">200</option>
                  <option value = "300">300</option>
                  <option value = "500">500+</option>
               </select>
            </div>
            <div class = "form-group text-center">
               <input type = "submit" class = "btn btn-primary" value = "crawl">
            </div>
         </form>
      </div>
   </div>

</div>
<div class="loader d-none">
   <div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>

</div>
<script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src = "https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.js"></script>
<script>
   $('.btn-primary').on('click', function () {
      if($('#form').valid())
      {
         $('.loader').removeClass('d-none');
      }
   });
</script>
</body>
</html>



