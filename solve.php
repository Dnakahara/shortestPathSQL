<?php
error_reporting(0);

define('V_NUM', 5000);
define('START_V', 1);
define('GOAL_V', V_NUM);

$Vs = array_fill(1, V_NUM, null);

function dfs($tmpVs, $tmpEs){

    // 頂点START_VからSTART_Vにはコスト0でいけるのでそれを記録し、$stackに入れる
    $tmpVs[START_V] = 0;
    $stack = new SplStack();
    $stack[] = array(0, START_V);

    // 調べるべき状態がもう無い($stackが空)なら探索終了
    while(!$stack->isEmpty()){
        // $nowVにたどり着くのに$nowTotalだけかかった状態から探索を進める
        $state = $stack->pop();
        $nowTotal = $state[0];
        $nowV = $state[1];

        // もうすでに$nowVに$nowTotalよりも小さいコストでたどり着けることが
        // わかっている場合は、この状態からの探索をやめる
        if(isset($tmpVs[$nowV]) && $tmpVs[$nowV] < $nowTotal) continue;
        
        // $nowVから出ている辺を全部みる
        foreach($tmpEs[$nowV] as $edge){
            $to = $edge[0];
            $cost = $edge[1];

            // 辺の先にある頂点にこれまでにたどり着いたことがない、
            // もしくはこれまでよりも小さいコストでたどり着けることがわかったら更新
            if(!isset($tmpVs[$to]) || $tmpVs[$to] > $tmpVs[$nowV] + $cost){
                $tmpVs[$to] = $tmpVs[$nowV] + $cost;

                // 探索を進めるべき状態として$stackに入れる
                $stack[]= array($tmpVs[$to], $to);
            }
        }
    }
    return $tmpVs[GOAL_V];
}

function bfs($tmpVs, $tmpEs){

    // 頂点START_Vから頂点START_Vにはコスト0でいけるのでそれを記録し、$queueに入れる
    $tmpVs[START_V] = 0;
    $queue = new SplQueue();
    $queue[] = array(0, START_V);

    // 調べるべき状態がもう無い($queueが空)なら探索終了
    while(!$queue->isEmpty()){

        // $nowVにたどり着くのに$nowTotalだけかかった状態から探索を進める
        $state = $queue->dequeue();
        $nowTotal = $state[0];
        $nowV = $state[1];

        // もうすでに$nowVに$nowTotalよりも小さいコストでたどり着けることが
        // わかっている場合は、この状態からの探索をやめる
        if(isset($tmpVs[$nowV]) && $tmpVs[$nowV] < $nowTotal) continue;

        // $nowVから出ている辺を全部みる
        foreach($tmpEs[$nowV] as $edge){
            $to = $edge[0];
            $cost = $edge[1];

            // 辺の先にある頂点にこれまでにたどり着いたことがない、
            // もしくはこれまでよりも小さいコストでたどり着けることがわかったら更新
            if(!isset($tmpVs[$to]) || $tmpVs[$to] > $tmpVs[$nowV] + $cost){
                $tmpVs[$to] = $tmpVs[$nowV] + $cost;

                // 探索を進めるべき状態として$queueに入れる
                $queue[]= array($tmpVs[$to], $to);
            }
        }
    }
    return $tmpVs[GOAL_V];
}

function dijkstra($tmpVs, $tmpEs){
    // 頂点START_Vから頂点START_Vにはコスト0でいけるのでそれを記録し、heapに入れる
    $tmpVs[START_V] = 0;
    $heap = new SplMinHeap();
    $heap->insert(array(0, START_V));

    // 調べるべき状態がもう無い(heapが空)なら探索終了
    while(!$heap->isEmpty()){

        // $nowVにたどり着くのに$nowTotalだけかかった状態から探索を進める
        $state = $heap->extract();
        $nowTotal = $state[0];
        $nowV = $state[1];

        // もうすでに$nowVに$nowTotalよりも小さいコストでたどり着けることが
        // わかっている場合は、この状態からの探索をやめる
        if(!isset($tmpVs[$nowV]) && $tmpVs[$nowV] < $nowTotal) continue;

        // $nowVから出ている辺を全部みる
        foreach($tmpEs[$nowV] as $edge){
            $to = $edge[0];
            $cost = $edge[1];

            // 辺の先にある頂点にこれまでにたどり着いたことがない、
            // もしくはこれまでよりも小さいコストでたどり着けることがわかったら更新
            if(!isset($tmpVs[$to]) || $tmpVs[$to] > $tmpVs[$nowV] + $cost){
                $tmpVs[$to] = $tmpVs[$nowV] + $cost;

                // 探索を進めるべき状態として$heapに入れる
                $heap->insert(array($tmpVs[$to], $to));
            }
        }
    }
    return $tmpVs[GOAL_V];
}

$csv = new SplFileObject(__DIR__ . '/GraphE.csv', 'r');
$csv->setFlags(SplFileObject::READ_CSV);
$cnt = 0;

foreach ($csv as $row) {
    if ($row === [null]) continue; // 最終行の処理
    $edge = array((int)$row[1], (int)$row[2]);
    $Edges[(int)$row[0]][] = $edge;
}

$tmpVs = $Vs;
$tmpEs = $Edges;

$startT = microtime(true);
$ret = dfs($tmpVs, $tmpEs);
$endT = microtime(true);
echo "DFS   result:" . $ret . " time:" . ($endT - $startT) . "秒" . PHP_EOL;


$tmpVs = $Vs;
$tmpEs = $Edges;

$startT = microtime(true);
$ret = bfs($tmpVs, $tmpEs);
$endT = microtime(true);
echo "BFS   result:" . $ret . " time:" . ($endT - $startT) . "秒" . PHP_EOL;


$tmpVs = $Vs;
$tmpEs = $Edges;

$startT = microtime(true);
$ret = dijkstra($tmpVs, $tmpEs);
$endT = microtime(true);
echo "Dijkstra   result:" . $ret . " time:" . ($endT - $startT) . "秒". PHP_EOL;
