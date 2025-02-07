<?php

namespace App\Services;

class NumberToWordsService
{
    private $words = [
        ["", "یک", "دو", "سه", "چهار", "پنج", "شش", "هفت", "هشت", "نه"],
        ["ده", "یازده", "دوازده", "سیزده", "چهارده", "پانزده", "شانزده", "هفده", "هجده", "نوزده", "بیست"],
        ["", "", "بیست", "سی", "چهل", "پنجاه", "شصت", "هفتاد", "هشتاد", "نود"],
        ["", "یکصد", "دوصد", "سیصد", "چهارصد", "پانصد", "ششصد", "هفتصد", "هشتصد", "نهصد"],
        ['', " هزار ", " میلیون ", " میلیارد ", " بیلیون ", " بیلیارد ", " تریلیون ", " تریلیارد ", " کوآدریلیون ", " کادریلیارد ", " کوینتیلیون ", " کوانتینیارد ", " سکستیلیون ", " سکستیلیارد ", " سپتیلیون ", " سپتیلیارد ", " اکتیلیون ", " اکتیلیارد ", " نانیلیون ", " نانیلیارد ", " دسیلیون "]
    ];

    public function convertNumber($number)
    {
        list($integer, $fraction) = explode(".", (string) $number . ".0");

        $output = "";

        if ($integer[0] == "-") {
            $output = "منفی ";
            $integer = ltrim($integer, "-");
        } elseif ($integer[0] == "+") {
            $output = " مثبت ";
            $integer = ltrim($integer, "+");
        }

        if ($integer[0] == "0") {
            $output .= " صفر ";
        } else {
            $integer = str_pad($integer, 36, "0", STR_PAD_LEFT);
            $group = rtrim(chunk_split($integer, 3, " "), " ");
            $groups = explode(" ", $group);
            $groups2 = array_map(fn($g) => $this->convertThreeDigit($g[0], $g[1], $g[2]), $groups);

            for ($z = 0; $z < count($groups2); $z++) {
                if ($groups2[$z] != "") {
                    $output .= $groups2[$z] . $this->convertGroup(11 - $z) . (
                            $z < 11 && !array_search('', array_slice($groups2, $z + 1, -1)) && $groups2[11] != '' && $groups[11][0] == '0'
                                ? " و "
                                : " , "
                        );
                }
            }

            $output = rtrim($output, ", ");
        }

        if ($fraction > 0) {
            $output .= " اعشاریه";
            for ($i = 0; $i < strlen($fraction); $i++) {
                $output .= " " . $this->convertDigit($fraction[$i]);
            }
        }

        return $output;
    }

    function convertGroup($index)
    {
        switch ($index)
        {
            case 11: return " کوانتینیارد";
            case 10: return " کوینتیلیون";
            case 9: return " کادریلیارد";
            case 8: return " کوآدریلیون";
            case 7: return " تریلیارد";
            case 6: return " تریلیون";
            case 5: return " بیلیارد";
            case 4: return " بیلیون";
            case 3: return " میلیارد";
            case 2: return " میلیون";
            case 1: return " هزار";  // Make sure this is "هزار"
            case 0: return "";  
        }
    }
    

    private function convertThreeDigit($digit1, $digit2, $digit3)
    {
        if ($digit1 == "0" && $digit2 == "0" && $digit3 == "0") return "";

        $buffer = $digit1 != "0" ? $this->convertDigit($digit1) . " صد" . ($digit2 != "0" || $digit3 != "0" ? " و " : "") : "";
        return $buffer . ($digit2 != "0" ? $this->convertTwoDigit($digit2, $digit3) : ($digit3 != "0" ? $this->convertDigit($digit3) : ""));
    }

    private function convertTwoDigit($digit1, $digit2)
    {
        if ($digit1 == "1") {
            $words = ["ده", "یازده", "دوازده", "سیزده", "چهارده", "پانزده", "شانزده", "هفده", "هجده", "نونزده"];
            return $words[$digit2];
        }

        $prefixes = ["", "", "بیست", "سی", "چهل", "پنجاه", "شصت", "هفتاد", "هشتاد", "نود"];
        return $digit2 == "0" ? $prefixes[$digit1] : $prefixes[$digit1] . " و " . $this->convertDigit($digit2);
    }

    private function convertDigit($digit)
    {
        return ["صفر", "یک", "دو", "سه", "چهار", "پنج", "شش", "هفت", "هشت", "نه"][$digit];
    }
}
