<?php

function time_ago($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60) {
        return $diff . ' seconds ago';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . ' minutes ago';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . ' hours ago';
    } elseif ($diff < 604800) {
        return floor($diff / 86400) . ' days ago';
    } elseif ($diff < 2592000) {
        return floor($diff / 604800) . ' weeks ago';
    } else {
        return floor($diff / 2592000) . ' months ago';
    }
} 