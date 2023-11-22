<?php
namespace App\MailSort;

class MailProcessor
{
    public function processFile($url)
    {
        $content = file_get_contents($url);
        // $content = mb_convert_encoding($content, 'UTF-8', $sourceEncoding);

        $words = preg_split("/\r\n|\r/", $content);
        foreach ($words as $word) {
            $this->processWord($word);
        }
    }

    private function processWord($word)
    {
        if (!empty($word)) {
            $wordLetters = preg_split('/[@]/', $word);
            $folderName = implode("_", preg_split('/\./', $wordLetters[1]));
            $folderPath = "library/{$folderName}";
            $filePath = "library/{$folderName}/words.txt";
            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0777, true);
            }
            file_put_contents($filePath, $word . PHP_EOL, FILE_APPEND);
        } else {
            echo "No match found for word: $word\n";
        }
    }

    public function handleWebUpload($file)
    {
        if (! empty($file)) {
            $content = file_get_contents($file);
            $content = mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content));

            $words = preg_split("/\r\n|\r/", $content);

            foreach ($words as $word) {
                $this->processWord($word);
            }
        }
    }
}