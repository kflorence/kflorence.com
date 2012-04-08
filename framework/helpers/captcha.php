<?php

// Convert an integer to a string (ie: 1 = "one")
function int_to_words($x)
{
   // Words array
   $nwords = array(
        "zero", "one", "two", "three", "four", "five", "six", "seven",
        "eight", "nine", "ten", "eleven", "twelve", "thirteen",
        "fourteen", "fifteen", "sixteen", "seventeen", "eighteen",
        "nineteen", "twenty", 30 => "thirty", 40 => "forty",
        50 => "fifty", 60 => "sixty", 70 => "seventy", 80 => "eighty",
        90 => "ninety"
   );

   // Not a valid number
   if(!is_numeric($x)) $w = '#';
   else if(fmod($x, 1) != 0) $w = '#';

   // $x is a valid number
   else
   {
      // $x is a negative integar
      if ($x < 0)
      {
         $w = 'minus ';
         $x = -$x;
      }

      // $x is a positive integer
      else $w = '';

      // 0 to 20
      if ($x < 21) $w .= $nwords[$x];

      // 21 to 99
      else if ($x < 100)
      {
         $w .= $nwords[10 * floor($x/10)];
         $r = fmod($x, 10);
         if($r > 0)
            $w .= '-'. $nwords[$r];
      }

      // 100 to 999 (hundreds)
      else if ($x < 1000)
      {
         $w .= $nwords[floor($x/100)] .' hundred';
         $r = fmod($x, 100);
         if ($r > 0) $w .= ' and '. int_to_words($r);
      }

      // 1000 to 999999 (thousands)
      else if ($x < 1000000)
      {
         $w .= int_to_words(floor($x/1000)) .' thousand';
         $r = fmod($x, 1000);

         if ($r > 0)
         {
            $w .= ' ';
            if ($r < 100) $w .= 'and ';
            $w .= int_to_words($r);
         }
      }

      //  millions
      else
      {
         $w .= int_to_words(floor($x/1000000)) .' million';
         $r = fmod($x, 1000000);

         if ($r > 0)
         {
            $w .= ' ';
            if($r < 100) $word .= 'and ';
            $w .= int_to_words($r);
         }
      }
   }

   // Return our word
   return $w;
}

// Generates our CAPTCHA information
function generate_captcha()
{
    // Generate CAPTCHA
    $num1     = rand(1, 20);
    $num2     = rand(1, 20);

    // Return question and answer
    return array(
        ('What is ' . int_to_words($num1) . ' + ' . int_to_words($num2) . '?'),
        md5(int_to_words($num1 + $num2))
    );
}

// Normalizes CAPTCHA answers
function normalize_captcha_answer($answer)
{
    // Replace spaces with dashes (ie: twenty five = twenty-five)
    $answer = str_replace(' ', '-', $answer);

    // If the answer is numeric, change it to words, return it md5 encrypted
    return (is_numeric($answer) ?  md5(int_to_words($answer)) : md5($answer));
}