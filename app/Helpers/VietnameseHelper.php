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

    /**
     * Tạo fuzzy search patterns để xử lý sai chính tả
     * Ví dụ: "meof" -> ["meo", "mèo", "meoo", "meooo"]
     */
    public static function createFuzzyPatterns(string $query): array
    {
        $patterns = [];
        $query = trim($query);
        $normalized = self::normalizeQuery($query);
        
        // Thêm query gốc
        $patterns[] = $query;
        $patterns[] = $normalized;
        
        // Xử lý các ký tự lặp lại (ví dụ: meooo -> meo)
        $deduplicated = preg_replace('/(.)\1{2,}/', '$1$1', $normalized);
        if ($deduplicated !== $normalized) {
            $patterns[] = $deduplicated;
        }
        
        // Xử lý các ký tự thường gặp khi gõ sai
        $commonTypos = [
            'f' => ['ph', 'p'],
            'ph' => ['f', 'p'],
            'c' => ['k', 'q'],
            'k' => ['c', 'q'],
            'q' => ['c', 'k'],
            'd' => ['đ'],
            'đ' => ['d'],
            'z' => ['s'],
            's' => ['z', 'x'],
            'x' => ['s'],
        ];
        
        // Tạo các biến thể với typo phổ biến
        foreach ($commonTypos as $char => $replacements) {
            if (strpos($normalized, $char) !== false) {
                foreach ($replacements as $replacement) {
                    $variant = str_replace($char, $replacement, $normalized);
                    if ($variant !== $normalized) {
                        $patterns[] = $variant;
                    }
                }
            }
        }
        
        return array_unique($patterns);
    }
}

