<?php 
$bul=base_url();  ini_set('display_errors',0);
error_reporting(0);
$words = [
  [ 
      "",
      "یک",
      "دو",
      "سه",
      "چهار",
      "پنج",
      "شش",
      "هفت",
      "هشت",
      "نه" 
  ],
  [
      "ده",
      "یازده",
      "دوازده", 
      "سیزده", 
      "چهارده", 
      "پانزده",
      "شانزده",
      "هفده",
      "هجده",
      "نوزده", 
      "بیست" 
  ],
  [ 
      "",
      "",
      "بیست",
      "سی",
      "چهل",
      "پنجاه",
      "شصت",
      "هفتاد",
      "هشتاد",
      "نود" 
  ],
  [
      "",
      "یکصد",
      "دوصد",
      "سیصد",
      "چهارصد",
      "پانصد",
      "ششصد",
      "هفتصد",
      "هشتصد",
      "نهصد" 
  ],
  [
      '',
      " هزار ",
      " میلیون ",
      " میلیارد ",
      " بیلیون ",
      " بیلیارد ",
      " تریلیون ",
      " تریلیارد ",
      " کوآدریلیون ",
      " کادریلیارد ",
      " کوینتیلیون ",
      " کوانتینیارد ",
      " سکستیلیون ", 
      " سکستیلیارد ", 
      " سپتیلیون ", 
      " سپتیلیارد ", 
      " اکتیلیون ", 
      " اکتیلیارد ", 
      " نانیلیون ", 
      " نانیلیارد ", 
      " دسیلیون "  
  ]
];

// n2w
function convertNumber($number)
{
  list($integer, $fraction) = explode(".", (string) $number);

  $output = "";

  if ($integer[0] == "-")
  {
      $output = "منفی ";
      $integer    = ltrim($integer, "-");
  }
  else if ($integer[0] == "+")
  {
      $output = " مثبت ";
      $integer    = ltrim($integer, "+");
  }

  if ($integer[0] == "0")
  {
      $output .= " منفی ";
  }
  else
  {
      $integer = str_pad($integer, 36, "0", STR_PAD_LEFT);
      $group   = rtrim(chunk_split($integer, 3, " "), " ");
      $groups  = explode(" ", $group);

      $groups2 = array();
      foreach ($groups as $g)
      {
          $groups2[] = convertThreeDigit($g[0], $g[1], $g[2]);
      }

      for ($z = 0; $z < count($groups2); $z++)
      {
          if ($groups2[$z] != "")
          {
              $output .= $groups2[$z] . convertGroup(11 - $z) . (
                      $z < 11
                      && !array_search('', array_slice($groups2, $z + 1, -1))
                      && $groups2[11] != ''
                      && $groups[11][0] == '0'
                          ? " و "
                          : " , "
                  );
          }
      }

      $output = rtrim($output, ", ");
  }

  if ($fraction > 0)
  {
      $output .= " اعشاریه";
      for ($i = 0; $i < strlen($fraction); $i++)
      {
          $output .= " " . convertDigit($fraction[$i]);
      }
  }

  return $output;
}

function convertGroup($index)
{
  switch ($index)
  {
      case 11:
          return " کوانتینیارد";
      case 10:
          return " کوینتیلیون";
      case 9:
          return " کادریلیارد";
      case 8:
          return " کوآدریلیون";
      case 7:
          return " تریلیارد";
      case 6:
          return " تریلیون";
      case 5:
          return " بیلیارد";
      case 4:
          return " بیلیون";
      case 3:
          return " میلیارد";
      case 2:
          return " میلیون";
      case 1:
          return " هزار";
      case 0:
          return "";
  }
}

function convertThreeDigit($digit1, $digit2, $digit3)
{
  $buffer = "";

  if ($digit1 == "0" && $digit2 == "0" && $digit3 == "0")
  {
      return "";
  }

  if ($digit1 != "0")
  {
      $buffer .= convertDigit($digit1) . " صد";
      if ($digit2 != "0" || $digit3 != "0")
      {
          $buffer .= " و ";
      }
  }

  if ($digit2 != "0")
  {
      $buffer .= convertTwoDigit($digit2, $digit3);
  }
  else if ($digit3 != "0")
  {
      $buffer .= convertDigit($digit3);
  }

  return $buffer;
}

function convertTwoDigit($digit1, $digit2)
{
  if ($digit2 == "0")
  {
      switch ($digit1)
      {
          case "1":
              return "ده";
          case "2":
              return "بیست";
          case "3":
              return "سی";
          case "4":
              return "چهل";
          case "5":
              return "پنجاه";
          case "6":
              return "شصت";
          case "7":
              return "هفتاد";
          case "8":
              return "هشتاد";
          case "9":
              return "نود";
      }
  } else if ($digit1 == "1")
  {
      switch ($digit2)
      {
          case "1":
              return "یازده";
          case "2":
              return "دوازده";
          case "3":
              return "سیزده";
          case "4":
              return "چهارده";
          case "5":
              return "پانزده";
          case "6":
              return "شانزده";
          case "7":
              return "هفده";
          case "8":
              return "هجده";
          case "9":
              return "نونزده";
      }
  } else
  {
      $temp = convertDigit($digit2);
      switch ($digit1)
      {
          case "2":
              return "بیست و$temp";
          case "3":
              return "سی و$temp";
          case "4":
              return "چهل و$temp";
          case "5":
              return "پنجاه و$temp";
          case "6":
              return "شصت و$temp";
          case "7":
              return "هفتاد و$temp";
          case "8":
              return "هشتاد و$temp";
          case "9":
              return "نود و$temp";
      }
  }
}

function convertDigit($digit)
{
  switch ($digit)
  {
      case "0":
          return "صفر";
      case "1":
          return "یک";
      case "2":
          return "دو";
      case "3":
          return "سه";
      case "4":
          return "چهار";
      case "5":
          return "پنج";
      case "6":
          return "شش";
      case "7":
          return "هفت";
      case "8":
          return "هشت";
      case "9":
          return "نه";
  }
}
?>