<?php
$dataFile1 = file_get_contents('6.json');
$arr1 = json_decode($dataFile1, true);
$dataFile2 = file_get_contents('7.json');
$arr2 = json_decode($dataFile2, true);

function getArrPrepared($arr, $levelDipth = 1)
{
    $j = 0;
    foreach ($arr as $key => $value) {
        $result[$j] = ['key' => $key, 'diepth' => $levelDipth, 'status' => 'no', 'arg' => $value];
        if (is_array($value)) {
            $result[$j]['arg'] = getArrPrepared($value, $levelDipth + 1);
        }
        $j++;
    }
    return $result;
}

function getKey($arr)
{
    return $arr['key'];
}

function getDiepth($arr)
{
    return $arr['diepth'];
}

function getStatus($arr)
{
    return $arr['status'];
}

function getArg($arr)
{
    return $arr['arg'];

}

function makeArrWithStatus($arr1, $arr2, $mark = 'no')
{
    $arr2Simple = array_reduce($arr2, function ($accum, $value) {
        $keyArr2 = getKey($value);
        $argArr2 = getArg($value);
        $accum[$keyArr2] = $argArr2;
        return $accum;
    }, []);
    $arr2SimpleKeys = array_keys($arr2Simple);
    $diff = array_map(function ($child1) use ($arr2Simple, $arr2SimpleKeys, $mark) {
        $keyArr1 = getKey($child1);
        $argArr1 = getArg($child1);
        if (in_array($keyArr1, $arr2SimpleKeys)) {
            if ((is_array(getArg($child1)) and is_array($arr2Simple[$keyArr1])) or (getArg($child1) === $arr2Simple[$keyArr1])) {
                $child1['status'] = 'both';
            } else {
                $child1['status'] = $mark;
            }
        } elseif (!in_array($keyArr1, $arr2SimpleKeys)) {
            $child1['status'] = $mark;
        }
        if (in_array($keyArr1, $arr2SimpleKeys) and is_array($argArr1) and is_array($arr2Simple[$keyArr1])) {
            $child1['arg'] = makeArrWithStatus($argArr1, $arr2Simple[$keyArr1], $mark);
        }
        return $child1;
    }, $arr1);
    return $diff;
}


//print_r(getArrPrepared($arr1));
//print_r(makeArrWithStatus(getArrPrepared($arr2), getArrPrepared($arr1), '+'));
function makeDiff ($file1, $file2)
{
    $arr1 = makeArrWithStatus(getArrPrepared($file1), getArrPrepared($file2), '-');
    $arr2 = makeArrWithStatus(getArrPrepared($file2), getArrPrepared($file1), '+');
    $diff =  array_merge($arr1, $arr2);
    //asort($diff);
    return $diff;
}

function makeKeyForStylish ( $key, int $dieph, string  $status)
{
    if ($status === 'both' or $status === 'no')
    {
        $status = ' ';
    }
    $key1 = "  {$status} {$key}";
    if ($dieph === 1)
    {
        return $key1;
    } else
    {
        $keyN = str_repeat('    ', ($dieph - 1));
        return "{$keyN}{$key1}";
    }
}

function makeArrForStylish ($arr)
{ //var_dump($arr);die();
    $result = array_reduce($arr, function ($accum, $value){
        $newKey = makeKeyForStylish(getKey($value), getDiepth($value), getStatus($value)); //var_dump($value); die();
        if(array_key_exists($newKey, $accum))
        { //var_dump(getArg($value)); die();
            $accum[$newKey] = array_merge($accum[$newKey], getArg($value)); //var_dump($accum);echo "\n".'!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!';
        }

        $accum[$newKey] = getArg($value); //var_dump($accum); echo "\n".'!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!';

        return $accum;
    }, []);
    return $result;
}

makeArrForStylish((makeDiff($arr1, $arr2)));