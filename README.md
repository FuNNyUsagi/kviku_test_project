# kviku_test_project
Тестовое задание для компании Kviku
## Установка
У вас должен быть установлен [фреймворк Yii](https://github.com/yiisoft/yii.git), а также библиотека [arnaud-lb/php-rdkafka](https://github.com/arnaud-lb/php-rdkafka.git) для работы с системой Kafka
1. Клонирование репозитория

```git clone https://github.com/FuNNyUsagi/kviku_test_project.git```

2. Переход в директорию ```protected``` пректа

```cd yiiapp/protected```

3. Запуск скрипта для демонстрации возможностей

```php yiic kafkasend --safely=1```

4. JSON файлы по-умолчанию хранятся в ```/parsing```

## Описание параметра ```safely```
Данный параметр определяет безопасную отправку сообщений в топик Kafka:
1. При значении ```1``` - отправка без потерь сообщений, но с более длительным выполнением скрипта
   ![alt text](https://i.postimg.cc/hPcJTwKc/BWvp0n0p-Dwc.jpg)
2. При значении отличном от ```1``` - отправка с потерями, но практически мгновенная
   ![alt text](https://i.postimg.cc/VLgJRRNM/m-TSJlue-CZUQ.jpg)