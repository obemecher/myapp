pipeline {
    agent any

    environment {
        STACK = "mystack"
        COMPOSE_FILE = "docker-compose.yaml"
        MAX_RETRIES = "30"  // максимум 30 попыток (по 10 сек = до 5 минут)
        SLEEP_INTERVAL = "10"
    }

    stages {
        stage('1. Проверка Docker Swarm') {
            steps {
                script {
                    sh """
                        if ! docker info | grep -q 'Swarm: active'; then
                            echo "Swarm is not active. Initializing..."
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

        stage('4. Ожидание и проверка поднятия всех сервисов') {
            steps {
                script {
                    sh """
                        echo "Ожидание 10 секунд перед проверкой..."
                        sleep 10

                        retries=0
                        max_retries=${MAX_RETRIES}
                        success=false

                        while [ \$retries -lt \$max_retries ]; do
                            echo "Попытка проверки сервисов: \$((retries + 1))"

                            # Получаем список всех сервисов стека и проверяем реплики
                            all_ready=true
                            while IFS= read -r line; do
                                if [[ -z "\$line" ]]; then continue; fi

                                service_name=\$(echo "\$line" | awk '{print \$2}')
                                replicas=\$(echo "\$line" | awk '{print \$4}')

                                # Проверяем, что сервис относится к нашему стеку
                                if [[ "\$service_name" != ${STACK}_* ]]; then
                                    continue
                                fi

                                if [[ "\$replicas" != "1/1" ]]; then
                                    echo "Сервис \$service_name не готов: \$replicas"
                                    all_ready=false
                                    break
                                fi
                            done < <(docker service ls --filter label=com.docker.stack.namespace=${STACK} --format 'table {{.Name}}\\t{{.Replicas}}' | tail -n +2)

                            if [ "\$all_ready" = true ]; then
                                echo "Все сервисы стека ${STACK} подняты: готово."
                                success=true
                                break
                            fi

                            retries=\$((retries + 1))
                            sleep ${SLEEP_INTERVAL}
                        done

                        if [ "\$success" = false ]; then
                            echo "Ошибка: не все сервисы поднялись за отведённое время."
                            exit 1
                        fi
                    """
                }
            }
        }

        stage('5. Дополнительная диагностика (опционально)') {
            steps {
                sh 'docker service ls --filter label=com.docker.stack.namespace=${STACK}'
                sh 'docker service ps ${STACK}_web-server || true'
                sh 'docker service ps ${STACK}_db || true'
            }
        }
    }
}