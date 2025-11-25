<?php
function slugify(string $text): string {
    $text = trim($text);
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/','-', $text);
    $text = preg_replace('/-+/','-', $text);
    return trim($text,'-');
}
