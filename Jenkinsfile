pipeline {
    agent any

    environment {
        STACK = "mystack"
        COMPOSE_FILE = "docker-compose.yaml"
    }

    stages {

        stage('1. Проверка Docker Swarm') {
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

        stage('2. Очистка старого стека') {
            steps {
                script {
                    sh """
                        docker stack rm ${STACK} || true
                        sleep 10
                    """
                }
            }
        }

        stage('3. Развертывание стека') {
            steps {
                script {
                    sh """
                        docker stack deploy --with-registry-auth -c ${COMPOSE_FILE} ${STACK}
                    """
                }
            }
        }

        stage('4. Проверка сервисов после деплоя') {
            steps {
                script {
                    sh """
                        echo "Ожидание 10 секунд перед проверкой..."
                        sleep 10

                        echo "ПРОВЕРКА: что все сервисы поднялись и имеют REPLICAS 1/1"

                        FAILED=0
                        for SRV in \$(docker service ls --format '{{.Name}}'); do
                            REPL=\$(docker service ls --filter name=\$SRV --format '{{.Replicas}}')

                            echo "Сервис: \$SRV | Реплики: \$REPL"

                            if [ "\$REPL" != "1/1" ]; then
                                echo "❌ Ошибка: сервис \$SRV не поднялся корректно"
                                FAILED=1
                            fi
                        done

                        if [ \$FAILED -ne 0 ]; then
                            echo "❌ Не все сервисы поднялись корректно"
                            exit 1
                        fi

                        echo "Все сервисы в статусе 1/1 ✔"
                    """
                }
            }
        }

        stage('5. Проверка ошибок в web-server') {
            steps {
                script {
                    sh """
                        echo "Ожидание 5 секунд перед проверкой ошибок..."
                        sleep 5

                        echo "ПРОВЕРКА: отсутствие ошибок в docker service ps ${STACK}_web-server"

                        ERRORS=\$(docker service ps ${STACK}_web-server --format '{{.Error}}' | grep -v '^$' || true)

                        if [ ! -z "\$ERRORS" ]; then
                            echo "❌ Ошибка найдена в web-server:"
                            echo "\$ERRORS"
                            exit 1
                        fi

                        echo "Ошибок нет ✔"
                    """
                }
            }
        }

        stage('6. Финальный вывод') {
            steps {
                sh 'docker service ls'
                sh "docker service ps ${STACK}_web-server || true"
                sh "docker service ps ${STACK}_db || true"
            }
        }
    }
}
