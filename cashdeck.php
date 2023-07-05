<?php

    // Funkcja formatująca wyświetlanie nominału
    function coinFormat($coin) { 
        if ($coin >= 1) {
            return $coin." zł";
        } else {
             return ($coin*100)." gr";
        }
    }
    
    // Funkcja wyświetlajaca stan kasy
    function showCashdeck() {
    
        global $denomination, $count;
        
        echo "\nCashdeck status:\n";
        
        for ($i = 0; $i < count($count); $i++) {
            echo coinFormat($denomination[$i]).": ".$count[$i]." szt.\n";
        }
    }

    // Funkcja obliczajaca reszty, nominały muszą być posortowane malejąco, prostrza wersja
    function changeDistribution($change) {

        global $denomination, $count;

        echo "\nDla reszty ".number_format($change, 2)." zł:\n";
    
        $occurences_number = array(0,0,0,0,0,0,0,0,0);

        for ($i = 0; $i < count($occurences_number); $i++) {

            while ($denomination[$i]<=$change and $count[$i] > 0) {
                $change -= $denomination[$i];
                $change = number_format($change, 2);
                $occurences_number[$i] += 1;
                $count[$i] -= 1;
            }

            if ($occurences_number[$i]>0) {
                echo "Wydaj ".$occurences_number[$i]." monet ".coinFormat($denomination[$i])."\n";
            }
        }
    }

    // Funkcja obliczająca reszty, nominały mogą być podane w dowolnej kolejności, 
    // zabezpiecza np. w razie podania w tabeli $denomination najpierw 10gr a potem 20gr
    function changeDistributionWithControl($change) {

        global $denomination, $count;

        echo "\nDla reszty ".number_format($change, 2)." zł:\n";
    
        while ($change > 0) {
            $occurences_number = array(0,0,0,0,0,0,0,0,0);
            $min_i = 0;
            $min_count = INF;
            $min_val = 0;

            // Pętla tworzy tablicę rozkładu ile szt. danego nominału zmieści się w wydawanej reszcie
            for ($i = 0; $i < count($occurences_number); $i++) {

                $tmp = $change;
                
                while (number_format($denomination[$i]-$tmp,2)<=0 and $count[$i] > $occurences_number[$i]) {
                    $tmp -= $denomination[$i];
                    $tmp = number_format($tmp, 2);
                    $occurences_number[$i] += 1;
                }

                // Z utworzonej talicy wybierany jest nominał występujący najmniej razy
                // (Wybieramy najmniejszą liczbę potrzebnych monet)
                if ($occurences_number[$i] > 0 and $occurences_number[$i] < $min_count) {
                    $min_i = $i;
                    $min_count = $occurences_number[$i];
                    $min_val = $denomination[$i];
                }
            }

            echo "Wydaj ".$min_count." monet ".coinFormat($min_val)."\n";

            // Reszta jest zmniejszana o wybrany nominał * mieszcząca się ilość
            $change = number_format($change, 2);
            $change -= $min_val * $min_count;

            $count[$min_i] -= $min_count;
        }
    }


    $denomination = array(5, 2, 1, 0.5, 0.2, 0.1, 0.05, 0.02, 0.01); # Nominał
    $count = array(1, 3, 5, 10, 20, 200, 100, 100, 10000); # Ilość w kasie
    
    showCashdeck();
	
    $changes = array();

    for ($i = 1; $i < count($argv); $i++) {
	array_push($changes, (float) $argv[$i]);
    }

    
    foreach ($changes as $change) {
        changeDistributionWithControl($change);
    }

    showCashdeck();
?>