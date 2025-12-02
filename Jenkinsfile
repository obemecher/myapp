pipeline {
    agent any

    environment {
        STACK = "mystack"
        COMPOSE_FILE = "docker-compose.yaml"
        MAX_RETRIES = "30"
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
                    sh """#!/bin/bash
                        echo "Ожидание 10 секунд перед проверкой..."
                        sleep 10

                        retries=0
                        max_retries=${MAX_RETRIES}
                        success=false
                        stack="${STACK}"

                        while [ \$retries -lt \$max_retries ]; do
                            echo "Попытка проверки сервисов: \$((retries + 1))"

                            all_ready=true
                            while IFS= read -r line; do
                                if [[ -z "\$line" ]]; then continue; fi

                                service_name=\$(echo "\$line" | awk '{print \$1}')
                                replicas=\$(echo "\$line" | awk '{print \$2}')

                                if [[ "\$replicas" != "1/1" ]]; then
                                    echo "Сервис \$service_name не готов: \$replicas"
                                    all_ready=false
                                    break
                                fi
                            done < <(docker service ls --filter label=com.docker.stack.namespace=\$stack --format '{{.Name}} {{.Replicas}}')

                            if [[ "\$all_ready" == true ]]; then
                                echo "Все сервисы стека \$stack подняты: готово."
                                success=true
                                break
                            fi

                            retries=\$((retries + 1))
                            sleep ${SLEEP_INTERVAL}
                        done

                        if [[ "\$success" != true ]]; then
                            echo "Ошибка: не все сервисы достигли состояния 1/1 за отведённое время."
                            exit 1
                        fi
                    """
                }
            }
        }

        stage('5. Проверка ошибок в задачах web-server') {
            steps {
                script {
                    sh """#!/bin/bash
                        echo "Проверка ошибок в задачах сервиса ${STACK}_web-server..."

                        # Получаем все значения из столбца ERROR для задач web-server
                        errors=\$(docker service ps --no-trunc --format '{{.Error}}' ${STACK}_web-server)

                        # Удаляем пустые строки и проверяем, есть ли хоть какая-то ошибка
                        non_empty_errors=\$(echo "\$errors" | grep -v '^$')

                        if [[ -n "\$non_empty_errors" ]]; then
                            echo "Обнаружены ошибки в задачах сервиса ${STACK}_web-server:"
                            echo "\$non_empty_errors"
                            echo "Билд завершается с ошибкой."
                            exit 1
                        else
                            echo "Ошибок в задачах сервиса ${STACK}_web-server не обнаружено."
                        fi
                    """
                }
            }
        }

        stage('6. Дополнительная диагностика (опционально)') {
            steps {
                sh 'docker service ls --filter label=com.docker.stack.namespace=${STACK}'
                sh 'docker service ps ${STACK}_web-server'
                sh 'docker service ps ${STACK}_db || true'
                sh 'docker service ps ${STACK}_phpmyadmin || true'
            }
        }
    }
}