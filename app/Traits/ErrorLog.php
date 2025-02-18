<?php
/**
 * @author Ahmad Saeed
 */
namespace App\Traits;

trait ErrorLog
{
    /**
     * @param string $file
     * @param string $fileName
     * @param string $functionName
     * @param string $message
     * @return string
     */
    public function message(string $file, string $fileName, string $functionName, string $message): string
    {
        return "{$file} : {$fileName}  function : {$functionName}  errors : " . $message;
    }
}