<?php

namespace App\Helpers;

class VietnameseHelper
{
    /**
     * Bỏ dấu tiếng Việt
     */
    public static function removeVietnameseAccents(string $text): string
    {
        $accents = [
            'à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a',
            'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a',
            'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a',
            'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e',
            'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e',
            'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o',
            'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o',
            'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u',
            'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u',
            'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y',
            'đ' => 'd',
            'À' => 'A', 'Á' => 'A', 'Ạ' => 'A', 'Ả' => 'A', 'Ã' => 'A',
            'Â' => 'A', 'Ầ' => 'A', 'Ấ' => 'A', 'Ậ' => 'A', 'Ẩ' => 'A', 'Ẫ' => 'A',
            'Ă' => 'A', 'Ằ' => 'A', 'Ắ' => 'A', 'Ặ' => 'A', 'Ẳ' => 'A', 'Ẵ' => 'A',
            'È' => 'E', 'É' => 'E', 'Ẹ' => 'E', 'Ẻ' => 'E', 'Ẽ' => 'E',
            'Ê' => 'E', 'Ề' => 'E', 'Ế' => 'E', 'Ệ' => 'E', 'Ể' => 'E', 'Ễ' => 'E',
            'Ì' => 'I', 'Í' => 'I', 'Ị' => 'I', 'Ỉ' => 'I', 'Ĩ' => 'I',
            'Ò' => 'O', 'Ó' => 'O', 'Ọ' => 'O', 'Ỏ' => 'O', 'Õ' => 'O',
            'Ô' => 'O', 'Ồ' => 'O', 'Ố' => 'O', 'Ộ' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O',
            'Ơ' => 'O', 'Ờ' => 'O', 'Ớ' => 'O', 'Ợ' => 'O', 'Ở' => 'O', 'Ỡ' => 'O',
            'Ù' => 'U', 'Ú' => 'U', 'Ụ' => 'U', 'Ủ' => 'U', 'Ũ' => 'U',
            'Ư' => 'U', 'Ừ' => 'U', 'Ứ' => 'U', 'Ự' => 'U', 'Ử' => 'U', 'Ữ' => 'U',
            'Ỳ' => 'Y', 'Ý' => 'Y', 'Ỵ' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y',
            'Đ' => 'D',
        ];

        return strtr($text, $accents);
    }

    /**
     * Normalize query: bỏ dấu và lowercase
     */
    public static function normalizeQuery(string $query): string
    {
        $query = trim($query);
        $query = self::removeVietnameseAccents($query);
        return mb_strtolower($query, 'UTF-8');
    }

    /**
     * Tạo search terms từ query (bao gồm cả có dấu và không dấu)
     */
    public static function getSearchTerms(string $query): array
    {
        $terms = [];
        $query = trim($query);
        
        // Thêm query gốc
        $terms[] = $query;
        
        // Thêm query không dấu
        $noAccent = self::removeVietnameseAccents($query);
        if ($noAccent !== $query) {
            $terms[] = $noAccent;
        }
        
        // Thêm query lowercase
        $lowercase = mb_strtolower($query, 'UTF-8');
        if ($lowercase !== $query) {
            $terms[] = $lowercase;
        }
        
        // Thêm query không dấu và lowercase
        $normalized = self::normalizeQuery($query);
        if ($normalized !== $query && !in_array($normalized, $terms)) {
            $terms[] = $normalized;
        }
        
        return array_unique($terms);
    }

    public static function createFuzzyPatterns(string $query): array
    {
        $patterns = [];
        $query = trim($query);
        $queryLength = mb_strlen($query);
        
        if ($queryLength < 3) {
            return [$query];
        }
        
        $normalized = self::normalizeQuery($query);
        
        $patterns[] = $query;
        if ($normalized !== $query) {
            $patterns[] = $normalized;
        }
        
        $deduplicated = preg_replace('/(.)\1{2,}/', '$1$1', $normalized);
        if ($deduplicated !== $normalized && mb_strlen($deduplicated) >= max(3, $queryLength * 0.7)) {
            $patterns[] = $deduplicated;
        }
        
        if ($queryLength >= 4) {
            $commonTypos = [
                'f' => ['ph'],
                'ph' => ['f'],
                'd' => ['đ'],
                'đ' => ['d'],
            ];
            
            foreach ($commonTypos as $char => $replacements) {
                if (strpos($normalized, $char) !== false) {
                    foreach ($replacements as $replacement) {
                        $variant = str_replace($char, $replacement, $normalized);
                        if ($variant !== $normalized && mb_strlen($variant) >= max(3, $queryLength * 0.7)) {
                            $patterns[] = $variant;
                        }
                    }
                }
            }
        }
        
        return array_unique($patterns);
    }
    
    public static function isRelevantPattern(string $pattern, string $originalQuery): bool
    {
        $patternLength = mb_strlen($pattern);
        $queryLength = mb_strlen($originalQuery);
        
        if ($patternLength < 3 || $queryLength < 3) {
            return false;
        }
        
        $lengthRatio = $patternLength / max($queryLength, 1);
        if ($lengthRatio < 0.6 || $lengthRatio > 1.4) {
            return false;
        }
        
        $normalizedPattern = self::normalizeQuery($pattern);
        $normalizedQuery = self::normalizeQuery($originalQuery);
        
        if ($normalizedPattern === $normalizedQuery) {
            return true;
        }
        
        $queryWords = explode(' ', trim($normalizedQuery));
        $patternWords = explode(' ', trim($normalizedPattern));
        
        $matchedWords = 0;
        foreach ($queryWords as $queryWord) {
            if (mb_strlen($queryWord) >= 2) {
                foreach ($patternWords as $patternWord) {
                    if (mb_strlen($patternWord) >= 2) {
                        if (strpos($patternWord, $queryWord) !== false || strpos($queryWord, $patternWord) !== false) {
                            $matchedWords++;
                            break;
                        }
                    }
                }
            }
        }
        
        if (count($queryWords) > 0 && $matchedWords / count($queryWords) >= 0.5) {
            return true;
        }
        
        $commonChars = 0;
        $queryChars = str_split($normalizedQuery);
        foreach ($queryChars as $char) {
            if (strpos($normalizedPattern, $char) !== false) {
                $commonChars++;
            }
        }
        
        $similarity = $commonChars / max(count($queryChars), 1);
        return $similarity >= 0.8;
    }
}

