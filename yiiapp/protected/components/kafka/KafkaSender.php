<?php

use RdKafka\Producer;
use RdKafka\Conf;
class KafkaSender {
    protected $topic;
    protected $producer;

    // Настройки Kafka
    protected $brokers = "kafka:9092"; // адрес и порт вашего Kafka брокера
    protected $topicName = "kafkatest"; // название вашего топика

    public function __construct()
    {
        // Создаем конфигурацию производителя
        $conf = new RdKafka\Conf();
        $conf->set('metadata.broker.list', $this->brokers);

        // Создаем производителя
        $this->producer = new RdKafka\Producer($conf);

        // Получаем топик
        $this->topic = $this->producer->newTopic($this->topicName);
    }

    public function sendToKafka($object)
    {
        // Сообщение, которое мы хотим отправить
        $message = json_encode($object);

        // Отправляем сообщение в топик
        $this->topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);

        $this->producer->poll(1);
        $this->producer->flush(5000);
    }
}