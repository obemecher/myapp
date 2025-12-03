pipeline {
    agent any

    environment {
        STACK = "student-delivery"
        COMPOSE_FILE = "docker-compose.yaml"
        SITE_URL = "http://localhost"   // сюда поставь URL своего сайта
    }

    stages {

        stage('1. Проверка наличия важных файлов') {
            steps {
                script {
                    sh """
                        echo 'Проверяем структуру проекта...'
                        test -f app/connect.php
                        test -f app/login.php
                        test -f app/main.php
                    """
                }
            }
        }

        stage('2. PHP Lint') {
            steps {
                script {
                    sh """
                        echo 'Проверяем синтаксис PHP-файлов...'
                        find app -name '*.php' -print0 | xargs -0 -n1 php -l
                    """
                }
            }
        }

        stage('3. Проверка переменных в connect.php') {
            steps {
                script {
                    sh """
                        echo 'Проверяем наличие нужных параметров подключения...'
                        grep -q "servername" app/connect.php
                        grep -q "username" app/connect.php
                        grep -q "password" app/connect.php
                        grep -q "dbname" app/connect.php
                    """
                }
            }
        }

        stage('4. Проверка Docker окружения') {
            steps {
                script {
                    sh """
                        if ! docker info | grep -q 'Swarm: active'; then
                            docker swarm init || true
                        fi
                    """
                }
            }
        }

        stage('5. Деплой стека') {
            steps {
                script {
                    sh """
                        docker stack rm ${STACK} || true
                        sleep 5
                        docker stack deploy --with-registry-auth -c ${COMPOSE_FILE} ${STACK}
                    """
                }
            }
        }

        stage('6. Проверка что сайт отвечает') {
            steps {
                script {
                    sh """
                        echo 'Ожидаем поднятие сервиса...'
                        sleep 10

                        echo 'Пробуем открыть сайт: ${SITE_URL}'
                        curl -I --silent --fail ${SITE_URL} | head -n 1
                    """
                }
            }
        }
    }

    post {
        success {
            echo "Пайплайн успешно выполнен!"
        }
        failure {
            echo "Пайплайн завершился с ошибкой."
        }
    }
}