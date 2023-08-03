<?php

namespace HichemtabTech\LangifyLaravel;

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Langify
{

    private LangGeneratingType $generatingMode = LangGeneratingType::COMPLETE_THE_MISSING;
    private array $languages = [];

    private string $defaultLanguage = 'en';

    private array $defaultData = [];

    private array $existedData = [];

    private array $results = [];

    private ?Closure $initProgress = null;

    private ?Closure $progress = null;

    private int $all = 0;
    private int $done = 0;

    public function __construct()
    {
        //
    }

    /**
     * @param Closure|null $progress
     */
    public function setProgress(?Closure $progress): void
    {
        $this->progress = $progress;
    }

    /**
     * @param Closure|null $initProgress
     */
    public function setInitProgress(?Closure $initProgress): void
    {
        $this->initProgress = $initProgress;
    }

    public function setGeneratingMode(LangGeneratingType $generatingMode): void
    {
        $this->generatingMode = $generatingMode;
    }

    /**
     * @param array $defaultData
     */
    public function setDefaultData(array $defaultData): void
    {
        $this->defaultData = $defaultData;
    }

    /**
     * @param array $existedData
     */
    public function setExistedData(array $existedData): void
    {
        $this->existedData = $existedData;
    }

    /**
     * @param array|string[] $languages
     */
    public function setLanguages(array $languages): void
    {
        $this->languages = $languages;
    }

    /**
     * @param string $defaultLanguage
     */
    public function setDefaultLanguage(string $defaultLanguage): void
    {
        $this->defaultLanguage = $defaultLanguage;
    }

    private function prepare(): void
    {
        $this->all = 0;
        $this->done = 0;
        $this->results = [];
        $this->languages = array_unique($this->languages);
        $index = array_search($this->defaultLanguage, $this->languages);

        // Check if the value is found and remove it
        if ($index !== false) {
            unset($this->languages[$index]);
        }

        $this->languages = array_values($this->languages);
    }

    public function generate(): void
    {
        $this->prepare();
        $oneLevel = $this->flattenArray($this->defaultData);
        $this->all = count($oneLevel) * count($this->languages);
        call_user_func($this->initProgress, $this->all);
        foreach ($this->languages as $language) {
            $this->results[$language] = $this->generateLanguage($language);
        }
        call_user_func($this->progress, $this->all, $this->all, 100);
    }

    /**
     */
    private function generateLanguage(string $language): array
    {
        $translated = [];
        $default = $this->defaultData;
        $this->copyOneLevel($translated, $default, $language, $this->existedData[$language]??null);
        return $translated;
    }

    private function copyOneLevel(&$target, $source, $language, $existedSource): void
    {
        foreach ($source as $key => $value) {
            if (is_array($value)) {
                $target[$key] = [];
                $this->copyOneLevel($target[$key], $value, $language, $existedSource[$key]??null);
            }
            else {
                if ($this->generatingMode == LangGeneratingType::COMPLETE_THE_MISSING && $existedSource != null && isset($existedSource[$key])) {
                    $translatedValue = $existedSource[$key];
                }
                else{
                    try {
                        $translatedValue = $this->translate($value, $language);
                    } catch (GuzzleException $e) {
                        $translatedValue = "";
                    }
                }
                $target[$key] = $translatedValue;
                $this->done++;
                call_user_func($this->progress, $this->all, $this->done, round($this->done / $this->all * 100, 2));
            }
        }
    }

    /**
     * @return array
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @throws GuzzleException
     */
    private function translate(string $val, string $language): string
    {
        // Create a Guzzle client instance
        $client = new Client();

        // Set the URL to make the request to
        $url = 'https://api.mymemory.translated.net/get'; // Replace this with the actual URL you want to request data from

        $params = [
            'q' => $val,
            'langpair' => $this->defaultLanguage . '|' . $language,
        ];

        // Make the request using Guzzle
        $response = $client->get($url, [
            'query' => $params,
        ]);

        // Get the response body as a string
        $body = $response->getBody()->getContents();
        $data = json_decode($body);

        return $data?->responseData?->translatedText??"";
    }

    private function flattenArray($array): array
    {
        $result = [];
        foreach ($array as $value) {
            if (is_array($value)) {
                $result = array_merge($result, $this->flattenArray($value));
            } else {
                $result[] = $value;
            }
        }
        return $result;
    }

    private function refillArray($flatArray, $originalArray): array
    {
        $result = [];
        foreach ($originalArray as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->refillArray($flatArray, $value);
            } else {
                $result[$key] = array_shift($flatArray);
            }
        }
        return $result;
    }
}