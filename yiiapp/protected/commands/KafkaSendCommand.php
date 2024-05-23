<?php

require 'components/kafka/KafkaSender.php';

class KafkaSendCommand extends CConsoleCommand
{
    public function actionIndex($safely)
    {
        //Ограничение по памяти
        $memoryLimit = 2 * 1024 * 1024; // 2мб
        $startMemory = memory_get_usage();
        $startTime = microtime(true);

        //Продюсер сообщений кафки
        $kafka = new KafkaSender($safely);

        //Место хранения json-файлов
        $f1 = 'parsing/users_first.json';
        $f2 = 'parsing/users_second.json';

        //Открываем файлы на чтение
        $handle1 = fopen($f1, "r");
        $handle2 = fopen($f2, "r");

        //Инициализируем переменную подсчёта общего количества обработанных объектов
        $cnt = 0;
        //Переменные-буферы, для формирования json-строки при чтении файлов построчно
        $buffer1 = '';
        $buffer2 = '';

        while (!feof($handle1) || !feof($handle2)) {
            if (!feof($handle1)){
                $tmp1 = trim(fgets($handle1));
                if (strpos($tmp1, "}") !== false) { //Если строка содержит '}' то пытаемся декодировать json в буфере
                    $buffer1 .= str_replace(',', '', $tmp1);
                    $data = json_decode($buffer1, true);
                    if ($data !== null) { //если декодирование успешно - отправляем в кафку
                        $kafka->sendToKafka($data);
                        $cnt++;
                    }
                    $buffer1 = ''; //обнуляем буфер
                } else {
                    if (strpos($tmp1, "[") !== false) { //обнуляем буфер, т.к. знаем, что состроки, содержащей '[' начинается массив объектов
                        $buffer1 = '';
                    } else {
                        $buffer1 .= $tmp1;
                    }
                }
            }

            if (!feof($handle2)) {
                $tmp2 = trim(fgets($handle2));
                if (strpos($tmp2, "}") !== false) {
                    $buffer2 .= str_replace(',', '', $tmp2);
                    $data = json_decode($buffer2, true);
                    if ($data !== null) {
                        $kafka->sendToKafka($data);
                        $cnt++;
                    }
                    $buffer2 = '';
                } else {
                    if (strpos($tmp2, "[") !== false) {
                        $buffer2 = '';
                    } else {
                        $buffer2 .= $tmp2;
                    }
                }
            }

            //Проверяем потребление памяти
            $currentMemory = memory_get_usage() - $startMemory;
            if ($currentMemory > $memoryLimit) {
                echo "Превышено потребление памяти!\n";
                break;
            }
        }

        fclose($handle1);
        fclose($handle2);

        $endTime = microtime(true);

        $duration = $endTime - $startTime;
        $memoryUsage = memory_get_peak_usage();
        $objectsPerSecond = $cnt / $duration;

        echo "Обработано: $cnt элементов\n";
        echo "Время выполнения: $duration сек\n";
        echo "Потребление памяти: $memoryUsage байт\n";
        echo "Элементов в секунду: $objectsPerSecond\n";
    }
}
?>