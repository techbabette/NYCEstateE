<?php
function getPageVisits($timeLimit, $convertToPercentage, $sortType){
    $lines = file("../../data/activity.txt", FILE_IGNORE_NEW_LINES);

    $resultArray = array();
    $totalNumOfVisits = 0;

    if(!$timeLimit)
    foreach($lines as $line){
        $totalNumOfVisits++;

        $data = explode("::", $line);
        $page = $data[1];

        if(array_key_exists($page, $resultArray)){
            $resultArray[$page]++;
        }
        else{
            $resultArray[$page] = 1;
        }
    }
    else{
        $currTime = time();
        for($i = count($lines) - 1; $i >= 0; $i--){
            $line = $lines[$i];
    
            $data = explode("::", $line);
            $page = $data[1];
            $timeOfVisit = (int)$data[2];

            if($currTime - 86400 > $timeOfVisit) break;

            $totalNumOfVisits++;

            if(array_key_exists($page, $resultArray)){
                $resultArray[$page]++;
            }
            else{
                $resultArray[$page] = 1;
            }
        }
    
    }

    $numberToDivideBy = $convertToPercentage ? $totalNumOfVisits / 100 : 1;

    $resultArray = array_map(function($elem, $key) use ($numberToDivideBy){
        $newElem["name"] = $key;
        $newElem["number"] = round($elem / $numberToDivideBy, 2);
        return $newElem;
    }, $resultArray, array_keys($resultArray));

    if($sortType == 0)
    usort($resultArray, function($b, $a) {
        return $a['number'] <=> $b['number'];
    });

    if($sortType == 1)
    usort($resultArray, function($a, $b) {
        return $a['number'] <=> $b['number'];
    });

    if($sortType == 2)
    usort($resultArray, function($b, $a) {
        return $a['name'] <=> $b['name'];
    });

    if($sortType == 3)
    usort($resultArray, function($a, $b) {
        return $a['name'] <=> $b['name'];
    });

    return $resultArray;
}
function getLogins(){
    $lines = file("../../data/successfulLogins.txt", FILE_IGNORE_NEW_LINES);

    $arrayOfUsers = array();

    $currTime = time();
    $numberOfLogins = 0;

    for($i = count($lines) - 1; $i >= 0; $i--){
        $line = $lines[$i];
        $data = explode("::", $line);

        $user = $data[0];
        $timeOfVisit = $data[1];

        if($currTime - 86400 > $timeOfVisit) break;

        if(in_array($user, $arrayOfUsers)) continue;

        array_push($arrayOfUsers, $user);
        $numberOfLogins++;
    }
    $returnArray["name"] = "Number of logins in the last 24h";
    $returnArray["number"] = $numberOfLogins;
    $returnArray = array($returnArray);
    return $returnArray;
}
?>