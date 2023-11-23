<?php
namespace App\MailSort;

class MailProcessor
{
    private array $unifyDomens = [];
    private array $unifyMails = [];

    public function processFile($url)
    {
        $content = file_get_contents($url);
        // $content = mb_convert_encoding($content, 'UTF-8', $sourceEncoding);

        $words = preg_split("/\r\n|\r/", $content);
        foreach ($words as $word) {
            
            $domen = $this->processWord($word);
            if (!in_array($domen, $this->unifyDomens)) {
                $this->unifyDomens[] = $domen;
                $this->unifyMails[] = $word;

            }
        }
        $this->unifyMailList($this->unifyMails);
    }

    private function processWord(string $word)
    {
        if (!empty($word)) {
            $wordLetters = preg_split('/[@]/', $word);
            $folderName = implode("_", preg_split('/\./', $wordLetters[1]));
            $folderPath = "library/{$folderName}";
            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0777, true);
            }
            $filePath = fopen("library/{$folderName}/domens.csv", "a+");
            
            fputcsv($filePath, [iconv("UTF-8", "Windows-1251", $word)]);
            fclose($filePath);
            

        } else {
            echo "No match found for word: $word\n";
        }

        return $wordLetters[1];
    }

    public function handleWebUpload($file)
    {
        if (! empty($file)) {
            $content = file_get_contents($file);
            $words = preg_split("/\r\n|\r/", $content);

            foreach ($words as $word) {
                $domen = $this->processWord($word);
                if (!in_array($domen, $this->unifyDomens)) {
                    $this->unifyDomens[] = $domen;
                    $this->unifyMails[] = $word;
                }
            }
            $this->unifyMailList($this->unifyMails);
        }
    }

    private function unifyMailList(array $list): void
    {
        foreach ($list as $item) {
            $folderPath = "library/!unify";
            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0777, true);
            }
            $filePath = fopen("library/!unify/domens.csv", "a+");
            fputcsv($filePath, [iconv("UTF-8", "Windows-1251", $item)]);
            fclose($filePath);
        }
    }
}
