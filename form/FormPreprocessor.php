<?php
namespace App\Form;
require_once("./FluidLineForm.php");

use App\Form\FluidLineForm;

class FormPreprocessor
{
    private array $preparedInput = [];
    private array $keysList = [];

    public function formInput(string $data): void
    {
        $rows = $this->breakRow($data);
        foreach ($rows as $key => $row) {
            if ($key === 0) {
                $this->keysList = $this->breakTab($row);
                continue;
            }

            $cells = $this->breakTab($row);
            foreach($cells as $key => $cell) {
                if ($key === 0) {
                    continue;
                }
                $this->preparedInput[$cells[0]][$this->keysList[$key]] = $cell;
            }
        }

        $processInput = new FluidLineForm();
        $processInput->processInput($this->preparedInput)   ;
    }

    private function breakRow(string $data): array
    {
        return preg_split("/\r\n|\n|\r/", $data);
    }

    private function breakTab(string $row): array
    {
        return preg_split("/\t/", $row);
    }
}