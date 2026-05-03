<?php

function calculatePoints($orderTotal) {
    return floor($orderTotal / 100000); // 100k = 1 điểm
}

function getRankBySpending($totalSpending) {
    if ($totalSpending >= 25000000) return 4;
    if ($totalSpending >= 10000000) return 3;
    if ($totalSpending >= 3000000) return 2;
    return 1;
}
?>