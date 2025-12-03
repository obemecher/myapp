pipeline {
    agent any

    environment {
        STACK = "mystack"
        COMPOSE_FILE = "docker-compose.yaml"
    }

    stages {

        /* ---------- СТАТИЧЕСКИЕ ПРОВЕРКИ КОДА ---------- */

        stage('0. PHP Lint через Docker') {
            steps {
                sh '''
                    echo "Проверяем синтаксис PHP..."

                    docker run --rm \
                        -v $PWD/app:/src \
                        php:8.2-cli \
                        sh -c "find /src -type f -name '*.php' -print0 | xargs -0 -n1 php -l"
                '''
            }
        }

        stage('0.1 Проверка SQL-запросов в PHP-коде') {
            steps {
                script {
                    def sqlCheck = sh(
                        script: """
                            grep -R --include='*.php' -E 'SELECT|INSERT|UPDATE|DELETE' ./app |
                            grep -i 'users' | grep -vi 'password' || true
                        """,
                        returnStdout: true
                    ).trim()

                    if (sqlCheck) {
                        error """
                        ❌ SQL-ПРОВЕРКА НЕ ПРОЙДЕНА
                        Найдены SQL-запросы к таблице 'users' БЕЗ обязательного поля 'password'
                        ---------------------------------------------------------------
                        ${sqlCheck}
                        ---------------------------------------------------------------
                        """
                    } else {
                        echo "✔ SQL-проверка пройдена — все запросы корректны"
                    }
                }
            }
        }

        /* ---------- РАЗВЁРТЫВАНИЕ И ПРОВЕРКИ ДОКЕРА ---------- */

        stage('1. Проверка Docker Swarm') {
            steps {
                sh '''
                    if ! docker info | grep -q "Swarm: active"; then
                        docker swarm init || true
                    fi
                '''
            }
        }

        stage('2. Очистка старого стека') {
            steps {
                sh '''
                    docker stack rm ${STACK} || true
                    sleep 10
                '''
            }
        }

        stage('3. Развертывание стека') {
            steps {
                sh '''
                    docker stack deploy --with-registry-auth -c ${COMPOSE_FILE} ${STACK}
                '''
            }
        }

        stage('4. Проверка сервисов') {
            steps {
                sh 'docker service ls'
                sh 'docker service ps ${STACK}_web-server || true'
                sh 'docker service ps ${STACK}_db || true'
            }
        }
    }

    post {
        success {
            echo "✔ ПАЙПЛАЙН ВЫПОЛНЕН УСПЕШНО"
        }
        failure {
            echo "❌ ПАЙПЛАЙН УПАЛ — смотри лог выше"
        }
    }
}