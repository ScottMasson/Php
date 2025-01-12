<?php 
declare (strict_types = 1);
namespace scottmasson\elephant\helper;
final class Variable
{
    static public function dump($variable, $label = null){
        ob_start();
        if ($label) {
            echo "<strong>{$label}:</strong><br>";
        }
        var_export($variable);
        $output = ob_get_clean();
        echo "<pre style='background-color: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>$output</pre>";
    }
}