<?php
namespace App\Services\Utility;

class CDNService {

        /**
        The name of the storage zone we are working on
    */
    private $storageZoneName = "";

    /**
        The API access key used for authentication
    */
    private $apiAccessKey = "";

    /**
        The storage zone region code
    */
    private $storageZoneRegion = "de";


    public function __construct()
    {
        $this->apiAccessKey = env("CDN_KEY");
        $this->storageZoneName = env("CDN_STORAGE");
    }
    /*
        Returns the base URL with the endpoint based on the current storage zone region
    */
    private function getBaseUrl()
    {
        if($this->storageZoneRegion == "de" || $this->storageZoneRegion == "")
        {
            return "https://storage.bunnycdn.com/";
        }
        else
        {
            return "https://{$this->storageZoneRegion}.storage.bunnycdn.com/";
        }
    }

    /**
        Get the list of storage objects on the given path
    */
    public function getStorageObjects($path)
    {
        $normalizedPath = $this->normalizePath($path, true);
        return $this->sendHttpRequest($normalizedPath);
    }

    /**
        Delete an object at the given path. If the object is a directory, the contents will also be deleted.
    */
    public function deleteObject($path)
    {
        $normalizedPath = $this->normalizePath($path);
        return $this->sendHttpRequest($normalizedPath, "DELETE");
    }

    /**
        Upload a local file to the storage
    */
    public function uploadFile($localPath, $path)
    {
        // Open the local file
        $fileStream = fopen($localPath, "r");
        if($fileStream == false)
        {
            return false;
        }
        $dataLength = filesize($localPath);
        $normalizedPath = $this->normalizePath($path);
        return $this->sendHttpRequest($normalizedPath, "PUT", $fileStream, $dataLength);
    }

    /**
        Download the object to a local file
    */
    public function downloadFile($path, $localPath)
    {
        // Open the local file
        $fileStream = fopen($localPath, "w+");
        if($fileStream == false)
        {
             return false;
        }

        $dataLength = filesize($localPath);
        $normalizedPath = $this->normalizePath($path);
        return $this->sendHttpRequest($normalizedPath, "GET", NULL, NULL, $fileStream);
    }

    private function sendHttpRequest($url, $method = "GET", $uploadFile = NULL, $uploadFileSize = NULL, $downloadFileHandler = NULL)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getBaseUrl() . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_FAILONERROR, 0);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "AccessKey: {$this->apiAccessKey}",
        ));
        if($method == "PUT" && $uploadFile != NULL)
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_UPLOAD, 1);
            curl_setopt($ch, CURLOPT_INFILE, $uploadFile);
            curl_setopt($ch, CURLOPT_INFILESIZE, $uploadFileSize);
        }
        else if($method != "GET")
        {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }


        if($method == "GET" && $downloadFileHandler != NULL)
        {
            curl_setopt($ch, CURLOPT_FILE, $downloadFileHandler);
        }


        $output = curl_exec($ch);
        $curlError = curl_errno($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($curlError)
        {
            return [
                "error" => 1,
                "body" => "Curl Error Number: " . $curlError
            ];
        }

        if($responseCode < 200 || $responseCode > 299)
        {
            return [
                "error" => 1,
                "body" => "Response code: " . $responseCode
            ];
        }

        return [
            "error" => 0,
            "body" => $output
        ];
    }

    /**
        Normalize a path string
    */
    private function normalizePath($path, $isDirectory = NULL)
    {
        $path = $this->storageZoneName . $path;

        $path = str_replace('\\', '/', $path);
        if ($isDirectory != NULL)
        {
            if ($isDirectory)
            {
                if (!$this->endsWith($path, '/'))
                {
                    $path = $path . "/";
                }
            }
            else
            {
                if ($this->endsWith($path, '/') && $path != '/')
                {
                    die('The requested path is invalid.');
                }
            }
        }

        // Remove double slashes
        while (strpos($path, '//') !== false) {
            $path = str_replace('//', '/', $path);
        }

        // Remove the starting slash
        if (substr($path, 0, 1) === '/')
        {
            $path = substr($path, 1);
        }
        return $path;
    }

    private function startsWith($haystack, $needle)
    {
         $length = strlen($needle);
         return (substr($haystack, 0, $length) === $needle);
    }

    private function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

}
?>
