<?php

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;


function cleanDescription($content, $limit = null)
{
    $text = strip_tags($content);

    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

    $text = preg_replace('/\s+/', ' ', $text);

    $text = trim($text);

    if ($limit === null) {
        return $text;
    }

    return Str::limit($text, $limit);
}

function generateRandomOTP($length = 6)
{
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= rand(0, 9);
    }
    return $otp;
}

function extractTableOfContents($content)
{
    $toc = [];
    
    preg_match_all('/<h([1-6])[^>]*>(.*?)<\/h[1-6]>/i', $content, $matches, PREG_SET_ORDER);
    
    foreach ($matches as $match) {
        $level = $match[1];
        $text = strip_tags($match[2]);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        
        // Normalize various dash characters to regular hyphen
        $text = str_replace(['–', '—', '−'], '-', $text);
        
        $slug = Str::slug($text);
        
        $toc[] = [
            'level' => $level,
            'text' => $text,
            'slug' => $slug,
        ];
    }
    
    return $toc;
}

function addIdsToHeadings($content)
{
    $content = preg_replace_callback(
        '/<h([1-6])([^>]*)>(.*?)<\/h[1-6]>/i',
        function ($matches) {
            $level = $matches[1];
            $attributes = $matches[2];
            $text = $matches[3];
            
            $cleanText = strip_tags($text);
            $cleanText = html_entity_decode($cleanText, ENT_QUOTES, 'UTF-8');
            
            // Normalize various dash characters to regular hyphen
            $cleanText = str_replace(['–', '—', '−'], '-', $cleanText);
            
            $slug = Str::slug($cleanText);
            
            if (strpos($attributes, 'id=') === false) {
                $attributes .= ' id="' . $slug . '"';
            }
            
            return "<h{$level}{$attributes}>{$text}</h{$level}>";
        },
        $content
    );
    
    return $content;
}
