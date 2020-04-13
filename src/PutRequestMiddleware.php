<?php

namespace Djunehor\PutHelper;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class PutRequestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */

    public function handle(Request $request, Closure $next)
    {
        // to indicate put middleware was called
        session()->put(self::class, true);
        if ($request->isMethod('PUT')) {
            // to indicate that action is performed for PUT request
            session()->put(self::class, session()->token());
            $string = file_get_contents('php://input');
            $array = explode("Content-Disposition: form-data;", $string);
            $payload = [];

            // reformat payload
            foreach ($array as $item) {
                if (stripos($item, 'name') !== false) {
                    $key = $this->getStringBetween($item, 'name="', '"');

                    // if it's a file
                    if (stripos($item, 'filename') != false) {
                        $filename = $this->getStringBetween($item, 'filename="', '"');
                        if (!$filename) continue;

                        $contentType = $this->getStringBetween($item, 'Content-Type: ', "\r");
                        $newArray = explode('Content-Type: ' . $contentType, $string);
                        $filePath = sys_get_temp_dir() . $filename;
                        file_put_contents($filePath, trim($newArray[1]));
                        $value = new UploadedFile($filePath, $filename, null, null, false, true);

                        $payload[$key] = $value;
                        $_FILES[$key] = [
                            'name' => $filename,
                            'type' => $contentType,
                            'tmp_name' => $filePath,
                            "error" => 0,
                            "size" => $value->getSize()
                        ];
                    } else {
                        $value = $this->getStringBetween($item, 'name="' . $key . '"', '----------------------------');
                        $payload[$key] = $value;
                        $_POST[$key] = $value;
                    }

                }
            }

            $request->merge($payload);
        }

        $response = $next($request);

        // let's delete tmp uploaded file, if any, to save space
        if (isset($filePath)) {
            @unlink($filePath);
        }
        return $response;
    }

    private function getStringBetween($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        $newString = substr($string, $ini, $len);
        return trim($newString);
    }
}
