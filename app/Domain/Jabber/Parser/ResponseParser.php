<?php


/**
 * Class ResponseParser
 */
class ResponseParser
{
    /**
     * @param string $input
     * @return array
     */
    public static function parse(string $input)
    {
        $regex = <<<regex
        /(\S+)=["']?((?:.(?!["']?\s+(?:\S+)=|\s*\/?[>"']))+.)["']?/
        regex;
        $items = [];
        $result = [];
        preg_match_all($regex, $input, $items);
        foreach ($items[1] as $key => $item) {
            $result[$item] = $items[2][$key];
        }
        if (isset($result['from'])) {
            $result['from'] = explode("/", $result['from'])[0];
        }
        return [
            'type' => $result['type'] ?? null,
            'content' => $result,
        ];
    }
}
