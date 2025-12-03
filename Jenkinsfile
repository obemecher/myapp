pipeline {
    agent any

    environment {
        STACK = "delivery-service"
        COMPOSE_FILE = "docker-compose.yaml"
        APP_DIR = "app"
    }

    stages {
        stage('1. Проверка PHP-синтаксиса') {
            steps {
                script {
                    echo "Проверка синтаксиса PHP-файлов..."
                    findFiles(glob: "${APP_DIR}/**/*.php").each { file ->
                        sh "php -l ${file.path}"
                    }
                }
            }
        }

        stage('2. Поиск опасных функций') {
            steps {
                script {
                    echo "Поиск потенциально опасных функций (eval, shell_exec и т.п.)..."
                    def dangerous = sh(
                        script: "grep -rE 'eval|exec|shell_exec|system|passthru|popen' ${APP_DIR}/ || true",
                        returnStdout: true
                    ).trim()

                    if (dangerous) {
                        echo "⚠️ Обнаружены потенциально опасные функции:\n${dangerous}"
                        // Можно либо остановить, либо просто предупредить
                        // error("Найдены опасные функции в коде!")
                    } else {
                        echo "✅ Опасные функции не найдены."
                    }
                }
            }
        }

        stage('3. Проверка Docker Swarm') {
            steps {
                script {
                    sh """
                        if ! docker info | grep -q 'Swarm: active'; then
                            echo "Инициализация Docker Swarm..."
                            docker swarm init || true
                        fi
                    """
                }
            }
        }

        stage('4. Очистка старого стека') {
            steps {
                script {
                    sh """
                        docker stack rm ${STACK} || true
                        sleep 10
                    """
                }
            }
        }

        stage('5. Развертывание стека') {
            steps {
                script {
                    sh """
                        docker stack deploy --with-registry-auth -c ${COMPOSE_FILE} ${STACK}
                    """
                }
            }
        }

        stage('6. Проверка запущенных сервисов') {
            steps {
                sh 'docker service ls'
                sh "docker service ps ${STACK}_web-server || true"
                sh "docker service ps ${STACK}_db-galera || true"
            }
        }
    }

    post {
        success {
            echo "✅ Пайплайн успешно завершён. Приложение развернуто."
        }
        failure {
            echo "❌ Пайплайн завершился с ошибкой."
        }
    }
}