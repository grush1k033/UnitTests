<?php

namespace App;

/**
 * Класс для статистического анализа текста
 */
class TextStatistics
{
    /**
     * Подсчитывает количество слов в тексте
     *
     * @param string $text Входной текст
     * @return int Количество слов
     */
    public function countWords(string $text): int
    {
        // Убираем лишние пробелы
        $text = trim($text);

        // Если текст пустой - возвращаем 0
        if (empty($text)) {
            return 0;
        }

        // Заменяем все whitespace символы на одиночные пробелы
        $text = preg_replace('/\s+/u', ' ', $text);

        // Разделяем текст на слова по пробелам
        $words = explode(' ', $text);

        // Фильтруем пустые элементы и элементы, состоящие только из пунктуации
        $words = array_filter($words, function($word) {
            $cleanWord = preg_replace('/[^\p{L}\p{N}]/u', '', $word);
            return !empty($cleanWord);
        });

        return count($words);
    }

    /**
     * Подсчитывает количество предложений в тексте
     *
     * @param string $text Входной текст
     * @return int Количество предложений
     */
    public function countSentences(string $text): int
    {
        // Убираем лишние пробелы
        $text = trim($text);

        // Если текст пустой - возвращаем 0
        if (empty($text)) {
            return 0;
        }

        // Заменяем множественные пробелы на одиночные
        $text = preg_replace('/\s+/u', ' ', $text);

        // Временная замена сокращений, чтобы они не считались концом предложения
        $text = preg_replace('/(?:Dr|Mr|Mrs|Ms|Prof|Gen|Col|Maj|Capt|Lt|Sgt|Cpl|Pvt|Rep|Sen|Gov|Pres|Ph\.D|U\.S|U\.K|U\.S\.A|etc|vs|ie|eg|cf)\./iu', '$0<ABBR>', $text);

        // Разделяем текст на предложения по знакам препинания
        // Учитываем . ! ? ... но игнорируем точки в сокращениях
        $pattern = '/(?<=[.!?]|\.{3})(?:\s+|$)(?![^<]*>)/u';

        $sentences = preg_split($pattern, $text, -1, PREG_SPLIT_NO_EMPTY);

        // Восстанавливаем оригинальные сокращения
        $sentences = array_map(function($sentence) {
            return str_replace('<ABBR>', '', $sentence);
        }, $sentences);

        // Фильтруем пустые предложения
        $sentences = array_filter($sentences, function($sentence) {
            $cleanSentence = preg_replace('/[^\p{L}\p{N}]/u', '', $sentence);
            return !empty(trim($sentence)) && !empty($cleanSentence);
        });

        return count($sentences);
    }

    /**
     * Вычисляет среднюю длину слова в тексте
     *
     * @param string $text Входной текст
     * @return float Средняя длина слова
     */
    public function getAverageWordLength(string $text): float
    {
        // Получаем слова из текста
        $words = $this->extractWords($text);

        // Если слов нет - возвращаем 0
        if (count($words) === 0) {
            return 0.0;
        }

        // Вычисляем общую длину всех слов
        $totalLength = 0;
        foreach ($words as $word) {
            // Убираем пунктуацию вокруг слова для более точного подсчета
            $cleanWord = preg_replace('/[^\p{L}\p{N}]/u', '', $word);
            $totalLength += mb_strlen($cleanWord, 'UTF-8');
        }

        // Вычисляем среднее значение
        return round($totalLength / count($words), 2);
    }

    /**
     * Извлекает слова из текста
     *
     * @param string $text Входной текст
     * @return array Массив слов
     */
    private function extractWords(string $text): array
    {
        $text = trim($text);
        if (empty($text)) {
            return [];
        }

        // Заменяем все whitespace символы на одиночные пробелы
        $text = preg_replace('/\s+/u', ' ', $text);

        // Разделяем на слова
        $words = explode(' ', $text);

        // Фильтруем пустые элементы и элементы, состоящие только из пунктуации
        return array_filter($words, function($word) {
            $cleanWord = preg_replace('/[^\p{L}\p{N}]/u', '', $word);
            return !empty($cleanWord);
        });
    }
}